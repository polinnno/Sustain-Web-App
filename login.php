<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "it210_sustain");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
    } else {
        // Query the database to retrieve the user's information
        $query = "SELECT id, password FROM Users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];

            // Verify password
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $row['id'];
                header("Location: account.php");
                exit();
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="login.css">
    <title>Sustain - Login</title>
</head>
<body>
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
<?php include('footer.php'); ?>

</body>
</html>
