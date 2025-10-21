<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Check if the user is logged in and is a harbor master
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Harbor Master') {
    header("Location: ../../login/login.php");
    exit();
}

require_once '../../config/db.php';
$pdo = pms_pdo();

$harbor_master_id = $_SESSION['user_id'];
$harbor_master_name = $_SESSION['user']['full_name'];

// Fetch all shipping requests
$stmt = $pdo->prepare("
    SELECT 
        sr.*, 
        p.company_name,
        sa.full_name AS agent_name 
    FROM 
        shipping_requests sr
    JOIN 
        partners p ON sr.company_partner_id = p.id
    JOIN 
        shipping_agents sa ON sr.agent_id = sa.id
    ORDER BY 
        sr.created_at DESC
");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

$flash_message = $_SESSION['flash_message'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_error']);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Harbor Master</title>
  <link rel="stylesheet" href="../dashboard.css">
  <style>
    .card .table {
      table-layout: fixed;
      width: 100%;
    }

    #requestsTable th, #requestsTable td {
      padding: 14px 16px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    #requestsTable th:nth-child(1),
    #requestsTable td:nth-child(1) { width: 8%; }

    #requestsTable th:nth-child(2),
    #requestsTable td:nth-child(2) { width: 15%; white-space: normal; }

    #requestsTable th:nth-child(3),
    #requestsTable td:nth-child(3) { width: 12%; white-space: normal; }

    #requestsTable th:nth-child(4),
    #requestsTable td:nth-child(4) { width: 12%; }

    #requestsTable th:nth-child(5),
    #requestsTable td:nth-child(5) { width: 10%; }

    #requestsTable th:nth-child(6),
    #requestsTable td:nth-child(6) { width: 15%; }

    #requestsTable th:nth-child(7),
    #requestsTable td:nth-child(7) { width: 10%; }

    #requestsTable th:nth-child(8),
    #requestsTable td:nth-child(8) { width: 18%; white-space: normal; }

    .action-form {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: flex-start;
    }

    .action-form textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid var(--line);
      border-radius: 8px;
      font-family: inherit;
      font-size: 14px;
    }

    .action-form .btn {
      padding: 6px 12px;
      font-size: 13px;
    }
    
    .action-form .btn-group {
      display: flex;
      gap: 8px;
    }

    .action-form .btn.reject {
      background: #e53e3e;
    }
    
    .filters {
      display: flex;
      gap: 10px;
      margin-bottom: 16px;
      align-items: center;
    }
    
    .filters input, .filters select {
      padding: 10px 14px;
      border: 1px solid var(--line);
      border-radius: 12px;
      outline: none;
      font-size: 15px;
      background: #fff;
    }

    #berthForm {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      align-items: flex-end;
    }

    #berthForm label {
      display: block;
      font-weight: 700;
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 8px;
    }

    #berthForm input,
    #berthForm select {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid var(--line);
      border-radius: 12px;
      outline: none;
      font-size: 15px;
      background: #fff;
    }
  </style>
</head>
<body>
<header class="topbar">
    <div class="title">
        <span class="logo">NeoPort</span>
        <span class="divider">/</span>
        <span class="role">Harbor Master</span>
    </div>
    <div class="user-info">
        <span class="who am i"><?php echo htmlspecialchars($harbor_master_name); ?></span>
        <a href="/pms/logout.php" class="btn ghost">Logout</a>
    </div>
</header>

<main class="page">
<section class="card">
  <div class="card-head">
    <h3>Ship Arrival Requests</h3>
    <p class="small">View incoming ship requests. Filter by status and take action.</p>
  </div>
  <div class="filters">
    <select id="filterStatus"><option>All</option><option>Pending</option><option>Approved</option><option>Rejected</option></select>
    <input id="searchShip" placeholder="Search ship name...">
    <button class="btn" onclick="loadRequests()">Refresh</button>
  </div>
  <table id="requestsTable" class="table"><thead><tr><th>Request ID</th><th>Company</th><th>Agent Name</th><th>Ship Name</th><th>IMO</th><th>ETA</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
        <?php if (empty($requests)): ?>
            <tr><td colspan="8">No shipping requests found.</td></tr>
        <?php else: ?>
            <?php foreach ($requests as $request): ?>
                <tr data-status="<?= htmlspecialchars($request['status']) ?>">
                    <td><?= $request['id'] ?></td>
                    <td><?= htmlspecialchars($request['company_name']) ?></td>
                    <td><?= htmlspecialchars($request['agent_name']) ?></td>
                    <td><?= htmlspecialchars($request['ship_name']) ?></td>
                    <td><?= htmlspecialchars($request['imo_number']) ?></td>
                    <td><?= htmlspecialchars(date('M j, Y H:i', strtotime($request['estimated_arrival_time']))) ?></td>
                    <td><span class="status-badge status-<?= htmlspecialchars($request['status']) ?>"><?= ucfirst(htmlspecialchars($request['status'])) ?></span></td>
                    <td class="actions">
                        <?php if ($request['status'] === 'pending'): ?>
                            <form class="action-form" onsubmit="event.preventDefault(); handleAction(this);">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <textarea name="rejection_reason" placeholder="Reason for rejection..." rows="2"></textarea>
                                <div class="btn-group">
                                    <button class="btn" type="submit" name="action" value="approve">Approve</button>
                                    <button class="btn reject" type="submit" name="action" value="reject">Reject</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <span>No actions available</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</section>

<section class="card">
  <div class="card-head">
    <h3>Berth Allocation</h3>
  </div>
  <form id="berthForm" onsubmit="event.preventDefault(); allocateBerth();">
    <div>
      <label for="shipId">Ship ID</label>
      <input id="shipId" placeholder="Ship ID">
    </div>
    <div>
      <label for="berthSelect">Berth</label>
      <select id="berthSelect"><option>Berth-1</option><option>Berth-2</option><option>Berth-3</option></select>
    </div>
    <div>
      <label for="dockTime">Est Dock Time</label>
      <input id="dockTime" type="datetime-local">
    </div>
    <div>
      <button class="btn">Allocate</button>
    </div>
  </form>
</section>

<section class="card">
  <div class="card-head">
    <h3>Current Berth Usage</h3>
  </div>
  <table class="table" id="berthUsageTable">
      <thead>
          <tr>
              <th>Berth</th>
              <th>Ship</th>
              <th>Status</th>
          </tr>
      </thead>
      <tbody>
          <!-- Data will be populated by JavaScript -->
      </tbody>
  </table>
</section>

</main>

</main>

<footer class="footer"><p>Port Management System © <?= date('Y') ?></p></footer>

<script src="script.js"></script>
<script>
    function allocateBerth() {
        const shipId = document.getElementById('shipId').value;
        const berthId = document.getElementById('berthSelect').value;
        const dockTime = document.getElementById('dockTime').value;

        if (!shipId || !berthId || !dockTime) {
            alert('Please fill all fields.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'allocate_berth');
        formData.append('shipping_request_id', shipId);
        formData.append('berth_id', berthId);
        formData.append('docking_time', dockTime);

        fetch('harbor_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Berth allocated successfully.');
                loadBerthUsage();
            } else {
                alert('Failed to allocate berth: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while allocating the berth.');
        });
    }

    function loadBerthUsage() {
        fetch('harbor_api.php?action=get_berth_usage')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#berthUsageTable tbody');
            tableBody.innerHTML = '';
            if (data.success && data.data.length > 0) {
                data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.berth_name}</td>
                        <td>${row.ship_name}</td>
                        <td>${row.status}</td>
                    `;
                    tableBody.appendChild(tr);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="3">No ships are currently docked.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = '<tr><td colspan="3">An error occurred while fetching berth usage.</td></tr>';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadBerthUsage();
    });

    function handleAction(form) {
        const formData = new FormData(form);
        const action = event.submitter.value;
        formData.append('action', action);

        fetch('harbor_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Failed to perform action: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while performing the action.');
        });
    }
</script>
</body>
</html>
