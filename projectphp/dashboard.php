<?php
require 'functions.php'; require_login(); $user=current_user();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="topbar">
<div>Welcome, <?php echo htmlspecialchars($user['username'].' ('.$user['role'].')'); ?></div>
<div><a href="logout.php">Logout</a></div>
</div>
<div class="container">
<h2>Dashboard</h2>
<ul class="menu">
<?php if($user['role']=='agent'): ?>
<li><a href="agent_ship_request.php">Submit Ship Arrival Request</a></li>
<li><a href="agent_requests.php">My Requests</a></li>
<li><a href="agent_invoices.php">Invoices</a></li>
<?php endif; ?>
<?php if($user['role']=='exporter'): ?>
<li><a href="exporter_containers.php">My Containers</a></li>
<li><a href="upload_docs.php">Upload Documents</a></li>
<li><a href="exporter_invoices.php">Invoices</a></li>
<?php endif; ?>
<?php if($user['role']=='importer'): ?>
<li><a href="importer_track.php">Track Incoming Shipments</a></li>
<li><a href="importer_storage.php">Container Storage & Delivery</a></li>
<?php endif; ?>
<?php if($user['role']=='supplier'): ?>
<li><a href="supplier_requests.php">Equipment Requests</a></li>
<li><a href="supplier_invoices.php">Invoices</a></li>
<?php endif; ?>
<?php if($user['role']=='admin'): ?>
<li><a href="admin_manage.php">Admin Panel</a></li>
<?php endif; ?>
</ul>
</div>
</body>
</html>

