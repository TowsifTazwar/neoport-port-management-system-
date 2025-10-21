// Function to handle approve/reject actions
function markStatus(id, verb){ 
  const reason = (verb==='reject') ? prompt('Enter rejection reason (optional):','') : '';
  fetch('customs_api.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({ action: verb, request_id: id, reason: reason })
  })
  .then(r=>r.json())
  .then(({success,message})=>{
    if(!success){ alert('Failed to update status: '+(message||'Unknown error')); return; }
    // update row UI
    const row=document.getElementById('row-'+id);
    if(row){
      const cell=row.querySelector('.actions');
      cell.textContent = (verb==='approve') ? 'Approved by Customs' : 'Rejected by Customs';
      cell.style.color = (verb==='approve') ? 'green' : 'red'; // color changes

      // Add background color to the row based on approval/rejection
      row.classList.add(verb === 'approve' ? 'approved-row' : 'rejected-row');
    }
    alert(message);
  })
  .catch(()=>alert('Network error while updating status.'));
}

// Function to handle undo action
function undoAction(id) {
    if (!confirm("Are you sure you want to undo this action?")) return;

    fetch('customs_api.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'undo', 
            request_id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Action undone successfully.");
            fetchCustomsData(); // Re-fetch the data to update the table
        } else {
            alert("Failed to undo the action: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while undoing the action.');
    });
}

// Function to fetch customs data from the backend
function fetchCustomsData() {
    fetch('customs_api.php?action=get_customs_data')
        .then(response => response.json())
        .then(({success, data}) => {
            const tbody = document.querySelector('#customsDataTable tbody');
            tbody.innerHTML = '';
            if (!success || !data || !data.length) {
                tbody.innerHTML = '<tr><td colspan="5">No data available.</td></tr>';
                return;
            }

            data.forEach(item => {
                const rowClass = item.customs_status === 'approved_by_customs' ? 'approved-row' : item.customs_status === 'rejected_by_customs' ? 'rejected-row' : '';
                const statusText = item.customs_status === 'approved_by_customs' ? 'Approved by Customs' : item.customs_status === 'rejected_by_customs' ? 'Rejected by Customs' : item.status;

                tbody.insertAdjacentHTML('beforeend', `
                    <tr id="row-${item.id}" class="${rowClass}">
                        <td>${item.id}</td>
                        <td>${item.ship_name}</td>
                        <td class="${item.customs_status === 'approved_by_customs' ? 'approved-status' : item.customs_status === 'rejected_by_customs' ? 'rejected-status' : ''}">${statusText}</td>
                        <td>${item.customs_status === 'approved_by_customs' ? 'Approved by Customs' : item.customs_status === 'rejected_by_customs' ? 'Rejected by Customs' : 'Pending'}</td>
                        <td class="actions">
                            <button class="btn" onclick="markStatus(${item.id}, 'approve')" ${item.customs_status ? 'disabled' : ''}>Approve</button>
                            <button class="btn danger" onclick="markStatus(${item.id}, 'reject')" ${item.customs_status ? 'disabled' : ''}>Reject</button>
                            <button class="btn primary" onclick="startInspection(${item.id})">Start Inspection</button>
                            <button class="btn secondary" onclick="undoAction(${item.id})" ${item.customs_status === 'approved_by_customs' || item.customs_status === 'rejected_by_customs' ? '' : 'disabled'}>Undo</button>
                        </td>
                    </tr>
                `);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load data.');
        });
}

// Fetch importer requests from the API
function fetchImporterRequests() {
    fetch('customs_api.php?action=get_importer_requests')
        .then(response => response.json())
        .then(({success, data}) => {
            const tbody = document.querySelector('#importer-requests-table tbody');
            tbody.innerHTML = '';
            if (!success || !data || !data.length) {
                tbody.innerHTML = '<tr><td colspan="5">No data available.</td></tr>';
                return;
            }

            data.forEach(item => {
                const rowClass = item.status === 'approved' ? 'approved-row' : item.status === 'rejected' ? 'rejected-row' : '';
                const statusText = item.status.charAt(0).toUpperCase() + item.status.slice(1);

                tbody.insertAdjacentHTML('beforeend', `
                    <tr id="importer-row-${item.id}" class="${rowClass}">
                        <td>${item.id}</td>
                        <td>${item.company_name}</td>
                        <td>${item.tax_id}</td>
                        <td>${item.trade_license}</td>
                        <td>${item.lc_number}</td>
                        <td>${item.invoice_id}</td>
                        <td><a href="/pms/dashboard/importer/${item.document_path}" target="_blank" class="btn">View</a></td>
                        <td class="${item.status}-status">${statusText}</td>
                        <td class="actions">
                            <button class="btn" onclick="handleImporterRequest(${item.id}, 'approve')" ${item.status !== 'pending' ? 'disabled' : ''}>Approve</button>
                            <button class="btn danger" onclick="handleImporterRequest(${item.id}, 'reject')" ${item.status !== 'pending' ? 'disabled' : ''}>Reject</button>
                            <button class="btn secondary" onclick="sendToLogistics(${item.id})" ${item.status !== 'approved' ? 'disabled' : ''}>Send to Logistics</button>
                        </td>
                    </tr>
                `);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load importer requests.');
        });
}

// Function to handle importer request actions
function handleImporterRequest(id, action) {
    fetch('customs_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: `importer_${action}`, request_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fetchImporterRequests();
        } else {
            alert('Failed to update status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Network error while updating status.'));
}

// Function to send approved requests to logistics
function sendToLogistics(id) {
    fetch('customs_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: 'send_to_logistics', request_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fetchImporterRequests();
        } else {
            alert('Failed to send to logistics: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Network error while sending to logistics.'));
}

// Fetch exporter requests from the API
function fetchExporterRequests() {
    fetch('customs_api.php?action=get_exporter_requests')
        .then(response => response.json())
        .then(({success, data}) => {
            const tbody = document.querySelector('#exporter-requests-table tbody');
            tbody.innerHTML = '';
            if (!success || !data || !data.length) {
                tbody.innerHTML = '<tr><td colspan="10">No data available.</td></tr>';
                return;
            }

            data.forEach(item => {
                const rowClass = item.status === 'approved' ? 'approved-row' : item.status === 'rejected' ? 'rejected-row' : '';
                const statusText = item.status.charAt(0).toUpperCase() + item.status.slice(1);

                tbody.insertAdjacentHTML('beforeend', `
                    <tr id="exporter-row-${item.id}" class="${rowClass}">
                        <td>${item.id}</td>
                        <td>${item.company_name}</td>
                        <td>${item.tax_id}</td>
                        <td>${item.trade_license}</td>
                        <td>${item.lc_number}</td>
                        <td>${item.invoice_id}</td>
                        <td>${item.ship_id}</td>
                        <td><a href="/pms/dashboard/exporter/${item.document_path}" target="_blank" class="btn">View</a></td>
                        <td class="${item.status}-status">${statusText}</td>
                        <td class="actions">
                            <button class="btn" onclick="handleExporterRequest(${item.id}, 'approve')" ${item.status !== 'pending' ? 'disabled' : ''}>Approve</button>
                            <button class="btn danger" onclick="handleExporterRequest(${item.id}, 'reject')" ${item.status !== 'pending' ? 'disabled' : ''}>Reject</button>
                            <button class="btn secondary" onclick="sendExporterToLogistics(${item.id})" ${item.status !== 'approved' ? 'disabled' : ''}>Send to Logistics</button>
                        </td>
                    </tr>
                `);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load exporter requests.');
        });
}

// Function to handle exporter request actions
function handleExporterRequest(id, action) {
    fetch('customs_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: `exporter_${action}`, request_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fetchExporterRequests();
        } else {
            alert('Failed to update status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Network error while updating status.'));
}

// Function to send approved exporter requests to logistics
function sendExporterToLogistics(id) {
    fetch('customs_api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ action: 'send_exporter_to_logistics', request_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            fetchExporterRequests();
        } else {
            alert('Failed to send to logistics: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Network error while sending to logistics.'));
}

// Call fetchCustomsData, fetchImporterRequests, and fetchExporterRequests to populate the tables on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchCustomsData();
    fetchImporterRequests();
    fetchExporterRequests();
});
