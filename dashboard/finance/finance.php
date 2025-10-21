<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Finance & Billing Officer') {
    header("Location: ../../login/login.php");
    exit();
}

$finance_officer_name = $_SESSION['user']['full_name'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Finance & Billing Officer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <h1>Port System — Finance & Billing Officer</h1>
  <div class="role-desc">Welcome, <?php echo htmlspecialchars($finance_officer_name); ?></div>
</header>

<main class="container">
<section class="card">
  <h2>Invoice Generation</h2>
  <p class="small">Fill fees and generate invoice (frontend demo).</p>
  <form onsubmit="event.preventDefault(); generateInvoice();">
    <label>Client</label><input id="clientName" placeholder="Client Name">
    <label>Berth Fee</label><input id="berthFee" type="number" value="0">
    <label>Storage Fee</label><input id="storageFee" type="number" value="0">
    <label>Customs Duty</label><input id="dutyFee" type="number" value="0">
    <div style="margin-top:8px;"><button class="btn primary">Generate Invoice</button></div>
  </form>
</section>

<section class="card">
  <h2>Invoices</h2>
  <table id="financeDataTable">
    <thead>
      <tr>
        <th>Request ID</th>
        <th>Ship Name</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Data will be populated by JavaScript -->
    </tbody>
  </table>
</section>

</main>

<footer class="footer"><p>Port Management System © <?= date('Y') ?></p></footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('finance_api.php?action=get_finance_data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tableBody = document.querySelector('#financeDataTable tbody');
                tableBody.innerHTML = '';
                if (data.data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="4">No data available.</td></tr>';
                } else {
                    data.data.forEach(item => {
                        const row = `
                            <tr>
                                <td>${item.id}</td>
                                <td>${item.ship_name}</td>
                                <td>${item.status}</td>
                                <td>
                                    <button class="btn">View Details</button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                }
            } else {
                alert('Failed to fetch data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching data.');
        });
});
</script>
<script src="script.js"></script>
</body>
</html>
