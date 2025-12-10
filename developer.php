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
  <title>Meet the Developer</title>
  <!-- Font Awesome for Social Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- External CSS -->
  <link rel="stylesheet" href="style.css">
</head>
<body id="developer-page">

  <?php include("header.php"); ?>

  <!-- Developer Section -->
  <section class="developer-section">
    <h2>Meet the Developer</h2><br>
    <img src="dp.png" alt="Developer Photo" class="developer-photo">

    <h3>Aum Sheth</h3>
    <p>
      Hi! I am the developer behind <strong>Crimson Vault</strong>.  
      This project was built with a vision to simplify and secure case management 
      using modern web technologies. With a passion for coding, security, and innovation, 
      I have designed this platform to be reliable, efficient, and user-friendly.
    </p>

    <h3>Skills & Expertise :- </h3>
    <ul class="skills-list">
      <li>PHP & MySQL</li>
      <li>HTML, CSS, JavaScript</li>
      <li>Bootstrap & Responsive Design</li>
      <li>Database Management</li>
      <li>Core Java</li>
      <li>Problem Solving</li>
      <li>C++</li>
    </ul>

    <h3>Developing Expertise In :-</h3>
    <ul class="skills-list">
      <li>Advanced Java</li>
      <li>Python</li>
      <li>Data Structure and Algorithms</li>
      <li>Cyber Security</li>
      <li>S.E.O</li>
    </ul>

    <h3>Contact Information</h3>
    <div class="contact-info">
      📧 <b>Email:</b> <a href="mailto:aums35470@gmail.com">aums35470@gmail.com</a><br>
      📞 <b>Phone:</b> +91 88490 81153
    </div>

    <h3>Social Media</h3>
    <div class="contact-links">
      <div class="social-icons">
        <a href="https://www.facebook.com/share/16t8deRJFF/" target="_blank" class="facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/ig.aumsheth?igsh=MWMwaGxkNHY1NmswOA==" target="_blank" class="instagram"><i class="fab fa-instagram"></i></a>
        <a href="https://www.snapchat.com/add/aumsheth2021?share_id=AsEqVgan_tU&locale=en-GB" target="_blank" class="snapchat"><i class="fab fa-snapchat-ghost"></i></a>
        <a href="https://www.linkedin.com/in/aum-sheth-56381b365?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank" class="linkedin"><i class="fab fa-linkedin-in"></i></a>
        <a href="https://x.com/ShethAum?t=5wH7444scak9Ki09tcJBUQ&s=09" target="_blank" class="x"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="https://www.threads.com/@ig.aumsheth" target="_blank" class="threads"><i class="fa-brands fa-threads"></i></a>
      </div>
    </div>
  </section><br><br><br><br>

  <?php include("footer.php"); ?>
</body>
</html>
