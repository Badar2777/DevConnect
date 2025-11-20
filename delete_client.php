<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

// Delete messages where client is sender or receiver (optional, depending on structure)
$conn->query("DELETE FROM messages WHERE sender_id = $client_id OR receiver_id = $client_id");

// Delete any resumes (if implemented)
$conn->query("DELETE FROM resumes WHERE user_id = $client_id");

// Delete the user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();

// Clear session
session_unset();
session_destroy();

// Redirect to goodbye page
header("Location: goodbye.php");
exit();
?>