<?php
ini_set('display_errors', '1');
session_start();
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $errors = [];
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if (!preg_match('/^[a-zA-Z\s]+$/', $name) || !preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        $errors[] = 'Name and last name must contain only letters and spaces.';
    }

    // If there are errors, display them:
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . '<br>';
        }
    } else {
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
            $emailAlreadyExists = true;
            include 'register-overlay.php';
            echo '<script>document.getElementById("overlay").style.display = "block";</script>';

            $stmt->close();
            $conn->close();
        }
        else {
            $stmt->close();
        }

        $newUserID = generateUniqueUserID();
        $isUnique = false;
        $maxAttempts = 10;

        while (!$isUnique && $maxAttempts > 0) {
            // Query to check if the generated ID already exists
            $query = "SELECT id FROM Users WHERE id = '$newUserID'";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                die('Database query failed: ' . mysqli_error($conn));
            }

            if (mysqli_num_rows($result) > 0) {
                $newUserID = generateUniqueUserID();
            } else {
                $isUnique = true;
            }
            mysqli_free_result($result);
            $maxAttempts--;
        }

        if (!$isUnique) {
            die('Unable to generate a unique ID.');
        }

        if (isset($errorMessage)) {
            include 'register-overlay.php';
        }

        $uploadDir = 'user-media' . DIRECTORY_SEPARATOR;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tempFilePath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];

            error_log("pp recognized");

            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($tempFilePath, $targetFilePath)) {
            } else {
                echo 'Error uploading file.';
            }
        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $targetFilePath = null;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        $stmt = $conn->prepare("INSERT INTO Users (id, name, last_name, email, password, role, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $newUserID, $name, $last_name, $email, $hashedPassword, $role, $fileName);


        error_log("targetFilePath:");
        error_log($targetFilePath);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $newUserID;
            header("Location: account.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<html lang="">
<head>
    <title>Sustain - Register</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<div id="notification" class="notification">
    This email is already in use. Please choose a different email.
</div>
<div class="register-container">
<h2>Register</h2>

<form action="register.php" method="post" enctype="multipart/form-data">
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
<?php include('footer.php'); ?>
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

    /*
    Overlay
     */
    const overlay = document.getElementById('overlay');
    const overlayBackground = document.getElementById('overlay-background');
    const overlayCloseButton = document.getElementById('overlay-close');

    function showOverlay() {
        overlay.style.display = 'block';
        overlayBackground.style.display = 'block';
    }

    function hideOverlay() {
        overlay.style.display = 'none';
        overlayBackground.style.display = 'none';
    }

    overlayCloseButton.addEventListener('click', hideOverlay);

    // Show overlay when needed
    <?php if (isset($emailAlreadyExists)) { ?>
    showOverlay();
    <?php } ?>
</script>
</html>