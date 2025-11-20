<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['chat'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$current_user  = $_SESSION['user_id'];
$selected_user = intval($_GET['chat']);

$stmt = $conn->prepare("
    SELECT m.*, u.user_type
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?) 
       OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
");
$stmt->bind_param("iiii", $current_user, $selected_user, $selected_user, $current_user);
$stmt->execute();
$res = $stmt->get_result();

$last_date = '';
date_default_timezone_set('Asia/Karachi'); // optional: adjust for your region

while ($msg = $res->fetch_assoc()) {
    $side = $msg['sender_id'] == $current_user ? 'msg-right' : 'msg-left';
    $text = htmlspecialchars($msg['message']);
    $escapedText = htmlspecialchars(addslashes($msg['message']));
    $time = date("h:i A", strtotime($msg['created_at']));
    $attachment = $msg['attachment'];
    $message_id = $msg['id'];
    $role = ucfirst($msg['user_type'] ?? 'User');

    $msg_date = date("Y-m-d", strtotime($msg['created_at']));
    if ($msg_date !== $last_date) {
        // Print date separator
        $today = date("Y-m-d");
        $yesterday = date("Y-m-d", strtotime("-1 day"));
        if ($msg_date === $today) {
            $dateLabel = "Today";
        } elseif ($msg_date === $yesterday) {
            $dateLabel = "Yesterday";
        } else {
            $dateLabel = date("F j, Y", strtotime($msg['created_at']));
        }
        echo "<div class='text-center text-muted my-3'><small>— $dateLabel —</small></div>";
        $last_date = $msg_date;
    }

    echo "<div class='message $side' style='margin-bottom: 10px;'>";

    // Sender role badge (for received messages only)
    if ($side === 'msg-left') {
        echo "<div class='mb-1'><span class='badge bg-secondary'>$role</span></div>";
    }

    // Message text
    if (!empty($text)) {
        echo "<span style='display:inline-block;padding:8px 14px;border-radius:16px;background:#d1e7dd;color:#000;max-width:80%;word-wrap:break-word;'>$text</span>";
    }

    // Attachment display
    if ($attachment) {
        $file_path = "uploads/messages/" . $attachment;
        $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            echo "<div class='mt-2'><img src='$file_path' style='max-width:200px;border-radius:8px;margin-top:5px;'></div>";
        } else {
            echo "<div class='mt-2'><a href='$file_path' download style='font-size:0.9rem;text-decoration:none;color:#0d6efd;'><i class='bi bi-paperclip'></i> Download Attachment</a></div>";
        }
    }

    // Meta section with time, status, edit/delete
    echo "<div class='meta' style='font-size:0.75rem;color:gray;margin-top:5px;'>$time";

    if ($msg['sender_id'] == $current_user) {
        echo "<span style='margin-left: 10px;'>";
        echo $msg['is_read']
            ? "<i class='bi bi-check2-all text-primary' title='Seen'></i>"
            : "<i class='bi bi-check2 text-muted' title='Delivered'></i>";
        echo "</span>";

        // Edit/Delete Actions
        echo "<span class='actions' style='margin-left: 12px;'>";
        echo "<i class='bi bi-pencil-square text-primary edit-icon' data-id='$message_id' data-message=\"$escapedText\" style='cursor:pointer;margin-right:8px;'></i>";
        echo "<i class='bi bi-trash text-danger delete-icon' data-id='$message_id' style='cursor:pointer;'></i>";
        echo "</span>";
    }

    echo "</div>"; // .meta
    echo "</div>"; // .message
}
?>