<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$resume = null;

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
    $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx'];
    if (in_array($ext, $allowed)) {
        $filename = "resume_" . $user_id . "." . $ext;
        $destination = "uploads/resumes/" . $filename;
        if (!is_dir("uploads/resumes")) {
            mkdir("uploads/resumes", 0777, true);
        }
        move_uploaded_file($_FILES['resume']['tmp_name'], $destination);

        $stmt = $conn->prepare("UPDATE users SET resume_path = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $user_id);
        $stmt->execute();

        $_SESSION['upload_success'] = "Resume uploaded successfully!";
        header("Location: upload_resume.php");
        exit();
    } else {
        $error = "Only PDF, DOC, DOCX allowed.";
    }
}

// Fetch resume if exists
$stmt = $conn->prepare("SELECT resume_path FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if ($row && $row['resume_path']) {
    $resume = $row['resume_path'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Upload Resume</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .card {
      background-color: #1e1e1e;
      border-color: #444;
    }
    .dark-mode .form-control {
      background-color: #2a2a2a;
      color: #f0f0f0;
      border-color: #555;
    }
    .dark-mode input::placeholder {
      color: #ccc;
    }
    /* Label visibility */
body.dark-mode label {
  color: #ffffff !important;
}
body:not(.dark-mode) label {
  color: #000000 !important;
}

/* Placeholder visibility */
body.dark-mode input::placeholder,
body.dark-mode textarea::placeholder {
  color: #cccccc !important;
}
body:not(.dark-mode) input::placeholder,
body:not(.dark-mode) textarea::placeholder {
  color: #666666 !important;
}
body.dark-mode h4{
    color:#cccccc  !important;
}
body:not(.dark-mode) h4{
    color: #666666 !important;
}
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-upload"></i> Upload Resume</a>
  <div class="ms-auto d-flex gap-2">
    <button class="btn btn-outline-light" onclick="toggleTheme()" title="Toggle Theme">
      <i class="bi bi-brightness-high-fill" id="themeIcon"></i>
    </button>
    <a href="profile_developer.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Profile</a>
  </div>
</nav>

<div class="container mt-5">
  <div class="card p-4 shadow-lg animate_animated animate_fadeIn">
    <h4 class="mb-3"><i class="bi bi-cloud-arrow-up"></i> Upload Your Resume</h4>

    <?php if (isset($_SESSION['upload_success'])): ?>
      <div class="alert alert-success"><?php echo $_SESSION['upload_success']; unset($_SESSION['upload_success']); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Select Resume File (PDF, DOC, DOCX):</label>
        <input type="file" name="resume" class="form-control" required>
      </div>
      <div class="d-flex gap-3">
        <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Upload Resume</button>
        <?php if ($resume): ?>
          <a href="uploads/resumes/<?php echo $resume; ?>" download class="btn btn-outline-primary">
            <i class="bi bi-download"></i> Download Resume
          </a>
        <?php endif; ?>
        <a href="create_resume.php" class="btn btn-outline-info ms-auto">
          <i class="bi bi-pencil-square"></i> Create Resume
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }
  document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }
  });
</script>
</body>
</html>