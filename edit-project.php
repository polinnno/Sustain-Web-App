<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_log($_SESSION['user_id']);


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['project_id'])) {
    header('Location: home.php');
    exit;
}

$projectId = $_GET['project_id'];

$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('header.php');

$query = "SELECT * FROM project WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 1) {
    $project = $result->fetch_assoc();
    if ($_SESSION['user_id'] !== $project['organizer_id']) {
        header('Location: home.php');
        exit;
    }
} else {
    header('Location: error.php');
    exit;
}


$selectedTags = [];
$tagsQuery = "SELECT skill_name FROM skills WHERE project_id = ?";
$stmt = $conn->prepare($tagsQuery);
$stmt->bind_param("s", $projectId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $selectedTags[] = $row['skill_name'];
}
$stmt->close();
$conn->close();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

    if ($conn->connect_error) {
        error_log('connection failed');
        die("Connection failed: " . $conn->connect_error);
    }


    $uploadDir = 'user-media' . DIRECTORY_SEPARATOR;
    if (substr($uploadDir, -1) !== DIRECTORY_SEPARATOR) {
        $uploadDir .= DIRECTORY_SEPARATOR;
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];

        $targetFilePath = 'project-media' . DIRECTORY_SEPARATOR . $fileName;
        error_log($targetFilePath);

        if (move_uploaded_file($tempFilePath, $targetFilePath)) {
        } else {
            echo 'Error uploading file.';
        }
    } else {
        $fileName = $project['image'];
    }


    // Check Tags:
    if (isset($_POST['tags']) && is_array($_POST['tags'])) {
        $selectedTags = $_POST['tags'];
    } else {
        $selectedTags = [];
    }



    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $place = $_POST['place'];
    $organizer_id = $_SESSION['user_id'];

    $sql = "UPDATE project SET title = ?, description = ?, start_date = ?, end_date = ?, place = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $title, $description, $start_date, $end_date, $place, $fileName, $projectId);

    if ($stmt->execute()) {
        echo "Project updated successfully!";
        error_log("the new title is:");
        error_log($title);
    } else {
        echo "Error updating project: " . $stmt->error;
    }


    $deleteTagsQuery = "DELETE FROM skills WHERE project_id = ?";
    $stmtDeleteTags = $conn->prepare($deleteTagsQuery);
    $stmtDeleteTags->bind_param("s", $projectId);
    $stmtDeleteTags->execute();
    $stmtDeleteTags->close();
    foreach ($selectedTags as $tag) {
        $sql = "INSERT INTO skills (project_id, skill_name) VALUES ('$projectId', '$tag')";
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error inserting tag: " . $conn->error;
        }
    }


    $stmt->close();
    $conn->close();

    // Redirect to the project-details.php page with the project_id
    header('Location: project-details.php?project_id=' . $projectId);
}
?>

<html lang="en">
<head>
    <title>Sustain - Edit</title>
    <link rel="stylesheet" href="edit-project.css">
    </head>
<body>
<div class="add-project-container">

    <h2>Edit Project</h2>
    <form action="edit-project.php?project_id=<?php echo $projectId; ?>" method="POST" enctype="multipart/form-data">
        <div class="input-group">
            <label for="title">Title:</label>
            <p></p>
            <input type="text" id="title" name="title" value="<?php echo $project['title']; ?>" required>
        </div>

        <div class="input-group">
            <label for="description">Description:</label>
            <p></p>

            <textarea id="description" name="description"  rows="4" cols="50" class="textarea"  required><?php echo $project['description']; ?></textarea>
        </div>
        <p></p>
        <p></p>

        <br><br><br>
        <div class="input-group">
            <label for="start_date">Start Date:</label>
            <p></p>
            <input type="date" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>" required>
        </div>

        <div class="input-group">
            <label for="end_date">End Date:</label>
            <p></p>
            <input type="date" id="end_date" name="end_date" value="<?php echo $project['end_date']; ?>"required>
        </div>

        <div class="input-group">
            <label for="place">Place:</label>
            <p></p>
            <input type="text" id="place" name="place" value="<?php echo $project['place']; ?>"required>
        </div>

        <div class="input-group">
            <label for="image">Image:</label>
            <p></p>
            <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png">
        </div>
        <div class="input-group">
            <br>
            <br>
            <label>Tags:</label>
            <p></p>
            <div class="checkbox-group">
                <?php
                // List of available tags
                $availableTags = ["Writing", "Teaching", "Event Planning", "Communication", "Technical", "Leadership", "Artistic", "Language", "Hands-on Tasks", "Nature", "Other"];

                foreach ($availableTags as $tag) {
                    $isChecked = in_array($tag, $selectedTags) ? 'checked' : '';
                    echo '<label for="tag-' . $tag . '"><input type="checkbox" id="tag-' . $tag . '" name="tags[]" value="' . $tag . '" ' . $isChecked . '> ' . $tag . '</label><br>';
                }
                ?>
            </div>
        </div>
        <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
        <button type="submit">Save</button>
    </form>
</div>
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
<?php include('footer.php'); ?>
</body>
</html>