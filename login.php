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
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        echo "<script>alert('⚠️ Email and Password are required.'); window.history.back();</script>";
        exit();
    }

    // ✅ Check user
    $stmt = $conn->prepare("SELECT * FROM members WHERE email = ? AND status = 'active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
            
            // ✅ Save user temporarily in session
            $_SESSION['pending_user'] = $user;

            // ✅ Generate OTP
            $otp = str_pad((string) random_int(111111, 999999), 6, '0', STR_PAD_LEFT);

            // ✅ Clear old OTP
            $conn->query("DELETE FROM password_resets WHERE email = '".$conn->real_escape_string($email)."'");

            // ✅ Insert new OTP (3 min validity)
            $stmt = $conn->prepare("
                INSERT INTO password_resets (email, otp, expires_at, created_at)
                VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 3 MINUTE), NOW())
            ");
            $stmt->bind_param("ss", $email, $otp);
            $stmt->execute();

            // ✅ Send OTP email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'crimsonvaultofficial.web.co@gmail.com';
                $mail->Password   = 'adtqfecrinzhsagg'; // Gmail App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault');
                $mail->addAddress($email, $user['full_name']);

                $mail->isHTML(false);
                $mail->Subject = "Crimson Vault - Login OTP";
                $mail->Body    = "Dear {$user['full_name']},\n\nYour OTP is: $otp\nValid for 3 minutes.";

                if ($mail->send()) {
                    // ✅ Redirect to OTP verify with mode=login
                    header("Location: verify_otp.php?mode=login&email=" . urlencode($email));
                    exit();
                } else {
                    echo "<script>alert('❌ Failed to send OTP.'); window.history.back();</script>";
                    exit();
                }
            } catch (Exception $e) {
                echo "<script>alert('❌ Mailer Error: {$mail->ErrorInfo}'); window.history.back();</script>";
                exit();
            }

        } else {
            echo "<script>alert('❌ Incorrect password.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('❌ User not found or inactive account.'); window.history.back();</script>";
        exit();
    }
}
?>
