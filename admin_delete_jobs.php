<?php
session_start();
require_once 'includes/db.php';

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
}

header("Location: admin_manage_jobs.php");
exit();