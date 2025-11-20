<?php  
session_start();  
require_once 'includes/db.php';  

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {  
    header("Location: login.php");  
    exit();  
}  

$search = $_GET['q'] ?? '';  
$developers = [];  

$sql = "SELECT u.id, u.first_name, u.last_name, u.profile_picture, u.email,  
        (SELECT GROUP_CONCAT(skill SEPARATOR ', ') FROM skills WHERE user_id = u.id) AS skill_list,  
        ROUND((SELECT AVG(rating) FROM ratings WHERE developer_id = u.id), 1) AS avg_rating  
        FROM users u WHERE u.user_type = 'developer'";  

if (!empty($search)) {  
    $sql .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%'   
             OR u.email LIKE '%$search%' OR u.id IN   
             (SELECT user_id FROM skills WHERE skill LIKE '%$search%'))";  
}  

$result = $conn->query($sql);  
while ($row = $result->fetch_assoc()) {  
    $developers[] = $row;  
}  
?>  

<!DOCTYPE html>  
<html lang="en">  

<head>  
    <meta charset="UTF-8">  
    <title>View Developers</title>  
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />  
    <style>  
        body.dark-mode {  
            background-color: #121212;  
            color: #f0f0f0;  
        }  

        .dark-mode .card,  
        .dark-mode .form-control {  
            background-color: #1e1e1e;  
            color: #fff;  
        }  

        .dark-mode .form-control::placeholder {  
            color: #ccc;  
        }  

        .badge {  
            margin: 2px;  
        }  

        .star-rating .star.text-warning {  
            color: #ffc107 !important;  
        }  

        body.dark-mode .star-rating .star {  
            color: #ccc !important;  
        }  

        body.dark-mode .star-rating .star.text-warning {  
            color: #ffc107 !important;  
        }  
    </style>  
</head>  

<body>  
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">  
        <a class="navbar-brand fw-bold" href="#"><i class="bi bi-search"></i> View Developers</a>  

        <form class="d-flex ms-auto" method="GET">  
            <input type="text" class="form-control me-2" name="q" placeholder="Search developers..."  
                value="<?= htmlspecialchars($search) ?>">  
            <button class="btn btn-outline-light" type="submit"><i class="bi bi-search"></i></button>  
        </form>  

        <div class="ms-3">  
            <button class="btn btn-outline-light" onclick="toggleTheme()">  
                <i class="bi bi-moon-stars-fill"></i>  
            </button>  
            <a href="profile_client.php" class="btn btn-outline-light"><i class="bi bi-house-door"></i> Dashboard</a>  
        </div>  
    </nav>  

    <div class="container mt-4">  
        <div class="row">  
            <?php if (empty($developers)): ?>  
                <div class="col-12 text-center">  
                    <div class="alert alert-info">No developers found.</div>  
                </div>  
            <?php else: ?>  
                <?php foreach ($developers as $dev): ?>  
                    <div class="col-md-4 mb-4">  
                        <div class="card shadow h-100">  
                            <div class="card-body text-center">  
                                <img src="images/developer/<?= file_exists("images/developer/" . $dev['profile_picture']) ? $dev['profile_picture'] : 'default.png' ?>"  
                                    class="rounded-circle mb-2" width="80" height="80" />  
                                <h5><?= htmlspecialchars($dev['first_name'] . ' ' . $dev['last_name']) ?></h5>  
                                <?php if ($dev['avg_rating'] !== null): ?>  
                                    <div class="mb-2">  
                                        <?php  
                                        $rating = round($dev['avg_rating']);  
                                        for ($i = 1; $i <= 5; $i++) {  
                                            echo $i <= $rating  
                                                ? '<i class="bi bi-star-fill text-warning"></i>'  
                                                : '<i class="bi bi-star text-muted"></i>';  
                                        }  
                                        echo " <small class='text-muted ms-1'>(" . $dev['avg_rating'] . ")</small>";  
                                        ?>  
                                    </div>  
                                <?php endif; ?>  
                                <p class="text-muted small"><?= htmlspecialchars($dev['email']) ?></p>  
                                <div>  
                                    <?php foreach (explode(',', $dev['skill_list']) as $skill): ?>  
                                        <?php if ($skill): ?>  
                                            <span class="badge bg-info text-dark"><?= htmlspecialchars(trim($skill)) ?></span>  
                                        <?php endif; ?>  
                                    <?php endforeach; ?>  
                                </div>  
                                <hr>  
                                <a href="start_chat.php?user=<?= $dev['id'] ?>" class="btn btn-sm btn-outline-primary">  
                                    <i class="bi bi-chat-dots"></i> Message  
                                </a>  
                                <a href="view_resume.php?dev_id=<?= $dev['id'] ?>" class="btn btn-sm btn-outline-success"><i  
                                        class="bi bi-file-earmark-person"></i> View Resume</a> 
                                         
                                <?php if ($_SESSION['user_type'] === 'client'): ?>  
                                    <form action="submit_rating.php" method="POST" class="mt-3 rating-form">  
                                        <input type="hidden" name="developer_id" value="<?= $dev['id'] ?>">  
                                        <input type="hidden" name="rating" class="rating-value" value="0">  

                                        <div class="star-rating d-flex justify-content-center mb-2" data-rating="0">  
                                            <?php for ($i = 1; $i <= 5; $i++): ?>  
                                                <i class="bi bi-star-fill star text-secondary mx-1" data-value="<?= $i ?>"  
                                                    style="cursor:pointer;font-size:1.2rem;"></i>  
                                            <?php endfor; ?>  
                                        </div>  

                                        <textarea name="comment" class="form-control form-control-sm mb-2"  
                                            placeholder="Add a comment (optional)" rows="2"></textarea>  
                                        <button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-star-half"></i>  
                                            Submit Rating</button>  
                                    </form>  
                                <?php endif; ?>  
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
    <script>  
        document.querySelectorAll('.star-rating').forEach(ratingDiv => {  
            const stars = ratingDiv.querySelectorAll('.star');  
            const ratingInput = ratingDiv.closest('form').querySelector('.rating-value');  

            stars.forEach(star => {  
                star.addEventListener('mouseover', () => {  
                    const val = parseInt(star.dataset.value);  
                    stars.forEach((s, i) => {  
                        s.classList.toggle('text-warning', i < val);  
                        s.classList.toggle('text-secondary', i >= val);  
                    });  
                });  

                star.addEventListener('mouseout', () => {  
                    const selected = parseInt(ratingInput.value || 0);  
                    stars.forEach((s, i) => {  
                        s.classList.toggle('text-warning', i < selected);  
                        s.classList.toggle('text-secondary', i >= selected);  
                    });  
                });  

                star.addEventListener('click', () => {  
                    const val = parseInt(star.dataset.value);  
                    ratingInput.value = val;  
                    stars.forEach((s, i) => {  
                        s.classList.toggle('text-warning', i < val);  
                        s.classList.toggle('text-secondary', i >= val);  
                    });  
                });  
            });  
        });  
    </script>  
</body>  

</html>