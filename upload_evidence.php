<?php
session_start();

// Ensure only Police can upload evidence
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Police') {
    header("Location: index.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * ===== Encryption helpers (AES-256-CBC, Encrypt-then-MAC) =====
 * File format: "EV1" | IV(16 bytes) | HMAC(32 bytes) | CIPHERTEXT
 */
const ENC_MAGIC = "EV1"; 
$APP_SECRET = "k7W9xPq3rFz5h2L8jN4mB6cVd1gY0eA9sIu7oP2tE4wC8qZ6xV9yM3bN5jK0lD4fG7hJ1kL2mQ8wE3rT5yU7iO9pA6sD4fG2hJ8kL";

function derive_key(string $secret): string {
    return hash('sha256', $secret, true);
}
// const ENC_MAGIC = "EV1"; // 3 bytes header

function encrypt_bytes(string $plaintext, string $key): string {
    $iv = random_bytes(16);
    $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) throw new RuntimeException("Encryption failed.");
    $hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
    return ENC_MAGIC . $iv . $hmac . $ciphertext;
}

function encrypt_file_to(string $srcTmpPath, string $targetPath, string $key): void {
    $data = file_get_contents($srcTmpPath);
    if ($data === false) throw new RuntimeException("Failed to read uploaded file.");
    $enc = encrypt_bytes($data, $key);
    $dir = dirname($targetPath);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    if (file_put_contents($targetPath, $enc, LOCK_EX) === false) {
        throw new RuntimeException("Failed to write encrypted file.");
    }
}

/* ========================== Page logic =========================== */

$message = "";
$officer_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (!isset($_POST['case_id']) || !ctype_digit($_POST['case_id'])) {
            throw new RuntimeException("Invalid case selected.");
        }
        if (!isset($_FILES["evidence_file"])) {
            throw new RuntimeException("Please choose at least one file.");
        }

        $case_id = (int)$_POST['case_id'];
        $files   = $_FILES["evidence_file"];
        $allowed = ["jpg","jpeg","png","pdf","doc","docx","xlsx","csv","txt","mp4"];
        $maxBytes = 100 * 1024 * 1024; // 100 MB

        $key = derive_key($APP_SECRET);
        $uploadDir = "uploads/evidence/";
        $uploadedCount = 0;

        // Loop through each uploaded file
        for ($i = 0; $i < count($files["name"]); $i++) {
            if ($files["error"][$i] !== UPLOAD_ERR_OK) continue;

            $origName = basename($files["name"][$i]);
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) continue;
            if ($files["size"][$i] > $maxBytes) continue;

            $uniqueName = time() . "_" . bin2hex(random_bytes(4)) . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', $origName);
            $encPath = $uploadDir . $uniqueName . ".enc";

            // Encrypt and save
            encrypt_file_to($files["tmp_name"][$i], $encPath, $key);

            // Insert record into evidence (using uploaded_by instead of officer_id)
            $stmt = $conn->prepare("INSERT INTO evidence (case_id, uploaded_by, file_name, file_path, file_type, access_level, uploaded_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $access = "Private"; // default, can add select field in form if you want
            $stmt->bind_param("iissss", $case_id, $officer_id, $origName, $encPath, $ext, $access);
            if ($stmt->execute()) {
                $uploadedCount++;
            } else {
                @unlink($encPath);
            }
        }

        if ($uploadedCount > 0) {
        echo "<script>
                if(confirm('✅ Successfully uploaded {$uploadedCount} file(s)! Do you want to add accused now?')) {
                    window.location='accussed.php?case_id=$case_id';
                } else {
                    window.location='police_dashboard.php';
                }
              </script>";
        exit;
        } else {
            $message = "<p class='error-msg'>⚠️ No valid files uploaded.</p>";
        }


    } catch (Throwable $e) {
        $message = "<p class='error-msg'>❌ " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Fetch cases assigned to this officer
$cases = $conn->query("SELECT case_id, title FROM cases WHERE officer_id = $officer_id");

include("header.php");
?>

<div class="content">
    <h2>Upload Evidence (AES-256 Encrypted)</h2>
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data" class="form upload-form">
        <label for="case_id">Select Case</label>
        <select name="case_id" id="case_id" required>
            <option value="">-- Select Case --</option>
            <?php if ($cases && $cases->num_rows) { while ($row = $cases->fetch_assoc()) { ?>
                <option value="<?php echo (int)$row['case_id']; ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </option>
            <?php }} ?>
        </select>

        <label for="evidence_file">Upload Files</label>
        <input type="file" name="evidence_file[]" id="evidence_file" multiple required>

        <button type="submit">📤 Upload Encrypted Evidence</button>
    </form>
</div>

<?php
$conn->close();
include("footer.php");
?>