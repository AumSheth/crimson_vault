<?php
session_start();

// ✅ Ensure only Police can access
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Police') {
    header("Location: index.php");
    exit();
}

// ✅ DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "crimson_vault";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$officer_id = $_SESSION['user_id'];

// ✅ Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_accused'])) {
    $accused_id = intval($_POST['accused_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $father_name = mysqli_real_escape_string($conn, $_POST['father_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    $skin_colour = mysqli_real_escape_string($conn, $_POST['skin_colour']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $current_charges = mysqli_real_escape_string($conn, $_POST['current_charges']);

    // ✅ Ensure the officer owns the case for this accused
    $check = mysqli_query($conn, "
        SELECT a.accused_id 
        FROM accused a
        JOIN cases c ON a.case_id = c.case_id
        WHERE a.accused_id='$accused_id' AND c.officer_id='$officer_id'
    ");
    if (mysqli_num_rows($check) > 0) {
        $update = "
            UPDATE accused SET
                name='$name',
                father_name='$father_name',
                gender='$gender',
                birthdate='$birthdate',
                height='$height',
                weight='$weight',
                skin_colour='$skin_colour',
                blood_group='$blood_group',
                current_charges='$current_charges'
            WHERE accused_id='$accused_id'
        ";
        mysqli_query($conn, $update);
        echo "<script>alert('✅ Accused updated successfully'); window.location.href='view_accused.php?case_id=".$_POST['case_id']."';</script>";
        exit();
    } else {
        echo "<script>alert('🚫 You are not authorized to edit this accused'); window.location.href='view_accused.php';</script>";
        exit();
    }
}

include("header.php");
?>

<body id="feedback-page">
<div class="feedback-section">
    <h2 style="text-align:center;">👤 View & Edit Accused (Your Assigned Cases)</h2>

    <!-- ✅ Case Selection -->
    <form method="GET" style="text-align:center; margin-top:8px;">
        <label for="case_id" style="display:block; font-weight:700; margin-bottom:6px;">Select Case:</label>
        <select name="case_id" class="case-select" required>
            <option value="">-- Select a Case --</option>
            <?php
            $caseQuery = "SELECT case_id, title FROM cases WHERE officer_id='$officer_id' ORDER BY date_filed DESC";
            $caseResult = mysqli_query($conn, $caseQuery);
            while ($case = mysqli_fetch_assoc($caseResult)) {
                $selected = (isset($_GET['case_id']) && $_GET['case_id'] == $case['case_id']) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($case['case_id']) . "' $selected>
                        Case #" . $case['case_id'] . " - " . htmlspecialchars($case['title']) . "
                      </option>";
            }
            ?>
        </select>
        <div style="margin-top:16px;">
            <button type="submit" class="btn-view">View</button>
        </div>
    </form>
</div>

<?php if (isset($_GET['case_id']) && $_GET['case_id'] !== ''): ?>
<div class="feedback-section" style="margin-top:30px;">
    <h2 style="text-align:center;">Accused for Case #<?php echo htmlspecialchars($_GET['case_id']); ?></h2>

    <div class="accused-grid">
    <?php
    $case_id = mysqli_real_escape_string($conn, $_GET['case_id']);

    // ✅ Ensure officer owns this case
    $verifyCase = mysqli_query($conn, "SELECT case_id FROM cases WHERE case_id='$case_id' AND officer_id='$officer_id'");
    if (mysqli_num_rows($verifyCase) > 0) {
        $query = "SELECT * FROM accused WHERE case_id='$case_id'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="accused-card">
                    <form method="POST" style="border:1px solid #007acc; padding:10px; border-radius:6px; margin-bottom:12px;">
                        <input type="hidden" name="accused_id" value="<?= $row['accused_id'] ?>">
                        <input type="hidden" name="case_id" value="<?= $case_id ?>">

                        <p><b>Name:</b> <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required></p>
                        <p><b>Father's Name:</b> <input type="text" name="father_name" value="<?= htmlspecialchars($row['father_name']) ?>" required></p>
                        <p><b>Gender:</b> 
                            <select name="gender">
                                <option value="Male" <?= $row['gender']=='Male'?'selected':'' ?>>Male</option>
                                <option value="Female" <?= $row['gender']=='Female'?'selected':'' ?>>Female</option>
                                <option value="Other" <?= $row['gender']=='Other'?'selected':'' ?>>Other</option>
                            </select>
                        </p>
                        <p><b>Birthdate:</b> <input type="date" name="birthdate" value="<?= $row['birthdate'] ?>"></p>
                        <p><b>Height (cm):</b> <input type="number" step="0.01" name="height" value="<?= $row['height'] ?>"></p>
                        <p><b>Weight (kg):</b> <input type="number" step="0.01" name="weight" value="<?= $row['weight'] ?>"></p>
                        <p><b>Skin Colour:</b> <input type="text" name="skin_colour" value="<?= htmlspecialchars($row['skin_colour']) ?>"></p>
                        <p><b>Blood Group:</b> <input type="text" name="blood_group" value="<?= htmlspecialchars($row['blood_group']) ?>"></p>
                        <p><b>Current Charges:</b><br>
                            <textarea name="current_charges" rows="3"><?= htmlspecialchars($row['current_charges']) ?></textarea>
                        </p>
                        <button type="submit" name="update_accused" style="background-color:#28a745; color:white; padding:6px 12px; border-radius:6px; border:none; cursor:pointer;">Update Accused</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align:center; font-weight:600;'>No accused found for this case.</p>";
        }
    } else {
        echo "<p style='text-align:center; color:red; font-weight:600;'>🚫 You are not authorized to view/edit accused for this case.</p>";
    }
    ?>
    </div>
</div>
<?php endif; ?>
</body><br><br><br><br>

<?php include("footer.php"); ?>
