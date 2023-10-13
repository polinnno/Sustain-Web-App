<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// Database connection
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include('header.php');

$userId = $_SESSION['user_id'];

if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
} elseif (isset($_POST['project_id'])) {
    $projectId = $_POST['project_id'];
}


$query = "SELECT u.id, u.name, u.last_name
        FROM users u
        INNER JOIN participation p ON u.id = p.user_id
        WHERE p.project_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$result = $stmt->get_result();

if (isset($_POST['delete'])) {
    $participantId = $_GET['participant_id'];
    error_log($participantId);
    error_log($projectId);
    $deleteQuery = "DELETE FROM participation WHERE user_id = ? AND project_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("ss", $participantId, $projectId);
    if ($deleteStmt->execute()) {
        header('Location: participants.php?project_id='. $projectId);
    } else {
        echo "Error removing participant: " . $deleteStmt->error;
    }
}

$conn->close();
?>

<html lang="en">
<head>
    <link rel="stylesheet" href="participants.css">
    <title>Participant List</title>
</head>
<body>
<div class="info">
    <h2>Participant List</h2>

    <?php
    while ($row = $result->fetch_assoc()) {

        echo '<div class="details">';
        echo '<div class="user">';
        echo '<p>' . $row["name"] . ' ' .  $row["last_name"] . '</p>';
        echo '</div>';
        echo '<div class="button-container">';
            ?>
            <form action="participants.php?participant_id=<?php echo $row['id']; ?>"
                  method="POST" enctype="multipart/form-data">
                <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
                <!-- Remove button -->
                <button class="delete-button" type="submit" name="delete" onclick="return confirm
            ('Are you sure you want to delete this participant?')">Remove</button>
            </form>
            <?php
            echo '</div>';
        echo '</div>';
    }
    ?>
    <br>
    <br>
</div>
<?php include('footer.php'); ?>
</body>
</html>