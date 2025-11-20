<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Fetch all jobs with client details
$stmt = $conn->prepare("SELECT jobs.*, users.first_name, users.last_name, users.email 
                        FROM jobs 
                        JOIN users ON jobs.client_id = users.id 
                        ORDER BY jobs.created_at DESC");
$stmt->execute();
$jobs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Jobs - Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card,
    .dark-mode .table {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .text-muted {
      color: #aaa !important;
    }
    .truncate {
      max-width: 400px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-shield-lock"></i> Admin Panel</a>
  <div class="ms-auto d-flex gap-2">
    <a href="admin_dashboard.php" class="btn btn-outline-light">Dashboard</a>
    <button onclick="toggleTheme()" class="btn btn-outline-light">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
    <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</nav>

<div class="container py-4">
  <h3 class="mb-4 text-center">Manage Job Postings</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Posted By</th>
          <th>Posted On</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($job = $jobs->fetch_assoc()): ?>
          <tr>
            <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
            <td class="text-muted truncate" title="<?= htmlspecialchars($job['description']) ?>">
              <?= htmlspecialchars($job['description']) ?>
            </td>
            <td>
              <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?><br>
              <small class="text-muted"><?= htmlspecialchars($job['email']) ?></small>
            </td>
            <td><?= date('d M Y', strtotime($job['created_at'])) ?></td>
            <td>
              <form action="admin_delete_jobs.php" method="POST" onsubmit="return confirm('Delete this job?');">
                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
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