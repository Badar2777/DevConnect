<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];
$developer_id = intval($_POST['developer_id']);
$rating = intval($_POST['rating']);
$comment = trim($_POST['comment'] ?? '');

if ($rating < 1 || $rating > 5) {
    die("Invalid rating value.");
}

// Check if rating exists
$stmt = $conn->prepare("SELECT id FROM ratings WHERE client_id = ? AND developer_id = ?");
$stmt->bind_param("ii", $client_id, $developer_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    // Update
    $stmt = $conn->prepare("UPDATE ratings SET rating = ?, comment = ?, created_at = NOW() WHERE client_id = ? AND developer_id = ?");
    $stmt->bind_param("isii", $rating, $comment, $client_id, $developer_id);
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO ratings (client_id, developer_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $client_id, $developer_id, $rating, $comment);
}

if ($stmt->execute()) {
    header("Location: view_developers.php?success=1");
    exit();
} else {
    echo "Error saving rating.";
}
?>