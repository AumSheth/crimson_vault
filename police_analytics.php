<?php
session_start();

// Ensure only Police can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Police') {
    header("Location: index.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$officer_id = $_SESSION['user_id'];

// Fetch officer’s cases
$cases = $conn->query("SELECT case_id, title, status FROM cases WHERE officer_id = $officer_id");

// Fetch predictions from analytics table
$predictions = [];
if ($cases->num_rows > 0) {
    $caseIds = [];
    while ($c = $cases->fetch_assoc()) {
        $caseIds[$c['case_id']] = $c; // store case info
    }

    $ids = implode(",", array_keys($caseIds));
    if ($ids) {
        $sqlPred = "SELECT a.case_id, a.prediction
                    FROM analytics a
                    WHERE a.case_id IN ($ids)";
        $resPred = $conn->query($sqlPred);
        while ($row = $resPred->fetch_assoc()) {
            $predictions[$row['case_id']][] = $row;
        }
    }
}

include("header.php");
?>

<div class="content">
    <h2>📊 Case Analytics & Legal Predictions</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here are your cases and the predictions provided by Legal Personnel:</p>

    <?php if (!empty($caseIds)) { ?>
        <?php foreach ($caseIds as $cid => $case) { ?>
            <div class="case-box">
                <h3>Case: <?php echo htmlspecialchars($case['title']); ?> 
                    <small>(<?php echo htmlspecialchars($case['status']); ?>)</small>
                </h3>
                
                <?php if (!empty($predictions[$cid])) { ?>
                    <ul class="predictions">
                        <?php foreach ($predictions[$cid] as $pred) { ?>
                            <li>
                                <?php echo htmlspecialchars($pred['prediction']); ?>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="no-data">⚠️ No predictions submitted yet.</p>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No cases assigned to you yet.</p>
    <?php } ?>
</div>

<?php
$conn->close();
include("footer.php");
?>
