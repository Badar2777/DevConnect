<?php
session_start();
require_once 'includes/db.php';

// Load Dompdf manually
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

$user_id = $_SESSION['user_id'];

// Fetch resume data
$stmt = $conn->prepare("SELECT * FROM resumes WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();

if (!$resume) {
    exit("No resume data found.");
}

// Create PDF HTML
ob_start();
?>
<html>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { margin-bottom: 5px; }
    .section { margin-top: 15px; }
    .section-title { font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #ccc; }
  </style>
  <body>
    <h2><?= htmlspecialchars($resume['full_name']) ?></h2>
    <p>Email: <?= htmlspecialchars($resume['email']) ?></p>
    <p>Contact: <?= htmlspecialchars($resume['contact']) ?></p>
    <p>Address: <?= htmlspecialchars($resume['address']) ?></p>

    <div class="section">
      <div class="section-title">Summary</div>
      <p><?= nl2br(htmlspecialchars($resume['summary'])) ?></p>
    </div>

    <div class="section">
      <div class="section-title">Skills</div>
      <p><?= nl2br(htmlspecialchars($resume['skills'])) ?></p>
    </div>

    <div class="section">
      <div class="section-title">Education</div>
      <p><?= nl2br(htmlspecialchars($resume['education'])) ?></p>
    </div>

    <div class="section">
      <div class="section-title">Experience</div>
      <p><?= nl2br(htmlspecialchars($resume['experience'])) ?></p>
    </div>
  </body>
</html>
<?php
$html = ob_get_clean();

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Send PDF to browser
$dompdf->stream("resume.pdf", ["Attachment" => true]);