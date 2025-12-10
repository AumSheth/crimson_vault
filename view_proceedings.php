<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// Ensure Police or Legal Personnel can access
if($_SESSION['role_name'] !== 'Police' && $_SESSION['role_name'] !== 'Legal Personnel'){
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

include("header.php");
?>

<body id="feedback-page">
<div class="feedback-section">
    <h2>View Trial Proceedings</h2>

    <!-- Case Selection -->
    <form method="GET">
        <label for="case_id"><b>Select Case:</b></label>
        <select name="case_id" required style="width:100%; padding:10px; margin-bottom:20px; border:2px solid #007acc; border-radius:8px;">
            <option value="">-- Select a Case --</option>
            <?php
            $caseQuery = "SELECT case_id, title FROM cases ORDER BY date_filed DESC";
            $caseResult = mysqli_query($conn, $caseQuery);
            while($case = mysqli_fetch_assoc($caseResult)){
                $selected = (isset($_GET['case_id']) && $_GET['case_id'] == $case['case_id']) ? "selected" : "";
                echo "<option value='".htmlspecialchars($case['case_id'])."' $selected>Case #".$case['case_id']." - ".htmlspecialchars($case['title'])."</option>";
            }
            ?>
        </select>
        <button type="submit">View</button>
    </form>
</div>

<?php if(isset($_GET['case_id'])): ?>
<div class="feedback-section" style="margin-top:30px;">
    <h2>Proceedings for Case #<?php echo htmlspecialchars($_GET['case_id']); ?></h2>

    <div class="accused-grid">
    <?php
    $case_id = mysqli_real_escape_string($conn, $_GET['case_id']);
    $query = "SELECT tp.proceeding_note, tp.created_at, m.full_name 
              FROM trial_proceedings tp 
              JOIN members m ON tp.user_id = m.user_id 
              WHERE tp.case_id = '$case_id'
              ORDER BY tp.created_at DESC";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            echo "<div class='accused-card'>
                    <p><b>Proceeding Note:</b> ".nl2br(htmlspecialchars($row['proceeding_note']))."</p>
                    <p><b>Added By:</b> ".htmlspecialchars($row['full_name'])."</p>
                    <p><b>Date:</b> ".htmlspecialchars($row['created_at'])."</p>
                  </div>";
        }
    } else {
        echo "<p style='text-align:center; font-weight:600;'>No proceedings found for this case.</p>";
    }
    ?>
    </div>
</div>
<?php endif; ?>
</body><br><br><br><br>

<?php include("footer.php"); ?>
