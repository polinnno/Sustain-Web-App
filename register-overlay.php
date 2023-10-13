<!DOCTYPE html>
<html lang="">
<head>
    <!-- Favicon -->
    <link rel="icon" href="media/circle.ico" type="image/x-icon">
    <link rel="shortcut icon" href="media/circle.ico" type="image/x-icon">
    <title>Sustain - Register</title>
    <link rel="stylesheet" href="base.css">
    <link rel="stylesheet" href="register-overlay.css">
</head>
<body>
<div id="overlay-background" class="overlay-background"></div>
<div id="overlay" class="overlay-register">
    <div class="overlay-register-content">
        <h2>Error</h2>
        <p>This email is already in use. Please choose a different email.</p>
        <button id="overlay-close">OK</button>
    </div>
</div>

<script>
    const overlayCloseButton = document.getElementById('overlay-close');

    // Event listener for the OK button
    overlayCloseButton.addEventListener('click', () => {
        history.back();
    });
</script>
</body>
</html>