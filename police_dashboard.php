<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

// Ensure only Police role can access
if($_SESSION['role_name'] !== 'Police'){
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

$officer_id = $_SESSION['user_id'];

// ================================
// Fetch counts
// ================================
$case_count = 0;
$judgment_count = 0;
$accused_count = 0;
$notif_count = 0;

// Get total cases
$case_count_query = "SELECT COUNT(*) AS total_cases FROM cases";
$case_count_result = mysqli_query($conn, $case_count_query);
if($case_count_result && mysqli_num_rows($case_count_result) > 0){
    $row = mysqli_fetch_assoc($case_count_result);
    $case_count = $row['total_cases'];
}

// Get total judgments
$judgment_count_query = "SELECT COUNT(*) AS total_judgments FROM judgments";
$judgment_count_result = mysqli_query($conn, $judgment_count_query);
if($judgment_count_result && mysqli_num_rows($judgment_count_result) > 0){
    $row = mysqli_fetch_assoc($judgment_count_result);
    $judgment_count = $row['total_judgments'];
}

// Get total accused
$accused_count_query = "SELECT COUNT(*) AS total_accused FROM accused";
$accused_count_result = mysqli_query($conn, $accused_count_query);
if($accused_count_result && mysqli_num_rows($accused_count_result) > 0){
    $row = mysqli_fetch_assoc($accused_count_result);
    $accused_count = $row['total_accused'];
}

// Get unread notifications for logged-in user
$user_id = $_SESSION['user_id'];
$notif_count_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = '$user_id' AND status = 'unread'";
$notif_count_result = mysqli_query($conn, $notif_count_query);
if($notif_count_result && mysqli_num_rows($notif_count_result) > 0){
    $row = mysqli_fetch_assoc($notif_count_result);
    $notif_count = $row['unread_count'];
}

$assignedCases = 0;
$stmtCount = $conn->prepare("SELECT COUNT(*) AS total FROM cases WHERE officer_id = ?");
$stmtCount->bind_param("i", $officer_id);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
if ($resultCount) {
    $assignedCases = $resultCount->fetch_assoc()['total'];
}
$stmtCount->close();

include("header.php");
?>

<div class="dashboard-content">
    <h2>Police Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here you can manage and track your assigned cases.</p>

    <!-- Quick Actions -->
    <section id="police-quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-tiles">
            <a href="add_case.php" class="tile">
                <div class="tile-icon">➕</div>
                <div class="tile-text">Add New Case</div>
            </a>
            <a href="upload_evidence.php" class="tile">
                <div class="tile-icon">📂</div>
                <div class="tile-text">Upload Evidence</div>
            </a>
            <a href="view_evidence.php" class="tile">
                <div class="tile-icon">📑</div>
                <div class="tile-text">View Evidence</div>
            </a>
            <a href="cases.php" class="tile">
                <div class="tile-icon">📋</div>
                <div class="tile-text">View Cases</div>
                <span class="case-badge"><?php echo $case_count; ?></span>
            </a>
            <a href="progress.php" class="tile">
                <div class="tile-icon">📈</div>
                <div class="tile-text">Case Progress</div>
                <span class="progress-badge"><?php echo $assignedCases; ?></span>
            </a>
            <a href="police_analytics.php" class="tile">
                <div class="tile-icon">📊</div>
                <div class="tile-text">Analytics</div>
            </a>
            <a href="accussed.php" class="tile">
                <div class="tile-icon">👤</div>
                <div class="tile-text">Add Accused</div>
            </a>
            <a href="view_accussed.php" class="tile">
                <div class="tile-icon">🕵️‍♀️</div>
                <div class="tile-text">View Accused</div>
            </a>
            <a href="view_proceedings.php" class="tile">
                <div class="tile-icon">🧑‍⚖️</div>
                <div class="tile-text">View Trial Proceedings</div>
            </a>
            <a href="view_judgments.php" class="tile">
                <div class="tile-icon">📜</div>
                <div class="tile-text">View Judgments</div>
                <span class="judgment-badge"><?php echo $judgment_count; ?></span>
            </a>
            <a href="search_accussed.php" class="tile">
                <div class="tile-icon">🔍</div>
                <div class="tile-text">Search Civil Record</div>
            </a>
            <a href="new_user.php" class="tile">
                <div class="tile-icon">👤</div>
                <div class="tile-text">Add New User</div>
            </a>
            <a href="notifications.php" class="tile">
                <div class="tile-icon">🔔</div>
                <div class="tile-text">Notifications</div>
                <span class="notif-badge"><?php echo $notif_count; ?></span>
            </a>
        </div>
    </section>

    <!-- Recent Cases -->
    <section id="police-case-overview">
        <h3>Recent Cases</h3>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Case ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date Filed</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $officer_id = $_SESSION['user_id'];
                $query = "SELECT case_id, title, case_type, status, date_filed 
                          FROM cases 
                          WHERE officer_id = '$officer_id' 
                          ORDER BY date_filed DESC 
                          LIMIT 5";
                $result = mysqli_query($conn, $query);

                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<tr>
                                <td>".htmlspecialchars($row['case_id'])."</td>
                                <td>".htmlspecialchars($row['title'])."</td>
                                <td>".htmlspecialchars($row['case_type'])."</td>
                                <td>".htmlspecialchars($row['status'])."</td>
                                <td>".htmlspecialchars($row['date_filed'])."</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No recent cases found.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </section><br><br><br><br>
</div>

<?php include("footer.php"); ?>
