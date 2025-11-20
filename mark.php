<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['chat_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'unauthorized']);
    exit();
}

$current_user = $_SESSION['user_id'];
$chat_user_id = intval($_POST['chat_id']);

$stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
$stmt->bind_param("ii", $chat_user_id, $current_user);
$stmt->execute();

echo json_encode(['status' => 'success']);