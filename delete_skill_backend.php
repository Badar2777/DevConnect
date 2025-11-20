<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    echo json_encode(["success" => false]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['skill'])) {
    $user_id = $_SESSION['user_id'];
    $skill = trim($_POST['skill']);

    $stmt = $conn->prepare("DELETE FROM skills WHERE user_id = ? AND skill = ?");
    $stmt->bind_param("is", $user_id, $skill);
    $success = $stmt->execute();

    echo json_encode(["success" => $success]);
    exit;
}

echo json_encode(["success" => false]);
?>