<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'], $_POST['message'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $msg_id = intval($_POST['id']);
    $new_msg = trim($_POST['message']);

    if ($user_id && $msg_id && $new_msg !== '') {
        // Check in private messages
        $checkPrivate = $conn->prepare("SELECT id FROM messages WHERE id = ? AND sender_id = ?");
        $checkPrivate->bind_param("ii", $msg_id, $user_id);
        $checkPrivate->execute();
        $resPrivate = $checkPrivate->get_result();

        if ($resPrivate->num_rows === 1) {
            $stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ?");
            $stmt->bind_param("si", $new_msg, $msg_id);
            $stmt->execute();
            echo "success";
            exit;
        }

        // Check in group messages
        $checkGroup = $conn->prepare("SELECT id FROM group_messages WHERE id = ? AND user_id = ?");
        $checkGroup->bind_param("ii", $msg_id, $user_id);
        $checkGroup->execute();
        $resGroup = $checkGroup->get_result();

        if ($resGroup->num_rows === 1) {
            $stmt = $conn->prepare("UPDATE group_messages SET message = ? WHERE id = ?");
            $stmt->bind_param("si", $new_msg, $msg_id);
            $stmt->execute();
            echo "success";
            exit;
        }
    }
}

echo "error";
?>