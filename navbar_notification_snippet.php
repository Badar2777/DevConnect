<?php
// This snippet displays a message bell icon with unread message count

$user_id = $_SESSION['user_id'];
$count_q = $conn->query("SELECT COUNT(*) AS total FROM messages WHERE receiver_id = $user_id AND is_read = 0");
$unread_count = $count_q->fetch_assoc()['total'];
?>
<li class="nav-item dropdown">
  <a class="nav-link position-relative" href="inbox.php">
    <i class="bi bi-bell-fill fs-5"></i>
    <?php if ($unread_count > 0): ?>
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        <?php echo $unread_count; ?>
      </span>
    <?php endif; ?>
  </a>
</li>
