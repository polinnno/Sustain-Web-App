<?php
session_start();

// Get the user's ID from the session if logged in
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


// Fetch data from the previous page
if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
}

$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['user_id'])) {


    $projectId = $_POST['project_id'];
    $userId = $_POST['user_id'];
    if (isset($_POST['join'])) {
        $addParticipationQuery = "INSERT INTO participation (user_id, project_id) VALUES (?, ?)";
        $stmt = $conn->prepare($addParticipationQuery);
        $stmt->bind_param("ss", $userId, $projectId);

        if ($stmt->execute()) {
            echo "Joined successfully!";
        } else {
            echo "Error joining the project: " . $stmt->error;
        }
        header('Location: project-details.php?project_id=' . $projectId);
        exit;
    } elseif (isset($_POST['leave'])) {
        $removeParticipationQuery = "DELETE FROM participation WHERE user_id = ? AND project_id = ?";
        $stmt = $conn->prepare($removeParticipationQuery);
        $stmt->bind_param("ss", $userId, $projectId);

        if ($stmt->execute()) {
            echo "Left the project successfully!";
        } else {
            echo "Error leaving the project: " . $stmt->error;
        }    }
    header('Location: project-details.php?project_id=' . $projectId);
    exit;
}


// Fetch project and organizer information from the database based on the project_id
$query = "SELECT p.id, p.title, p.description, p.start_date, p.end_date, p.place, p.organizer_id, p.image, u.role, u.name, u.last_name
        FROM project p
        JOIN users u ON p.organizer_id = u.id
        WHERE p.id = ? LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$stmt->bind_result($projectId, $title, $description, $startDate, $endDate, $place, $organizerId, $image, $userRole, $name, $lastName);

if ($stmt->fetch()) {
    // Values fetched successfully
} else {
    die("No results found.");
}

$stmt->fetch();
error_log($title);
$stmt->close();

// Close the database connection
$conn->close();

$imagePath = 'project-media' . DIRECTORY_SEPARATOR . $image;
error_log($imagePath);


$isProjectOrganizer = ($userId === $organizerId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Home</title>
    <link rel="stylesheet" href="project-details.css">
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
            <a href="contact.html">Contact Form</a>
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
        </div>
    </div>

</nav>
<div class="vertical-menu" id="vertical-menu">
    <!-- Add your vertical menu options here -->
    <a href="add-project.php">Add Project</a>
    <a href="project-history.php">Project History</a>
    <a href="logout.php">Log Out</a>
</div>

<div class="info">
    <div class="head-img">
        <img src="<?php echo $imagePath ?>" alt="">
    </div>
    <h2><?php echo $title ?></h2>



    <p><?php echo $description; ?></p>
    <br>
    <!-- Display project tags -->
    <div class="project-tags">
        <?php
        // Fetch project tags associated with the project
        $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $tagsQuery = "SELECT skill_name FROM skills WHERE project_id = ?";
        $stmt = $conn->prepare($tagsQuery);
        $stmt->bind_param("s", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Define CSS class based on tag name
            $tagClass = strtolower(str_replace(' ', '-', $row['skill_name']));
            echo '<span class="tag ' . $tagClass . '">' . $row['skill_name'] . '</span>';
        }


        ?>
    </div>
    <br>
    <p> <strong>Organized by <?php echo $name; ?> <?php echo $lastName; ?> </strong></p>
    <br>
    <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
    <p><strong>End Date:</strong> <?php echo $endDate; ?></p>
    <p><strong>Location:</strong> <?php echo $place; ?></p>
    <br>
    <br>





<?php

error_log($userRole);
// Check the user's role
if (($userRole === "volunteer" || !isset($userId)) || !$isProjectOrganizer){
    $checkParticipationQuery = "SELECT * FROM participation WHERE user_id = ? AND project_id = ?";
    $stmt = $conn->prepare($checkParticipationQuery);
    $stmt->bind_param("ss", $_SESSION['user_id'], $projectId);
    $stmt->execute();
    $result = $stmt->get_result();

    // If a record exists, the user is already participating, show a "Leave" button
    if ($result->num_rows > 0) {
        echo '<form action="" method="POST" class="join-form">';
        echo '<input type="hidden" name="project_id" value="' . $projectId . '">';
        echo '<input type="hidden" name="user_id" value="' . $_SESSION['user_id'] . '">';
        echo '<input type="submit" name="leave" class="leave-button" value="Leave">';
        echo '</form>';
    } else {
        // If no record exists, the user is not participating, show a "Join" button
        echo '<form action="" method="POST" class="join-form">';
        echo '<input type="hidden" name="project_id" value="' . $projectId . '">';
        echo '<input type="hidden" name="user_id" value="' . $_SESSION['user_id'] . '">';
        echo '<input type="submit" name="join" class="join-button" value="Join">';
        echo '</form>';
    }


} else if (isset($userId) & $isProjectOrganizer) {
    error_log("edit button shown")
    ?>
    <a href="edit-project.php?project_id=<?php echo $projectId; ?>" class="edit-btn">Edit</a>
    <?php
}
else {
    error_log("some guy");
}

$stmt->close();
$conn->close();
?>

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
