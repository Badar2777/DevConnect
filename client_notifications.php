<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
  header("Location: login.php");
  exit();
}

$client_id = $_SESSION['user_id'];

// Fetch applications for this client
$stmt = $conn->prepare("
  SELECT aj.id, aj.status, aj.applied_at,
         j.title AS job_title,
         u.first_name, u.last_name, u.id AS developer_id, u.profile_picture
  FROM applied_jobs aj
  JOIN jobs j ON aj.job_id = j.id
  JOIN users u ON aj.developer_id = u.id
  WHERE aj.client_id = ?
  ORDER BY aj.applied_at DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$applications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Applications - DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #eee;
    }
    .dark-mode .card,
    .dark-mode .table,
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

    .dark-mode table,
    .dark-mode .table {
      background-color: #1f1f1f;
      color: #fff;
      border-color: #444;
    }

    .dark-mode thead.table-light {
      background-color: #2c2c2c !important;
      color: #f8f9fa;
      border-color: #555;
    }

    .dark-mode tbody tr {
      background-color: #1f1f1f;
      border-color: #444;
    }

    .dark-mode td, .dark-mode th {
      color: #fff;
      border-color: #444;
    }

    .dark-mode .btn-outline-dark {
      border-color: #ccc;
      color: #ccc;
    }

    .dark-mode .btn-outline-dark:hover {
      background-color: #333;
      border-color: #eee;
      color: #fff;
    }
    /* Global dark mode */
body.dark-mode {
  background-color: #121212;
  color: #f0f0f0;
}

/* Table dark mode fixes */
.dark-mode table,
.dark-mode .table {
  background-color: #1e1e1e;
  color: #f0f0f0;
}

.dark-mode th,
.dark-mode td {
  background-color: #1e1e1e !important;
  color: #f0f0f0 !important;
  border-color: #444 !important;
}

/* Optional: hover row styling */
.dark-mode tbody tr:hover {
  background-color: #2a2a2a !important;
}
  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="profile_client.php">
    <i class="bi bi-briefcase"></i> Client Panel
  </a>
  <div class="ms-auto">
    <button class="btn btn-outline-light" onclick="toggleTheme()">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
  </div>
</nav>

<div class="container mt-5">
  <h3 class="mb-4 text-primary">📥 Developer Job Applications</h3>

  <?php if ($applications->num_rows === 0): ?>
    <div class="alert alert-info text-center">No applications have been submitted yet.</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Developer</th>
            <th>Job Title</th>
            <th>Status</th>
            <th>Applied On</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $applications->fetch_assoc()): ?>
            <tr>
              <td>
                <img src="images/developer/<?= htmlspecialchars($row['profile_picture'] ?? 'default.png') ?>" class="profile-pic me-2">
                <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
              </td>
              <td><?= htmlspecialchars($row['job_title']) ?></td>
              <td><span class="badge bg-secondary badge-cap"><?= htmlspecialchars($row['status']) ?></span></td>
              <td><?= date('d M Y', strtotime($row['applied_at'])) ?></td>
              <td>
                <?php if ($row['status'] === 'pending'): ?>
                  <button onclick="handleApplication(<?= $row['id'] ?>, 'approve')" class="btn btn-success btn-sm">Approve</button>
                  <button onclick="handleApplication(<?= $row['id'] ?>, 'reject')" class="btn btn-danger btn-sm">Reject</button>
                <?php elseif ($row['status'] === 'approved'): ?>
                  <span class="text-success fw-bold">Approved</span>
                  <a href="inbox.php?chat=<?= $row['developer_id'] ?>" class="btn btn-outline-dark btn-sm ms-2">
                    <i class="bi bi-chat-dots"></i> Message
                  </a>
                <?php else: ?>
                  <span class="text-muted">Rejected</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
  <a href="profile_client.php" class="btn btn-outline-primary ms-3 mt-3">
    <i class="bi bi-arrow-left"></i> Back to Profile
  </a>
</div>

<script>
  function handleApplication(applicationId, action) {
    if (!confirm(`Are you sure you want to ${action} this application?`)) return;

    fetch('handle_application_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `application_id=${applicationId}&action=${action}`
    })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.success) location.reload();
    })
    .catch(err => alert("Something went wrong."));
  }

  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }
  });
</script>
</body>
</html>