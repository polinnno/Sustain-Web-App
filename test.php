<?php
$conn = new mysqli("localhost", "root", "root", "it210_sustain", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch project data from the database
$sql = "SELECT * FROM project";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Projects</title>
    <link rel="stylesheet" href="test.css">
    <!-- Favicon -->
    <link rel="icon" href="media/circle.ico" type="image/x-icon">
    <link rel="shortcut icon" href="media/circle.ico" type="image/x-icon">

</head>
<body>

<div class="projects-rotating-gallery">
    <div class="gallery-slides">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="project-2">
                <img src="project-media/<?php echo $row["image"]; ?>" alt="Project Image">
                <h3><?php echo $row["title"]; ?></h3>
            </div>
        <?php endwhile; ?>
    </div>
    <button class="rotate-button" id="prev-button">&#9664;</button>
    <button class="rotate-button" id="next-button">&#9654;</button>
</div>

<script>
    const slidesContainer = document.querySelector('.gallery-slides');
    const projects = document.querySelectorAll('.project-2');
    const slideWidth = projects[0].offsetWidth; // Width of one project
    const totalSlides = projects.length;
    const visibleSlides = 5; // Number of slides to display at a time
    let currentIndex = 0;

    function startRotatingGallery() {
        function rotateGallery() {
            currentIndex = (currentIndex + 1) % totalSlides;
            const translateX = -currentIndex * slideWidth;
            slidesContainer.style.transform = `translateX(${translateX}px)`;
        }

        const rotateInterval = setInterval(rotateGallery, 3500);

        // Add event listeners for previous and next buttons
        document.getElementById('prev-button').addEventListener('click', () => {
            clearInterval(rotateInterval);
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            const translateX = -currentIndex * slideWidth;
            slidesContainer.style.transform = `translateX(${translateX}px)`;
        });

        document.getElementById('next-button').addEventListener('click', () => {
            clearInterval(rotateInterval);
            currentIndex = (currentIndex + 1) % totalSlides;
            const translateX = -currentIndex * slideWidth;
            slidesContainer.style.transform = `translateX(${translateX}px)`;
        });
    }

    startRotatingGallery();

</script>
</body>
</html>