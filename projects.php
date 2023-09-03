<?php
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project data from the database
$sql = "SELECT * FROM project";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Projects</title>
    <link rel="stylesheet" href="projects.css">
    <!-- Favicon -->
    <link rel="icon" href="media/circle.ico" type="image/x-icon">
    <link rel="shortcut icon" href="media/circle.ico" type="image/x-icon">

</head>
<body>
<header>
    <h1>Sustain</h1>
</header>
<nav>
    <a href="home.php">Home</a>
    <a href="projects.php">Projects</a>
    <a href="contact.html">Contact Form</a>
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id'])) {
        echo '<a href="account.php">Account</a>';
    } else {
        echo '<a href="login.php">Log in</a>';
    }
    ?>
</nav>
<h2>Projects</h2>
<p></p>
<div class="projects-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="project">
            <img src="project-media/<?php echo $row["image"]; ?>" alt="Project Image">
            <h3><?php echo $row["title"]; ?></h3>
            <p><?php echo $row["description"]; ?></p>
            <!-- Display other project attributes here -->

        </div>
    <?php endwhile; ?>
</div>
<p>

</p>
<p>  </p>
<p></p>
</body>
</html>


