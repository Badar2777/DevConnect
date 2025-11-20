<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Goodbye - DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      background-color: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      transition: background-color 0.3s ease;
    }

    .card {
      padding: 2rem;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }

    .dark-mode .card {
      background-color: #1e1e1e;
      color: #fff;
    }

    .btn-theme {
      position: absolute;
      top: 20px;
      right: 20px;
    }
  </style>
</head>
<body>
  <button class="btn btn-outline-light btn-theme" onclick="toggleTheme()">
    <i class="bi bi-moon-stars-fill"></i>
  </button>

  <div class="card">
    <h2 class="text-danger mb-3"><i class="bi bi-door-closed-fill"></i> Account Deleted</h2>
    <p class="text-muted">We're sorry to see you go. Thank you for being part of <strong>DevConnect</strong>.</p>
    <a href="index.php" class="btn btn-primary mt-3"><i class="bi bi-house-door-fill"></i> Return Home</a>
  </div>

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