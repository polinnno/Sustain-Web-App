<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input
    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
    } else {
        $conn = new mysqli("localhost", "root", "root", "it210_sustain");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query the database to retrieve the user's information
        $query = "SELECT id, password FROM Users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            // Verify the provided password against the hashed password
            if (password_verify($password, $hashedPassword)) {
                // Authentication successful, set session or token and redirect
                $_SESSION['user_id'] = $row['id'];
                header("Location: account.php");
                exit();
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "User not found.";
        }

        $stmt->close();
        $conn->close();
    }
}

$conn = new mysqli("localhost", "root", "root", "it210_sustain");
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
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="login.css">
    <title>Sustain - Login</title>

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


<div class="login-container">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <div class="input-group">
            <input type="email" name="email" required>
            <label>Email</label>
        </div>
        <div class="input-group">
            <input type="password" name="password" required>
            <label>Password</label>
        </div>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Register instead?</a>

    <br>
    <br>
    <br>
</div>
<br>
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
