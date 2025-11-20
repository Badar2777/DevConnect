<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit();
}

$dev_id = isset($_GET['dev_id']) ? intval($_GET['dev_id']) : 0;

$stmt = $conn->prepare("SELECT r.*, u.first_name, u.last_name FROM resumes r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.user_id = ?");
$stmt->bind_param("i", $dev_id);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();

if (!$resume) {
    echo "<script>alert('Resume not found!'); window.location.href = 'view_developers.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Developer Resume</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #fff;
    }
    .dark-mode .card,
    .dark-mode .btn {
      background-color: #1e1e1e;
      color: #fff;
    }
    .resume-box {
      background-color: #fff;
      color: #000;
      border-radius: 10px;
      padding: 30px;
    }
    .dark-mode .resume-box {
      background-color: #1e1e1e;
      color: #fff;
    }
    .resume-box h3 {
      font-weight: bold;
    }
    .resume-box p {
      margin: 0;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <span class="navbar-brand fw-bold"><i class="bi bi-file-earmark-person"></i> Developer Resume</span>
  <div class="ms-auto d-flex gap-2">
    <button class="btn btn-outline-light" onclick="toggleTheme()"><i class="bi bi-brightness-high"></i></button>
    <a href="view_developers.php" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</nav>

<div class="container my-5">
  <div class="resume-box shadow">
    <h3><?= htmlspecialchars($resume['full_name']) ?></h3>
    <p><strong>Email:</strong> <?= htmlspecialchars($resume['email']) ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($resume['contact']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($resume['address']) ?></p>
    <hr>
    <h5>Summary</h5>
    <p><?= nl2br(htmlspecialchars($resume['summary'])) ?></p>
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
    <div class="text-end">
      <a href="download_developer_resume.php?dev_id=<?= $dev_id ?>" class="btn btn-success">
        <i class="bi bi-download"></i> Download Resume
      </a>
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