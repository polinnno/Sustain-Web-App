<?php
global $conn;
$isOrganiser = false;
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT name, last_name, role FROM Users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $stmt->bind_result($name, $last_name, $role);
    $stmt->fetch();
    $stmt->close();
    if ($role ==='organiser'){
        $isOrganiser = true;
    }
} else {
    $name="       ";
    $last_name="        ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="base.css">
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
        </div>
        <?php
        if (isset($_SESSION['user_id'])){ ?>
        <p id="user">
            <?php
            echo $name;
            echo ' ';
            echo $last_name;?>
        </p>
        <?php } else { ?>
            <p id="empty-user"></p>
           <?php }
        ?>

    </div>

</nav>
<div class="vertical-menu" id="vertical-menu">
    <!-- Vertical menu -->
    <?php
    if ($isOrganiser){
        echo '<a href="add-project.php">Add Project</a>';
    }
    ?>
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

