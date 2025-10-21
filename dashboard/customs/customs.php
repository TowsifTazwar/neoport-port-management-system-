<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Ensure only Customs & Compliance Officer can access this page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Customs & Compliance Officer') {
    header("Location: ../../login/login.php");
    exit();
}

$customs_officer_name = $_SESSION['user']['full_name'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customs & Compliance Officer Dashboard</title>
  <link rel="stylesheet" href="../dashboard.css?v=<?php echo time(); ?>">
  <style>
    .table {
      table-layout: fixed;
      width: 100%;
    }
    
    .table th, .table td {
      padding: 10px 8px;
      white-space: normal;
      overflow: hidden;
      text-overflow: ellipsis;
      font-size: 14px;
    }
    #importer-requests-table th, #importer-requests-table td {
        padding: 10px 8px;
        font-size: 14px;
        white-space: normal;
    }
    .btn {
        padding: 8px 12px;
        font-size: 12px;
    }
    .popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .popup-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        text-align: left;
    }

    .close-btn {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        right: 10px;
        top: 10px;
        cursor: pointer;
    }

    .approved-row {
        background-color: #d4edda;
    }

    .rejected-row {
        background-color: #f8d7da;
    }

    .approved-status {
        color: green;
    }

    .rejected-status {
        color: red;
    }

    #importer-requests-table .actions .btn {
        display: inline-block;
        margin-right: 8px;
    }

    #importer-requests-table .actions .btn:last-child {
        margin-right: 0;
    }

    #exporter-requests-table .actions {
        text-align: center;
    }

    #exporter-requests-table .actions .btn {
        display: block;
        width: 150px;
        margin: 5px auto;
    }

    #exporter-requests-table .actions .btn:last-child {
        margin-bottom: 0;
    }

    #customsDataTable .actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .table {
        font-size: 16px;
    }

    .btn.danger {
        background-color: #dc3545 !important;
        color: white;
    }

    .btn.primary {
        background-color: #007bff;
        color: white;
    }

    .btn.secondary {
        background-color: #6c757d;
        color: white;
    }
  </style>
</head>
<body>
<header class="topbar">
    <div class="title">
        <span class="logo">PMS</span>
        <span class="divider">/</span>
        <span class="role">Customs & Compliance Officer</span>
    </div>
    <div class="user-info">
        <span class="who am i"><?php echo htmlspecialchars($customs_officer_name); ?></span>
        <a href="/pms/logout.php" class="btn ghost">Logout</a>
    </div>
</header>

<main class="page">
  <section class="card">
    <div class="card-head">
        <h3>Importer Document Requests</h3>
    </div>
    <table id="importer-requests-table">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>Company Name</th>
          <th>Tax ID</th>
          <th>Trade License</th>
          <th>LC Number</th>
          <th>Invoice ID</th>
          <th>Document</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data will be populated by JavaScript -->
      </tbody>
    </table>
  </section>
  <section class="card">
    <div class="card-head">
        <h3>Exporter Document Requests</h3>
    </div>
    <table id="exporter-requests-table" class="table">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>Company Name</th>
          <th>Tax ID</th>
          <th>Trade License</th>
          <th>LC Number</th>
          <th>Invoice ID</th>
          <th>Ship ID/Ship Name</th>
          <th>Document</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Data will be populated by JavaScript -->
      </tbody>
    </table>
  </section>
  <section class="card">
    <div class="card-head">
        <h3>Cargo Inspection Queue</h3>
        <p class="small">Start inspections and change clearance status with reasons.</p>
    </div>
    <table id="customsDataTable" class="table">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>Ship Name</th>
          <th>Status</th>
          <th>Customs Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </section>
</main>

</main>

<footer class="footer"><p>Port Management System © <?= date('Y') ?></p></footer>

<!-- Partner popup -->
<div id="partnerPopup" class="popup" role="dialog" aria-modal="true">
  <div class="popup-content">
    <span class="close-btn" onclick="closePopup()">×</span>
    <h3>Partner Details</h3>
    <div id="partnerDetails" class="small">Loading…</div>
  </div>
</div>

<script>
// Function to show the pop-up and load partner data
function startInspection(requestId){
  fetch('customs_api.php?action=get_partner_data&request_id=' + requestId)
    .then(r=>r.json())
    .then(({success, partner, message})=>{
      if(!success){ alert(message || 'Failed to fetch partner data.'); return; }
      const d=document.getElementById('partnerDetails');
      d.innerHTML =
        `<strong>Company Name:</strong> ${partner.company_name}<br>
         <strong>Contact Name:</strong> ${partner.contact_name}<br>
         <strong>Contact Email:</strong> ${partner.contact_email}<br>
         <strong>Phone:</strong> ${partner.phone}<br>
         <strong>Address:</strong> ${partner.address}<br>
         <strong>Trade License:</strong> ${partner.trade_license}<br>
         <strong>Tax ID:</strong> ${partner.tax_id}`;
      document.getElementById('partnerPopup').style.display='flex';
    })
    .catch(()=>alert('An error occurred while fetching partner data.'));
}

function closePopup(){ document.getElementById('partnerPopup').style.display='none'; }

// Function to handle approve/reject actions
function markStatus(id, verb){ 
  const reason = (verb === 'reject') ? prompt('Enter rejection reason (optional):','') : '';
  fetch('customs_api.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({ action: verb, request_id: id, reason: reason })
  })
  .then(r => r.json())
  .then(({ success, message, updatedData }) => {
    if (!success) { alert('Failed to update status: ' + (message || 'Unknown error')); return; }

    // Update the table row dynamically
    const row = document.getElementById('row-' + id);
    const customsStatusCell = row.querySelector('.customs-status');
    const actionButtonsCell = row.querySelector('.actions');

    customsStatusCell.textContent = updatedData.customs_status === 'approved_by_customs' ? 'Approved by Customs' : 'Rejected by Customs';
    customsStatusCell.style.color = updatedData.customs_status === 'approved_by_customs' ? 'green' : 'red';

    actionButtonsCell.innerHTML = `<span>${updatedData.customs_status === 'approved_by_customs' ? 'Approved by Customs' : 'Rejected by Customs'}</span>`;

    // Disable buttons after action
    actionButtonsCell.querySelectorAll('button').forEach(button => button.disabled = true);

    alert(message);
  })
  .catch(() => alert('Network error while updating status.'));
}

// Fetch all approved shipping requests from the API
document.addEventListener('DOMContentLoaded', function () {
  fetch('customs_api.php?action=get_customs_data')
    .then(r => r.json())
    .then(({ success, data, message }) => {
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
            <td>${item.status}</td>
            <td class="customs-status">${item.customs_status === 'approved_by_customs' ? 'Approved by Customs' : item.customs_status === 'rejected_by_customs' ? 'Rejected by Customs' : 'Pending'}</td>
            <td class="actions">
              <button class="btn" onclick="markStatus(${item.id}, 'approve')" ${item.customs_status ? 'disabled' : ''}>Approve</button>
              <button class="btn danger" onclick="markStatus(${item.id}, 'reject')" ${item.customs_status ? 'disabled' : ''}>Reject</button>
              <button class="btn primary" onclick="startInspection(${item.id})">Start Inspection</button>
              ${item.customs_status && !item.customs_status.includes('by customs') ? `<button class="btn danger" onclick="markStatus(${item.id}, 'undo')">Undo</button>` : ''}
            </td>
          </tr>
        `);
      });
    })
    .catch(() => alert('Failed to load queue.'));
});
</script>

<script src="script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
