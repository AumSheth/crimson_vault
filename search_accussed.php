<?php
session_start();

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ✅ Database Connection
$conn = new mysqli("localhost", "root", "", "crimson_vault");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result_message = "";
$accused_records = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if (!empty($name)) {
        // Search accused by name (case-insensitive)
        $stmt = $conn->prepare("
            SELECT a.*, c.title, c.case_type, c.date_filed, c.status, c.description 
            FROM accused a
            LEFT JOIN cases c ON a.case_id = c.case_id
            WHERE a.name LIKE ?
        ");
        $search = "%$name%";
        $stmt->bind_param("s", $search);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $accused_records = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $result_message = "❌ No criminal record found for <b>" . htmlspecialchars($name) . "</b>.";
        }
        $stmt->close();
    } else {
        $result_message = "⚠️ Please enter a name.";
    }
}

include("header.php");
?>

<div class="dashboard-content">
    <h2>🔍 Search Accused Records</h2>

    <!-- Search Form -->
    <form method="POST" action="search_accussed.php">
        <input type="text" name="name" placeholder="Enter accused name..." required>
        <button type="submit">Search</button>
    </form>
    <br>

    <!-- Results Section (styled like feedback list) -->
    <section id="search-results">
        <h3>Results</h3>
        <ul>
            <?php if (!empty($result_message)): ?>
                <li><?= $result_message ?></li>
            <?php endif; ?>

            <?php foreach ($accused_records as $rec): ?>
                <li>
                    <strong><?= htmlspecialchars($rec['name']) ?></strong> 
                    (Father: <?= htmlspecialchars($rec['father_name']) ?>, 
                    Gender: <?= htmlspecialchars($rec['gender']) ?>, 
                    DOB: <?= htmlspecialchars($rec['birthdate']) ?>)<br>
                    
                    <em>Case:</em> <?= htmlspecialchars($rec['title']) ?>  
                    (<?= htmlspecialchars($rec['case_type']) ?>)<br>
                    <em>Status:</em> <?= htmlspecialchars($rec['status']) ?>  
                    | <em>Date Filed:</em> <?= htmlspecialchars($rec['date_filed']) ?><br>
                    <em>Description:</em> <?= nl2br(htmlspecialchars($rec['description'])) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

<?php include("footer.php"); ?>
