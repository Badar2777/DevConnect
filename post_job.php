<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit;
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);
    $budget = trim($_POST['budget']);
    $deadline = $_POST['deadline'];

    if (empty($title) || empty($description)) {
        $error = "Job title and description are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO jobs (client_id, title, description, skills, budget, deadline) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $client_id, $title, $description, $skills, $budget, $deadline);

        if ($stmt->execute()) {
            $success = "✅ Job posted successfully!";
        } else {
            $error = "❌ Failed to post the job. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create Job</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card, .dark-mode .form-control {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .form-control::placeholder {
      color: #bbb;
    }
    .fade-in {
      animation: fadeIn 0.8s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .back-btn {
  transition: all 0.3s ease-in-out;
  border-radius: 25px;
  font-weight: 500;
  padding: 8px 20px;
}

.back-btn:hover {
  transform: scale(1.05);
  background-color: #e9ecef;
}

.dark-mode .back-btn:hover {
  background-color: #333 !important;
  color: #fff;
}
  </style>
</head>
<body>
<div class="container mt-5 fade-in">
  <div class="card shadow rounded-4 p-4">
    <div class="text-center mb-4 mt-3">
  <a href="profile_client.php" class="btn btn-outline-secondary shadow-sm back-btn">
    <i class="bi bi-arrow-left"></i> Back to Dashboard
  </a>
</div>
    <h3 class="text-primary mb-4">📢 Create a New Job</h3>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="mb-3">
        <label class="form-label">Job Title</label>
        <input type="text" name="title" class="form-control" placeholder="e.g., Frontend Web Developer" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control" placeholder="Describe your project in detail..." required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Required Skills</label>
        <input type="text" name="skills" class="form-control" placeholder="e.g., HTML, CSS, JS, PHP">
      </div>
      <div class="mb-3">
        <label class="form-label">Budget</label>
        <input type="text" name="budget" class="form-control" placeholder="e.g., $500 - $1000">
      </div>
      <div class="mb-3">
        <label class="form-label">Deadline</label>
        <input type="date" name="deadline" class="form-control">
      </div>
      <button type="submit" class="btn btn-success w-100 mt-3">📩 Post Job</button>
    </form>
  </div>
</div>

<script>
  // Theme toggle (sync with client profile)
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }
</script>
</body>
</html>