<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us</title>
  <link rel="stylesheet" href="style.css">
</head>
<body id="about-page">

  <?php include("header.php"); ?>

  <!-- About Content -->
  <section class="about-container">
    <h2>About Crimson Vault</h2>
    <p>
        Crimson Vault is a secure, innovative, and user-friendly platform built to streamline 
        case management and information handling for law enforcement and authorized personnel.  
        Designed with both security and efficiency in mind, Crimson Vault ensures that sensitive 
        data remains protected while offering a smooth experience for users at every level.
    </p>

    <h3>Our Vision</h3>
    <p>
        We envision a future where technology empowers safety and justice. Crimson Vault is not 
        just a platform, but a commitment to building a transparent, efficient, and secure system 
        for managing critical information. Our aim is to reduce complexity, eliminate inefficiency, 
        and provide law enforcement officers with the tools they need to protect and serve effectively.
    </p>

    <h3>Core Features</h3>
    <ul>
        <li><strong>Secure Access Control:</strong> Only authorized users with valid credentials can log in.</li>
        <li><strong>Case Management:</strong> Create, update, and track cases with real-time data logging.</li>
        <li><strong>Role-Based Dashboard:</strong> Tailored dashboards for Admins, Police Officers, and Users.</li>
        <li><strong>Data Transparency:</strong> Organized tables and clear records to avoid confusion.</li>
        <li><strong>Modern UI:</strong> A clean, simple, and responsive interface that adapts across devices.</li>
    </ul>

    <h3>Why Crimson Vault?</h3>
    <p>
        At its heart, Crimson Vault was developed to solve a real problem — managing sensitive records 
        with security and ease. Unlike traditional systems that are bulky or outdated, our platform is 
        lightweight, modern, and focused on user experience. We believe that technology should serve 
        people, not complicate their work.
    </p>

    <h3>Our Commitment</h3>
    <p>
        We are committed to continuously improving Crimson Vault by listening to user feedback, 
        strengthening security, and expanding features. This project represents our dedication 
        to bridging the gap between law enforcement needs and modern technology.
    </p>
  </section><br><br><br><br>

  <?php include("footer.php"); ?>
</body>
</html>
