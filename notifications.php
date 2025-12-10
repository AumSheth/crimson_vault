<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "crimson_vault";
$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle Mark as Read button
if(isset($_POST['mark_read']) && isset($_POST['ntf_id'])){
    $ntf_id = intval($_POST['ntf_id']);
    $updateQuery = "UPDATE notifications SET status='read' WHERE ntf_id='$ntf_id' AND user_id='$user_id'";
    mysqli_query($conn, $updateQuery);
}

include("header.php");
?>

<body id="feedback-page">

<div class="feedback-section">
    <h2 style="text-align:center;">My Notifications</h2>

    <div class="notification-grid">
    <?php
    $query = "SELECT ntf_id, message, created_at, status 
              FROM notifications 
              WHERE user_id = '$user_id' 
              ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);

    if($result && mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $statusText = ($row['status'] === 'unread') ? "🔔 Unread" : "✅ Read";

            echo "<div class='notification-card'>
                    <p><b>Message:</b> ".htmlspecialchars($row['message'])."</p>
                    <p><b>Date:</b> ".htmlspecialchars($row['created_at'])."</p>
                    <p><b>Status:</b> $statusText</p>";

            // Show Mark as Read button only if status is unread
            if($row['status'] === 'unread'){
                echo "<form method='POST' style='margin-top:10px;'>
                        <input type='hidden' name='ntf_id' value='".$row['ntf_id']."'>
                        <button type='submit' name='mark_read' class='btn-view'>Mark as Read</button>
                      </form>";
            }

            echo "</div>";
        }
    } else {
        echo "<p style='text-align:center; font-weight:600;'>No notifications found.</p>";
    }
    ?>
    </div>
</div>

</body>

<?php include("footer.php"); ?>
