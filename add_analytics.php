<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// Allow only Police to add analytics
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

$message = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $case_id = mysqli_real_escape_string($conn, $_POST['case_id']);
    $prediction = mysqli_real_escape_string($conn, $_POST['prediction']);

    if(!empty($case_id) && !empty($prediction)){
        $query = "INSERT INTO analytics (case_id, prediction, generated_at) 
                  VALUES ('$case_id', '$prediction', NOW())";
        if(mysqli_query($conn, $query)){
            $message = "<p style='color:green; font-weight:600;'>✅ Analytics added successfully.</p>";
        } else {
            $message = "<p style='color:red; font-weight:600;'>❌ Error: ".mysqli_error($conn)."</p>";
        }
    } else {
        $message = "<p style='color:red; font-weight:600;'>⚠ Please fill all fields.</p>";
    }
}

include("header.php");
?>

<body id="feedback-page">

<div class="feedback-section">
    <h2>Add Analytics</h2>
    <?php if(!empty($message)) echo $message; ?>

    <form method="POST">
        <!-- Case Selection -->
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

        <!-- Prediction Text -->
        <label for="prediction"><b>Prediction / Analysis:</b></label>
        <textarea name="prediction" required placeholder="Enter prediction or analysis details..."></textarea>

        <!-- Submit -->
        <button type="submit">Add Analytics</button>
    </form>
</div>

</body>

<?php include("footer.php"); ?>
