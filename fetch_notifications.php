<?php
session_start();
header('Content-Type: application/json'); // 🟢 Important

require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    echo json_encode([]);
    exit();
}

$client_id = $_SESSION['user_id'];

// Fetch applied developers for this client's jobs
$stmt = $conn->prepare("SELECT aj.*, u.first_name, u.last_name, j.title AS job_title
                        FROM applied_jobs aj
                        JOIN users u ON aj.developer_id = u.id
                        JOIN jobs j ON aj.job_id = j.id
                        WHERE j.client_id = ?
                        ORDER BY aj.applied_at DESC");

$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        "developer_name" => $row['first_name'] . ' ' . $row['last_name'],
        "job_title" => $row['job_title'],
        "applied_at" => date('M d, Y H:i', strtotime($row['applied_at']))
    ];
}

echo json_encode($notifications);