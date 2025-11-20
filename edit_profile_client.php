<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $filename = 'client_' . $user_id . '.' . $ext;
        $dest = 'images/client/' . $filename;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest);

        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, contact=?, dob=?, profile_picture=? WHERE id=?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $contact, $dob, $filename, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, contact=?, dob=? WHERE id=?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $contact, $dob, $user_id);
    }

    $stmt->execute();
    $_SESSION['update_success'] = true;
    header("Location: profile_client.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Client Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .dark-mode .form-control,
    .dark-mode .card {
      background-color: #1e1e1e;
      color: #fff;
      border-color: #444;
    }
    .dark-mode input::placeholder,
    .dark-mode textarea::placeholder {
      color: #ccc !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
  <span class="navbar-brand fw-bold"><i class="bi bi-pencil-square"></i> Edit Profile</span>
  <div class="ms-auto d-flex align-items-center gap-2">
    <button class="btn btn-outline-light" onclick="toggleTheme()"><i class="bi bi-brightness-high"></i></button>
    <a href="profile_client.php" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
</nav>

<div class="container mt-5">
  <div class="card p-4 shadow">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="update_profile" value="1">
      <div class="row g-3">
        <div class="col-md-6">
          <label>First Name</label>
          <input class="form-control" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label>Last Name</label>
          <input class="form-control" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label>Contact Number</label>
          <input class="form-control" name="contact" value="<?= htmlspecialchars($user['contact']) ?>" required>
        </div>
        <div class="col-md-6">
          <label>Date of Birth</label>
          <input type="date" class="form-control" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" required>
        </div>
        <div class="col-md-12">
          <label>Profile Picture</label>
          <input type="file" class="form-control" name="profile_picture">
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-between">
        <a href="profile_client.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
      </div>
    </form>
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