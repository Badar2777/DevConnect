<?php
session_start();
require_once 'includes/db.php';

// Redirect if not developer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Get applied jobs for this developer
$applied_jobs = [];
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'developer') {
  $dev_id = $_SESSION['user_id'];
  $applied_q = $conn->prepare("SELECT job_id FROM applied_jobs WHERE developer_id = ?");
  $applied_q->bind_param("i", $dev_id);
  $applied_q->execute();
  $applied_result = $applied_q->get_result();
  while ($row = $applied_result->fetch_assoc()) {
    $applied_jobs[] = $row['job_id'];
  }
}

// Fetch all jobs
$stmt = $conn->prepare("SELECT jobs.*, users.first_name, users.last_name FROM jobs 
                        JOIN users ON jobs.client_id = users.id
                        ORDER BY created_at DESC");
$stmt->execute();
$jobs = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Explore Jobs</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f0f0f0;
    }

    .dark-mode .card {
      background-color: #1f1f1f;
      color: #fff;
    }

    .dark-mode .text-muted {
      color: #aaa !important;
    }

    .dark-mode .navbar {
      background-color: #1e1e1e !important;
    }

    .job-card {
      animation: fadeInUp 0.6s ease-in-out;
      border-radius: 1rem;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .tag {
      display: inline-block;
      background: #0d6efd;
      color: white;
      font-size: 0.75rem;
      padding: 0.2rem 0.6rem;
      border-radius: 0.5rem;
      margin-right: 5px;
      margin-top: 5px;
    }

    .dark-mode .tag {
      background-color: #198754;
    }

    .back-btn {
      transition: all 0.3s ease-in-out;
      border-radius: 25px;
      padding: 8px 20px;
    }

    .back-btn:hover {
      transform: scale(1.05);
      background-color: #e9ecef;
    }

    .dark-mode .back-btn:hover {
      background-color: #333;
      color: #fff;
    }
  </style>
</head>

<body>

  <!-- Navbar with logo and back-to-profile -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-2 shadow-sm">
    <a class="navbar-brand fw-bold text-light" href="index.php">
      &lt;/&gt; DevConnect
    </a>
    <div class="ms-auto">
      <a href="profile_developer.php" class="btn btn-outline-light">
        <i class="bi bi-person-circle"></i> Back to Profile
      </a>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="text-primary">🌐 Explore All Posted Jobs</h2>
      <button onclick="toggleTheme()" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-moon"></i>
      </button>
    </div>

    <?php if ($jobs->num_rows === 0): ?>
      <div class="alert alert-info">No jobs have been posted yet.</div>
    <?php else: ?>
      <div class="row">
        <?php while ($job = $jobs->fetch_assoc()): ?>
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow job-card h-100 p-3">
              <h5 class="text-primary"><?= htmlspecialchars($job['title']) ?></h5>
              <p class="text-muted mb-1 small">Posted by:
                <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?></p>
              <p class="mb-2"><?= nl2br(htmlspecialchars(substr($job['description'], 0, 120))) ?>...</p>

              <?php if (!empty($job['skills'])): ?>
                <div class="mb-2">
                  <?php
                  $skills = explode(',', $job['skills']);
                  foreach ($skills as $skill): ?>
                    <span class="tag"><?= htmlspecialchars(trim($skill)) ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <div class="d-flex justify-content-between mt-2 mb-3">
                <span class="text-muted">💰 <?= htmlspecialchars($job['budget']) ?></span>
                <span class="text-muted">📅 <?= htmlspecialchars($job['deadline']) ?></span>
              </div>

              <!-- Apply Button Logic -->
              <?php if ($_SESSION['user_type'] === 'developer'): ?>
                <?php if (in_array($job['id'], $applied_jobs)): ?>
                  <button class="btn btn-outline-secondary w-100" disabled>✅ Applied</button>
                <?php else: ?>
                  <button class="btn btn-outline-success w-100 apply-btn" data-job-id="<?= $job['id'] ?>">
                    <i class="bi bi-check2-circle"></i> Apply
                  </button>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php endif; ?>
  </div>

<script>
  function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  }
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

 document.querySelectorAll('.apply-btn').forEach(button => {
  button.addEventListener('click', function () {
    const jobId = this.dataset.jobId;
    const btn = this;

    btn.disabled = true;
    btn.innerHTML = "⏳ Applying...";

    fetch('apply_job.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'job_id=' + encodeURIComponent(jobId)
    })
    .then(res => res.text())
    .then(data => {
      if (data.toLowerCase().includes("applied")) {
        btn.classList.remove('btn-outline-success');
        btn.classList.add('btn-outline-secondary');
        btn.innerHTML = "✅ Applied";
      } else {
        alert(data); // Show error message like "Already applied" or "Error"
        btn.disabled = false;
        btn.innerHTML = "Apply";
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error while applying for job.");
      btn.disabled = false;
      btn.innerHTML = "Apply";
    });
  });
});
</script>
</body>

</html>