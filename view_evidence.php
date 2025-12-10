<?php
session_start();

// ✅ Allow only Police
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Police') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ DB connection
$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===== Encryption Helpers ===== */
const ENC_MAGIC = "EV1";
$APP_SECRET = "k7W9xPq3rFz5h2L8jN4mB6cVd1gY0eA9sIu7oP2tE4wC8qZ6xV9yM3bN5jK0lD4fG7hJ1kL2mQ8wE3rT5yU7iO9pA6sD4fG2hJ8kL";

function derive_key(string $secret): string
{
    return hash('sha256', $secret, true);
}

function decrypt_bytes(string $data, string $key): string
{
    if (substr($data, 0, 3) !== ENC_MAGIC) {
        throw new RuntimeException("Invalid file format.");
    }
    $iv = substr($data, 3, 16);
    $hmac = substr($data, 19, 32);
    $ciphertext = substr($data, 51);

    $calcHmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
    if (!hash_equals($hmac, $calcHmac)) {
        throw new RuntimeException("Integrity check failed.");
    }

    $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($plaintext === false) {
        throw new RuntimeException("Decryption failed.");
    }

    return $plaintext;
}

/* ===== Handle View/Download ===== */
if (isset($_GET['evidence_id']) && ctype_digit($_GET['evidence_id'])) {
    $evidence_id = (int) $_GET['evidence_id'];

    $stmt = $conn->prepare("
        SELECT e.file_name, e.file_path, e.file_type, e.uploaded_by, c.officer_id
        FROM evidence e
        JOIN cases c ON e.case_id = c.case_id
        WHERE e.evidence_id = ?
    ");
    $stmt->bind_param("i", $evidence_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['officer_id'] != $user_id) {
            die("Access denied.");
        }

        $data = file_get_contents($row['file_path']);
        if ($data === false)
            die("File not found on server.");

        $key = derive_key($APP_SECRET);
        $plain = decrypt_bytes($data, $key);

        $ext = strtolower($row['file_type']);
        $mimeTypes = [
            "jpg" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "png" => "image/png",
            "pdf" => "application/pdf",
            "doc" => "application/msword",
            "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "csv" => "text/csv",
            "txt" => "text/plain",
            "mp4" => "video/mp4"
        ];
        $contentType = $mimeTypes[$ext] ?? "application/octet-stream";

        // ✅ Determine action
        if (isset($_GET['download'])) {
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . basename($row['file_name']) . "\"");
        } else {
            header("Content-Type: $contentType");
            header("Content-Disposition: inline; filename=\"" . basename($row['file_name']) . "\"");
        }

        echo $plain;
        exit();
    } else {
        die("Evidence not found.");
    }
}

include("header.php");
?>

<body id="feedback-page">
    <div class="feedback-section">
        <h2>📁 View Evidence</h2>

        <!-- ✅ Case Selection -->
        <form method="GET" style="text-align:center; margin-top:10px;">
            <label for="case_id" style="font-weight:600;">Select Assigned Case:</label><br>
            <select name="case_id" id="case_id" required>
                <option value="">-- Select a Case --</option>
                <?php
                $caseQuery = "SELECT case_id, title FROM cases WHERE officer_id = ? ORDER BY date_filed DESC";
                $stmt = $conn->prepare($caseQuery);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $caseResult = $stmt->get_result();

                while ($case = $caseResult->fetch_assoc()) {
                    $selected = (isset($_GET['case_id']) && $_GET['case_id'] == $case['case_id']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($case['case_id']) . "' $selected>
                        Case #" . $case['case_id'] . " - " . htmlspecialchars($case['title']) . "
                      </option>";
                }
                ?>
            </select><br>
            <button type="submit" class="btn-view" style="margin-left:10px;">View</button>
        </form>
    </div>

    <?php
    // ✅ Show Evidence for selected case
    if (isset($_GET['case_id']) && ctype_digit($_GET['case_id'])) {
        $case_id = (int) $_GET['case_id'];

        $stmt = $conn->prepare("
        SELECT evidence_id, file_name, file_type, uploaded_at 
        FROM evidence e 
        JOIN cases c ON e.case_id = c.case_id 
        WHERE e.case_id = ? AND c.officer_id = ?
        ORDER BY uploaded_at DESC
    ");
        $stmt->bind_param("ii", $case_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <div class="feedback-section" style="margin-top:25px;">
            <h3>Evidence for Case #<?= htmlspecialchars($case_id) ?></h3>
            <?php if ($result->num_rows > 0): ?>
                <ul style="list-style:none; padding:0;">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li style="margin-bottom:15px; padding:12px; border:1px solid #007acc; border-radius:8px;">
                            <b>File Name:</b> <?= htmlspecialchars($row['file_name']) ?><br>
                            <b>Type:</b> <?= strtoupper(htmlspecialchars($row['file_type'])) ?><br>
                            <b>Uploaded At:</b> <?= htmlspecialchars($row['uploaded_at']) ?><br><br>
                            <a href="view_evidence.php?evidence_id=<?= $row['evidence_id'] ?>" target="_blank"
                                style="background-color:#007bff; color:white; padding:6px 14px; border-radius:6px; text-decoration:none; font-weight:600; margin-right:8px;">
                                👁️ View
                            </a>
                            <a href="view_evidence.php?evidence_id=<?= $row['evidence_id'] ?>&download=1"
                                style="background-color:#28a745; color:white; padding:6px 14px; border-radius:6px; text-decoration:none; font-weight:600;">
                                ⬇️ Download
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p style="text-align:center; font-weight:600;">No evidence found for this case.</p>
            <?php endif; ?>
        </div>

    <?php } ?>
</body><br><br><br><br>

<?php include("footer.php"); ?>