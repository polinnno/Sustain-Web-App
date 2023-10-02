<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log($_SESSION['user_id']);


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit;
}

// Check if the project_id is set in the URL
if (!isset($_GET['project_id'])) {
    error_log("project not set");
    header('Location: 404.php'); // Redirect to a 404 page if project_id is not set
    exit;
}

$projectId = $_GET['project_id'];
error_log("id of the project being edited is:");
error_log($projectId);

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

$query = "SELECT * FROM project WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 1) {
    $project = $result->fetch_assoc();
    if ($_SESSION['user_id'] !== $project['organizer_id']) {
        header('Location: 404.php'); // Redirect to a 404 page if the user is not the organizer
        exit;
    }
} else {
    // Redirect to an error page or display an error message
    header('Location: error.php');
    exit;
}


$selectedTags = [];
$tagsQuery = "SELECT skill_name FROM skills WHERE project_id = ?";
$stmt = $conn->prepare($tagsQuery);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $selectedTags[] = $row['skill_name'];
}

$stmt->close();
$conn->close();




if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

    if ($conn->connect_error) {
        error_log('connection failed');
        die("Connection failed: " . $conn->connect_error);
    }


    $uploadDir = 'user-media' . DIRECTORY_SEPARATOR;
    if (substr($uploadDir, -1) !== DIRECTORY_SEPARATOR) {
        $uploadDir .= DIRECTORY_SEPARATOR;
    }

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];

        // Move the uploaded file to the specified directory
        $targetFilePath = 'project-media' . DIRECTORY_SEPARATOR . $fileName;
        error_log($targetFilePath);

        if (move_uploaded_file($tempFilePath, $targetFilePath)) {
            // File was successfully uploaded, and $targetFilePath now contains the new image file path
        } else {
            echo 'Error uploading file.';
        }
    } else {
        // No new image uploaded, keep the existing image filename
        $fileName = $project['image'];
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

// Update the existing project in the database
    $sql = "UPDATE project SET title = ?, description = ?, start_date = ?, end_date = ?, place = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $title, $description, $start_date, $end_date, $place, $fileName, $projectId);

    if ($stmt->execute()) {
        echo "Project updated successfully!";
        error_log("the new title is:");
        error_log($title);
    } else {
        echo "Error updating project: " . $stmt->error;
    }


    $deleteTagsQuery = "DELETE FROM skills WHERE project_id = ?";
    $stmtDeleteTags = $conn->prepare($deleteTagsQuery);
    $stmtDeleteTags->bind_param("s", $projectId);
    $stmtDeleteTags->execute();
    $stmtDeleteTags->close();
    // Insert each selected tag into the skills table
    foreach ($selectedTags as $tag) {
        $sql = "INSERT INTO skills (project_id, skill_name) VALUES ('$projectId', '$tag')";
        if ($conn->query($sql) === TRUE) {
            // Tag inserted successfully
        } else {
            echo "Error inserting tag: " . $conn->error;
        }
    }


    $stmt->close();
    $conn->close();
    header('Location: project-details.php?project_id=' . $projectId); // Redirect to the edit-project.php page with the project_id

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Edit</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="edit-project.css">
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



<div class="add-project-container">

    <h2>Edit Project</h2>
    <form action="edit-project.php?project_id=<?php echo $projectId; ?>" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label for="title">Title:</label>
            <p></p>
            <input type="text" id="title" name="title" value="<?php echo $project['title']; ?>" required>
        </div>

        <div class="input-group">
            <label for="description">Description:</label>
            <p></p>

            <textarea id="description" name="description"  rows="4" cols="50" class="textarea"  required><?php echo $project['description']; ?></textarea>
        </div>
        <p></p>
        <p></p>

        <br><br><br>
        <div class="input-group">
            <label for="start_date">Start Date:</label>
            <p></p>
            <input type="date" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>" required>
        </div>

        <div class="input-group">
            <label for="end_date">End Date:</label>
            <p></p>
            <input type="date" id="end_date" name="end_date" value="<?php echo $project['end_date']; ?>"required>
        </div>

        <div class="input-group">
            <label for="place">Place:</label>
            <p></p>
            <input type="text" id="place" name="place" value="<?php echo $project['place']; ?>"required>
        </div>

        <div class="input-group">
            <label for="image">Image:</label>
            <p></p>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">
        </div>
        <div class="input-group">
            <br>
            <br>
            <label>Tags:</label>
            <p></p>
            <div class="checkbox-group">
                <?php
                // List of available tags
                $availableTags = ["Writing", "Teaching", "Event Planning", "Communication", "Technical", "Leadership", "Artistic", "Language", "Hands-on Tasks", "Nature", "Other"];

                foreach ($availableTags as $tag) {
                    $isChecked = in_array($tag, $selectedTags) ? 'checked' : '';
                    echo '<label for="tag-' . $tag . '"><input type="checkbox" id="tag-' . $tag . '" name="tags[]" value="' . $tag . '" ' . $isChecked . '> ' . $tag . '</label><br>';
                }
                ?>
            </div>
        </div>
            <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
            <button type="submit">Save</button>

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