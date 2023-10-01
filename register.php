<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validate input

    $errors = [];

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if (!preg_match('/^[a-zA-Z\s]+$/', $name) || !preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        $errors[] = 'Name and last name must contain only letters and spaces.';
    }



    // If there are errors, display them to the user
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
    } else {
        $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        function generateUniqueUserID($length = 6) {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $randomString = '';

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }

            return $randomString;
        }


        $emailQuery = "SELECT id FROM Users WHERE email = ?";
        $stmt = $conn->prepare($emailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Email already exists, display an error message to the user
            $emailAlreadyExists = true;

            include 'register-overlay.php';
            error_log("Overlay is displayed");

            echo '<script>document.getElementById("overlay").style.display = "block";</script>';

            $stmt->close();
            $conn->close();
            //exit(); // Stop further processing
        }
        else {
            $stmt->close();
        }




// Generate a new unique user ID
        $newUserID = generateUniqueUserID();

// Check if the generated ID is already used by an existing user
        $isUnique = false;
        $maxAttempts = 10;

        while (!$isUnique && $maxAttempts > 0) {
            // Query the database to check if the generated ID already exists
            $query = "SELECT id FROM Users WHERE id = '$newUserID'";

            // Run the query (you'll need to replace this with your database connection code)
            $result = mysqli_query($conn, $query);

            if (!$result) {
                die('Database query failed: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($result) > 0) {
                // Generated ID already exists, generate a new one
                $newUserID = generateUniqueUserID();
            } else {
                $isUnique = true;
            }
            mysqli_free_result($result); // Free the result set
            $maxAttempts--;
        }

        if (!$isUnique) {
            die('Unable to generate a unique ID.');
        }


// Show overlay in case there is an error.

        if (isset($errorMessage)) {
            include 'register-overlay.php'; // Include the overlay content
        }


        // The directory where uploaded images will be stored
        $uploadDir = 'user-media' . DIRECTORY_SEPARATOR;

// Check if an image file was uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tempFilePath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];

            // Move the uploaded file to the specified directory
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($tempFilePath, $targetFilePath)) {
                // File was successfully uploaded, you can save $targetFilePath in the database
            } else {
                echo 'Error uploading file.';
            }
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            // Use the default image when no file was uploaded
            $targetFilePath = 'media' . DIRECTORY_SEPARATOR . 'user-pp.jpg';
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $stmt = $conn->prepare("INSERT INTO Users (id, name, last_name, email, password, role, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $newUserID, $name, $last_name, $email, $hashedPassword, $role, $targetFilePath);


        if ($stmt->execute()) {
            // Successful registration, redirect to success page or show confirmation message
            $_SESSION['user_id'] = $newUserID;
            header("Location: account.php");
            exit();
        } else {
            // Failed to insert data, handle the error
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="">
<head>

    <!-- Favicon -->
    <link rel="icon" href="media/circle.ico" type="image/x-icon">
    <link rel="shortcut icon" href="media/circle.ico" type="image/x-icon">
    <title>Sustain - Register</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="register.css">

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
    <a href="logout.php">Log Out</a>
</div>


<div id="notification" class="notification">
    This email is already in use. Please choose a different email.
</div>
<div class="register-container">
<h2>Register</h2>

<form action="register.php" method="post">
    <div class="input-group">
        <label for="name">First Name:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div class="input-group">
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
    </div>

    <div class="input-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>

    <div class="input-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <div class="input-group">
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="organiser">Organiser</option>
            <option value="volunteer">Volunteer</option>
        </select>
    </div>

    <div class="input-group">
        <label for="image">Profile Image:</label>
        <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">
    </div>
    <button type="submit">Register</button></form>

</div>
<p></p>
</body>
<script>
    const nameInput = document.getElementById('name');
    const lastNameInput = document.getElementById('last_name');

    nameInput.addEventListener('input', validateOneWord);
    lastNameInput.addEventListener('input', validateOneWord);

    function validateOneWord() {
        const value = this.value;
        if (value.includes(' ')) {
            this.setCustomValidity('Please enter only one word.');
        } else {
            this.setCustomValidity('');
        }
    }

    // Get references to the overlay and overlay background
    const overlay = document.getElementById('overlay');
    const overlayBackground = document.getElementById('overlay-background');
    const overlayCloseButton = document.getElementById('overlay-close');

    // Function to show the overlay and overlay background
    function showOverlay() {
        overlay.style.display = 'block';
        overlayBackground.style.display = 'block';
    }

    // Function to hide the overlay and overlay background
    function hideOverlay() {
        overlay.style.display = 'none';
        overlayBackground.style.display = 'none';
    }

    // Event listener for the OK button
    overlayCloseButton.addEventListener('click', hideOverlay);

    // Show overlay when necessary
    <?php if (isset($emailAlreadyExists)) { ?>
    showOverlay();
    <?php } ?>


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
</html>