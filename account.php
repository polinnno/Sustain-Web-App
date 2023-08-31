<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="account.css">
    <title>Sustain - Account</title>

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
<div class="info">
    <h2>My Account</h2>
    <?php
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // Replace with your database connection code
        $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $userId = $_SESSION['user_id'];

        // Fetch user information from the database based on the user's ID
        $query = "SELECT name, last_name, email, role FROM Users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $stmt->bind_result($name, $last_name, $email, $role);
        $stmt->fetch();
        $stmt->close();
        $conn->close();

        // Display the user's information
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Last Name:</strong> $last_name</p>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Role:</strong> $role</p>";
    } else {
        echo "<p>Please log in to view your account information.</p>";
    }
    ?>

    <!-- TODO: add photo spawn -->

    <p> </p>
    <p></p>
    <p></p>
    <p></p>

    <a href="project-history.php" class="btn">Project History</a>
    <p></p>
    <p></p>
    <div class="logout-btn">
        <a href="logout.php" class="btn">Log Out</a>
    </div>

    <!-- TODO: link to add an event -->
    <a href="add-project.php">Add Project</a>

</div>
</body>
</html>
