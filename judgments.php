<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// Ensure only Legal Personnel can access
if($_SESSION['role_name'] !== 'Legal Personnel'){
    header("Location: dashboard.php");
    exit();
}

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "crimson_vault";
$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $case_id = mysqli_real_escape_string($conn, $_POST['case_id']);
    $judgment_text = mysqli_real_escape_string($conn, $_POST['judgment_text']);
    $user_id = $_SESSION['user_id'];

    // Insert into judgments table
    $insert = "INSERT INTO judgments (case_id, user_id, judgment_text) 
               VALUES ('$case_id', '$user_id', '$judgment_text')";
    if(mysqli_query($conn, $insert)){
        $success = "Final judgment added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

include("header.php");
?>

<body id="feedback-page">

<!-- Add Final Judgment -->
<div class="feedback-section">
    <h2>Add Final Judgment</h2>

    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="POST">
        <label for="case_id"><b>Select Case:</b></label>
        <select name="case_id" required style="width:100%; padding:10px; margin-bottom:20px; border:2px solid #007acc; border-radius:8px;">
            <option value="">-- Select a Case --</option>
            <?php
            $caseQuery = "SELECT case_id, title FROM cases ORDER BY date_filed DESC";
            $caseResult = mysqli_query($conn, $caseQuery);
            while($case = mysqli_fetch_assoc($caseResult)){
                echo "<option value='".htmlspecialchars($case['case_id'])."'>Case #".$case['case_id']." - ".htmlspecialchars($case['title'])."</option>";
            }
            ?>
        </select>

        <textarea name="judgment_text" placeholder="Enter final judgment details..." required></textarea>
        <button type="submit">Save Judgment</button>
    </form>
</div>
</body>

<?php include("footer.php"); ?>
