<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ensure only Police role can access
if ($_SESSION['role_name'] !== 'Police') {
    header("Location: dashboard.php");
    exit();
}

include("header.php");

// DB connection
$host = "localhost";
$user = "root";
$pass = "";  
$db   = "crimson_vault";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ✅ Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $case_type   = mysqli_real_escape_string($conn, $_POST['case_type']);
    $date_filed  = mysqli_real_escape_string($conn, $_POST['date_filed']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $officer_id  = $_SESSION['user_id'];

    // ✅ Server-side validation: title and description must not be only digits
    if (preg_match('/^\d+$/', $title)) {
        $message = "<p class='error-msg'>❌ Case title cannot be only digits.</p>";
    } elseif (preg_match('/^\d+$/', $description)) {
        $message = "<p class='error-msg'>❌ Description cannot be only digits.</p>";
    } else {
        $sql = "INSERT INTO cases (officer_id, title, case_type, date_filed, status, description, created_at) 
                VALUES ('$officer_id', '$title', '$case_type', '$date_filed', '$status', '$description', NOW())";

        if (mysqli_query($conn, $sql)) {
            $last_case_id = mysqli_insert_id($conn);

            // Officer details
            $officerEmail = $_SESSION['email']; 
            $officerName  = $_SESSION['full_name'];

            // Email content
            $subject = "New Case Added - Crimson Vault";
            $body = "Hello $officerName,<br><br>"
                  . "Your new case has been successfully recorded in <b>Crimson Vault</b>.<br><br>"
                  . "<b>Case Title:</b> $title<br>"
                  . "<b>Type:</b> $case_type<br>"
                  . "<b>Status:</b> $status<br>"
                  . "<b>Date Filed:</b> $date_filed<br><br>"
                  . "Thank you for keeping the records updated.<br><br>"
                  . "Regards,<br>Crimson Vault System";

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'crimsonvaultofficial.web.co@gmail.com'; 
                $mail->Password   = 'adtqfecrinzhsagg';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault');
                $mail->addAddress($officerEmail, $officerName);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();

            } catch (Exception $e) {
                error_log("PHPMailer Error: " . $mail->ErrorInfo);
            }

            echo "<script>
                    if(confirm('✅ Case added successfully! Do you want to upload evidence now?')) {
                        window.location='upload_evidence.php?case_id=$last_case_id';
                    } else {
                        window.location='police_dashboard.php';
                    }
                  </script>";
            exit;

        } else {
            $message = "<p class='error-msg'>❌ Error: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<div class="content">
    <h2>Add New Case</h2>
    <?php echo $message; ?>

    <form method="POST" class="form add-case-form" onsubmit="return validateForm();">
        <label for="title">Case Title</label>
        <input type="text" name="title" id="title" required>

        <label for="case_type">Case Type</label>
        <select name="case_type" id="case_type" required>
          <option value="Civil">Civil</option>
          <option value="Criminal">Criminal</option>
          <option value="Family">Family</option>
          <option value="Corporate">Corporate</option>
          <option value="Cyber">Cyber</option>
        </select>

        <label for="date_filed">Date Filed</label>
        <input type="date" name="date_filed" id="date_filed" required>

        <label for="status">Status</label>
        <select name="status" id="status" required>
            <option value="Open">Open</option>
            <option value="Under Trial">Under Trial</option>
            <option value="Closed">Closed</option>
        </select>

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <button type="submit">➕ Add Case</button>
    </form>
</div>

<script>
function validateForm() {
    const title = document.getElementById("title").value.trim();
    const description = document.getElementById("description").value.trim();
    const onlyDigits = /^\d+$/;

    if (onlyDigits.test(title)) {
        alert("❌ Case title cannot be only digits.");
        return false;
    }

    if (onlyDigits.test(description)) {
        alert("❌ Description cannot be only digits.");
        return false;
    }

    return true;
}
</script>

<?php 
mysqli_close($conn);
include("footer.php"); 
?>
