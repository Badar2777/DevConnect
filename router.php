<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'start_chat':
        include 'start_chat.php';
        break;
    // Add other routes like:
    case 'profile':
        include 'profile_developer.php';
        break;
    // ...
    default:
        echo "Page not found.";
}
?>