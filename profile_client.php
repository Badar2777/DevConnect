<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$profile_image = file_exists("images/client/" . $user['profile_picture']) ? $user['profile_picture'] : 'default.png';
// Fetch unread notifications count for the client
$notif_stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0");
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result()->fetch_assoc();
$notif_count = $notif_result['total'] ?? 0; // fallback to 0 if null
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Client Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card,
    .dark-mode .form-control,
    .dark-mode .btn,
    .dark-mode .text-muted {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .text-muted {
      color: #aaa !important;
    }
    body.dark-mode input::placeholder,
    body.dark-mode textarea::placeholder {
      color: #ccc !important;
      opacity: 0.8;
    }
    .card-enhanced {
      border-radius: 1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-enhanced:hover {
      transform: scale(1.02);
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-briefcase"></i> Client Panel</a>
  <div class="ms-auto d-flex align-items-center gap-2">
    <form class="d-flex me-2" method="GET" action="search_users.php">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Search developers..." required>
    </form>
    <a href="inbox.php" class="btn btn-outline-light" title="Inbox">
      <i class="bi bi-chat-dots"></i>
    </a>
    <!-- Notifications -->
    <a href="client_notifications.php" class="btn btn-outline-light position-relative" title="Notifications">
  <i class="bi bi-bell"></i>
  <?php if (`$notif_count > 0`) : ?>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
      <?= $notif_count ?>
    </span>
  <?php endif; ?>
</a>
    <button class="btn btn-outline-light" onclick="toggleTheme()">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
    <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</nav>

<div class="container mt-4 text-center">
  <img src="images/client/<?= htmlspecialchars($profile_image) ?>" class="rounded-circle mb-2" width="120">
  <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
  <p class="text-muted">Client | <?= htmlspecialchars($user['email']) ?></p>

  <!-- Notification Panel -->
  <div class="mt-3 mb-4" id="notifPanel" style="display: none;">
    <h5 class="text-start">🔔 New Job Applications</h5>
    <ul class="list-group" id="notifList"></ul>
  </div>

  <div class="row mt-5">
    <!-- Edit Profile -->
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow card-enhanced h-100">
        <div class="card-body text-center">
          <i class="bi bi-person-lines-fill display-5 text-info"></i>
          <h5 class="mt-3">Edit Profile</h5>
          <a href="edit_profile_client.php" class="btn btn-outline-info mt-2 w-100">Edit</a>
        </div>
      </div>
    </div>

    <!-- View Developers -->
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow card-enhanced h-100">
        <div class="card-body text-center">
          <i class="bi bi-search display-5 text-primary"></i>
          <h5 class="mt-3">View Developers</h5>
          <a href="view_developers.php" class="btn btn-outline-primary mt-2 w-100">Browse</a>
        </div>
      </div>
    </div>

    <!-- Delete Account -->
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow card-enhanced border-danger h-100">
        <div class="card-body text-center">
          <i class="bi bi-person-x-fill display-5 text-danger"></i>
          <h5 class="mt-3">Delete Account</h5>
      
          <button class="btn btn-outline-danger mt-2 w-100" onclick="confirmClientDelete()">Delete My Account</button>
        </div>
      </div>
    </div>

    <!-- Post Job -->
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow card-enhanced h-100">
        <div class="card-body text-center">
          <i class="bi bi-plus-square display-5 text-success"></i>
          <h5 class="mt-3">Post a Job</h5>
          <a href="post_job.php" class="btn btn-outline-success mt-2 w-100">Create Job</a>
        </div>
      </div>
    </div>

    <!-- My Jobs -->
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="card shadow card-enhanced h-100">
        <div class="card-body text-center">
          <i class="bi bi-kanban display-5 text-primary"></i>
          <h5 class="mt-3">My Jobs</h5>
          <a href="my_jobs.php" class="btn btn-outline-primary mt-2 w-100">Manage</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script>
  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }

  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

  function confirmClientDelete() {
    if (confirm("Are you sure you want to permanently delete your account? This action cannot be undone.")) {
      window.location.href = "delete_client.php";
    }
  }

  function toggleNotifications() {
    const panel = document.getElementById('notifPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';

    fetch('fetch_notifications.php')
      .then(res => res.json())
      .then(data => {
        const notifList = document.getElementById('notifList');
        notifList.innerHTML = '';
        if (data.length === 0) {
          notifList.innerHTML = '<li class="list-group-item">No new applications.</li>';
        } else {
          data.forEach(n => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML = `<strong>${n.developer_name}</strong> applied for <em>${n.job_title}</em> <span class="text-muted small">(${n.applied_at})</span>`;
            notifList.appendChild(li);
          });
        }
      });
  }

  function updateNotifCount() {
    fetch('fetch_notifications.php')
      .then(res => res.json())
      .then(data => {
        document.getElementById('notifCount').textContent = data.length;
      });
  }

  updateNotifCount();
  setInterval(updateNotifCount, 10000); // every 10 seconds
</script>

</body>
</html>