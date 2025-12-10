<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST['email'] ?? ''));
    if ($email === '') { exit("Email required"); }

    $otp = str_pad((string) random_int(111111, 999999), 6, '0', STR_PAD_LEFT);

    // Insert new OTP each time
    $stmt = $conn->prepare("
        INSERT INTO password_resets (email, otp, expires_at, created_at)
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 3 MINUTE), NOW())
    ");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();

    // ✅ Setup PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // 🔑 Sender Gmail + App Password (replace with your real ones)
        $mail->Username   = 'crimsonvaultofficial.web.co@gmail.com';
        $mail->Password   = 'adtqfecrinzhsagg'; // Gmail App Password

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(false); // plain text
        $mail->Subject = "Crimson Vault - Password Reset OTP";
        $mail->Body    = "Your OTP is: $otp\nValid for 3 minutes.";

        if ($mail->send()) {
            echo "<script>
                alert('✅ OTP sent to your email.');
                window.location='verify_otp.php?mode=forgot&email=" . urlencode($email) . "';
            </script>";
        } else {
            echo "<script>
                alert('❌ Failed to send OTP.');
                window.history.back();
            </script>";
        }
    } catch (Exception $e) {
        echo "<script>
            alert('❌ Mailer Error: {$mail->ErrorInfo}');
            window.history.back();
        </script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password - Crimson Vault</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <img src="logo.png" alt="Crimson Vault Logo" class="logo">
    <div class="form-container">
      <h2>Forgot Password</h2>
      <form method="POST" class="form">
        <input type="email" name="email" placeholder="📧 Enter your email" required>
        <button type="submit">Send OTP</button>
      </form>
    </div>
  </div>
</body>
</html>
