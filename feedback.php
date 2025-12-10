<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// DB connection
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "crimson_vault";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = $conn->real_escape_string($_POST['message']);
    $created_at = date('Y-m-d H:i:s');

    // Insert feedback into DB
    $sql = "INSERT INTO feedback (user_id, message, created_at) 
            VALUES ('$user_id', '$message', '$created_at')";

    if ($conn->query($sql) === TRUE) {
        // Get officer's email (currently logged in user)
        $officer_id = $_SESSION['user_id'];
        $emailQuery = "SELECT email FROM members WHERE user_id = '$officer_id' LIMIT 1";
        $emailResult = $conn->query($emailQuery);

        if ($emailResult && $emailResult->num_rows > 0) {
            $row = $emailResult->fetch_assoc();
            $officerEmail = $row['email'];
        } else {
            echo "<script>alert('Error: Could not fetch officer email.'); window.location.href='feedback.php';</script>";
            exit();
        }

        // Send appreciation email to officer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;

            // 🔑 SENDER EMAIL + APP PASSWORD
            $mail->Username   = 'crimsonvaultofficial.web.co@gmail.com';
            $mail->Password   = 'adtqfecrinzhsagg';

            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipient = Officer only
            $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault');
            $mail->addAddress($officerEmail);

            $mail->isHTML(false);
            $mail->Subject = "Thank You for Your Feedback – Crimson Vault";
            $mail->Body    = "Dear User,\n\n
Thank you for sharing your valuable feedback with us. 
Your thoughts and suggestions help us improve Crimson Vault 
and continue building a platform that supports law enforcement 
with security, transparency, and efficiency.\n\n
We truly appreciate your time and effort in helping us grow. 
Together, we are making case management smarter and more reliable.\n\n
With gratitude,\n
Team Crimson Vault";

            $mail->send();
            echo "<script>alert('Thank you! Your feedback has been submitted successfully.'); window.location.href='feedback.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Feedback saved, but email could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.location.href='feedback.php';</script>";
        }
    } else {
        echo "<script>alert('Error submitting feedback. Please try again later.'); window.location.href='feedback.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback</title>
  <link rel="stylesheet" href="style.css">
</head>
<body id="feedback-page">

<?php include("header.php"); ?>

<section class="feedback-section">
  <h2>We Value Your Feedback</h2>
  <form method="POST">
    <textarea name="message" placeholder="Write your feedback here..." required></textarea><br>
    <button type="submit">Submit Feedback</button>
  </form>
</section><br><br>

<?php include("footer.php"); ?>

</body>
</html>
