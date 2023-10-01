<?php
session_start();

// Generate random id
function generateRandomId($length = 3) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}


// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to the login page
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission (save the message to the database)
    $messageContent = $_POST['message'];
    $senderId = $_SESSION['user_id'];

    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Generate a random ID and check if it exists in the database
    $randomId = generateRandomId();
    $query = "SELECT id FROM message WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $randomId);
    $stmt->execute();
    $stmt->store_result();

// Repeat until an unused ID is found
    while ($stmt->num_rows > 0) {
        $randomId = generateRandomId();
        $stmt->bind_param("s", $randomId);
        $stmt->execute();
        $stmt->store_result();
    }
    // Prepare and execute an SQL query to insert the message
    $query = "INSERT INTO message (id, sender_id, message, sent_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $randomId, $senderId, $messageContent);

    if ($stmt->execute()) {
        // Message saved successfully
    } else {
        // Message saving failed
        echo "Error saving message: " . $stmt->error;
    }


    header('Location: home.php'); // Redirect to home page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="contact.css">
    <title>Sustain - Contact</title>

    <!-- Favicon -->
    <link rel="icon" href="media/circle.ico" type="image/x-icon">
    <link rel="shortcut icon" href="media/circle.ico" type="image/x-icon">
</head>
<body>
<header>
    <h1>Sustain</h1>
</header>
<nav>
    <div class="menu-button">
        <img src="media/menu-ico.jpg" alt="Menu" id="menu-icon" class="menu-btn">
        <div class="menu-content" id="menu-content">
            <!-- Add your menu options here -->


            <a href="home.php">Home</a>
            <a href="projects.php">Projects</a>
            <a href="contact.php">Contact Form</a>
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['user_id'])) {
                echo '<a href="account.php" class="last-btn">Account</a>';
            } else {
                echo '<a href="login.php" class="last-btn">Log in</a>';
            }
            ?>
        </div>
    </div>

</nav>
<div class="vertical-menu" id="vertical-menu">
    <a href="add-project.php">Add Project</a>
    <a href="project-history.php">Project History</a>
    <a href="logout.php">Log Out</a>
</div>
<div class="info">
    <h2>Contact Form</h2>

    <div id="notification" class="notification"></div>


    <div id="notification" style="display: none;">Message sent successfully!</div>

    <form action="contact.php" method="POST">
        <label>
            <textarea name="message" rows="4" cols="50" required></textarea>
        </label>
        <button type="submit">Send</button>
    </form>



</div>
<script>

    /*
    Vertical Menu
     */
    var verticalMenu = document.getElementById("vertical-menu");

    document.getElementById("menu-icon").addEventListener("click", function () {
        verticalMenu.classList.toggle("open");
    });

    document.getElementById("menu-icon").addEventListener("click", function () {
        var verticalMenu = document.getElementById("vertical-menu");
        if (verticalMenu.style.display === "block") {
            verticalMenu.style.display = "none";
        } else {
            verticalMenu.style.display = "block";
        }
    });
</script>
</body>
</html>
