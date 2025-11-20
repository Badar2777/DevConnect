<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";
$skills = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['skill'])) {
    $skill = trim($_POST['skill']);

    if (!empty($skill)) {
        // Check if skill already exists
        $check = $conn->prepare("SELECT * FROM skills WHERE user_id = ? AND skill = ?");
        $check->bind_param("is", $user_id, $skill);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $msg = "Skill already exists!";
        } else {
            $insert = $conn->prepare("INSERT INTO skills (user_id, skill) VALUES (?, ?)");
            $insert->bind_param("is", $user_id, $skill);
            if ($insert->execute()) {
                $msg = "Skill added successfully!";
            } else {
                $msg = "Failed to add skill.";
            }
        }
    } else {
        $msg = "Skill cannot be empty.";
    }
}

// Fetch skills
$result = $conn->query("SELECT skill FROM skills WHERE user_id = $user_id");
while ($row = $result->fetch_assoc()) {
    $skills[] = $row['skill'];
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <title>Add Skills</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #eee;
    }
    .dark-mode .form-control,
    .dark-mode .card {
      background-color: #1f1f1f;
      color: #fff;
    }
    .dark-mode .form-control::placeholder {
      color: #bbb;
    }
  </style>
</head>
<body>
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
      <div class="card shadow animate_animated animate_fadeIn">
        <div class="card-body">
          <h4 class="mb-4 text-center">Add Skill</h4>

          <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
          <?php endif; ?>

          <form method="POST" class="d-flex">
            <input type="text" name="skill" class="form-control me-2" placeholder="e.g. JavaScript" required>
            <button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Add</button>
          </form>

          <?php if ($skills): ?>
            <div class="mt-4">
              <h6 class="mb-2">Your Skills:</h6>
              <?php foreach ($skills as $skill): ?>
                <span class="badge bg-info text-dark me-1 mb-1 animate_animated animate_fadeInUp"><?= htmlspecialchars($skill) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div class="mt-4 text-center">
            <a href="profile_developer.php" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left"></i> Back to Profile
            </a>
          </div>
        </div>
      </div>
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