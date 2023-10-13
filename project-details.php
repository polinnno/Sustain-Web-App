<?php
session_start();
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
}

$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('header.php');

if (isset($_POST['user_id'])) {
    $projectId = $_POST['project_id'];
    $userId = $_POST['user_id'];
    if (isset($_POST['join'])) {
        if ($userId == null){
            // Redirect to the login.php if user is a guest
            header('Location: login.php');
            exit;
        }
        $addParticipationQuery = "INSERT INTO participation (user_id, project_id) VALUES (?, ?)";
        $stmt = $conn->prepare($addParticipationQuery);
        $stmt->bind_param("ss", $userId, $projectId);

        if ($stmt->execute()) {
            echo "Joined successfully!";
        }
        header('Location: project-details.php?project_id=' . $projectId);
        exit;
    } elseif (isset($_POST['leave'])) {
        $removeParticipationQuery = "DELETE FROM participation WHERE user_id = ? AND project_id = ?";
        $stmt = $conn->prepare($removeParticipationQuery);
        $stmt->bind_param("ss", $userId, $projectId);
        $stmt->execute();
    }
    header('Location: project-details.php?project_id=' . $projectId);
    exit;
}


$query = "SELECT p.id, p.title, p.description, p.start_date, p.end_date, p.place, p.organizer_id, p.image, u.role, u.name, u.last_name
        FROM project p
        JOIN users u ON p.organizer_id = u.id
        WHERE p.id = ? LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$stmt->bind_result($projectId, $title, $description, $startDate, $endDate, $place, $organizerId, $image, $userRole, $name, $lastName);

if ($stmt->fetch()) {
} else {
    die("No results found.");
}

$stmt->fetch();
error_log($title);
$stmt->close();
$conn->close();

$imagePath = 'project-media' . DIRECTORY_SEPARATOR . $image;
$isProjectOrganizer = ($userId === $organizerId);

?>
<html lang="en">
<head>
    <title>Sustain - Project</title>
    <link rel="stylesheet" href="project-details.css">
    </head>
<body>
<div class="info">
    <div class="head-img">
        <img src="<?php echo $imagePath ?>" alt="">
    </div>
    <h2><?php echo $title ?></h2>
    <p><?php echo $description; ?></p>
    <br>
    <div class="project-tags">
        <?php
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
            // CSS class based on Tag:
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
// Check the user role
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
    ?>
    <a href="edit-project.php?project_id=<?php echo $projectId; ?>" class="edit-btn">Edit</a>
    <?php
}
$stmt->close();
$conn->close();
?>
</div>
<?php include('footer.php'); ?>
</body>
</html>
