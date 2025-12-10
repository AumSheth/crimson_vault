<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ensure only Police officers can create accounts
if ($_SESSION['role_name'] !== 'Police') {
    header("Location: dashboard.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "crimson_vault");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];
    $role_name = trim($_POST['role_name']);

    // ✅ Server-side validations
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($role_name)) {
        $message = "❌ All fields are required.";
    } elseif (!preg_match("/^[A-Za-z\s]+$/", $full_name)) {
        $message = "❌ Full name must contain only letters and spaces.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "❌ Phone number must be exactly 10 digits.";
    } elseif (strlen($password) < 8) {
        $message = "❌ Password must be at least 8 characters long.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user safely
        $stmt = $conn->prepare("INSERT INTO members 
            (full_name, email, phone, password_hash, mfa_enabled, last_login, status, role_name)
            VALUES (?, ?, ?, ?, 1, NULL, 'Active', ?)");
        $stmt->bind_param("sssss", $full_name, $email, $phone, $password_hash, $role_name);

        if ($stmt->execute()) {
            $message = "✅ New user created successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<?php include("header.php"); ?>

<section id="add-user-section" class="form-section" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
  <div id="add-user-container" class="form-container">
    <div id="add-user-box" class="form-box">
      <h2 id="add-user-title">Add new User</h2>

      <?php if (!empty($message)): ?>
        <p id="add-user-message" class="alert"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <form id="add-user-form" method="POST" onsubmit="return validateUserForm();">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" id="full_name" class="form-input"
               required pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" class="form-input" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" class="form-input"
               required pattern="[0-9]{10}" title="Enter a 10-digit phone number">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" class="form-input" 
               required minlength="8" title="Password must be at least 8 characters">

        <label for="role_name">Role:</label>
        <select name="role_name" id="role_name" class="form-select" required>
          <option value="" disabled selected>Select Role</option>
          <option value="Police">Police Officer</option>
          <option value="Legal">Legal Personnel</option>
        </select><br><br>
        <button type="submit" id="create-user-btn" class="form-btn">Create User</button>
      </form>
    </div>
  </div>
</section>
<br><br>

<script>
function validateUserForm() {
  const name = document.getElementById("full_name").value.trim();
  const phone = document.getElementById("phone").value.trim();
  const password = document.getElementById("password").value.trim();

  if (!/^[A-Za-z\s]+$/.test(name)) {
    alert("❌ Full name can only contain letters and spaces");
    return false;
  }
  if (!/^[0-9]{10}$/.test(phone)) {
    alert("❌ Phone must be exactly 10 digits");
    return false;
  }
  if (password.length < 8) {
    alert("❌ Password must be at least 8 characters long");
    return false;
  }
  return true;
}
</script>

<?php include("footer.php"); ?>
