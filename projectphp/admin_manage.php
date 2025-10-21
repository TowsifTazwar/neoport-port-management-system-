<?php
require 'functions.php';
require_login();
if(!check_role('admin')) { header('Location: dashboard.php'); exit; }

// Ship requests
$shipreq = $conn->query("SELECT sr.*, u.username FROM ship_requests sr LEFT JOIN users u ON sr.agent_id=u.id ORDER BY sr.created_at DESC");

// Approve / reject
if($_SERVER['REQUEST_METHOD']==='POST') {
    if(isset($_POST['approve_ship'])) {
        $id = intval($_POST['ship_id']); $berth = trim($_POST['berth']);
        if($berth!=='') {
            $s = $conn->prepare("UPDATE ship_requests SET status='approved', berth=? WHERE id=?");
            $s->bind_param('si',$berth,$id); $s->execute();
        }
    } elseif(isset($_POST['reject_ship'])) {
        $id = intval($_POST['ship_id']);
        $s = $conn->prepare("UPDATE ship_requests SET status='rejected' WHERE id=?");
        $s->bind_param('i',$id); $s->execute();
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Panel</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Admin Panel - Ship Requests</h2>
  <div class="table-responsive">
  <table>
    <thead><tr><th>ID</th><th>Vessel</th><th>Agent</th><th>Arrival</th><th>Status</th><th>Berth</th><th>Action</th></tr></thead>
    <tbody>
    <?php while($r=$shipreq->fetch_assoc()): ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo htmlspecialchars($r['vessel_name']); ?></td>
        <td><?php echo htmlspecialchars($r['username']); ?></td>
        <td><?php echo $r['arrival_date']; ?></td>
        <td><?php echo htmlspecialchars($r['status']); ?></td>
        <td><?php echo htmlspecialchars($r['berth']); ?></td>
        <td>
          <?php if($r['status']=='pending'): ?>
            <form method="post" style="display:inline-block">
              <input type="hidden" name="ship_id" value="<?php echo $r['id']; ?>">
              <input name="berth" placeholder="Assign berth">
              <button name="approve_ship">Approve</button>
            </form>
            <form method="post" style="display:inline-block">
              <input type="hidden" name="ship_id" value="<?php echo $r['id']; ?>">
              <button name="reject_ship">Reject</button>
            </form>
          <?php else: echo htmlspecialchars($r['status']); endif; ?>
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

