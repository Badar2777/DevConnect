<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$search = $_GET['q'] ?? '';
$users = [];

$sql = "SELECT u.id, u.first_name, u.last_name, u.email, u.profile_picture, u.user_type,
       (SELECT GROUP_CONCAT(skill SEPARATOR ', ') FROM skills WHERE user_id = u.id) AS skill_list,
       ROUND((SELECT AVG(rating) FROM ratings WHERE developer_id = u.id), 1) AS avg_rating
        FROM users u
        WHERE u.id != $current_user";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (
        u.first_name LIKE '%$search%' OR
        u.last_name LIKE '%$search%' OR
        u.email LIKE '%$search%' OR
        u.id IN (SELECT user_id FROM skills WHERE skill LIKE '%$search%') OR
        u.id IN (SELECT user_id FROM projects WHERE title LIKE '%$search%' OR description LIKE '%$search%') OR
        u.id IN (SELECT user_id FROM resumes WHERE full_name LIKE '%$search%' OR skills LIKE '%$search%' OR education LIKE '%$search%')
    )";
}

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Users</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #fff;
    }
    .dark-mode .card,
    .dark-mode .form-control {
      background-color: #1e1e1e;
      color: #fff;
    }
    .dark-mode .form-control::placeholder,
    .dark-mode .text-muted {
      color: #ccc !important;
    }
    .badge {
      margin: 2px;
    }
    .star-rating i {
      font-size: 1rem;
    }
    .dark-mode .star-rating .bi-star {
      color: #777 !important;
    }
    .dark-mode .star-rating .bi-star-fill {
      color: #ffc107 !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <span class="navbar-brand fw-bold"><i class="bi bi-search"></i> Search Users</span>
  <form class="d-flex ms-auto" method="GET">
    <input class="form-control me-2" type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" required>
    <button class="btn btn-outline-light"><i class="bi bi-search"></i></button>
  </form>
  <div class="ms-3 d-flex gap-2">
    <button class="btn btn-outline-light" onclick="toggleTheme()"><i class="bi bi-moon-stars-fill"></i></button>
    <a href="<?= $user_type === 'developer' ? 'profile_developer.php' : 'profile_client.php' ?>" class="btn btn-outline-light">
      <i class="bi bi-person-circle"></i> Back to Profile
    </a>
  </div>
</nav>

<div class="container mt-4">
  <div class="row">
    <?php if (empty($users)): ?>
      <div class="col-12 text-center">
        <div class="alert alert-info">No users found.</div>
      </div>
    <?php else: ?>
      <?php foreach ($users as $user): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
              <img src="images/developer/<?= file_exists("images/developer/" . $user['profile_picture']) ? $user['profile_picture'] : 'default.png' ?>"
                   class="rounded-circle mb-2" width="80" height="80">
              <h5><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
              <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
              <span class="badge bg-secondary"><?= ucfirst($user['user_type']) ?></span>

              <?php if ($user['user_type'] === 'developer' && $user['avg_rating'] !== null): ?>
                <div class="star-rating my-2">
                  <?php
                  $avg = round($user['avg_rating']);
                  for ($i = 1; $i <= 5; $i++) {
                      echo $i <= $avg
                          ? '<i class="bi bi-star-fill text-warning"></i>'
                          : '<i class="bi bi-star text-muted"></i>';
                  }
                  ?>
                  <small class="text-muted ms-1">(<?= $user['avg_rating'] ?>)</small>
                </div>
              <?php endif; ?>

              <?php if (!empty($user['skill_list'])): ?>
                <div class="mt-2">
                  <?php foreach (explode(',', $user['skill_list']) as $skill): ?>
                    <?php if (trim($skill)): ?>
                      <span class="badge bg-info text-dark"><?= htmlspecialchars(trim($skill)) ?></span>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <hr>
              <a href="start_chat.php?user=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-chat-dots"></i> Message
              </a>
              <a href="view_resume.php?dev_id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-earmark-person"></i> View Resume
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
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