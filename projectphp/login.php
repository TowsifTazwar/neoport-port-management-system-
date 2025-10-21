<?php
require 'database.php';
session_start();
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['login'])){
    $email=$_POST['email']; $password=$_POST['password'];
    $stmt=$conn->prepare("SELECT id,password FROM users WHERE email=?");
    $stmt->bind_param('s',$email); $stmt->execute();
    $user=$stmt->get_result()->fetch_assoc();
    if($user && password_verify($password,$user['password'])){
        $_SESSION['user_id']=$user['id']; header('Location: dashboard.php'); exit;
    } else $error='Invalid credentials';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
<h2>Login</h2>
<?php if(isset($_GET['registered'])) echo '<p class="success">Registration successful. Please login.</p>'; ?>
<?php if(isset($error)) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
<form method="post">
<input name="email" type="email" placeholder="Email" required>
<input name="password" type="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
</form>
<p>Don't have account? <a href="index.php">Register</a></p>
</div>
</body>
</html>


