<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch resume data
$stmt = $conn->prepare("SELECT * FROM resumes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();

if (!$resume) {
    echo "<div class='text-center mt-5 text-danger'>No resume data found. Please <a href='create_resume.php'>create a resume</a>.</div>";
    exit;
}
?><!DOCTYPE html><html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resume Preview</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card,
    .dark-mode .btn,
    .dark-mode .form-control {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .btn-light {
      background-color: #333;
      color: #fff;
    }
    .resume-box {
      background: #fff;
      color: #000;
      padding: 30px;
      border-radius: 10px;
      max-width: 800px;
      margin: auto;
    }
    .dark-mode .resume-box {
      background: #1e1e1e;
      color: #fff;
    }
    .resume-box h2 {
      font-weight: bold;
      margin-bottom: 10px;
    }
    .resume-box p {
      margin: 2px 0;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-person-badge"></i> Resume Preview</a>
  <div class="ms-auto d-flex align-items-center gap-2">
    <button class="btn btn-outline-light" onclick="toggleTheme()" title="Toggle Theme">
      <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>
    <a href="profile_developer.php" class="btn btn-outline-light">Back to Profile</a>
  </div>
</nav><div class="container my-5">
  <div class="resume-box shadow">
    <h2><?= htmlspecialchars($resume['full_name']) ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($resume['email']) ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($resume['contact']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($resume['address']) ?></p>
    <hr>
    <h5>Education</h5>
    <p><?= nl2br(htmlspecialchars($resume['education'])) ?></p>
    <hr>
    <h5>Experience</h5>
    <p><?= nl2br(htmlspecialchars($resume['experience'])) ?></p>
    <hr>
    <h5>Skills</h5>
    <p><?= nl2br(htmlspecialchars($resume['skills'])) ?></p>
    <hr>
    <div class="d-flex justify-content-between mt-4">
      <a href="create_resume.php" class="btn btn-outline-warning"><i class="bi bi-pencil-square"></i> Edit</a>
      <a href="download_resume.php" class="btn btn-success"><i class="bi bi-download"></i> Download as PDF</a>
    </div>
  </div>
</div><script>
function toggleTheme() {
  document.body.classList.toggle('dark-mode');
  localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
}

document.addEventListener('DOMContentLoaded', () => {
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }
});
</script></body>
</html>