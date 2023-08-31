<?php
session_start();

// Simulate a user with known credentials (replace this with database queries)
$validEmail = 'user@example.com';
$validPassword = 'password123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === $validEmail && $password === $validPassword) {
        // Set up session or token for authentication
        $_SESSION['user_id'] = 123; // Replace with actual user ID
        header('Location: account_page.php');
        exit;
    } else {
        // Invalid credentials, show error message
        $errorMessage = 'Invalid email or password.';
    }
}
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
    <a href="account.php">Account</a>
</nav>
<div class="info">
    <h2>Project history</h2>

</body>
</html>
