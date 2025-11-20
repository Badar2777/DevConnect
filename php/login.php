<?php
session_start();
require_once 'includes/db.php';

$msg = "";
$lockout_time = 120; // seconds
$max_attempts = 5;

if (isset($_COOKIE['remember_user'])) {
    header("Location: router.php");
    exit();
}

if (isset($_SESSION['just_registered']) && $_SESSION['just_registered'] === true) {
    $msg = "Registration successful! You can now login.";
    unset($_SESSION['just_registered']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        $user_type = $user['user_type'];

        $check_lock = $conn->prepare("SELECT * FROM login_attempts WHERE user_id = ?");
        $check_lock->bind_param("i", $user_id);
        $check_lock->execute();
        $lock_result = $check_lock->get_result();
        $attempt = $lock_result->fetch_assoc();

        if ($attempt && $attempt['locked_until'] && strtotime($attempt['locked_until']) > time()) {
            $remaining = strtotime($attempt['locked_until']) - time();
            $msg = "Account locked. Try again in {$remaining} seconds.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_type'] = $user_type;

            if ($remember) {
                setcookie('remember_user', $user_id, time() + (86400 * 30), "/");
            }
            $conn->query("DELETE FROM login_attempts WHERE user_id = $user_id");

            if ($user_type === 'developer') {
                header("Location: profile_developer.php");
            } else {
                header("Location: profile_client.php");
            }
            exit();
        } else {
            $current_attempts = $attempt ? $attempt['attempts'] : 0;
            $current_attempts++;
            $locked_until = $current_attempts >= $max_attempts ? date("Y-m-d H:i:s", time() + $lockout_time) : null;

            if ($attempt) {
                $update = $conn->prepare("UPDATE login_attempts SET attempts = ?, locked_until = ? WHERE user_id = ?");
                $update->bind_param("isi", $current_attempts, $locked_until, $user_id);
                $update->execute();
            } else {
                $insert = $conn->prepare("INSERT INTO login_attempts (user_id, attempts, locked_until) VALUES (?, ?, ?)");
                $insert->bind_param("iis", $user_id, $current_attempts, $locked_until);
                $insert->execute();
            }

            if ($current_attempts >= $max_attempts) {
                $msg = "Account locked for 2 minutes due to multiple failed attempts.";
            } elseif ($current_attempts >= 3) {
                $remaining_attempts = $max_attempts - $current_attempts;
                $msg = "Incorrect password. $remaining_attempts attempts left.";
            } else {
                $msg = "Incorrect email or password.";
            }
        }
    } else {
        $msg = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Developer Network - Login</h2>
    <?php if ($msg) echo "<div class='alert alert-warning'>$msg</div>"; ?>
    <form method="POST" class="card p-4 shadow">
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <p class="mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
    </form>
</div>
</body>
</html>