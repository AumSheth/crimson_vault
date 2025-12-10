<?php
session_start();

// Ensure officer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Police') {
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

// Get officer's ID
$officer_id = $_SESSION['user_id'];

// Fetch only cases assigned to this officer
$case_query = "SELECT case_id, title FROM cases WHERE officer_id = ?";
$stmt = $conn->prepare($case_query);
$stmt->bind_param("i", $officer_id);
$stmt->execute();
$case_result = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $case_id = $_POST['case_id'];
  $name = $conn->real_escape_string($_POST['name']);
  $father_name = $conn->real_escape_string($_POST['father_name']);
  $gender = $_POST['gender'];
  $birthdate = $_POST['birthdate'];
  $height = $_POST['height'];
  $weight = $_POST['weight'];
  $skin_colour = $conn->real_escape_string($_POST['skin_colour']);
  $blood_group = $conn->real_escape_string($_POST['blood_group']);
  $current_charges = $conn->real_escape_string($_POST['current_charges']);
  if (!preg_match("/^[a-zA-Z\s]+$/", $name) || !preg_match("/^[a-zA-Z\s]+$/", $father_name)) {
    echo "<script>alert('Name and Father\'s Name must contain only letters and spaces.');</script>";
    exit();
}
  $sql = "INSERT INTO accused 
            (case_id, name, father_name, gender, birthdate, height, weight, skin_colour, blood_group, current_charges) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("issssddsss", $case_id, $name, $father_name, $gender, $birthdate, $height, $weight, $skin_colour, $blood_group, $current_charges);

  if ($stmt->execute()) {
    echo "<script>alert('Accused added successfully!'); window.location.href='accussed.php';</script>";
    exit();
  } else {
    echo "<script>alert('Error: Could not add accused.');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add Accused</title>
  <link rel="stylesheet" href="style.css">
  <script>
    function validateForm() {
      const name = document.getElementById("name").value.trim();
      const fatherName = document.getElementById("father_name").value.trim();
      const alphaRegex = /^[A-Za-z\s]+$/;

      if (!alphaRegex.test(name)) {
        alert("Full Name must contain only letters and spaces.");
        return false;
      }

      if (!alphaRegex.test(fatherName)) {
        alert("Father's Name must contain only letters and spaces.");
        return false;
      }

      return true;
    }
  </script>
</head>

<body>

  <?php include 'header.php'; ?>

  <main>
    <section class="form-section">
      <h2 class="form-title">Add Accused to Case</h2>
      <form method="POST" class="form-box" onsubmit="return validateForm();>

      <label for=" case_id">Related Case:</label>
        <select name="case_id" id="case_id" required>
          <option value="">-- Select Case --</option>
          <?php if ($case_result->num_rows > 0): ?>
            <?php while ($row = $case_result->fetch_assoc()): ?>
              <option value="<?= $row['case_id']; ?>"><?= htmlspecialchars($row['title']); ?></option>
            <?php endwhile; ?>
          <?php else: ?>
            <option value="">No cases assigned</option>
          <?php endif; ?>
        </select>

        <label for="name">Full Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="father_name">Father's Name:</label>
        <input type="text" name="father_name" id="father_name" required>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
          <option value="">-- Select --</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>

        <label for="birthdate">Birthdate:</label>
        <input type="date" name="birthdate" id="birthdate">

        <label for="height">Height (cm):</label>
        <input type="number" step="0.01" name="height" id="height">

        <label for="weight">Weight (kg):</label>
        <input type="number" step="0.01" name="weight" id="weight">

        <label for="skin_colour">Skin Colour:</label>
        <select name="skin_colour" id="skin_colour" required>
          <option value="">-- Select --</option>
          <option value="Fair">Fair</option>
          <option value="Wheatish">Wheatish</option>
          <option value="Dusky">Dusky</option>
          <option value="Dark">Dark</option>
        </select>

        <label for="blood_group">Blood Group:</label>
        <select name="blood_group" id="blood_group">
          <option value="">-- Select --</option>
          <option value="A+">A+</option>
          <option value="A-">A-</option>
          <option value="B+">B+</option>
          <option value="B-">B-</option>
          <option value="AB+">AB+</option>
          <option value="AB-">AB-</option>
          <option value="O+">O+</option>
          <option value="O-">O-</option>
        </select>

        <label for="current_charges">Current Charges:</label>
        <textarea name="current_charges" id="current_charges"></textarea>

        <button type="submit" class="btn-submit">Add Accused</button>
      </form>
    </section><br><br>
  </main>

  <?php include 'footer.php'; ?>

</body>

</html>