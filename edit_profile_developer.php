<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['delete_image'])) {
        $conn->query("UPDATE users SET profile_picture = NULL WHERE id = $user_id");
        $msg = "Profile picture removed.";
    } else {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $dob = $_POST['dob'];
        $contact = $_POST['contact'];
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        $profile_picture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $filename = 'developer_' . time() . '.' . $ext;
            $upload_path = 'images/developer/' . $filename;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path);
            $profile_picture = $filename;
        }

        $query = "UPDATE users SET first_name=?, last_name=?, dob=?, contact=?";
        $params = [$first_name, $last_name, $dob, $contact];

        if ($profile_picture) {
            $query .= ", profile_picture=?";
            $params[] = $profile_picture;
        }
        if ($password) {
            $query .= ", password=?";
            $params[] = $password;
        }

        $query .= " WHERE id=?";
        $params[] = $user_id;

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
        $stmt->execute();

        $msg = "Profile updated successfully!";
    }
}

$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
?><!DOCTYPE html><html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f1f4f9;
      color: #333;
    }
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }
    .form-card {
      background-color: #ffffff;
    }
    body.dark-mode .form-card {
      background-color: #1f1f1f;
      color: #f0f0f0;
    }
    body.dark-mode input,
    body.dark-mode textarea,
    body.dark-mode select {
      background-color: #2b2b2b;
      color: #f0f0f0;
    }
    body.dark-mode input::placeholder,
    body.dark-mode textarea::placeholder {
      color: #ccc;
    }
    .btn-sm {
      padding: 0.4rem 1rem;
      font-size: 0.875rem;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2 class="text-center mb-4">Edit Profile</h2>
  <?php if ($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
  <form method="POST" enctype="multipart/form-data" class="card p-4 shadow form-card" onsubmit="return confirm('Are you sure you want to update your profile?')">
    <div class="mb-3">
      <label>First Name</label>
      <input type="text" name="first_name" value="<?php echo isset($user['first_name']) ? $user['first_name'] : ''; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Last Name</label>
      <input type="text" name="last_name" value="<?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Date of Birth</label>
      <input type="date" name="dob" value="<?php echo isset($user['dob']) ? $user['dob'] : ''; ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>Contact Number</label>
      <input type="text" name="contact" value="<?php echo isset($user['contact']) ? $user['contact'] : ''; ?>" class="form-control">
    </div>
    <div class="mb-3">
      <label>New Password <small>(Leave blank to keep current)</small></label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
      <label>Profile Picture</label><br>
      <img src="images/developer/<?php echo isset($user['profile_picture']) ? $user['profile_picture'] : 'default.png'; ?>" width="100" class="mb-2 rounded-circle"><br>
      <input type="file" name="profile_picture" class="form-control">
      <button name="delete_image" class="btn btn-outline-danger btn-sm mt-2" onclick="return confirm('Are you sure you want to delete your profile picture?')">Delete Profile Picture</button>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success btn-sm">Update Profile</button>
      <a href="profile_developer.php" class="btn btn-secondary btn-sm">Cancel</a>
      <a href="profile_developer.php" class="btn btn-outline-primary btn-sm">Return to Main Page</a>
    </div>
  </form>
</div>
<script>
  // Theme sync on load
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }
</script>
</body>
</html>