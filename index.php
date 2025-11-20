
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to DevConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: url('https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed;
      background-size: cover;
      transition: background-color 0.3s, color 0.3s;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.65);
      height: 100%;
      width: 100%;
      position: absolute;
      top: 0;
      left: 0;
    }

    .content {
      position: relative;
      z-index: 2;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #fff;
      text-align: center;
    }

    .btn-glow {
      border-radius: 30px;
      padding: 12px 30px;
      margin: 10px;
      font-size: 18px;
      font-weight: bold;
      transition: 0.3s;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    }

    .btn-glow:hover {
      box-shadow: 0 0 25px rgba(255, 255, 255, 0.4);
      transform: scale(1.05);
    }

    .dark-mode {
      background-color: #121212 !important;
      color: #f0f0f0 !important;
    }

    .dark-mode .btn-glow {
      background-color: #198754 !important;
      border: none;
      color: white;
    }

    .toggle-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      z-index: 999;
    }

    @media (max-width: 768px) {
      .btn-glow {
        width: 80%;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>

  <div class="overlay"></div>


  <div class="content">
    <h1 class="mb-4">👨‍💻 Welcome to <span class="text-info">DevConnect</span></h1>
    <p class="lead mb-5">Where developers meet clients and build amazing things.</p>
    <div>
      <a href="register.php" class="btn btn-success btn-glow">Sign Up</a>
      <a href="login.php" class="btn btn-outline-light btn-glow">Sign In</a>
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
</body>
</html>