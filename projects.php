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
// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Projects</title>
    <link rel="stylesheet" href="base.css">
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


<h2>Projects</h2>
<p></p>
<div class="search-bar">
    <input type="text" id="search-input" placeholder="Search projects...">
    <button id="search-button">Search</button>
</div>

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
            <?php if ($userRole === "volunteer" || !isset($_SESSION['user_id']) || $row['organizer_id'] != $_SESSION['user_id'] ): { ?>
                <form action="project-details.php" method="GET" class="join-form">
                    <input type="hidden" name="project_id" value="<?php echo $row['id']; ?>">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <?php
                    endif; ?>
                    <button type="submit" class="join-button">More</button>
                </form>
            <?php
            } elseif ($row['organizer_id'] === $_SESSION['user_id']): { ?>
                <a href="edit-project.php?project_id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
            <?php
            }
            endif; ?>
        </div>
    <?php endwhile; ?>
</div>
<p></p>
<p></p>
<p></p>
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


    /*
    Search button
     */
    var searchInput = document.getElementById("search-input");
    var searchButton = document.getElementById("search-button");

    // Event listener to the search button
    searchButton.addEventListener("click", function () {
        // Get the search query from the input field
        var searchTerm = searchInput.value.toLowerCase();

        // Get all project items in the grid
        var projectItems = document.querySelectorAll(".projects-grid .project");

        // Loop through each project to check if it matches query
        projectItems.forEach(function (item) {
            var projectTitle = item.querySelector("h3").textContent.toLowerCase();
            var projectDescription = item.querySelector("p").textContent.toLowerCase();

            // Check if either the title or description contains the search term
            if (projectTitle.includes(searchTerm) || projectDescription.includes(searchTerm)) {
                // If it matches, display
                item.style.display = "block";
            } else {
                // If it doesn't match, hide
                item.style.display = "none";
            }
        });
    });
</script>




</body>
</html>


