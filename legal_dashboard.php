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

include("header.php");
?>

<div class="dashboard-content">
    <h2>Legal Personnel Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here you can review case proceedings, judgments, and legal acts.</p>

    <!-- Quick Actions -->
    <section id="legal-quick-actions">
        <h3>Quick Actions</h3>
        <div class="action-tiles">
            <a href="trial_proceedings.php" class="tile">
                <div class="tile-icon">⚖️</div>
                <div class="tile-text">Trial Proceedings</div>
            </a>
            <a href="view_proceedings.php" class="tile">
                <div class="tile-icon">🧑‍⚖️</div>
                <div class="tile-text">View Trials</div>
            </a>
            <a href="judgments.php" class="tile">
                <div class="tile-icon">📜</div>
                <div class="tile-text">Judgments</div>
            </a>
            <a href="view_judgments.php" class="tile">
                <div class="tile-icon">📜</div>
                <div class="tile-text">View Judgments</div>
            </a>
            <a href="add_analytics.php" class="tile">
                <div class="tile-icon">📊</div>
                <div class="tile-text">Add analytics</div>
            </a>
            <a href="notifications.php" class="tile">
                <div class="tile-icon">🔔</div>
                <div class="tile-text">Notifications</div>
            </a>
        </div>
    </section><br><br><br><br><br><br>
</div>

<?php include("footer.php"); ?>
