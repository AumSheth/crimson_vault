<?php
session_start();
$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$email = strtolower(trim($_GET['email'] ?? ''));
$bgColor = "#cce6ff"; // Default blue

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST['email']));
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE members SET password_hash=? WHERE email=?");
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            $del = $conn->prepare("DELETE FROM password_resets WHERE email=?"); 
            $del->bind_param("s", $email);
            $del->execute();
            $del->close();
            
            $bgColor = "#d4f8d4"; // Green
            echo "<script>
                    alert('✅ Password reset successful! Redirecting to login...');
                    window.location.href = 'index.php';
                  </script>";
            exit();
        } else {
            $bgColor = "#ffd6d6"; // Red
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $bgColor = "#ffd6d6"; // Red
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reset Password - Crimson Vault</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background-color: <?= $bgColor ?>;
    }
    .error-message {
      color: red;
      font-weight: bold;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="logo.png" alt="Crimson Vault Logo" class="logo">
    <div class="form-container">
      <h2>Reset Password</h2>
      <form method="POST" class="form">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
       <div class="password-container">
        <input type="password" id="new_password" name="new_password" placeholder="Enter New Password" required>
        <span class="password-toggle" onclick="togglePassword('new_password', this)">👁</span>
      </div>
      <div class="password-container">
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required>
        <span class="password-toggle" onclick="togglePassword('confirm_password', this)">👁</span>
      </div>
        <button type="submit">Reset Password</button>
      </form>
      <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </div>
  </div>
<script src="script.js"></script>
</body>
</html>
