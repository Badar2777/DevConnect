<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit();
}

$client_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $developer_id = intval($_POST['developer_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // Validate rating
    if ($rating < 1 || $rating > 5) {
        $_SESSION['error'] = "Invalid rating.";
        header("Location: view_developers.php");
        exit();
    }

    // Check if already rated by this client
    $check = $conn->prepare("SELECT id FROM ratings WHERE client_id = ? AND developer_id = ?");
    $check->bind_param("ii", $client_id, $developer_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing rating
        $stmt = $conn->prepare("UPDATE ratings SET rating = ?, comment = ?, updated_at = NOW() WHERE client_id = ? AND developer_id = ?");
        $stmt->bind_param("isii", $rating, $comment, $client_id, $developer_id);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO ratings (client_id, developer_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $client_id, $developer_id, $rating, $comment);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Rating submitted successfully!";
    } else {
        $_SESSION['error'] = "Something went wrong.";
    }

    header("Location: view_developers.php");
    exit();
} else {
    header("Location: view_developers.php");
    exit();
}
?>