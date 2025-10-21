<?php
require 'functions.php';
require_login();
if(!check_role('exporter')) { header('Location: dashboard.php'); exit; }
$user = current_user();

// Create container
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['create_container'])) {
    $cno = trim($_POST['container_no']);
    if($cno==='') $msg = "Container no required.";
    else {
        $chk = $conn->prepare("SELECT id FROM containers WHERE owner_id=? AND container_no=?");
        $chk->bind_param("is",$user['id'],$cno); $chk->execute();
        if($chk->get_result()->num_rows>0) $msg = "Container already exists.";
        else {
            $st = 'awaiting'; $typ='export';
            $ins = $conn->prepare("INSERT INTO containers (owner_id, container_no, type, status) VALUES (?,?,?,?)");
            $ins->bind_param("isss",$user['id'],$cno,$typ,$st); $ins->execute(); $msg="Container added.";
        }
    }
}

// Fetch containers
$stmt = $conn->prepare("SELECT * FROM containers WHERE owner_id=? ORDER BY created_at DESC");
$stmt->bind_param('i',$user['id']); $stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>My Containers</title>
<link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>My Containers</h2>
  <?php if(!empty($msg)) echo '<p class="info">'.htmlspecialchars($msg).'</p>'; ?>
  <form method="post" class="form-inline">
    <input name="container_no" placeholder="Container No" required>
    <button name="create_container" type="submit">Add Container</button>
  </form>

  <div class="table-responsive">
  <table>
    <thead><tr><th>ID</th><th>Container No</th><th>Type</th><th>Status</th><th>Customs</th><th>Exp Departure</th></tr></thead>
    <tbody>
    <?php while($c = $res->fetch_assoc()): ?>
      <tr>
        <td><?php echo $c['id']; ?></td>
        <td><?php echo htmlspecialchars($c['container_no']); ?></td>
        <td><?php echo $c['type']; ?></td>
        <td><?php echo htmlspecialchars($c['status']); ?></td>
        <td><?php echo htmlspecialchars($c['customs_status']); ?></td>
        <td><?php echo $c['expected_departure'] ? $c['expected_departure'] : '-'; ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>

  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>
