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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sustain - Home</title>
    <link rel="stylesheet" href="home.css">
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
            <a href="contact.html">Contact Form</a>
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
    <a href="logout.php">Log Out</a>
</div>


<div class="projects-rotating-gallery">
    <div class="gallery-slides-2">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="project-2">
                <a href="project-details.php?project_id=<?php echo $row['id']; ?>">
                    <img src="project-media/<?php echo $row["image"]; ?>" alt="Project Image">
                </a>
                <h3><?php echo $row["title"]; ?></h3>
            </div>
        <?php endwhile; ?>
    </div>
    <button class="rotate-button" id="prev-button">&#9664;</button>
    <button class="rotate-button" id="next-button">&#9654;</button>
</div>

<div class="home-greeting-container">
    <p class="home-greeting">
        Whether you're eager to lend a helping hand or seeking driven
        individuals to turn your altruistic dreams into reality, this
        is where aspirations take flight. Join us in forging connections,
        sharing dreams, and turning compassion into impactful action.
        <br>Together, we have the power to inspire, uplift, and make
        a difference. Welcome to a place where kindness finds its purpose and the
        world transforms, one act of volunteering at a time.
    </p>
</div>

<div class="about-us">
    <h2>About Us</h2>
    <div class="about-us-container">
        <p>Welcome to Sustain! We're here to connect individuals who are passionate
            about making a difference with organizations that are driving positive change. Whether you're an eager
            volunteer looking to contribute your time and skills, or an organizer seeking dedicated individuals to
            help bring your projects to life, our platform is your go-to destination.
            <br>
            Our team understands the power of collective action. We're volunteers and organizers ourselves,
            driven by the belief that every small effort can create a ripple of impact. Our platform simplifies
            the process, making it easy for you to find opportunities that resonate with your values or recruit
            enthusiastic volunteers for your initiatives.
            <br>
            With a user-friendly interface and intuitive features, you can browse through various projects,
            filter based on your interests and availability, and seamlessly get involved. We're all about
            efficiency and effectiveness, ensuring that your journey from signing up to making a difference
            is as smooth as possible.
            <br>
            Join us in building a global community that stands for positive change. Together, we amplify
            our impact and inspire others to take part. Thank you for being a part of our mission to
            create a better world through the power of volunteering."
        </p>
    </div>

</div>
<section class="gallery-container">
    <div class="gallery-slides">
        <div class="gallery-slide" id="slide-1">
            <img src="media/home-gallery/01.jpg" alt="Slide 1" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-2">
            <img src="media/home-gallery/02.jpg" alt="Slide 2" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-3">
            <img src="media/home-gallery/03.jpg" alt="Slide 3" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-4">
            <img src="media/home-gallery/04.jpg" alt="Slide 4" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-5">
            <img src="media/home-gallery/05.jpg" alt="Slide 5" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-6">
            <img src="media/home-gallery/06.jpg" alt="Slide 6" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-7">
            <img src="media/home-gallery/07.jpg" alt="Slide 7" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-8">
            <img src="media/home-gallery/08.jpg" alt="Slide 8" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-9">
            <img src="media/home-gallery/09.jpg" alt="Slide 9" class="gallery-image main-slide">
        </div>
        <div class="gallery-slide" id="slide-10">
            <img src="media/home-gallery/10.jpg" alt="Slide 10" class="gallery-image main-slide">
        </div>


    </div>
</section>
<script>
    /*
    Moving Gallery
     */
    const slidesContainer = document.querySelector('.gallery-slides');
    const slides = document.querySelectorAll('.gallery-slide');
    const slideWidth = slides[0].offsetWidth; // Assuming all slides have the same width

    function startContinuousScrolling() {
        let currentPosition = 0;

        function moveSlides() {
            currentPosition -= 1; // Move by 1 pixel at a time
            slidesContainer.style.transform = `translateX(${currentPosition}px)`;

            // Reset position to the right when it reaches the end
            if (currentPosition <= -slideWidth) {
                currentPosition = slideWidth;
            }

            requestAnimationFrame(moveSlides); // Continue animation
        }
        // Start the continuous scrolling animation
        moveSlides();
    }

    startContinuousScrolling();

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
    Rotating Project Gallery:
     */
    const slidesContainer_2 = document.querySelector('.gallery-slides-2');
    const projects = document.querySelectorAll('.project-2');
    const slideWidth_2 = projects[0].offsetWidth; // Width of one project
    const totalSlides = projects.length;
    const visibleSlides = 7; // Number of slides to display at a time
    let currentIndex = 0;

    function startRotatingGallery() {
        function rotateGallery() {
            currentIndex = (currentIndex + 1) % totalSlides;
            const translateX = -currentIndex * slideWidth_2;
            slidesContainer_2.style.transform = `translateX(${translateX}px)`;
        }

        const rotateInterval = setInterval(rotateGallery, 3500);

        // Add event listeners for previous and next buttons
        document.getElementById('prev-button').addEventListener('click', () => {
            clearInterval(rotateInterval);
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            const translateX = -currentIndex * slideWidth_2;
            slidesContainer_2.style.transform = `translateX(${translateX}px)`;
        });

        document.getElementById('next-button').addEventListener('click', () => {
            clearInterval(rotateInterval);
            currentIndex = (currentIndex + 1) % totalSlides;
            const translateX = -currentIndex * slideWidth_2;
            slidesContainer_2.style.transform = `translateX(${translateX}px)`;
        });
    }
    startRotatingGallery();
</script>
</body>
</html>
