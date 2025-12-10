<?php
// progress.php
session_start();

// ✅ Check if officer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$officer_id = $_SESSION['user_id']; // Officer ID from session

// ✅ Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crimson_vault"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// ✅ Fetch last 5 recent cases of this officer
$sql = "SELECT title, status FROM cases 
        WHERE officer_id = ? 
        ORDER BY created_at DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $officer_id);
$stmt->execute();
$result = $stmt->get_result();

$caseTitles = [];
$caseProgress = [];

// ✅ Map each case status to progress %
while ($row = $result->fetch_assoc()) {
    $caseTitles[] = $row['title'];
    $status = strtolower(trim($row['status']));

    switch ($status) {
        case 'open':
            $caseProgress[] = 33.33;
            break;
        case 'under trial':
            $caseProgress[] = 66.66;
            break;
        case 'closed':
            $caseProgress[] = 100;
            break;
        default:
            $caseProgress[] = 0; // If any undefined status
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Case Progress</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<?php include("header.php"); ?>

<body id="feedback-page">
    <div class="feedback-section">
        <h2>📊 Case Progress Report</h2>
        <canvas id="caseProgressChart" style="max-height:400px;"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('caseProgressChart').getContext('2d');
        const caseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($caseTitles); ?>,
                datasets: [{
                    label: 'Progress (%)',
                    data: <?php echo json_encode($caseProgress); ?>,
                    backgroundColor: '#007acc',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => context.parsed.y + '%'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Progress (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Case Title'
                        }
                    }
                }
            }
        });
    </script><br><br>
</body>

<?php include("footer.php"); ?>
</html>
