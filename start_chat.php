<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    $current_user = $_SESSION['user_id'];
    $current_role = 'developer';
} elseif (isset($_SESSION['client_id'])) {
    $current_user = $_SESSION['client_id'];
    $current_role = 'client';
} else {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['user'])) {
    header("Location: inbox.php");
    exit();
}

$other_user = intval($_GET['user']);

// Prevent sending to self
if ($current_user === $other_user) {
    header("Location: inbox.php");
    exit();
}

// Fetch recipient role from users table
$receiver = $conn->query("SELECT id FROM users WHERE id = $other_user")->fetch_assoc();
$receiver_role = $receiver ? 'developer' : 'client';

// Check if conversation exists
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) 
       OR (sender_id = ? AND receiver_id = ?)");
$stmt->bind_param("iiii", $current_user, $other_user, $other_user, $current_user);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['total'] == 0) {
    $greeting = "👋 Hello! I'd like to connect with you.";

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, sender_role, receiver_role, message, created_at) 
                            VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisss", $current_user, $other_user, $current_role, $receiver_role, $greeting);
    $stmt->execute();
}

header("Location: inbox.php?chat=$other_user");
exit();
?>