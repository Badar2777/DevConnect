<?php
session_start();
require_once 'includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
  echo json_encode(['success' => false]);
  exit;
}

if (isset($_POST['id'])) {
  $developer_id = $_SESSION['user_id'];
  $notif_id = intval($_POST['id']);

  $stmt = $conn->prepare("UPDATE developer_notifications SET is_read = 1 WHERE id = ? AND developer_id = ?");
  $stmt->bind_param("ii", $notif_id, $developer_id);

  if ($stmt->execute()) {
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false]);
  }
} else {
  echo json_encode(['success' => false]);
}