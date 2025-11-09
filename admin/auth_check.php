<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')
) {
    header("Location: ../index.php");
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$userId   = $_SESSION['user_id'];
$role     = $_SESSION['role'];
?>