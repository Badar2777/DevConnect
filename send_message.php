<?php
session_start();
require_once 'includes/db.php';
header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'unauthorized']);
    exit;
}

$sender_id = $_SESSION['user_id'];
$message = isset($_POST['message']) ? trim($_POST['message']) : null;
if ($message === '') $message = null;

$attachment = null;

// Handle file upload
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'zip', 'doc', 'docx', 'ppt', 'pptx'];
    $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        $filename = uniqid('file_', true) . '.' . $ext;
        $target = 'uploads/messages/' . $filename;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
            $attachment = $filename;
        }
    }
}

// Handle private message only
if (isset($_POST['receiver_id']) && intval($_POST['receiver_id']) > 0 && ($message || $attachment)) {
    $receiver_id = intval($_POST['receiver_id']);

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, attachment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $attachment);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'type' => 'private',
        'message' => htmlspecialchars($message),
        'attachment' => $attachment,
        'time' => date("h:i A"),
        'sender_id' => $sender_id
    ]);
    exit;
}

// If nothing sent
echo json_encode(['status' => 'invalid']);