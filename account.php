<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include('header.php');
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="account.css">
    <title>Sustain - Account</title>
</head>
<body>
<div class="info">
    <h2>My Account</h2>
    <div class="user-info">
        <?php
        if (isset($_SESSION['user_id'])) {
            $conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $userId = $_SESSION['user_id'];

            // Fetch user information from the database based on the user's ID
            $query = "SELECT name, last_name, email, role, image FROM Users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $stmt->bind_result($name, $last_name, $email, $role, $image);
            $stmt->fetch();
            $stmt->close();
            $conn->close();

            echo "<p><strong>Name:</strong> $name</p>";
            echo "<p><strong>Last Name:</strong> $last_name</p>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Role:</strong> $role</p>";
        } else {
            echo "<p>Please log in to view your account information.</p>";
        }
        ?>
    </div>
    <div class="pp">
        <img src="<?php if($image){
        echo 'user-media' . DIRECTORY_SEPARATOR . $image;
       } else {
            echo 'media'. DIRECTORY_SEPARATOR . 'user-pp.jpg';
       }?>" alt="">
    </div>
    <p></p>
    <a href="project-history.php" class="btn">Project History</a>
    <p></p>
    <?php
    if ($role === 'organiser'){ ?>
        <div class="add-project-btn">
        <a href="add-project.php" class="add-project-btn">Add Project</a>
        </div>
    <?php } ?>

    <br><br><br>
    <div class="logout-btn">
        <a href="logout.php" class="btn">Log Out</a>
    </div>
</div>
<?php
    include('footer.php');
?>
</body>
</html>
