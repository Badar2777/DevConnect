<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['generate_resume'])) {
  $full_name  = trim($_POST['full_name']);
  $email      = trim($_POST['email']);
  $contact    = trim($_POST['contact']);
  $address    = trim($_POST['address']);
  $summary    = trim($_POST['summary']);
  $skills     = trim($_POST['skills']);
  $education  = trim($_POST['education']);
  $experience = trim($_POST['experience']);

  // Check if resume already exists
  $check = $conn->prepare("SELECT id FROM resumes WHERE user_id = ?");
  $check->bind_param("i", $user_id);
  $check->execute();
  $res = $check->get_result();

  if ($res->num_rows > 0) {
    // Update resume
    $stmt = $conn->prepare("UPDATE resumes SET full_name=?, email=?, contact=?, address=?, summary=?, skills=?, education=?, experience=? WHERE user_id=?");
    $stmt->bind_param("ssssssssi", $full_name, $email, $contact, $address, $summary, $skills, $education, $experience, $user_id);
  } else {
    // Insert resume
    $stmt = $conn->prepare("INSERT INTO resumes (user_id, full_name, email, contact, address, summary, skills, education, experience) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $user_id, $full_name, $email, $contact, $address, $summary, $skills, $education, $experience);
  }

  $stmt->execute();
  header("Location: preview_resume.php");
  exit();
}
?>
<!DOCTYPE html><html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Resume</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #fff;
    }
    .dark-mode .form-control {
      background-color: #2a2a2a;
      color: #fff;
      border-color: #444;
    }
    /* Fix placeholder color in dark mode */
body.dark-mode input::placeholder,
body.dark-mode textarea::placeholder {
  color: #f0f0f0 !important;
  opacity: 0.8;
}
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
  <span class="navbar-brand">Create Resume</span>
  <button class="btn btn-outline-light" onclick="toggleTheme()"><i class="bi bi-brightness-high"></i></button>
</nav>
<div class="container py-4">
  <form method="POST" action="create_resume.php">
    <div class="row g-3">
      <div class="col-md-6">
        <input class="form-control" name="name" placeholder="Full Name" required>
      </div>
      <div class="col-md-6">
        <input class="form-control" name="email" placeholder="Email" required>
      </div>
      <div class="col-md-6">
        <input class="form-control" name="contact" placeholder="Contact Number" required>
      </div>
      <div class="col-md-6">
        <input class="form-control" name="address" placeholder="Address" required>
      </div>
      <div class="col-12">
        <textarea class="form-control" name="summary" rows="3" placeholder="Summary or Objective" required></textarea>
      </div>
      <div class="col-12">
        <textarea class="form-control" name="education" rows="3" placeholder="Education (e.g., BS Computer Science, FAST NUCES, 2021-2025)" required></textarea>
      </div>
      <div class="col-12">
        <textarea class="form-control" name="experience" rows="3" placeholder="Experience (e.g., Web Developer at XYZ)" required></textarea>
      </div>
      <div class="col-12">
        <textarea class="form-control" name="skills" rows="2" placeholder="Skills (comma-separated)" required></textarea>
      </div>
      <div class="col-12">
        <textarea class="form-control" name="languages" rows="2" placeholder="Languages (e.g., English, Urdu)" required></textarea>
      </div>
      <div class="col-md-6">
        <input class="form-control" name="github" placeholder="GitHub URL">
      </div>
      <div class="col-md-6">
        <input class="form-control" name="linkedin" placeholder="LinkedIn URL">
      </div>
    </div>
    <div class="mt-4 d-flex justify-content-between">
      <a href="profile_developer.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
      <button type="submit" name="generate_resume" class="btn btn-primary"><i class="bi bi-file-earmark-pdf"></i> Generate Resume</button>
    </div>
  </form>
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