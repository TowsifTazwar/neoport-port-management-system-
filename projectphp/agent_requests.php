<?php
require 'functions.php'; 
require_login(); 
if(!check_role('agent')){ header('Location: dashboard.php'); exit; }
$user = current_user();
$stmt = $conn->prepare('SELECT * FROM ship_requests WHERE agent_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i',$user['id']); 
$stmt->execute(); 
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>My Requests</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>My Ship Requests</h2>
  <table>
    <tr><th>ID</th><th>Vessel</th><th>Arrival</th><th>Berth</th><th>Status</th></tr>
    <?php while($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['vessel_name']); ?></td>
        <td><?php echo $row['arrival_date']; ?></td>
        <td><?php echo htmlspecialchars($row['berth']); ?></td>
        <td><?php echo htmlspecialchars($row['status']); ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>
