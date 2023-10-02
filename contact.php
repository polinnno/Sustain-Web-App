<?php
session_start();

// Generate random id
function generateRandomId($length = 3)
{
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


$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


/*
 * Subscription submission
 */
if (isset($_POST['subscribe'])) {
    // Get the email from the form
    $email = $_POST['email'];

    // Validate the email (you can add more robust validation)
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Prepare and execute the SQL query to insert the email into the database
        $query = "INSERT INTO subscribers (email) VALUES (?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                echo "Thank you for subscribing!";
            } else {
                echo "Error adding email: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Invalid email address.";
    }
}

$userId = ($_SESSION['user_id']);
$query = "SELECT name, last_name FROM Users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$stmt->bind_result($name, $last_name);
$stmt->fetch();
$stmt->close();


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission (save the message to the database)
    $messageContent = $_POST['message'];
    $senderId = $userId;



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
        $conn->close();

        // Message saved successfully
    } else {
        // Message saving failed
        echo "Error saving message: " . $stmt->error;
        $conn->close();

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
    <link rel="stylesheet" href="base.css">
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
    <!-- Add your vertical menu options here -->
    <a href="add-project.php">Add Project</a>
    <a href="project-history.php">Project History</a>
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['user_id'])) {
        echo '<a href="logout.php">Log Out</a>';
    } else {
        echo '<a href="login.php" class="last-btn">Log in</a>';
    }
    ?>
</div>
<div class="info">
    <h2>Contact Form</h2>

    <div class="map">
        <!-- Map embed (Google Maps) -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3147.588353420284!2d-122.31266374825798!3d37.91668343188164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085790176577d37%3A0x10dfa2251b332c79!2s1234%20Elm%20St%2C%20El%20Cerrito%2C%20CA%2094530%2C%20USA!5e0!3m2!1sen!2srs!4v1696182619281!5m2!1sen!2srs" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <div class="all-info">
        <div class="user-info">
            <p><strong>Name:</strong> <?php echo $name?></p>
            <p><strong>Last name:</strong> <?php echo $last_name?></p>
            <p><strong>Message:</strong></p>
        </div>
        <div class="company-info">
            <h3>Our address:</h3>


            <p class="address">
                1234 Elm Street <br>Cityville, ST 56789 <br>
                Phone: (123) 456-7890<br>Email: info@example.com
            </p>
        </div>
    </div>
    <!-- TODO: Form validation for first and last names -->
    <!-- TODO: Map, address -->
    <!-- TODO: Username at the header -->

    <form action="contact.php" method="POST">
        <label>
            <textarea name="message" rows="4" cols="50" required></textarea>
        </label>
        <button type="submit">Send</button>
    </form>
</div>
<div class="footer-content">
    <div class="footer-links">
        <a href="home.php">Home</a> <br><br>
        <a href="projects.php">Projects</a><br><br>
        <a href="contact.php">Contact</a><br><br>
        <a href="account.php">About</a><br><br>
    </div>
    <div class="footer-info">
        <h3>Contact Us</h3>
        <p>1234 Elm Street<br>Cityville, ST 56789</p>
        <p>Phone: (123) 456-7890<br>Email: info@example.com</p>
    </div>
    <form id="subscription-form" method="POST">
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <button type="submit" name="subscribe">Subscribe</button>
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