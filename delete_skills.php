<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$skills = [];
$res = $conn->query("SELECT skill FROM skills WHERE user_id = $user_id");
while ($row = $res->fetch_assoc()) {
    $skills[] = $row['skill'];
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <title>Delete Skills - DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .skill-badge {
      background-color: #2a2a2a;
      color: #fff;
    }
    .skill-badge {
      padding: 10px 16px;
      border-radius: 20px;
      background-color: #e7f3ff;
      margin: 5px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease-in-out;
    }
    .btn-delete-skill {
      font-size: 0.75rem;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-code-slash"></i> DevConnect</a>
  <div class="ms-auto">
    <button class="btn btn-outline-light" onclick="toggleTheme()">
      <i class="bi bi-moon-stars-fill" id="themeIcon"></i> Theme
    </button>
  </div>
</nav>

<!-- Content -->
<div class="container mt-5">
  <h3 class="text-center mb-4">Delete Your Skills</h3>
  <div id="response" class="mb-3 text-center"></div>

  <div class="d-flex flex-wrap justify-content-center">
    <?php foreach ($skills as $skill): 
      $id = 'skill_' . md5($skill); ?>
      <span class="skill-badge" id="<?= $id ?>" data-skill="<?= htmlspecialchars($skill) ?>">
        <?= htmlspecialchars($skill) ?>
        <button class="btn btn-sm btn-outline-danger btn-delete-skill ms-2">
          <i class="bi bi-x-circle"></i> Delete
        </button>
      </span>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-4">
    <a href="profile_developer.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Return to Profile
    </a>
  </div>
</div>

<!-- Theme and Delete Logic -->
<script>
  // Theme toggle
  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }

  // Apply saved theme on load
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

  // Delete skill logic with confirmation
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete-skill').forEach(button => {
      button.addEventListener('click', function () {
        const badge = this.closest('.skill-badge');
        const skill = badge.dataset.skill;

        if (!confirm(`Are you sure you want to delete "${skill}"?`)) return;

        fetch('delete_skill_backend.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'skill=' + encodeURIComponent(skill)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            badge.remove();
            document.getElementById('response').innerHTML = `
              <div class="alert alert-success">Skill <strong>${skill}</strong> deleted successfully.</div>`;
          } else {
            document.getElementById('response').innerHTML = `
              <div class="alert alert-danger">Failed to delete skill.</div>`;
          }
        })
        .catch(err => {
          console.error('Error:', err);
          document.getElementById('response').innerHTML = `
            <div class="alert alert-danger">An error occurred. Try again.</div>`;
        });
      });
    });
  });
</script>

</body>
</html>