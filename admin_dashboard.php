<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
  header("Location: admin_login.php");
  exit();
}

$dev_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE user_type = 'developer'")->fetch_assoc()['total'];
$client_count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE user_type = 'client'")->fetch_assoc()['total'];
$job_count = $conn->query("SELECT COUNT(*) AS total FROM jobs")->fetch_assoc()['total'];
$message_count = $conn->query("SELECT COUNT(*) AS total FROM messages")->fetch_assoc()['total'];
// Count new users
$new_users = $conn->query("SELECT COUNT(*) AS total FROM users WHERE admin_seen = 0")->fetch_assoc()['total'];

// Count new jobs
$new_jobs = $conn->query("SELECT COUNT(*) AS total FROM jobs WHERE admin_seen = 0")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - DevConnect</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dashboard-card {
      transition: transform 0.2s ease;
    }
    .dashboard-card:hover {
      transform: scale(1.02);
    }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shield-lock"></i> Admin Panel</a>
  <div class="ms-auto d-flex gap-2">
    <button onclick="toggleTheme()" class="btn btn-outline-light">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
     <a href="admin_notification.php" class="btn btn-outline-warning position-relative">
  <i class="bi bi-bell-fill"></i>
  <?php if ($new_users + $new_jobs > 0): ?>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
      <?= $new_users + $new_jobs ?>
    </span>
  <?php endif; ?>
</a>
    <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</nav>

<div class="container py-5">
  <h3 class="mb-4 text-center">Admin Dashboard</h3>
  <div class="row g-4">
    <!-- Developer Card -->
    <div class="col-md-3">
      <div class="card dashboard-card p-3 shadow text-center">
        <i class="bi bi-code-slash display-4 text-primary"></i>
        <h5 class="mt-3">Developers</h5>
        <p class="text-muted"><?= $dev_count ?> total</p>
        <a href="admin_manage_developers.php" class="btn btn-outline-primary w-100">View</a>
      </div>
    </div>

    <!-- Client Card -->
    <div class="col-md-3">
      <div class="card dashboard-card p-3 shadow text-center">
        <i class="bi bi-person-fill display-4 text-success"></i>
        <h5 class="mt-3">Clients</h5>
        <p class="text-muted"><?= $client_count ?> total</p>
        <a href="admin_manage_clients.php" class="btn btn-outline-success w-100">View</a>
      </div>
    </div>

    <!-- Jobs Card -->
    <div class="col-md-3">
      <div class="card dashboard-card p-3 shadow text-center">
        <i class="bi bi-briefcase-fill display-4 text-warning"></i>
        <h5 class="mt-3">Jobs Posted</h5>
        <p class="text-muted"><?= $job_count ?> jobs</p>
        <a href="admin_manage_jobs.php" class="btn btn-outline-warning w-100">View</a>
      </div>
    </div>

    <!-- Messages Count Card (readonly) -->
    <div class="col-md-3">
      <div class="card dashboard-card p-3 shadow text-center">
        <i class="bi bi-chat-dots display-4 text-danger"></i>
        <h5 class="mt-3">Messages</h5>
        <p class="text-muted"><?= $message_count ?> total</p>
        <button class="btn btn-outline-danger w-100" disabled>Not Viewable</button>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }
  if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark-mode');
</script>
</body>
</html>