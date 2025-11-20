<?php
session_start();
require_once 'includes/db.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit();
}

if (!isset($_POST['skill'])) {
    echo json_encode(["success" => false, "message" => "No skill provided"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$skill = trim($_POST['skill']);

$stmt = $conn->prepare("DELETE FROM skills WHERE user_id = ? AND skill = ?");
$stmt->bind_param("is", $user_id, $skill);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete"]);
}
?>