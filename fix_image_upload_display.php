
<?php
// Part of profile_developer.php

// Image Upload during Profile Update or Project Upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_ext)) {
        $filename = 'profile_' . $user_id . '.' . $ext;
        $destination = 'images/developer' . $filename;
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $filename, $user_id);
            $stmt->execute();
        } else {
            echo "<div class='alert alert-danger'>❌ Failed to move uploaded file.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>⚠️ Invalid image format (jpg, png, gif only).</div>";
    }
}

// Image Display
$imgFile = $user['profile_picture'] ?: 'default.png';
$imgPath = '../images/' . $imgFile;

if (!file_exists($imgPath)) {
    echo "<div class='alert alert-danger'>⚠️ Profile image not found: $imgPath</div>";
    $imgPath = '../images/default.png';
}
?>
<img src="<?php echo $imgPath; ?>" class="rounded-circle" width="120" height="120" alt="Profile Picture">
