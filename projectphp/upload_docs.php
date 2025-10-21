<?php
require 'functions.php';
require_login();
if(!check_role('exporter')) { header('Location: dashboard.php'); exit; }
$user = current_user();

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['doc'])) {
    $uploaddir = __DIR__ . '/uploads/';
    if(!is_dir($uploaddir)) mkdir($uploaddir,0755,true);

    $orig = basename($_FILES['doc']['name']);
    $tmp = $_FILES['doc']['tmp_name'];
    $size = $_FILES['doc']['size'];
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = ['pdf','doc','docx','jpg','jpeg','png'];

    if(!in_array($ext,$allowed)) $msg = "Invalid file type.";
    elseif($size > 5 * 1024 * 1024) $msg = "File too large (max 5MB).";
    else {
        $new = time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','_',$orig);
        if(move_uploaded_file($tmp, $uploaddir.$new)) {
            $container_id = !empty($_POST['container_id']) ? intval($_POST['container_id']) : NULL;
            $ins = $conn->prepare("INSERT INTO documents (user_id, container_id, filename, original_name) VALUES (?,?,?,?)");
            $ins->bind_param("iiss",$user['id'],$container_id,$new,$orig);
            $ins->execute(); $msg = "Uploaded successfully.";
        } else $msg = "Upload failed.";
    }
}

// list containers
$stc = $conn->prepare("SELECT id, container_no FROM containers WHERE owner_id=? ORDER BY created_at DESC");
$stc->bind_param('i',$user['id']); $stc->execute(); $containers = $stc->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Upload Docs</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Upload Documents</h2>
  <?php if(!empty($msg)) echo '<p class="info">'.htmlspecialchars($msg).'</p>'; ?>
  <form method="post" enctype="multipart/form-data">
    <label>File (pdf/doc/docx/jpg/png) - max 5MB</label>
    <input type="file" name="doc" required>
    <label>Attach to Container (optional)</label>
    <select name="container_id">
      <option value="">-- none --</option>
      <?php while($r=$containers->fetch_assoc()): ?>
        <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['container_no']); ?></option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Upload</button>
  </form>

  <p><a href="dashboard.php">Back</a></p>
</div>
</body>
</html>

