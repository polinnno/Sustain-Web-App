<?php
session_start();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="base.css">
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
            <!-- TODO: dropdown menu login change-->
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
    <h2>My Account</h2>
    <div class="user-info">
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
            $query = "SELECT name, last_name, email, role, image FROM Users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $stmt->bind_result($name, $last_name, $email, $role, $image);
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
    </div>
    <div class="pp">
        <img src="<?php echo 'user-media' . DIRECTORY_SEPARATOR . $image ?>" alt="">

    </div>
    <!-- TODO: add photo spawn -->

    <p> </p>
    <p></p>
    <p></p>
    <p></p>

    <a href="project-history.php" class="btn">Project History</a>
    <p></p>
    <div class="add-project-btn">
        <a href="add-project.php" class="add-project-btn">Add Project</a>
    </div>
    <br><br><br>
    <div class="logout-btn">
        <a href="logout.php" class="btn">Log Out</a>
    </div>
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
