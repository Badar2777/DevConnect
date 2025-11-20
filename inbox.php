<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$current_user = $_SESSION['user_id'];
$selected_user = isset($_GET['chat']) ? intval($_GET['chat']) : null;

// Fetch all chat participants
$users = [];
$stmt = $conn->prepare("
  SELECT DISTINCT IF(sender_id = ?, receiver_id, sender_id) AS user_id 
  FROM messages 
  WHERE sender_id = ? OR receiver_id = ?
");
$stmt->bind_param("iii", $current_user, $current_user, $current_user);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
  $uid = $row['user_id'];
  $u = $conn->query("SELECT id, first_name, last_name, user_type FROM users WHERE id = $uid")->fetch_assoc();
  if ($u) $users[] = $u;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inbox</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="css/inbox.css"/>
  <style>
    body.dark-mode {
      background: #121212;
      color: #f0f0f0;
    }
    .dark-mode .form-control {
      background: #1e1e1e;
      color: #fff;
    }
    .dark-mode input::placeholder,
    .dark-mode textarea::placeholder {
      color: #ccc !important;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
  <a class="navbar-brand fw-bold" href="#"><i class="bi bi-chat-dots"></i> Messages</a>
  <div class="ms-auto d-flex align-items-center gap-2">
    <button class="btn btn-outline-light" onclick="toggleTheme()" title="Toggle Theme">
      <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
    </button>
    <a href="<?= $_SESSION['user_type'] === 'developer' ? 'profile_developer.php' : 'profile_client.php' ?>" class="btn btn-outline-secondary" title="Back">
      <i class="bi bi-arrow-left"></i>
    </a>
  </div>
</nav>

<div class="container mt-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-4">
      <h5>Conversations</h5>
      <ul class="list-group chat-list mb-3">
        <?php foreach ($users as $u): 
          $unread = $conn->query("SELECT COUNT(*) AS c FROM messages WHERE sender_id = {$u['id']} AND receiver_id = $current_user AND is_read = 0")->fetch_assoc()['c'];
        ?>
        <a href="?chat=<?= $u['id'] ?>" 
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center conversation <?= $selected_user == $u['id'] ? 'active' : '' ?>"
           data-user-id="<?= $u['id'] ?>">
          <div>
            <?= $u['first_name'] . ' ' . $u['last_name'] ?>
            <span class="badge <?= $u['user_type'] === 'developer' ? 'bg-primary' : 'bg-success' ?> ms-2">
              <?= ucfirst($u['user_type']) ?>
            </span>
          </div>
          <?php if ($unread > 0): ?>
            <span class="badge bg-danger unread-count"><?= $unread ?></span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Chat Area -->
    <div class="col-md-8">
      <h5 class="mb-3">Chat</h5>
      <div class="chat-box" id="chatBox">
        <!-- Messages loaded by JS -->
      </div>

      <div id="typingStatus" class="text-muted small mt-1" style="display:none;">Typing...</div>

      <!-- Message Form -->
      <?php if ($selected_user): ?>
      <form id="msgForm" class="d-flex align-items-center mt-3" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="receiver_id" value="<?= $selected_user ?>">
        <input type="text" name="message" id="messageInput" class="form-control me-2" placeholder="Type a message..." required>
        <input type="file" name="attachment" id="fileInput" style="display: none;" />
        <button type="button" class="btn btn-outline-secondary me-2" id="attachBtn"><i class="bi bi-paperclip"></i></button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<audio id="notificationSound" src="sounds/notify.mp3" preload="auto"></audio>
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="js/inbox.js" defer></script>
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