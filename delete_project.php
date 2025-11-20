<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$projects = [];
$res = $conn->query("SELECT * FROM projects WHERE user_id = $user_id ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) {
    $projects[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Projects - DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card, .dark-mode .btn {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .btn-outline-danger {
      color: #ff6b6b;
      border-color: #ff6b6b;
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
  <h3 class="text-center mb-4">Delete Your Projects</h3>
  <div id="response" class="mb-3 text-center"></div>
  <div class="row">
    <?php foreach ($projects as $project): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm" id="project_<?= $project['id'] ?>">
          <?php if (!empty($project['image_path'])): ?>
            <img src="images/developer/<?= htmlspecialchars($project['image_path']) ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
            <p class="card-text"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
          </div>
          <div class="card-footer d-flex justify-content-between">
            <?php if (!empty($project['github_link'])): ?>
              <a href="<?= htmlspecialchars($project['github_link']) ?>" target="_blank" class="btn btn-sm btn-dark">
                <i class="bi bi-github"></i>
              </a>
            <?php endif; ?>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteProject(<?= $project['id'] ?>)">Delete</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="text-center mt-4">
    <a href="profile_developer.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Return to Profile</a>
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

function deleteProject(id) {
  if (!confirm("Are you sure you want to delete this project?")) return;
  
  fetch('delete_project_backend.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'project_id=' + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.getElementById('project_' + id).remove();
      document.getElementById('response').innerHTML = `<div class="alert alert-success">Project deleted successfully.</div>`;
    } else {
      document.getElementById('response').innerHTML = `<div class="alert alert-danger">Failed to delete project.</div>`;
    }
  });
}
</script>
</body>
</html>