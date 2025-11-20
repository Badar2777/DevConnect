<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Optional: prevent deleting admin accounts
    $check = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();

    if ($res && $res['user_type'] !== 'admin') {
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
}

header("Location: admin_manage_clients.php");
exit();