<?php
require 'functions.php';
require_login();
if(!check_role('importer')) { header('Location: dashboard.php'); exit; }

// Show containers in port storage and any storage-related info
$stmt = $conn->prepare("SELECT c.*, u.username AS owner FROM containers c LEFT JOIN users u ON c.owner_id=u.id ORDER BY c.created_at DESC");
$stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Storage & Delivery</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Container Storage & Delivery</h2>
  <div class="table-responsive">
  <table>
    <thead><tr><th>ID</th><th>Container</th><th>Owner</th><th>Storage Location</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php while($r=$res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['container_no']); ?></td>
        <td><?php echo htmlspecialchars($r['owner']); ?></td>
        <td><?php echo htmlspecialchars($r['location']); ?></td>
        <td><?php echo htmlspecialchars($r['status']); ?></td>
        <td><?php echo ($r['status']!=='released') ? 'Awaiting pickup' : 'Released'; ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>
