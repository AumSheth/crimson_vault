<?php
session_start();
$conn = new mysqli("localhost", "root", "", "crimson_vault");

$mode  = $_GET['mode']  ?? ($_POST['mode'] ?? '');
$email = $_GET['email'] ?? ($_POST['email'] ?? '');

if (!$mode || !$email) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_input = implode("", $_POST['otp'] ?? []);

    // ✅ Fetch OTP from DB
    $sql = "SELECT * FROM password_resets WHERE email = ? AND otp = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $otp_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (strtotime($row['expires_at']) >= time()) {

            // ✅ Clear OTP after use
            $conn->query("DELETE FROM password_resets WHERE email = '".$conn->real_escape_string($email)."'");

            if ($mode === "login") {
                // --- LOGIN FLOW ---
                $user = $_SESSION['pending_user'];

                $_SESSION['user_id']    = $user['user_id'];
                $_SESSION['full_name']  = $user['full_name'];
                $_SESSION['role_name']  = $user['role_name'];
                $_SESSION['email']      = $user['email'];

                unset($_SESSION['pending_user']);

                // Redirect by role
                if ($user['role_name'] === 'Admin') {
                    header("Location: dashboard.php"); exit();
                } elseif ($user['role_name'] === 'Police') {
                    header("Location: police_dashboard.php"); exit();
                } elseif ($user['role_name'] === 'Legal Personnel') {
                    header("Location: legal_dashboard.php"); exit();
                } else {
                    header("Location: index.php"); exit();
                }

            } elseif ($mode === "forgot") {
                // --- FORGOT PASSWORD FLOW ---
                header("Location: reset_password.php?email=" . urlencode($email));
                exit();
            }

        } else {
            echo "<script>alert('❌ OTP expired. Please try again.'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('❌ Invalid OTP.'); window.location.href='verify_otp.php?mode=$mode&email=" . urlencode($email) . "';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OTP Verification - Crimson Vault</title>
<link rel="stylesheet" href="style.css"> 
<style>
  /* Extra styling for OTP boxes */
  .otp-boxes {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 20px 0;
  }
  .otp-input {
    width: 45px;
    height: 45px;
    text-align: center;
    font-size: 22px;
    border: 1px solid #007acc;
    border-radius: 4px;
    background-color: #f5faff;
  }
  #timer {
    font-weight: bold;
    color: red;
  }
</style>
<script>
function moveToNext(current, nextFieldID) {
    if (current.value.length >= 1) {
        document.getElementById(nextFieldID)?.focus();
    }
}
function startCountdown(duration) {
    let timer = duration, minutes, seconds;
    const display = document.getElementById("timer");
    const interval = setInterval(() => {
        minutes = String(Math.floor(timer / 60)).padStart(2, '0');
        seconds = String(timer % 60).padStart(2, '0');
        display.textContent = minutes + ":" + seconds;
        if (--timer < 0) {
            clearInterval(interval);
            display.textContent = "Expired!";
            document.getElementById("otpForm").style.display = "none";
        }
    }, 1000);
}
window.onload = () => { startCountdown(180); }
</script>
</head>
<body>
  <div class="container">
    <img src="logo.png" alt="Crimson Vault Logo" class="logo">

    <div class="form-container">
      <h2>Enter the 6-digit OTP</h2>
      <p>OTP sent to your email. Expires in <span id="timer">03:00</span></p>

      <form id="otpForm" method="POST" class="form">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">

        <div class="otp-boxes">
          <input class="otp-input" type="text" name="otp[]" maxlength="1" onkeyup="moveToNext(this, 'd2')" id="d1" autofocus>
          <input class="otp-input" type="text" name="otp[]" maxlength="1" onkeyup="moveToNext(this, 'd3')" id="d2">
          <input class="otp-input" type="text" name="otp[]" maxlength="1" onkeyup="moveToNext(this, 'd4')" id="d3">
          <input class="otp-input" type="text" name="otp[]" maxlength="1" onkeyup="moveToNext(this, 'd5')" id="d4">
          <input class="otp-input" type="text" name="otp[]" maxlength="1" onkeyup="moveToNext(this, 'd6')" id="d5">
          <input class="otp-input" type="text" name="otp[]" maxlength="1" id="d6">
        </div>

        <button type="submit">Verify OTP</button>
      </form>
    </div>
  </div>
</body>
</html>
