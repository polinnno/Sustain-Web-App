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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

</div>
</body>
</html>
