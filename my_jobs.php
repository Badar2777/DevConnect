<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit;
}

$client_id = $_SESSION['user_id'];

// Fetch all jobs by this client
$stmt = $conn->prepare("SELECT * FROM jobs WHERE client_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$jobs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Jobs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode { background-color: #121212; color: #fff; }
    .dark-mode .card { background-color: #1e1e1e; color: #fff; }
    .dark-mode .btn, .dark-mode .table { background-color: #222; color: #eee; }
    .fade-in {
      animation: fadeIn 0.7s ease-in-out;
    }
    <div class="text-center mb-4 mt-3">
  <a href="profile_client.php" class="btn btn-outline-secondary shadow-sm back-btn">
    <i class="bi bi-arrow-left"></i> Back to Dashboard
  </a>
</div>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
<div class="container mt-5 fade-in">
    <div class="text-center mb-4 mt-3">
  <a href="profile_client.php" class="btn btn-outline-secondary shadow-sm back-btn">
    <i class="bi bi-arrow-left"></i> Back to Dashboard
  </a>
</div>
  <h3 class="mb-4 text-primary">🗂 My Posted Jobs</h3>

  <?php if ($jobs->num_rows === 0): ?>
    <div class="alert alert-info">You haven't posted any jobs yet.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Title</th>
            <th>Skills</th>
            <th>Budget</th>
            <th>Deadline</th>
            <th>Posted On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($job = $jobs->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($job['title']) ?></td>
            <td><?= htmlspecialchars($job['skills']) ?></td>
            <td><?= htmlspecialchars($job['budget']) ?></td>
            <td><?= htmlspecialchars($job['deadline']) ?></td>
            <td><?= date('d M Y', strtotime($job['created_at'])) ?></td>
            <td>
              <a href="edit_job.php?id=<?= $job['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <a href="delete_job.php?id=<?= $job['id'] ?>" onclick="return confirm('Are you sure you want to delete this job?');" class="btn btn-sm btn-outline-danger">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }
</script>
</body>
</html>