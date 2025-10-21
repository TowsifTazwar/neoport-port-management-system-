<?php
require 'functions.php';
require_login();
if(!check_role('importer')) { header('Location: dashboard.php'); exit; }

// show import containers with status
$stmt = $conn->prepare("SELECT c.*, u.username AS owner FROM containers c LEFT JOIN users u ON c.owner_id=u.id WHERE c.type='import' ORDER BY c.created_at DESC");
$stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Track Shipments</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Incoming Shipments</h2>
  <div class="table-responsive">
  <table>
    <thead><tr><th>ID</th><th>Container</th><th>Owner</th><th>Status</th><th>Customs</th><th>Location</th><th>Exp Departure</th></tr></thead>
    <tbody>
    <?php while($r=$res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['container_no']); ?></td>
        <td><?php echo htmlspecialchars($r['owner']); ?></td>
        <td><?php echo htmlspecialchars($r['status']); ?></td>
        <td><?php echo htmlspecialchars($r['customs_status']); ?></td>
        <td><?php echo htmlspecialchars($r['location']); ?></td>
        <td><?php echo $r['expected_departure'] ? $r['expected_departure'] : '-'; ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>

