<?php
session_start();
require_once 'includes/db.php';

if ($_SESSION['user_type'] !== 'admin') {
  header("Location: admin_login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
  $user_id = intval($_POST['user_id']);
  $action = $_POST['action'] === 'freeze' ? 'frozen' : 'active';

  $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND user_type = 'client'");
  $stmt->bind_param("si", $action, $user_id);
  $stmt->execute();
}

header("Location: admin_manage_clients.php");
exit();