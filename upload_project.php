<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $github_link = $_POST['github_link'] ?? '';
  $image_path = null;

  if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] === 0) {
    $ext = pathinfo($_FILES['project_image']['name'], PATHINFO_EXTENSION);
    $filename = 'project_' . time() . '.' . $ext;
    $dest = 'images/developer/' . $filename;
    move_uploaded_file($_FILES['project_image']['tmp_name'], $dest);
    $image_path = $filename;
  }

  $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description, github_link, image_path) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $user_id, $title, $description, $github_link, $image_path);

  if ($stmt->execute()) {
    $msg = "Project uploaded successfully!";
  } else {
    $msg = "Upload failed. Please try again.";
  }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <title>Upload Project - DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
    .dark-mode input::placeholder,
    .dark-mode textarea::placeholder {
      color: #ccc;
    }
    .dark-mode .text-muted {
      color: #aaa !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
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
      <div class="card shadow">
        <div class="card-body">
          <h3 class="text-center mb-4">Upload Your Project</h3>

          <?php if (!empty($msg)): ?>
            <div class="alert alert-info text-center"><?= $msg ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label class="form-label">Project Title</label>
              <input type="text" name="title" class="form-control" required placeholder="Enter project title">
            </div>

            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="4" required placeholder="Describe your project"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">GitHub Link (optional)</label>
              <input type="url" name="github_link" class="form-control" placeholder="https://github.com/yourrepo">
            </div>

            <div class="mb-3">
              <label class="form-label">Project Image</label>
              <input type="file" name="project_image" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100"><i class="bi bi-upload"></i> Upload</button>
          </form>

          <div class="text-center mt-3">
            <a href="profile_developer.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Return to Profile</a>
          </div>
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