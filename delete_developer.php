<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete messages
$stmt1 = $conn->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
if ($stmt1) {
    $stmt1->bind_param("ii", $user_id, $user_id);
    $stmt1->execute();
}

// Delete skills
$stmt2 = $conn->prepare("DELETE FROM skills WHERE user_id = ?");
if ($stmt2) {
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
}

// Delete projects
$stmt3 = $conn->prepare("DELETE FROM projects WHERE user_id = ?");
if ($stmt3) {
    $stmt3->bind_param("i", $user_id);
    $stmt3->execute();
}

// Delete user
$stmt4 = $conn->prepare("DELETE FROM users WHERE id = ?");
if ($stmt4) {
    $stmt4->bind_param("i", $user_id);
    $stmt4->execute();
}

// Destroy session
session_destroy();
header("Location: goodbye.php");
exit();
?>