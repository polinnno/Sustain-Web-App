<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project data from the database
$sql = "SELECT * FROM project";
$result = $conn->query($sql);

// Settings for "Join" Button
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Use a prepared statement to prevent SQL injection
    $role_query = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($role_query);
    $stmt->bind_param("i", $user_id); // "i" represents an integer, change it if user_id is of a different type
    $stmt->execute();
    $role_result = $stmt->get_result();

    if ($role_result->num_rows > 0) {
        $row = $role_result->fetch_assoc();
        $userRole = $row['role'];
    }
} else {
    $userRole = "volunteer"; // Default to volunteer if not logged in
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Projects</title>
    <link rel="stylesheet" href="projects.css">
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
    <a href="contact.php">Contact Form</a>
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
<h2>Projects</h2>
<p></p>
<div class="projects-grid">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="project">
            <a href="project-details.php?project_id=<?php echo $row['id']; ?>">
                <img src="project-media/<?php echo $row["image"]; ?>" alt="Project Image">
            </a>
            <a href="project-details.php?project_id=<?php echo $row['id']; ?>">
                <h3><?php echo $row["title"]; ?></h3>
            </a>
            <p><?php echo $row["description"]; ?></p>

            <!-- Display the "Join" button based on user role -->
            <?php if ($userRole === "volunteer" || !isset($_SESSION['user_id'])): ?>
                <form action="project-details.php" method="GET" class="join-form">
                    <input type="hidden" name="project_id" value="<?php echo $row['id']; ?>">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <?php
                    endif; ?>
                    <button type="submit" class="join-button">Join</button>
                </form>
            <?php endif; ?>

        </div>
    <?php endwhile; ?>
</div>
<p></p>
<p></p>
<p></p>
</body>
</html>


