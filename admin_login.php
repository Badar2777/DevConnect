<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = md5($_POST['password']); // Use hashing like bcrypt in real apps

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND user_type = 'admin'");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_type'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - DevConnect</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card,
    .dark-mode .form-control {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .text-muted {
      color: #aaa !important;
    }
    .dark-mode input::placeholder {
      color: #ccc;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow w-100" style="max-width: 400px;">
      <h4 class="text-center mb-3"><i class="bi bi-shield-lock"></i> Admin Login</h4>
      
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="admin@devconnect.com" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <div class="mt-3 text-center">
        <button onclick="toggleTheme()" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-moon-stars-fill"></i> Toggle Theme
        </button>
      </div>
    </div>
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