<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

// Validate logged-in client
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

$client_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Ensure required data exists
  if (!isset($_POST['application_id'], $_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit();
  }

  $application_id = (int)$_POST['application_id'];
  $action = $_POST['action'];

  // Only approve or reject are valid actions
  if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
  }

  // Confirm application belongs to this client
  $stmt = $conn->prepare("SELECT * FROM applied_jobs WHERE id = ? AND client_id = ?");
  $stmt->bind_param("ii", $application_id, $client_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Application not found or access denied.']);
    exit();
  }

  $new_status = $action === 'approve' ? 'approved' : 'rejected';
  $update = $conn->prepare("UPDATE applied_jobs SET status = ? WHERE id = ?");
  $update->bind_param("si", $new_status, $application_id);

  if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => "Application $new_status successfully."]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
  }

} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}