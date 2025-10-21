<?php
require_once 'database.php';
session_start();

function is_logged_in(){ return isset($_SESSION['user_id']); }

function require_login(){
    if(!is_logged_in()){ header('Location: login.php'); exit; }
}

function current_user(){
    global $conn;
    if(!is_logged_in()) return null;
    $id = intval($_SESSION['user_id']);
    $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function check_role($needed){
    $user = current_user(); if(!$user) return false;
    if(is_array($needed)) return in_array($user['role'],$needed);
    return $user['role'] === $needed;
}
?>

