<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (
  !isset($_SESSION['user_id']) ||
  !isset($_SESSION['user']['role']) ||
  $_SESSION['user']['role'] !== 'Logistics & Transport Coordinator'
) {
  header("Location: ../../login/login.php");
  exit();
}

$logistics_coordinator_name = $_SESSION['user']['full_name'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Logistics & Transport Coordinator</title>
  <link rel="stylesheet" href="../dashboard.css">
  <style>
    .table {
      table-layout: fixed;
      width: 100%;
    }
    
    .table th, .table td {
      padding: 14px 16px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
<header class="topbar">
    <div class="title">
        <span class="logo">NeoPort</span>
        <span class="divider">/</span>
        <span class="role">Logistics & Transport Coordinator</span>
    </div>
    <div class="user-info">
        <span class="who am i"><?php echo htmlspecialchars($logistics_coordinator_name); ?></span>
        <a href="/pms/logout.php" class="btn ghost">Logout</a>
    </div>
</header>

<main class="page">
  <!-- Containers Available for Transport -->
  <section class="card">
    <div class="card-head">
        <h3>Containers Available for Transport</h3>
        <p class="small">These are containers that the Cargo & Warehouse team has already stored.</p>
    </div>
    <table id="availableTable" class="table">
      <thead>
        <tr>
          <th>Slot</th>
          <th>Container</th>
          <th>Ship</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody><!-- rows injected by JS --></tbody>
    </table>
  </section>

  <!-- Document tasks -->
  <section class="card">
    <div class="card-head">
        <h3>Importer and Exporter Tasks</h3>
        <p class="small">Preview of documents from customs.</p>
    </div>
    <table id="documentTasksTable" class="table">
      <thead>
        <tr>
          <th>Company Name</th>
          <th>Contact No.</th>
          <th>Container ID</th>
          <th>Batch #</th>
          <th>Address</th>
          <th>Ship ID/Ship Name</th>
        </tr>
      </thead>
      <tbody>
        <tr><td colspan="6" class="small">No tasks loaded yet.</td></tr>
      </tbody>
    </table>
  </section>

  <!-- Assign Transportation -->
  <section class="card">
    <div class="card-head">
        <h3>Assign Transportation</h3>
    </div>
    <form id="assignTransportForm" onsubmit="event.preventDefault(); assignTransportForm();">
      <label>Company Name</label>
      <input id="companyName" placeholder="e.g., ABC Ltd.">

      <label>Contact No.</label>
      <input id="contactNo" placeholder="e.g., +8801XXXXXXXXX">

      <label>Container ID / Name</label>
      <input id="containerName" placeholder="e.g., CONT-101">

      <label>Batch Number</label>
      <input id="batchNumber" placeholder="e.g., B-2025-001">

      <label>Address</label>
      <textarea id="address" placeholder="Street, City, Postcode"></textarea>

      <label>Assign Transport</label>
      <select id="transportSelect">
        <option>Transport-1</option><option>Transport-2</option><option>Transport-3</option>
        <option>Transport-4</option><option>Transport-5</option><option>Transport-6</option>
        <option>Transport-7</option><option>Transport-8</option><option>Transport-9</option>
        <option>Transport-10</option>
      </select>

      <div style="margin-top:8px;">
        <button class="btn">Assign</button>
      </div>
    </form>
  </section>

  <!-- Status of Shipment (now shows assignments + inline dropdown to update) -->
  <section class="card">
    <div class="card-head">
        <h3>Status of Shipment</h3>
        <p class="small">All consignments that have transport assigned. Update status using the dropdown.</p>
    </div>
    <table id="shipmentTable" class="table">
      <thead>
        <tr>
          <th>Assigned At</th>
          <th>Company</th>
          <th>Container</th>
          <th>Batch</th>
          <th>Transport</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody><!-- rows injected by JS --></tbody>
    </table>
  </section>
</main>

</main>

<footer class="footer"><p>Port Management System © <?= date('Y') ?></p></footer>

<script>
// Load "Available for Transport" rows
document.addEventListener('DOMContentLoaded', () => {
  fetch('logistics_api.php?action=get_logistics_data')
    .then(r => r.json())
    .then(({success, data}) => {
      const tbody = document.querySelector('#availableTable tbody');
      tbody.innerHTML = '';
      if (!success || !data || !data.length) {
        tbody.innerHTML = '<tr><td colspan="4">No containers available for transport.</td></tr>';
        return;
      }
      data.forEach(row => {
        tbody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>${row.storage_slot_name ?? ''}</td>
            <td>${row.container_name ?? ''}</td>
            <td>${row.ship_name ?? ''}</td>
            <td>${row.status ?? ''}</td>
          </tr>
        `);
      });
    })
    .catch(() => alert('Failed to load available containers.'));
});

// Fetch Document Tasks
document.addEventListener('DOMContentLoaded', () => {
  fetch('logistics_api.php?action=get_document_tasks')
    .then(r => r.json())
    .then(({success, data}) => {
      const tbody = document.querySelector('#documentTasksTable tbody');
      tbody.innerHTML = '';
      if (!success || !data || !data.length) {
        tbody.innerHTML = '<tr><td colspan="6">No tasks available.</td></tr>';
        return;
      }
      data.forEach(row => {
        tbody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>${row.company_name}</td>
            <td>${row.contact_no}</td>
            <td>${row.container_name}</td>
            <td>${row.batch_number}</td>
            <td>${row.address}</td>
            <td>${row.ship_id ?? ''}</td>
          </tr>
        `);
      });
    })
    .catch(() => alert('Failed to load document tasks.'));
});

// -------- SAVE transport assignment to DB (unchanged behavior) --------
function assignTransportForm() {
  const payload = {
    company:   document.getElementById('companyName').value.trim(),
    contact:   document.getElementById('contactNo').value.trim(),
    container: document.getElementById('containerName').value.trim(),
    batch:     document.getElementById('batchNumber').value.trim(),
    address:   document.getElementById('address').value.trim(),
    transport: document.getElementById('transportSelect').value
  };
  if (!payload.company || !payload.container) {
    alert('Company and Container are required.');
    return;
  }
  fetch('logistics_api.php?action=assign_transport', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(payload)
  })
  .then(r => r.json())
  .then(({success, message}) => {
    alert(message || (success ? 'Saved.' : 'Failed to save.'));
    if (success) {
      document.getElementById('assignTransportForm').reset();
      loadTransportAssignments(); // refresh the status table
    }
  })
  .catch(() => alert('Failed to assign transport.'));
}

// -------- STATUS OF SHIPMENT: list + update ----------
const STATUS_OPTIONS = [
  'Pending Pickup','Dispatched','En Route','Arrived at City',
  'Out for Delivery','Delivered','Completed'
];

function loadTransportAssignments() {
  fetch('logistics_api.php?action=get_transport_assignments')
    .then(r => r.json())
    .then(({success, data}) => {
      const tbody = document.querySelector('#shipmentTable tbody');
      tbody.innerHTML = '';
      if (!success || !data || !data.length) {
        tbody.innerHTML = '<tr><td colspan="7">No transport assignments yet.</td></tr>';
        return;
      }

      data.forEach(row => {
        const selectId = `status-${row.id}`;
        const opts = STATUS_OPTIONS.map(s =>
          `<option value="${s}" ${row.shipment_status === s ? 'selected' : ''}>${s}</option>`
        ).join('');
        tbody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>${row.created_at ?? ''}</td>
            <td>${row.company_name ?? ''}</td>
            <td>${row.container_name ?? ''}</td>
            <td>${row.batch_number ?? ''}</td>
            <td>${row.assigned_transport ?? ''}</td>
            <td>
              <select id="${selectId}">${opts}</select>
              ${row.status_updated_at ? `<div class="small">Updated: ${row.status_updated_at}</div>` : ''}
            </td>
            <td>
              <button class="btn primary" onclick="saveShipmentStatus(${row.id}, '${selectId}')">Save</button>
            </td>
          </tr>
        `);
      });
    })
    .catch(() => alert('Failed to load transport assignments.'));
}

function saveShipmentStatus(id, selectId) {
  const status = document.getElementById(selectId).value;
  fetch('logistics_api.php?action=update_shipment_status', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ id, status })
  })
  .then(r => r.json())
  .then(({success, message}) => {
    alert(message || (success ? 'Status saved.' : 'Failed to save status.'));
    if (success) loadTransportAssignments();
  })
  .catch(() => alert('Failed to update status.'));
}

// first load on page open
document.addEventListener('DOMContentLoaded', loadTransportAssignments);
</script>

<!-- keep existing role-local helpers -->
<script src="script.js"></script>
</body>
</html>
