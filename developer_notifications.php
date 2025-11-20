<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
  header("Location: login.php");
  exit();
}

$developer_id = $_SESSION['user_id'];

// Fetch applied jobs with client info
$stmt = $conn->prepare("
  SELECT aj.id, aj.status, aj.applied_at,
         j.title AS job_title,
         u.first_name, u.last_name, u.id AS client_id, u.profile_picture
  FROM applied_jobs aj
  JOIN jobs j ON aj.job_id = j.id
  JOIN users u ON j.client_id = u.id
  WHERE aj.developer_id = ?
  ORDER BY aj.applied_at DESC
");
$stmt->bind_param("i", $developer_id);
$stmt->execute();
$applications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notifications - Developer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card,
    .dark-mode .form-control {
      background-color: #1f1f1f;
      color: #fff;
    }
    .dark-mode .text-muted { color: #aaa !important; }
    .profile-pic {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }
    .badge-cap {
      text-transform: capitalize;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="profile_developer.php">
    <i class="bi bi-bell"></i> Notifications
  </a>
  <div class="ms-auto">
    <button class="btn btn-outline-light" onclick="toggleTheme()">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
  </div>
</nav>

<div class="container mt-5">
  <h3 class="mb-4 text-primary">📢 Application Status Notifications</h3>

  <?php if ($applications->num_rows === 0): ?>
    <div class="alert alert-info text-center">No applications found.</div>
  <?php else: ?>
    <?php while ($row = $applications->fetch_assoc()): ?>
      <div class="d-flex align-items-center justify-content-between border-bottom py-3">
        <div class="d-flex align-items-center">
          <img src="images/client/<?= htmlspecialchars($row['profile_picture'] ?? 'default.png') ?>" class="profile-pic me-3">
          <div>
            <h6 class="mb-1"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></h6>
            <div class="small text-muted"><?= htmlspecialchars($row['job_title']) ?></div>
          </div>
        </div>
        <div class="text-end">
          <?php
            $status = strtolower($row['status']);
            if ($status === 'approved') {
              echo '<span class="badge bg-success">Approved</span>';
              echo '<div class="mt-2"><a href="inbox.php?chat=' . $row['client_id'] . '" class="btn btn-outline-primary btn-sm"><i class="bi bi-chat-dots"></i> Message</a></div>';
            } elseif ($status === 'rejected') {
              echo '<span class="badge bg-danger">Rejected</span>';
            } else {
              echo '<span class="badge bg-secondary">Pending</span>';
            }
          ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>

  <a href="profile_developer.php" class="btn btn-outline-primary mt-4">
    <i class="bi bi-arrow-left"></i> Back to Profile
  </a>
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