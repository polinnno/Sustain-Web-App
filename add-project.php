<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log($_SESSION['user_id']);


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    error_log('first if');

    error_log('we good?');
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

    if ($conn->connect_error) {
        error_log('connection failed');
        die("Connection failed: " . $conn->connect_error);
    }

    // Generate a unique Project ID
    function generateUniqueProjectID($length = 4) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
    $newProjectID = generateUniqueProjectID();

    error_log('unique project id generated');
// Check if the generated ID is already used by an existing user
    $isUnique = false;
    $maxAttempts = 10;

    while (!$isUnique && $maxAttempts > 0) {
        // Query the database to check if the generated ID already exists
        $query = "SELECT id FROM project WHERE id = '$newProjectID'";

        // Run the query
        $result = mysqli_query($conn, $query);

        if (!$result) {
            error_log('database query failed');
            die('Database query failed: ' . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {
            // Generated ID already exists, generate a new one
            $newProjectID = generateUniqueProjectID();
        } else {
            $isUnique = true;
        }
        mysqli_free_result($result); // Free the result set
        $maxAttempts--;
    }

    if (!$isUnique) {
        die('Unable to generate a unique ID.');
    }

    error_log('unique id set');

    $uploadDir = 'user-media' . DIRECTORY_SEPARATOR;
    if (substr($uploadDir, -1) !== DIRECTORY_SEPARATOR) {
        $uploadDir .= DIRECTORY_SEPARATOR;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];

        // Move the uploaded file to the specified directory
        $targetFilePath = 'project-media' . DIRECTORY_SEPARATOR . $fileName;
        error_log($targetFilePath);

        if (move_uploaded_file($tempFilePath, $targetFilePath)) {

            // File was successfully uploaded, you can save $targetFilePath in the database
        } else {
            echo 'Error uploading file.';
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        // Use the default image when no file was uploaded
        $fileName = 'project-default.jpg';
    }

    // Check tags:
    if (isset($_POST['tags']) && is_array($_POST['tags'])) {
        $selectedTags = $_POST['tags'];
    } else {
        $selectedTags = []; // Default to an empty array if no tags are selected
    }



// Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $place = $_POST['place'];
    $organizer_id = $_SESSION['user_id'];

    error_log($organizer_id);


// Insert data into the database
    $sql = "INSERT INTO project (id, title, description, start_date, end_date, place, organizer_id, image)
        VALUES ('$newProjectID', '$title', '$description', '$start_date', '$end_date', '$place', '$organizer_id', '$fileName')";

    if ($conn->query($sql) === TRUE) {
        echo "Project added successfully!";
        error_log('project added');
    } else {
        error_log('project failed');
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

if (!empty($selectedTags)) {
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert each selected tag into the skills table
    foreach ($selectedTags as $tag) {
        $sql = "INSERT INTO skills (project_id, skill_name) VALUES ('$newProjectID', '$tag')";

        if ($conn->query($sql) === TRUE) {
            // Tag inserted successfully
        } else {
            echo "Error inserting tag: " . $conn->error;
        }
    }
}

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Projects</title>
    <link rel="stylesheet" href="add-project.css">
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

<div class="add-project-container">

    <h2>New Project</h2>
    <form action="add-project.php" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label for="title">Title:</label>
            <p></p>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="input-group">
            <label for="description">Description:</label>
            <p></p>

            <textarea id="description" name="description" rows="4" cols="50" class="textarea" required></textarea>
        </div>
        <p></p>
        <p></p>

        <br><br><br>
        <div class="input-group">
            <label for="start_date">Start Date:</label>
            <p></p>
            <input type="date" id="start_date" name="start_date" required>
        </div>

        <div class="input-group">
            <label for="end_date">End Date:</label>
            <p></p>
            <input type="date" id="end_date" name="end_date" required>
        </div>

        <div class="input-group">
            <label for="place">Place:</label>
            <p></p>
            <input type="text" id="place" name="place" required>
        </div>

        <div class="input-group">
            <label for="image">Image:</label>
            <p></p>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">
        </div>

        <!-- Inside your HTML form -->
        <div class="input-group">
            <br>
            <br>
            <label>Tags:</label>
            <p></p>
            <div class="checkbox-group">
                <label for="tag1"><input type="checkbox" id="tag1" name="tags[]" value="Writing"> Writing</label><br>
                <label for="tag2"><input type="checkbox" id="tag2" name="tags[]" value="Teaching"> Teaching</label><br>
                <label for="tag3"><input type="checkbox" id="tag3" name="tags[]" value="Event Planning"> Event Planning</label><br>
                <label for="tag4"><input type="checkbox" id="tag4" name="tags[]" value="Communication"> Communication</label><br>
                <label for="tag5"><input type="checkbox" id="tag5" name="tags[]" value="Technical"> Technical</label><br>
                <label for="tag6"><input type="checkbox" id="tag6" name="tags[]" value="Leadership"> Leadership</label><br>
                <label for="tag7"><input type="checkbox" id="tag7" name="tags[]" value="Artistic"> Artistic</label><br>
                <label for="tag8"><input type="checkbox" id="tag8" name="tags[]" value="Language"> Language</label><br>
                <label for="tag9"><input type="checkbox" id="tag9" name="tags[]" value="Hands-on Tasks"> Hands-on Tasks</label><br>
                <label for="tag10"><input type="checkbox" id="tag10" name="tags[]" value="Nature"> Nature</label><br>
                <label for="tag11"><input type="checkbox" id="tag11" name="tags[]" value="Other"> Other</label><br>
            </div>
        </div>


        <button type="submit">Add Project</button>
    </form>
</div>


</body>
</html>