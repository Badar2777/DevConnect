<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $msg_id = intval($_POST['id']);

    if ($user_id && $msg_id) {
        $check = $conn->prepare("SELECT id FROM messages WHERE id = ? AND sender_id = ?");
        $check->bind_param("ii", $msg_id, $user_id);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 1) {
            $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->bind_param("i", $msg_id);
            $stmt->execute();
            echo "deleted";
            exit;
        }
    }
}
echo "error";
?>
