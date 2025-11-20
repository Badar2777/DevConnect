<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch unseen users and jobs
$new_users = $conn->query("SELECT * FROM users WHERE admin_seen = 0 ORDER BY id DESC");
$new_jobs = $conn->query("SELECT * FROM jobs WHERE admin_seen = 0 ORDER BY id DESC");

// Mark as seen
$conn->query("UPDATE users SET admin_seen = 1 WHERE admin_seen = 0");
$conn->query("UPDATE jobs SET admin_seen = 1 WHERE admin_seen = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Notifications | DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card,
    .dark-mode .list-group-item {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .text-muted {
      color: #ccc !important;
    }
    .dark-mode input::placeholder {
      color: #aaa;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="#"><i class="bi bi-code-slash"></i> DevConnect</a>
    <div class="ms-auto d-flex align-items-center gap-2">
      <a href="admin_dashboard.php" class="btn btn-outline-light" title="Dashboard">
        <i class="bi bi-house-door-fill"></i> Home
      </a>
      <button onclick="toggleTheme()" class="btn btn-outline-light" title="Toggle Theme">
        <i class="bi bi-moon-stars-fill"></i>
      </button>
      <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
    </div>
  </nav>

  <!-- Main -->
  <div class="container py-5">
    <h2 class="mb-4"><i class="bi bi-bell-fill"></i> Admin Notifications</h2>

    <div class="mb-5">
      <h4><i class="bi bi-person-plus-fill text-success"></i> New Users</h4>
      <?php if ($new_users->num_rows > 0): ?>
        <ul class="list-group">
          <?php while ($user = $new_users->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
              <span class="badge bg-secondary"><?= ucfirst($user['user_type']) ?></span>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No new users.</p>
      <?php endif; ?>
    </div>

    <div>
      <h4><i class="bi bi-megaphone-fill text-primary"></i> New Jobs</h4>
      <?php if ($new_jobs->num_rows > 0): ?>
        <ul class="list-group">
          <?php while ($job = $new_jobs->fetch_assoc()): ?>
            <li class="list-group-item">
              <strong><?= htmlspecialchars($job['title']) ?></strong><br>
              <span class="text-muted"><?= htmlspecialchars($job['description']) ?></span>
            </li>
          <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No new jobs posted.</p>
      <?php endif; ?>
    </div>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-4"><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
  </div>

  <script>
    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
      localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    }
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }
  </script>
</body>
</html>