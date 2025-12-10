<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Crimson Vault - Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <img src="logo.png" alt="Crimson Vault Logo" class="logo">

    <div class="form-container">
      <form id="loginForm" class="form active" method="POST" action="login.php">
        <input type="text" name="email" placeholder="📧 Email" required>
        <div class="password-container">
          <input type="password" name="password" id="loginPassword" placeholder="🔑 Password" required>
          <span class="password-toggle" onclick="togglePassword('loginPassword', this)">👁</span>
        </div>
        <button type="submit">Login</button>
      </form>

      <!-- Forgot password link -->
      <p><a href="generate_otp.php">Forgot Password?</a></p>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
