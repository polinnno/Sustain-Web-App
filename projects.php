<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include('header.php');

$sql = "SELECT * FROM project";
$result = $conn->query($sql);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $role_query = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($role_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $role_result = $stmt->get_result();

    if ($role_result->num_rows > 0) {
        $row = $role_result->fetch_assoc();
        $userRole = $row['role'];
    }
} else {
    $userRole = "volunteer";
}
$conn->close();
?>

<html lang="en">
<head>
    <title>Sustain - Projects</title>
    <link rel="stylesheet" href="projects.css">
</head>
<body>
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
<?php include('footer.php'); ?>
<script>
    /*
    Search button
     */
    var searchInput = document.getElementById("search-input");
    var searchButton = document.getElementById("search-button");

    searchButton.addEventListener("click", function () {
        var searchTerm = searchInput.value.toLowerCase();

        var projectItems = document.querySelectorAll(".projects-grid .project");

        projectItems.forEach(function (item) {
            var projectTitle = item.querySelector("h3").textContent.toLowerCase();
            var projectDescription = item.querySelector("p").textContent.toLowerCase();

            // Check if either the title or description contains the search contents
            if (projectTitle.includes(searchTerm) || projectDescription.includes(searchTerm)) {
                item.style.display = "block";
            } else {
                item.style.display = "none";
            }
        });
    });
</script>
</body>
</html>


