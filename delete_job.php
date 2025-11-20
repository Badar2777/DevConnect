<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];
$job_id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM jobs WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $job_id, $client_id);
$stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Job Deleted</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body.dark-mode { background: #121212; color: #fff; }
    .fade-in { animation: fadeIn 0.7s ease-in-out; }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="fade-in text-center mt-5">
  <h2 class="text-danger">❌ Job Deleted Successfully</h2>
  <a href="my_jobs.php" class="btn btn-outline-primary mt-3 back-btn">← Back to My Jobs</a>

  <script>
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }
  </script>
</body>
</html>