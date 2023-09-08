<?php
// Fetch data from the previous page
if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
    error_log("Project ID: " . $projectId);

}
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    error_log("getting user id...");
    error_log("User ID: " . $userId);

}

// Get all the DB entries to be displayed
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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

$imagePath = 'user-media' . DIRECTORY_SEPARATOR . $image;
error_log($imagePath);

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
    <br> <br>
    <p> <strong>Organized by <?php echo $name; ?> <?php echo $lastName; ?> </strong></p>
    <br>
    <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
    <p><strong>End Date:</strong> <?php echo $endDate; ?></p>
    <p><strong>Location:</strong> <?php echo $place; ?></p>
</div>

<?php
// Check the user's role
if (($userRole === "volunteer" || !isset($userId)) ){
    // Replace with your database connection code



} else if (isset($userId) & $userId === $organizerId) {
    echo "<p>Please log in to view your account information.</p>";
    error_log("we in");
}
else {
    error_log("some guy");
}
?>



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
