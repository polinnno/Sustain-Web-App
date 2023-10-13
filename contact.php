<?php
session_start();

function generateRandomId($length = 3)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

include('header.php');

$userId = ($_SESSION['user_id']);
$query = "SELECT name, last_name FROM Users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$stmt->bind_result($name, $last_name);
$stmt->fetch();
$stmt->close();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageContent = $_POST['message'];
    $senderId = $userId;

    $randomId = generateRandomId();
    $query = "SELECT id FROM message WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $randomId);
    $stmt->execute();
    $stmt->store_result();

    while ($stmt->num_rows > 0) {
        $randomId = generateRandomId();
        $stmt->bind_param("s", $randomId);
        $stmt->execute();
        $stmt->store_result();
    }
    $query = "INSERT INTO message (id, sender_id, message, sent_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $randomId, $senderId, $messageContent);

    if ($stmt->execute()) {
        $conn->close();
    }
    header('Location: home.php');
    exit;
}
?>
<html lang="en">
<head>
    <link rel="stylesheet" href="contact.css">
    <title>Sustain - Contact</title>
</head>
<body>
<div class="info">
    <h2>Contact Form</h2>

    <div class="map">
        <!-- Map embed (Google Maps) -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3147.588353420284!2d-122.31266374825798!3d37.91668343188164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085790176577d37%3A0x10dfa2251b332c79!2s1234%20Elm%20St%2C%20El%20Cerrito%2C%20CA%2094530%2C%20USA!5e0!3m2!1sen!2srs!4v1696182619281!5m2!1sen!2srs" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <div class="all-info">
        <div class="user-info">
            <p><strong>Name:</strong> <?php echo $name?></p>
            <p><strong>Last name:</strong> <?php echo $last_name?></p>
            <p><strong>Message:</strong></p>
        </div>
        <div class="company-info">
            <h3>Our address:</h3>

            <p class="address">
                1234 Elm Street <br>Cityville, ST 56789 <br>
                Phone: (123) 456-7890<br>Email: info@example.com
            </p>
        </div>
    </div>
    <form action="contact.php" method="POST">
        <label>
            <textarea name="message" rows="4" cols="50" required></textarea>
        </label>
        <button type="submit">Send</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>