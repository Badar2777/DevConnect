<?php
session_start();
require_once 'includes/db.php';

require_once 'dompdf/vendor/autoload.php'; // Adjust this if your autoload path is different
use Dompdf\Dompdf;

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
    die("Unauthorized access");
}

$dev_id = isset($_GET['dev_id']) ? intval($_GET['dev_id']) : 0;

// Fetch resume
$stmt = $conn->prepare("SELECT r.*, u.first_name, u.last_name FROM resumes r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.user_id = ?");
$stmt->bind_param("i", $dev_id);
$stmt->execute();
$resume = $stmt->get_result()->fetch_assoc();

if (!$resume) {
    die("Resume not found.");
}

$html = '
<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; }
    h2 { color: #333; margin-bottom: 5px; }
    p { margin: 2px 0; }
    hr { margin: 10px 0; }
  </style>
</head>
<body>
  <h2>' . htmlspecialchars($resume['full_name']) . '</h2>
  <p><strong>Email:</strong> ' . htmlspecialchars($resume['email']) . '</p>
  <p><strong>Contact:</strong> ' . htmlspecialchars($resume['contact']) . '</p>
  <p><strong>Address:</strong> ' . htmlspecialchars($resume['address']) . '</p>
  <hr>
  <h3>Summary</h3>
  <p>' . nl2br(htmlspecialchars($resume['summary'])) . '</p>
  <hr>
  <h3>Education</h3>
  <p>' . nl2br(htmlspecialchars($resume['education'])) . '</p>
  <hr>
  <h3>Experience</h3>
  <p>' . nl2br(htmlspecialchars($resume['experience'])) . '</p>
  <hr>
  <h3>Skills</h3>
  <p>' . nl2br(htmlspecialchars($resume['skills'])) . '</p>
</body>
</html>
';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Download
$filename = "Resume_" . str_replace(' ', '_', $resume['full_name']) . ".pdf";
$dompdf->stream($filename, ["Attachment" => 1]);
exit;