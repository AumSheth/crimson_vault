<?php
session_start();

// 🔐 Authentication: Only Admin access
if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

// ✅ Database connection
$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// ================================ Handle Form Actions ================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* ---- ADD USER ---- */
    if ($action === 'add_user') {
        $name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password_hash'];
        $role = $_POST['role_name'];

        if (!preg_match("/^[A-Za-z\s]+$/", $name)) {
            die("<script>alert('❌ Full name must contain only letters and spaces'); window.history.back();</script>");
        }
        if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($role)) {
            die("<script>alert('❌ All fields are required'); window.history.back();</script>");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("<script>alert('❌ Invalid email format'); window.history.back();</script>");
        }
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            die("<script>alert('❌ Phone must be 10 digits'); window.history.back();</script>");
        }
        if (strlen($password) < 8) {
            die("<script>alert('❌ Password must be at least 8 characters'); window.history.back();</script>");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $mfa = 1;
        $status = 'active';
        $last_login = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("
            INSERT INTO members (full_name, email, phone, password_hash, mfa_enabled, last_login, status, role_name) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssisss", $name, $email, $phone, $hashedPassword, $mfa, $last_login, $status, $role);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php?msg=user_added");
        exit();
    }

    /* ---- UPDATE USER ---- */
    if ($action === 'update_user') {
        $id = intval($_POST['user_id']);
        $name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $role = $_POST['role_name'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("<script>alert('❌ Invalid email format'); window.history.back();</script>");
        }
        if (!preg_match("/^[0-9]{10}$/", $phone)) {
            die("<script>alert('❌ Phone must be 10 digits'); window.history.back();</script>");
        }

        $stmt = $conn->prepare("
            UPDATE members SET full_name=?, phone=?, email=?, role_name=? WHERE user_id=?
        ");
        $stmt->bind_param("ssssi", $name, $phone, $email, $role, $id);
        $stmt->execute();
        $stmt->close();
    }

    /* ---- DELETE USER ---- */
    if ($action === 'delete_user') {
        $id = intval($_POST['user_id']);
        $conn->query("DELETE FROM members WHERE user_id=$id");
    }

    /* ---- ADD CASE ---- */
    if ($action === 'add_case') {
        $title = trim($_POST['title']);
        $case_type = $_POST['case_type'];
        $date_filed = $_POST['date_filed'];
        $status = $_POST['status'];
        $description = trim($_POST['description']);
        $officer_id = intval($_POST['officer_id']);
        $created_at = date('Y-m-d H:i:s');

        if (empty($title) || empty($case_type) || empty($date_filed) || empty($status) || empty($description)) {
            die("<script>alert('❌ All fields are required'); window.history.back();</script>");
        }
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_filed)) {
            die("<script>alert('❌ Invalid date format (YYYY-MM-DD)'); window.history.back();</script>");
        }
        if ($officer_id <= 0) {
            die("<script>alert('❌ Invalid officer ID'); window.history.back();</script>");
        }

        $stmt = $conn->prepare("
            INSERT INTO cases (title, case_type, date_filed, status, description, officer_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssis", $title, $case_type, $date_filed, $status, $description, $officer_id, $created_at);
        $stmt->execute();
        $stmt->close();

        // Fetch officer email
        $email_stmt = $conn->prepare("SELECT email, full_name FROM members WHERE user_id = ?");
        $email_stmt->bind_param("i", $officer_id);
        $email_stmt->execute();
        $email_stmt->bind_result($officer_email, $officerName);
        $email_stmt->fetch();
        $email_stmt->close();

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'crimsonvaultofficial.web.co@gmail.com';
            $mail->Password = 'adtqfecrinzhsagg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault');
            $mail->addAddress($officer_email, $officerName);

            $mail->isHTML(true);
            $mail->Subject = "New Case Assigned: $title";
            $mail->Body = nl2br("Hello $officerName,\n\nCase Title: $title\nType: $case_type\nStatus: $status\nDate Filed: $date_filed\n\nRegards,\nCrimson Vault System");

            $mail->send();
            header("Location: dashboard.php?msg=case_assigned");
            exit();
        } catch (Exception $e) {
            echo "<script>alert('❌ Case saved but email failed: " . addslashes($mail->ErrorInfo) . "'); window.history.back();</script>";
            exit();
        }
    }

    /* ---- UPDATE CASE ---- */
    if ($action === 'update_case') {
        $id = intval($_POST['case_id']);
        $title = trim($_POST['title']);
        $status = $_POST['status'];
        $officer = intval($_POST['officer_id']);

        $stmt = $conn->prepare("UPDATE cases SET title=?, status=?, officer_id=? WHERE case_id=?");
        $stmt->bind_param("ssii", $title, $status, $officer, $id);
        $stmt->execute();
        $stmt->close();
    }

    /* ---- DELETE CASE ---- */
    if ($action === 'delete_case') {
        $id = intval($_POST['case_id']);
        $conn->query("DELETE FROM cases WHERE case_id=$id");
    }
}

// ---- SEND NOTIFICATION ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $user_id_raw = $_POST['user_id'];
    $message = trim($_POST['message']);
    $created_at = date("Y-m-d H:i:s");

    if (empty($message)) {
        die("<script>alert('❌ Message cannot be empty'); window.history.back();</script>");
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'crimsonvaultofficial.web.co@gmail.com';
    $mail->Password = 'adtqfecrinzhsagg';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('crimsonvaultofficial.web.co@gmail.com', 'Crimson Vault');
    $mail->isHTML(true);
    $mail->Subject = "New Notification from Crimson Vault";
    $mail->Body = nl2br($message);

    if ($user_id_raw === "all") {
        // Send to all users
        $result = $conn->query("SELECT user_id, email FROM members WHERE status='active'");
        while ($user = $result->fetch_assoc()) {
            $uid = $user['user_id'];
            $email = $user['email'];

            // Insert into notifications table
            $insert = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $uid, $message, $created_at);
            $insert->execute();

            // Send email
            try {
                $mail->clearAddresses();
                $mail->addAddress($email);
                $mail->send();
            } catch (Exception $e) {
                // continue to next user
            }
        }
        echo "<script>alert('✅ Notification sent to all users successfully!');</script>";
    } else {
        // Send to a specific user
        $user_id = intval($user_id_raw);
        $stmt = $conn->prepare("SELECT email FROM members WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $email = $row['email'];

            $insert = $conn->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, ?)");
            $insert->bind_param("iss", $user_id, $message, $created_at);
            $insert->execute();

            try {
                $mail->addAddress($email);
                $mail->send();
                echo "<script>alert('✅ Notification sent successfully!');</script>";
            } catch (Exception $e) {
                echo "<script>alert('❌ Notification saved but email failed: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('❌ User not found!');</script>";
        }
    }
}
// ================================ Fetch Data ================================
$users = $conn->query("SELECT user_id, full_name, phone, email, role_name FROM members");
$cases = $conn->query("SELECT * FROM cases");
$logs = $conn->query("
    SELECT l.log_id, l.user_id, l.action, l.timestamp, m.full_name 
    FROM audit_log l 
    JOIN members m ON l.user_id = m.user_id 
    ORDER BY l.timestamp DESC LIMIT 20
");
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5");
$feedbacks = $conn->query("
    SELECT f.fdb_id, f.message, f.created_at, m.full_name 
    FROM feedback f 
    JOIN members m ON f.user_id = m.user_id 
    ORDER BY f.created_at DESC LIMIT 5
");
$support_requests = $conn->query("SELECT * FROM support_requests ORDER BY created_at DESC LIMIT 5");

// Fetch all roles for dropdown
$role_options = [];
$roles_result = $conn->query("SELECT DISTINCT role_name FROM members");
if ($roles_result) {
    while ($r = $roles_result->fetch_assoc()) {
        $role_options[] = $r['role_name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Crimson Vault</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="dashboard-body">

    <!-- ===== HEADER ===== -->
    <header>
        <img src="logo.png" alt="Logo">
        <h1>Crimson Vault Admin Dashboard</h1>
    </header>

    <!-- ===== DASHBOARD LAYOUT ===== -->
    <div class="dashboard">

        <!-- ===== SIDEBAR ===== -->
        <aside class="sidebar">
            <h2>Admin Menu</h2>
            <a href="#user-management">👥 User Management</a>
            <a href="#content-management">📁 Case Management</a>
            <a href="#send-notification">📨 Send Notification</a>
            <a href="#activity-logs">🧾 Activity Logs</a>
            <a href="#support-queries">❓ Support Queries</a>
            <a href="#feedback">💬 Feedback</a>
            <a href="logout.php">🚪 Logout</a>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <main class="content">

            <!-- ===== USER MANAGEMENT ===== -->
            <section id="user-management">
                <h2>User Management</h2>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr>
                                <form method="POST">
                                    <td><?= $row['user_id'] ?>
                                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                    </td>
                                    <td><input type="text" name="full_name"
                                            value="<?= htmlspecialchars($row['full_name']) ?>"></td>
                                    <td><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>">
                                    </td>
                                    <td><input type="text" name="phone" value="<?= htmlspecialchars($row['phone']) ?>"></td>
                                    <td>
                                        <select name="role_name">
                                            <?php foreach ($role_options as $role): ?>
                                                <option value="<?= $role ?>" <?= $row['role_name'] === $role ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($role) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="action" value="update_user">Update</button>
                                        <button type="submit" name="action" value="delete_user"
                                            onclick="return confirm('Delete this user?')">Delete</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

                <h3>Add New User</h3>
                <form method="POST">
                    <input type="text" name="full_name" placeholder="Full Name" required pattern="[A-Za-z\s]+"
                        title="Only letters and spaces allowed">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="phone" placeholder="Phone Number" required>
                    <input type="text" name="password_hash" placeholder="Password" required>
                    <select name="role_name" required>
                        <option value="" disabled selected>Select Role</option>
                        <?php foreach ($role_options as $role): ?>
                            <option value="<?= $role ?>"><?= htmlspecialchars($role) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="action" value="add_user">Add User</button>
                </form>
            </section>

            <!-- ===== CASE MANAGEMENT ===== -->
            <section id="content-management">
                <h2>Case Management</h2>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Case ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Assigned Officer</th>
                            <th>Actions</th>
                        </tr>
                        <?php while ($row = $cases->fetch_assoc()): ?>
                            <tr>
                                <form method="POST">
                                    <td><?= $row['case_id'] ?>
                                        <input type="hidden" name="case_id" value="<?= $row['case_id'] ?>">
                                    </td>
                                    <td><input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>"></td>
                                    <td><input type="text" name="status" value="<?= htmlspecialchars($row['status']) ?>">
                                    </td>
                                    <td><input type="text" name="officer_id"
                                            value="<?= htmlspecialchars($row['officer_id']) ?>"></td>
                                    <td>
                                        <button type="submit" name="action" value="update_case">Update</button>
                                        <button type="submit" name="action" value="delete_case"
                                            onclick="return confirm('Delete this case?')">Delete</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

                <h3>Add New Case</h3>
                <form method="POST">
                    <input type="text" name="title" placeholder="Case Title" required>
                    <input type="date" name="date_filed" required>
                    <select name="status" required>
                        <option value="" disabled selected>Select Status</option>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                        <option value="Pending">Pending</option>
                        <option value="Under Investigation">Under Investigation</option>
                    </select>
                    <select name="case_type" required>
                        <option value="" disabled selected>Select Case Type</option>
                        <option value="Civil">Civil</option>
                        <option value="Criminal">Criminal</option>
                        <option value="Family">Family</option>
                        <option value="Corporate">Corporate</option>
                        <option value="Cyber">Cyber</option>
                    </select>
                    <input type="number" name="officer_id" placeholder="Assigned Officer ID" required><br>
                    <textarea name="description" placeholder="Case Description" cols="50" rows="10"
                        required></textarea><br>
                    <button type="submit" name="action" value="add_case">Add Case</button>
                </form>
            </section>

            <!-- ===== SEND NOTIFICATION ===== -->
            <section id="send-notification">
                <h2>Send Notification</h2>
                <?php $user_list = $conn->query("SELECT user_id, full_name, email FROM members"); ?>
                <form method="POST">
                    <div class="notification-row">
                        <label for="user_id">User:</label>
                        <select id="user_id" name="user_id" required onchange="fillEmail()">
                            <option value="" disabled selected>Select User</option>
                            <option value="all" data-email="all">Send to All Users</option>
                            <?php while ($u = $user_list->fetch_assoc()): ?>
                                <option value="<?= $u['user_id'] ?>" data-email="<?= htmlspecialchars($u['email']) ?>">
                                    <?= htmlspecialchars($u['full_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="email" id="email" name="email" placeholder="User Email" readonly required>
                    </div>
                    <label for="message">Message:</label><br>
                    <textarea id="message" name="message" rows="5" cols="80" required></textarea><br>
                    <button type="submit" name="send_notification">Send Notification</button>
                </form>
            </section>

            <script>
                function fillEmail() {
                    const dropdown = document.getElementById("user_id");
                    const selectedOption = dropdown.options[dropdown.selectedIndex];
                    const email = selectedOption.getAttribute("data-email");
                    const emailField = document.getElementById("email");

                    if (email === "all") {
                        emailField.value = "All Users";
                        emailField.readOnly = true;
                    } else {
                        emailField.value = email || "";
                        emailField.readOnly = true;
                    }
                }
            </script>

            <!-- ===== ACTIVITY LOGS ===== -->
            <section id="activity-logs">
                <h2>Recent Activity Logs</h2>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Log ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Timestamp</th>
                        </tr>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $log['log_id'] ?></td>
                                <td>[<?= $log['user_id'] ?>] <?= htmlspecialchars($log['full_name']) ?></td>
                                <td><?= htmlspecialchars($log['action']) ?></td>
                                <td><?= $log['timestamp'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </section>

            <!-- ===== SUPPORT QUERIES ===== -->
            <section id="support-queries">
                <h2>Support Queries</h2>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Issue</th>
                            <th>Submitted On</th>
                        </tr>
                        <?php while ($req = $support_requests->fetch_assoc()): ?>
                            <tr>
                                <td><?= $req['id'] ?></td>
                                <td><?= htmlspecialchars($req['name']) ?></td>
                                <td><?= htmlspecialchars($req['email']) ?></td>
                                <td><?= nl2br(htmlspecialchars($req['issue'])) ?></td>
                                <td><?= $req['created_at'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </section>

            <!-- ===== FEEDBACK ===== -->
            <section id="feedback">
                <h2>User Feedback</h2>
                <ul>
                    <?php while ($fb = $feedbacks->fetch_assoc()): ?>
                        <li>
                            <strong><?= htmlspecialchars($fb['full_name']) ?>:</strong>
                            <?= htmlspecialchars($fb['message']) ?>
                            <em>(<?= $fb['created_at'] ?>)</em>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>

        </main>
    </div>
</body>

</html>