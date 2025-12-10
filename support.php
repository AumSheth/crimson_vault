<?php
session_start();
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = "";

// Fetch logged-in user's info
$user_id = $_SESSION['user_id'];
$userQuery = $conn->query("SELECT full_name, email FROM members WHERE user_id = $user_id LIMIT 1");
$user = $userQuery->fetch_assoc();
$name = $user['full_name'];
$email = $user['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $issue = $conn->real_escape_string($_POST['issue']);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO support_requests (user_id, name, email, issue) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $name, $email, $issue);

    if ($stmt->execute()) {
        // Send email to Admin using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'crimsonvaultofficial.web.co@gmail.com'; // sender email
            $mail->Password   = 'adtqfecrinzhsagg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault Support');
            $mail->addAddress('aums35470@gmail.com', 'Admin'); // Admin email

            // Content
            $mail->isHTML(true);
            $mail->Subject = "New Support Query - Crimson Vault";
            $mail->Body    = "
                <h3>New Support Query</h3>
                <p><strong>From:</strong> {$name} ({$email})</p>
                <p><strong>Issue:</strong><br>" . nl2br(htmlspecialchars($issue)) . "</p>
                <p><em>Submitted on: " . date("Y-m-d H:i:s") . "</em></p>
            ";
            $mail->AltBody = "New support query from $name ($email)\n\nIssue:\n$issue";

            $mail->send();
            echo "<script>alert('✅ Your query has been submitted successfully.'); window.location='support.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<?php include("header.php"); ?>

<body id="feedback-page">
  <section class="feedback-section">
    <h2>Support</h2>

    <?php if (!empty($message)): ?>
      <p class="alert"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
      <textarea name="issue" id="issue" placeholder="Describe your issue..." required></textarea>
      <button type="submit">Submit Query</button>
    </form>
  </section><br><br>
</body>

<?php include("footer.php"); ?>
