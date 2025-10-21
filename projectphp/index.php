<?php
require 'database.php';
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['register'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss',$username,$email,$password,$role);
    if($stmt->execute()){ header('Location: login.php?registered=1'); exit; }
    else $error = 'Registration failed: '.$conn->error;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Port System - Register</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
<h2>Register</h2>
<?php if(isset($error)) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
<form method="post">
<input name="username" placeholder="Username" required>
<input name="email" type="email" placeholder="Email" required>
<input name="password" type="password" placeholder="Password" required>
<select name="role" required>
<option value="agent">Shipping Agent</option>
<option value="exporter">Exporter</option>
<option value="importer">Importer</option>
<option value="supplier">Supplier</option>
</select>
<button type="submit" name="register">Register</button>
</form>
<p>Already registered? <a href="login.php">Login</a></p>
</div>
</body>
</html>


