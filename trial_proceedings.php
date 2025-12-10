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
    $proceeding_note = mysqli_real_escape_string($conn, $_POST['proceeding_note']);
    $user_id = $_SESSION['user_id'];

    $insert = "INSERT INTO trial_proceedings (case_id, user_id, proceeding_note) 
               VALUES ('$case_id', '$user_id', '$proceeding_note')";
    if(mysqli_query($conn, $insert)){
        $success = "Trial proceeding added successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

include("header.php");
?>

<body id="feedback-page">
<div class="feedback-section">
    <h2>Add Trial Proceedings</h2>

    <?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <!-- Form -->
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

        <textarea name="proceeding_note" placeholder="Enter trial proceeding details..." required></textarea>
        <button type="submit">Save Proceeding</button>
    </form>
</div>
</body>

<?php include("footer.php"); ?>
