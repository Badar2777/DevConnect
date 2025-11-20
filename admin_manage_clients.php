<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Fetch all clients
$clients = $conn->query("SELECT * FROM users WHERE user_type = 'client' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Clients - Admin</title>
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
    .dark-mode .text-muted {
      color: #aaa !important;
    }
    .profile-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
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
  <h3 class="mb-4 text-center">Manage Clients</h3>
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>Profile</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($client = $clients->fetch_assoc()): ?>
          <tr>
            <td>
              <img src="images/client/<?= file_exists("images/client/" . $client['profile_picture']) ? $client['profile_picture'] : 'default.png' ?>" class="profile-img">
            </td>
            <td><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?></td>
            <td class="text-muted"><?= htmlspecialchars($client['email']) ?></td>
            <td>
              <span class="badge bg-<?= $client['status'] === 'frozen' ? 'danger' : 'success' ?>">
                <?= ucfirst($client['status']) ?>
              </span>
            </td>
            <td>
              <form action="admin_toggle_clients_status.php" method="POST" class="d-inline">
                <input type="hidden" name="user_id" value="<?= $client['id'] ?>">
                <input type="hidden" name="user_type" value="client">
                <button type="submit" name="action" value="<?= $client['status'] === 'frozen' ? 'activate' : 'freeze' ?>"
                        class="btn btn-sm btn-<?= $client['status'] === 'frozen' ? 'success' : 'warning' ?>">
                  <?= $client['status'] === 'frozen' ? 'Unfreeze' : 'Freeze' ?>
                </button>
              </form>
              <form action="admin_delete_clients.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this client?');">
                <input type="hidden" name="user_id" value="<?= $client['id'] ?>">
                <input type="hidden" name="user_type" value="client">
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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