<?php
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project data from the database
$sql = "SELECT * FROM project";
$result = $conn->query($sql);

// Fetch data from the previous page
if (isset($_GET['user_role']) && isset($_GET['project_id'])) {
    $userRole = $_GET['user_role'];
    $projectId = $_GET['project_id'];
}
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Home</title>
    <link rel="stylesheet" href="home.css">
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

<?php
// Check the user's role
if (($userRole === "volunteer" || !isset($_SESSION['user_id'])) ){
    error_log("volunteer");
    // Replace with your database connection code
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }



    // Fetch user information from the database based on the user's ID
    $query = "SELECT name, last_name, email, role FROM Users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $stmt->bind_result($name, $last_name, $email, $role);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Display the user's information
    echo "<p><strong>Name:</strong> $name</p>";
    echo "<p><strong>Last Name:</strong> $last_name</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Role:</strong> $role</p>";
} else if ($userRole === "organizer" & $_SESSION['user_id'] === $projectId) {
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
