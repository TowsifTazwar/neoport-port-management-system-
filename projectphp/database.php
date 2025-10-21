<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'portdb';


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
die('Connection failed: ' . $conn->connect_error);
}


// set charset
$conn->set_charset('utf8mb4');
?>
