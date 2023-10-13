<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('header.php');

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

if (isset($_POST['delete'])) {
    $projectIdToDelete = $_GET['project_id'];
    // Database connection
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $getImageFilenameQuery = "SELECT image FROM project WHERE id = ?";
    $stmtGetImageFilename = $conn->prepare($getImageFilenameQuery);
    $stmtGetImageFilename->bind_param("s", $projectIdToDelete);
    $stmtGetImageFilename->execute();
    $stmtGetImageFilename->bind_result($imageFilename);
    $stmtGetImageFilename->fetch();
    $stmtGetImageFilename->close();

    if (!empty($imageFilename) && $imageFilename !== "project-default.jpg") {
        // Delete the image file
        $imagePath = "project-media" . DIRECTORY_SEPARATOR . $imageFilename; // Assuming image files are in the "project-media" folder
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }


    $deleteSkillsQuery = "DELETE FROM skills WHERE project_id = ?";
    $stmtDeleteSkills = $conn->prepare($deleteSkillsQuery);
    $stmtDeleteSkills->bind_param("s", $projectIdToDelete);

    if ($stmtDeleteSkills->execute()) {

        // Delete project participation records from the participation table
        $deleteParticipationQuery = "DELETE FROM participation WHERE project_id = ?";
        $stmtDeleteParticipation = $conn->prepare($deleteParticipationQuery);
        $stmtDeleteParticipation->bind_param("s", $projectIdToDelete);

        if ($stmtDeleteParticipation->execute()) {

            // Delete project from the project table
            $deleteProjectQuery = "DELETE FROM project WHERE id = ?";
            $stmtDeleteProject = $conn->prepare($deleteProjectQuery);
            $stmtDeleteProject->bind_param("s", $projectIdToDelete);
            $stmtDeleteProject->execute();
        }
    }

    $stmtDeleteSkills->close();
    $stmtDeleteParticipation->close();
    $stmtDeleteProject->close();
    $conn->close();

    // Redirect to a page after deletion
    header('Location: project-history.php'); // You can specify a different page to redirect to
    exit;
}

$stmt->close();
$conn->close();
?>

<html lang="en">
<head>
    <link rel="stylesheet" href="project-history.css">
    <title>Sustain - Account</title>
</head>
<body>
<div class="info">
    <h2>Project history</h2>

    <?php
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

        if ($userId === $row['organizer_id']) {
            echo '<div class="button-container">';
            // Edit button
            echo '<a href="edit-project.php?project_id=' . $row['id'] . '" class="edit-button">Edit</a>';
            echo '<a href="participants.php?project_id=' . $row['id'] . '" class="edit-button" id="participantsBtn">Participants</a>';?>
            <form action="project-history.php?project_id=<?php echo $row['id']; ?>"
                  method="POST" enctype="multipart/form-data">
                <!-- Delete button -->
                <button class="delete-button" type="submit" name="delete" onclick="return confirm
            ('Are you sure you want to delete this project?')">Remove</button>
            </form>
            <?php
            echo '</div>';
        }
    echo '</div>'; // Close the "details" div
    echo '</div>'; // Close the "project" div
}
?>
    <br>
    <br>
</div>
<?php include('footer.php'); ?>
</body>

</html>
