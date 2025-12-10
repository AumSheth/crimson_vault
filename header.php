<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Role-aware dashboard
$dashboardLink = "dashboard.php";
if (isset($_SESSION['role_name'])) {
    switch ($_SESSION['role_name']) {
        case 'Police':
            $dashboardLink = "police_dashboard.php";
            break;
        case 'Legal Personnel':
            $dashboardLink = "legal_dashboard.php";
            break;
        case 'Admin':
            $dashboardLink = "dashboard.php"; // change if you add an admin_dashboard
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crimson Vault</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">

<header id="main-header">
  <div class="header-left">
    <img src="logo.png" alt="Crimson Vault Logo" id="logo">
  </div>
  <div class="header-right">
    <span id="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
  </div>
</header>

<nav id="main-nav">
  <ul>
    <li><a href="<?php echo $dashboardLink; ?>">🏠 Dashboard</a></li>
    <li><a href="about.php">ℹ️ About Us</a></li>
    <li><a href="developer.php">👨‍💻 About Developer</a></li>
    <li><a href="feedback.php">💬 Feedback</a></li>
    <li><a href="logout.php">🚪 Logout</a></li>
  </ul>
</nav>

<main id="page-content">
