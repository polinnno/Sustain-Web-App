<?php
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/*
 * Subscription submission
 */
if (isset($_POST['subscribe'])) {
    $email = $_POST['email'];

    // Validate email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check_query = "SELECT COUNT(*) FROM subscribers WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);

        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count > 0) {
                echo "This email is already subscribed.";
            } else {
                // Email doesn't exist, proceed to insert
                $insert_query = "INSERT INTO subscribers (email) VALUES (?)";
                $insert_stmt = $conn->prepare($insert_query);

                if ($insert_stmt) {
                    $insert_stmt->bind_param("s", $email);
                    if ($insert_stmt->execute()) {
                        echo "Thank you for subscribing!";
                    } else {
                        echo "Error adding email: " . $insert_stmt->error;
                    }
                    $insert_stmt->close();
                } else {
                    echo "Error preparing insert statement: " . $conn->error;
                }
            }

        } else {
            echo "Error preparing check statement: " . $conn->error;
        }
    } else {
        echo "Invalid email address.";
    }
}
$conn->close();


?>
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