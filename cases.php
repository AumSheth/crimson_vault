<?php
session_start();

// Ensure only Police can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Police') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$officer_id = $_SESSION['user_id'];

// Handle filters (checkbox toggle)
$assignedOnly = isset($_GET['assigned']) && $_GET['assigned'] == "1";

// Handle case update (only his cases)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_case'])) {
        $case_id = intval($_POST['case_id']);
        $title = $conn->real_escape_string($_POST['title']);
        $case_type = $conn->real_escape_string($_POST['case_type']);
        $status = $conn->real_escape_string($_POST['status']);
        $description = $conn->real_escape_string($_POST['description']);

        $updateSql = "UPDATE cases SET title=?, case_type=?, status=?, description=? WHERE case_id=? AND officer_id=?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("ssssii", $title, $case_type, $status, $description, $case_id, $officer_id);
        $stmtUpdate->execute();

        echo "<script>alert('✅ Case updated successfully!'); window.location.href='cases.php';</script>";
        exit();
    }

    if (isset($_POST['update_accused'])) {
        $accused_id = intval($_POST['accused_id']);
        $name = $conn->real_escape_string($_POST['name']);
        $father_name = $conn->real_escape_string($_POST['father_name']);
        $gender = $conn->real_escape_string($_POST['gender']);
        $birthdate = $conn->real_escape_string($_POST['birthdate']);
        $height = floatval($_POST['height']);
        $weight = floatval($_POST['weight']);
        $skin_colour = $conn->real_escape_string($_POST['skin_colour']);
        $blood_group = $conn->real_escape_string($_POST['blood_group']);
        $current_charges = $conn->real_escape_string($_POST['current_charges']);

        // Only update if officer owns the case
        $updateAccusedSql = "UPDATE accused a
                             JOIN cases c ON a.case_id = c.case_id
                             SET a.name=?, a.father_name=?, a.gender=?, a.birthdate=?, a.height=?, a.weight=?, 
                                 a.skin_colour=?, a.blood_group=?, a.current_charges=?
                             WHERE a.accused_id=? AND c.officer_id=?";
        $stmtAcc = $conn->prepare($updateAccusedSql);
        $stmtAcc->bind_param("ssssddssiii", $name, $father_name, $gender, $birthdate, $height, $weight, $skin_colour, $blood_group, $current_charges, $accused_id, $officer_id);
        $stmtAcc->execute();

        echo "<script>alert('✅ Accused updated successfully!'); window.location.href='cases.php';</script>";
        exit();
    }
}

// Build query
if ($assignedOnly) {
    $sql = "SELECT c.case_id, c.title, c.case_type, c.status, c.date_filed, c.description, c.created_at,
                   m.full_name AS officer_name, c.officer_id
            FROM cases c
            JOIN members m ON c.officer_id = m.user_id
            WHERE c.officer_id = ?
            ORDER BY c.date_filed DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $officer_id);
} else {
    $sql = "SELECT c.case_id, c.title, c.case_type, c.status, c.date_filed, c.description, c.created_at,
                   m.full_name AS officer_name, c.officer_id
            FROM cases c
            JOIN members m ON c.officer_id = m.user_id
            ORDER BY c.date_filed DESC";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$cases = $stmt->get_result();

// Preload evidence & accused
$caseIds = [];
while ($c = $cases->fetch_assoc()) {
    $caseIds[$c['case_id']] = $c;
}
$ids = implode(",", array_keys($caseIds));
$accusedData = [];
if ($ids) {
    $acc = $conn->query("SELECT accused_id, case_id, name, father_name, gender, birthdate, height, weight, skin_colour, blood_group, current_charges 
                         FROM accused WHERE case_id IN ($ids)");
    while ($row = $acc->fetch_assoc()) {
        $accusedData[$row['case_id']][] = $row;
    }
}

$accusedData = [];
if ($ids) {
    // Only load accused for cases assigned to this officer
    $assignedCaseIds = [];
    foreach ($caseIds as $c) {
        if ($c['officer_id'] == $officer_id) {
            $assignedCaseIds[] = $c['case_id'];
        }
    }

    if (!empty($assignedCaseIds)) {
        $assignedIds = implode(",", array_map('intval', $assignedCaseIds));
        $acc = $conn->query("SELECT accused_id, case_id, name, father_name, gender, birthdate, height, weight, skin_colour, blood_group, current_charges 
                             FROM accused WHERE case_id IN ($assignedIds)");
        while ($row = $acc->fetch_assoc()) {
            $accusedData[$row['case_id']][] = $row;
        }
    }
}


$stmt->close();
$conn->close();

include("header.php");
?>

<div class="content">
    <h2>📂 Cases</h2>
    <form method="GET" class="filter-checkbox">
    <input type="checkbox" name="assigned" value="1" <?php if ($assignedOnly) echo "checked"; ?> onchange="this.form.submit()">
    <label>Show only my assigned cases</label>
    </form>

    <?php if (!empty($caseIds)) { ?>
        <div class="cases-section">
    <table class="cases-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date Filed</th>
                <th>Officer</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($caseIds as $case) { ?>
            <tr>
                <td><?php echo $case['case_id']; ?></td>
                <td><?php echo htmlspecialchars($case['title']); ?></td>
                <td><?php echo htmlspecialchars($case['case_type']); ?></td>
                <td><?php echo htmlspecialchars($case['status']); ?></td>
                <td><?php echo date("d-m-Y", strtotime($case['date_filed'])); ?></td>
                <td><?php echo htmlspecialchars($case['officer_name']); ?></td>
                <td class="description">
                    <?php echo nl2br(htmlspecialchars($case['description'])); ?>
                </td>
                <td>
                    <?php if ($case['officer_id'] == $officer_id) { ?>
                        <a href="upload_evidence.php?case_id=<?php echo $case['case_id']; ?>" class="btn">📤 Upload Evidence</a>
                        <button class="btn edit-btn" data-caseid="<?php echo $case['case_id']; ?>">✏️ Edit</button>
                        <div class="edit-form" id="edit-form-<?php echo $case['case_id']; ?>" style="display:none;">
                            <!-- Case Edit -->
                            <form method="POST">
                                <input type="hidden" name="case_id" value="<?php echo $case['case_id']; ?>">
                                <h4>Edit Case</h4>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($case['title']); ?>" required>
                                <input type="text" name="case_type" value="<?php echo htmlspecialchars($case['case_type']); ?>" required>
                                <select name="status">
                                    <option value="Open" <?php if ($case['status']=="Open") echo "selected"; ?>>Open</option>
                                    <option value="In Progress" <?php if ($case['status']=="Under Trial") echo "selected"; ?>>In Progress</option>
                                    <option value="Closed" <?php if ($case['status']=="Closed") echo "selected"; ?>>Closed</option>
                                </select>
                                <textarea name="description" rows="3"><?php echo htmlspecialchars($case['description']); ?></textarea>
                                <button type="submit" name="update_case">Update Case</button>
                            </form>
                        </div>
                    <?php } else { echo "🔒 Read-only"; } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

    <?php } else { ?>
        <p class="no-data">⚠️ No cases found.</p>
    <?php } ?>
</div>

<script>
document.querySelectorAll(".edit-btn").forEach(btn=>{
    btn.addEventListener("click", e=>{
        e.preventDefault();
        let caseId = btn.getAttribute("data-caseid");
        let form = document.getElementById("edit-form-"+caseId);
        form.style.display = form.style.display === "none" ? "block" : "none";
    });
});
</script>
<br><br><br><br>
<?php include("footer.php"); ?>
