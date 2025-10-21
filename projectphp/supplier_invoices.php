<?php
require 'functions.php';
require_login();
if(!check_role('supplier')) { header('Location: dashboard.php'); exit; }
$user = current_user();

$stmt = $conn->prepare("SELECT * FROM invoices WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param('i',$user['id']); $stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Invoices</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>My Invoices</h2>
  <div class="table-responsive">
  <table>
    <thead><tr><th>ID</th><th>Ref</th><th>Amount</th><th>Paid</th><th>Date</th></tr></thead>
    <tbody>
      <?php while($inv=$res->fetch_assoc()): ?>
        <tr>
          <td><?php echo $inv['id']; ?></td>
          <td><?php echo htmlspecialchars($inv['ref_type'].' #'.$inv['ref_id']); ?></td>
          <td><?php echo $inv['amount']; ?></td>
          <td><?php echo $inv['paid']; ?></td>
          <td><?php echo $inv['created_at']; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>
