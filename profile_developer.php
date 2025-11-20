<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$count_q = $conn->query("SELECT COUNT(*) AS total FROM messages WHERE receiver_id = $user_id AND is_read = 0");
$unread_count = $count_q->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$skills = [];
$res = $conn->query("SELECT skill FROM skills WHERE user_id = $user_id");
while ($row = $res->fetch_assoc()) {
  $skills[] = $row['skill'];
}

$projects = [];
$res2 = $conn->query("SELECT * FROM projects WHERE user_id = $user_id ORDER BY created_at DESC");
while ($row = $res2->fetch_assoc()) {
  $projects[] = $row;
}

$profile_image = file_exists("images/developer/" . $user['profile_picture']) ? $user['profile_picture'] : 'default.png';
// Fetch unread notifications count for developer
$notif_query = $conn->query("SELECT COUNT(*) AS total FROM notifications WHERE user_id = $user_id AND user_type = 'developer' AND is_read = 0");
$notif_count = $notif_query->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Developer Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
 <style>
  body.dark-mode {
    background-color: #121212;
    color: #f0f0f0;
  }

  .dark-mode .card,
  .dark-mode .form-control,
  .dark-mode .btn {
    background-color: #1e1e1e;
    color: #f0f0f0;
    border-color: #444;
  }

  .dark-mode input::placeholder,
  .dark-mode textarea::placeholder {
    color: #aaa !important;
    opacity: 1;
  }

  .dark-mode .form-control::placeholder {
    color: #ccc;
  }

  .dark-mode .btn-outline-primary {
    color: #339af0;
    border-color: #339af0;
  }

  .dark-mode .btn-outline-danger {
    color: #ff6b6b;
    border-color: #ff6b6b;
  }

  .dark-mode .btn-outline-warning {
    color: #ffc107;
    border-color: #ffc107;
  }

  .dark-mode .btn-outline-success {
    color: #51cf66;
    border-color: #51cf66;
  }

  .dark-mode .btn-outline-secondary {
    color: #adb5bd;
    border-color: #adb5bd;
  }

  .dark-mode .btn-outline-primary:hover,
  .dark-mode .btn-outline-danger:hover,
  .dark-mode .btn-outline-warning:hover,
  .dark-mode .btn-outline-success:hover,
  .dark-mode .btn-outline-secondary:hover {
    background-color: transparent;
    opacity: 0.8;
  }
  body.dark-mode .text-muted {
  color: rgba(255, 255, 255, 0.6) !important;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-code-slash"></i> DevConnect</a>
  <div class="ms-auto d-flex align-items-center gap-2">
    <form class="d-flex me-2" method="GET" action="search_users.php">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Search developers..." required>
    </form>
        <a href="inbox.php" class="btn btn-outline-light" title="Inbox">
      <i class="bi bi-chat-dots"></i>
    </a>
    <a href="explore.php" class="btn btn-outline-light" title="Explore Jobs"><i class="bi bi-person-workspace"></i></a>
    <a href="developer_notifications.php" class="btn btn-outline-light position-relative" title="Notifications">
  <i class="bi bi-bell"></i>
  <?php if ($notif_count > 0): ?>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
      <?= $notif_count ?>
    </span>
  <?php endif; ?>
</a>
    <button class="btn btn-outline-light" id="themeToggle" title="Toggle Theme">
      <i class="bi bi-moon-stars-fill"></i>
    </button>
    <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</nav>

<div class="container mt-4">
  <div class="text-center">
    <img src="images/developer/<?php echo $profile_image; ?>" class="rounded-circle" width="120">
    <h3 class="mt-2 fw-bold"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
    <p class="text-muted">Developer | <?php echo $user['email']; ?></p>
    <div id="skillsBadges">
      <?php foreach ($skills as $skill): ?>
        <span class="badge bg-info text-dark me-1 mb-1"><?php echo htmlspecialchars($skill); ?></span>
      <?php endforeach; ?>
    </div>
  </div>

  <hr>

  <div class="row row-cols-1 row-cols-md-3 g-4 text-center mb-4">
    <div class="col">
      <div class="card shadow h-100">
        <div class="card-body">
          <i class="bi bi-person-lines-fill display-5 text-warning"></i>
          <h5 class="card-title mt-3">Edit Profile</h5>
          <a href="edit_profile_developer.php" class="btn btn-outline-warning">Edit</a>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card shadow h-100">
        <div class="card-body">
          <i class="bi bi-tools display-5 text-primary"></i>
          <h5 class="card-title mt-3">Add Skills</h5>
          <a href="add_skills.php" class="btn btn-outline-primary">Add</a>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card shadow h-100">
        <div class="card-body">
          <i class="bi bi-cloud-upload-fill display-5 text-success"></i>
          <h5 class="card-title mt-3">Upload Project</h5>
          <a href="upload_project.php" class="btn btn-outline-success">Upload</a>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card shadow h-100 border-danger">
        <div class="card-body">
          <i class="bi bi-person-x-fill display-5 text-danger"></i>
          <h5 class="card-title mt-3">Delete Profile</h5>
          <button class="btn btn-outline-danger mt-2" onclick="deleteProfile()">Delete My Account</button>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card shadow h-100 border-danger">
        <div class="card-body">
          <i class="bi bi-x-circle display-5 text-danger"></i>
          <h5 class="card-title mt-3">Delete Skills</h5>
          <a href="delete_skills.php" class="btn btn-outline-danger">Delete</a>
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card shadow h-100 border-danger">
        <div class="card-body">
          <i class="bi bi-kanban display-5 text-danger"></i>
          <h5 class="card-title mt-3">Delete Projects</h5>
          <a href="delete_project.php" class="btn btn-outline-danger">Delete</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Projects Section -->
  <h4 class="mt-5 text-center"><i class="bi bi-kanban-fill"></i> My Projects</h4>
  <hr class="mb-4">

  <div class="row">
    <?php if (empty($projects)): ?>
      <div class="col-12 text-center text-muted">
        <p>No projects uploaded yet.</p>
      </div>
    <?php endif; ?>
    <?php foreach ($projects as $project): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <?php if (!empty($project['image_path'])): ?>
            <img src="images/developer/<?php echo htmlspecialchars($project['image_path']); ?>" class="card-img-top" style="max-height: 200px; object-fit: cover;">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
          </div>
          <?php if (!empty($project['github_link'])): ?>
          <div class="card-footer text-end">
            <a href="<?php echo htmlspecialchars($project['github_link']); ?>" target="_blank" class="btn btn-sm btn-dark">
              <i class="bi bi-github"></i> View on GitHub
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  // Theme toggle
  document.addEventListener('DOMContentLoaded', () => {
    const themeButton = document.getElementById('themeToggle');
    if (themeButton) {
      themeButton.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
      });
    }

    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark-mode');
    }
  });

  // Global delete profile function
  function deleteProfile() {
    if (confirm("Are you sure you want to delete your account permanently? This action cannot be undone.")) {
      window.location.href = "delete_developer.php";
    }
  }
</script>
</body>
</html>