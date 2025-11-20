<?php
session_start();
require_once 'includes/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $contact    = $_POST['contact'];
    $dob        = $_POST['dob'];
    $user_type  = $_POST['user_type'];
    $password   = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $msg = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $msg = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, contact, dob, password, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $first_name, $last_name, $email, $contact, $dob, $hashed_password, $user_type);

            if ($stmt->execute()) {
                if ($user_type === 'admin') {
                    header("Location: admin_login.php");
                } else {
                    $_SESSION['just_registered'] = true;
                    header("Location: login.php");
                }
                exit;
            } else {
                $msg = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - DevConnect</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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
    .dark-mode input::placeholder {
      color: #ccc;
    }
    .dark-mode .text-muted {
      color: #aaa !important;
    }
  </style>
</head>
<body>

  <!-- Optional Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="#"><i class="bi bi-code-slash"></i> DevConnect</a>
    <div class="ms-auto">
      <button class="btn btn-outline-light" onclick="toggleTheme()">
        <i class="bi bi-moon-stars-fill" id="themeIcon"></i> Theme
      </button>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow animate_animated animate_fadeInDown">
          <div class="card-body">
            <h3 class="text-center mb-4">Register</h3>

            <?php if ($msg): ?>
              <div class="alert alert-info"><?= $msg ?></div>
            <?php endif; ?>

            <form method="POST">
              <div class="row mb-3">
                <div class="col">
                  <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col">
                  <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                </div>
              </div>

              <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
              </div>

              <div class="mb-3">
                <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
              </div>

              <div class="mb-3">
                <input type="date" name="dob" class="form-control" required>
              </div>

              <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
              </div>
               <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
              </div>

              <div class="mb-3">
                <select name="user_type" class="form-select" required>
                  <option value="developer">Developer</option>
                  <option value="client">Client</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary w-100">Register</button>
              <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Theme Script -->
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