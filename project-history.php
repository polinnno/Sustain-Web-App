<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit;
}

// Database connection
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


// Fetch projects that the user participates in
$userId = $_SESSION['user_id'];
$query = "SELECT id, title, start_date, end_date, 'organiser' AS source, organizer_id
          FROM project
          WHERE organizer_id = ? 
          UNION 
          SELECT p.id, p.title, p.start_date, p.end_date, organizer_id, 'participant' AS source
          FROM project p
          INNER JOIN participation pa ON p.id = pa.project_id
          WHERE pa.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the delete button is clicked
if (isset($_POST['delete'])) {
    // Get the project ID to delete
    $projectIdToDelete = $_GET['project_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }



    // Fetch the image filename associated with the project
    $getImageFilenameQuery = "SELECT image FROM project WHERE id = ?";
    $stmtGetImageFilename = $conn->prepare($getImageFilenameQuery);
    $stmtGetImageFilename->bind_param("s", $projectIdToDelete);
    $stmtGetImageFilename->execute();
    $stmtGetImageFilename->bind_result($imageFilename);
    $stmtGetImageFilename->fetch();
    $stmtGetImageFilename->close();

    // Check if the image filename is not empty and not "project-default.jpg"
    if (!empty($imageFilename) && $imageFilename !== "project-default.jpg") {
        // Delete the image file
        $imagePath = "project-media" . DIRECTORY_SEPARATOR . $imageFilename; // Assuming image files are in the "project-media" folder
        if (file_exists($imagePath)) {
            if (unlink($imagePath)) {
                // Image file deleted successfully
            } else {
                echo "Error deleting image file.";
            }
        } else {
            echo "Image file not found.";
        }
    }


    $deleteSkillsQuery = "DELETE FROM skills WHERE project_id = ?";
    $stmtDeleteSkills = $conn->prepare($deleteSkillsQuery);
    $stmtDeleteSkills->bind_param("s", $projectIdToDelete);

    if ($stmtDeleteSkills->execute()) {
        // Skill records deleted successfully

        // Delete project participation records from the participation table
        $deleteParticipationQuery = "DELETE FROM participation WHERE project_id = ?";
        $stmtDeleteParticipation = $conn->prepare($deleteParticipationQuery);
        $stmtDeleteParticipation->bind_param("s", $projectIdToDelete);

        if ($stmtDeleteParticipation->execute()) {
            // Participation records deleted successfully

            // Delete project from the project table
            $deleteProjectQuery = "DELETE FROM project WHERE id = ?";
            $stmtDeleteProject = $conn->prepare($deleteProjectQuery);
            $stmtDeleteProject->bind_param("s", $projectIdToDelete);

            if ($stmtDeleteProject->execute()) {
                // Project deleted successfully from the project table
                echo "Project and related records deleted successfully!";
            } else {
                echo "Error deleting project: " . $stmtDeleteProject->error;
            }
        } else {
            echo "Error deleting project participation records: " . $stmtDeleteParticipation->error;
        }
    } else {
        echo "Error deleting project skills: " . $stmtDeleteSkills->error;
    }

    // Close prepared statements and the database connection
    $stmtDeleteSkills->close();
    $stmtDeleteParticipation->close();
    $stmtDeleteProject->close();
    $conn->close();

    // Redirect to a page after deletion
    header('Location: project-history.php'); // You can specify a different page to redirect to
    exit;
}



// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="project-history.css">
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
    <h2>Project history</h2>

    <?php
    // Display the projects
    while ($row = $result->fetch_assoc()) {
    echo '<div class="project">';
    echo '<a href="project-details.php?project_id=' . $row['id'] . '">';
    echo '<h3>' . $row["title"] . '</h3>';
    echo '</a>';

    echo '<div class="details">';
        echo '<div class="date-info">';
        echo '<p><strong>Start Date:</strong> ' . $row["start_date"] . '</p>';
        echo '<p><strong>End Date:</strong> ' . $row["end_date"] . '</p>';
        echo '</div>';

        // Check if the user is the organizer
        if ($userId === $row['organizer_id']) {
            // Container for edit and delete buttons
            echo '<div class="button-container">';
            // Edit button
            echo '<a href="edit-project.php?project_id=' . $row['id'] . '" class="edit-button">Edit</a>'; ?>
            <form action="project-history.php?project_id=<?php echo $row['id']; ?>"
                  method="POST" enctype="multipart/form-data">
                <!-- Delete button -->
                <button class="delete-button" type="submit" name="delete" onclick="return confirm
            ('Are you sure you want to delete this project?')">Remove</button>
            </form>
            <?php
            // Close the button container
            echo '</div>';
        }
    echo '</div>'; // Close the "details" div
    echo '</div>'; // Close the "project" div
}
?>
    <br>
    <br>
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
