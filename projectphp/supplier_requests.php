<?php
require 'functions.php';
require_login();
if(!check_role('supplier')) { header('Location: dashboard.php'); exit; }
$user = current_user();

// list equipment requests
$res = $conn->query("SELECT * FROM equipment_requests ORDER BY created_at DESC");

// update status
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_status'])){
    $id = intval($_POST['req_id']); $status = $_POST['status'];
    $allowed = ['assigned','delivered','closed'];
    if(in_array($status,$allowed)){
        $upd = $conn->prepare("UPDATE equipment_requests SET status=? WHERE id=?");
        $upd->bind_param('si',$status,$id); $upd->execute();
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Equipment Requests</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Equipment & Supply Requests</h2>
  <div class="table-responsive">
  <table>
    <thead><tr><th>ID</th><th>Requester</th><th>Equipment</th><th>Qty</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php while($r=$res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['requester']); ?></td>
        <td><?php echo htmlspecialchars($r['equipment']); ?></td>
        <td><?php echo $r['qty']; ?></td>
        <td><?php echo htmlspecialchars($r['status']); ?></td>
        <td>
          <form method="post" style="display:inline-block">
            <input type="hidden" name="req_id" value="<?php echo $r['id']; ?>">
            <select name="status">
              <option value="assigned">Assign</option>
              <option value="delivered">Mark Delivered</option>
              <option value="closed">Close</option>
            </select>
            <button name="update_status" type="submit">Update</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>

