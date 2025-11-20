<?php
session_start();
require_once 'includes/db.php';

// Only allow developer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    die("Unauthorized");
}

$developer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
    $job_id = (int) $_POST['job_id'];

    // Get client ID
    $stmt = $conn->prepare("SELECT client_id FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Job not found.");
    }

    $row = $result->fetch_assoc();
    $client_id = $row['client_id'];

    // Check if already applied
    $check = $conn->prepare("SELECT id FROM applied_jobs WHERE job_id = ? AND developer_id = ?");
    $check->bind_param("ii", $job_id, $developer_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        die("Already applied to this job.");
    }

    // Insert application
    $insert = $conn->prepare("INSERT INTO applied_jobs (job_id, client_id, developer_id, status) VALUES (?, ?, ?, 'pending')");
    $insert->bind_param("iii", $job_id, $client_id, $developer_id);
    if ($insert->execute()) {
        echo "Applied";
    } else {
        echo "Error while applying.";
    }

} else {
    echo "Invalid request.";
}