<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['sender_id'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$receiver_id = $_SESSION['user_id'];
$sender_id = intval($_POST['sender_id']);

$conn->query("UPDATE messages SET is_read = 1 WHERE sender_id = $sender_id AND receiver_id = $receiver_id");

echo json_encode(['status' => 'success']);
?>