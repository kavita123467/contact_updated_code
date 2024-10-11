<?php
session_start();

// Initialize variables
$date = '';
$time = '';
$captcha = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set the session variables
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    $time = isset($_POST['selected_time']) ? $_POST['selected_time'] : '';
    $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';
    $session_captcha = $_SESSION['captcha_code'] ?? ''; // Get the current session captcha
    echo $date."".$time;

    // Validate the captcha
    if ($captcha !== $session_captcha) {
        echo "Invalid CAPTCHA. Please try again.";
    } else {
        // Now you can use the date and time in your PHP code
        echo "Date: $date, Time: $time";
    }
}

// Generate the CAPTCHA code if not already set
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = generateCaptchaCode(); // Ensure you have this function defined
}

$captcha = $_SESSION['captcha_code'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <script src="script.js"></script>

    <script>
        let timeLeft = 120; // Set to 2 minutes in seconds
        let interval;

        // Function to update the timer
        function updateTimer() {
            timeLeft--;
            const timerElement = document.getElementById('timer');
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `Time remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (timeLeft <= 0) {
                clearInterval(interval);  // Stop the timer
                refreshCaptcha(); // Refresh the CAPTCHA when the timer ends
                timeLeft = 120;  // Reset timer
                interval = setInterval(updateTimer, 1000); // Restart the timer
            }
        }

        // Function to refresh CAPTCHA
        function refreshCaptcha() {
            // Fetch a new CAPTCHA code from the server
            fetch('captcha.php?refresh=true')
                .then(response => response.blob()) // Expect a blob for the image
                .then(blob => {
                    // Create a URL for the new image
                    const imageUrl = URL.createObjectURL(blob);
                    document.getElementById('captcha-image').src = imageUrl; // Set new CAPTCHA image
                    console.log('Captcha refreshed successfully!');
                })
                .catch(error => console.error('Error refreshing captcha:', error));
        }

        // DOMContentLoaded Event Listener
        document.addEventListener('DOMContentLoaded', function() {
            interval = setInterval(updateTimer, 1000);  // Start the timer
            refreshCaptcha(); // Load the initial CAPTCHA
        });

        document.getElementById('refresh-captcha').addEventListener('click', function() {
            refreshCaptcha();  // Refresh the CAPTCHA
        });
    </script>

    <style>
        .circle {
            width: 100px;
            height: 45px;
            margin: 8px 0;
        }
        .refresh {
            width: 60px;
        }
        .rotateImage {
            transform: rotate(360deg);
            transition: 0.6s ease-in-out;
        }
    </style>
</head>
<body>
    <form action="check_availability.php" method="post" id="appointment-form">
        <!-- Hidden fields for date and time -->
        <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="time" value="<?php echo htmlspecialchars($time); ?>">

        <!-- Name -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <!-- Phone -->
        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required pattern="\d{10}" placeholder="Enter 10 digit phone number"><br><br>

        <!-- Captcha -->
        <label for="captcha">Captcha: <?php echo $captcha; ?></label>
        <input type="text" id="captcha" name="captcha" required><br><br>
        <img id="captcha-image" src="captcha.php" alt="CAPTCHA Image" />

        <!-- Timer Display -->
        <div id="timer">Time remaining: 2:00</div>
        <div class="circle">
            <img class="refresh" src="https://www.svgrepo.com/show/533701/refresh-cw.svg" alt="">
        </div>
        <br>
        <button type="button" id="refresh-captcha">Refresh CAPTCHA</button>

        <!-- City -->
        <label for="city">City:</label>
        <input type="text" id="city" name="city" required><br><br>

        <!-- Business -->
        <label for="business">Business:</label>
        <input type="text" id="business" name="business"><br><br>

        <!-- Submit Button -->
        <input type="submit" class="next-button" value="Save Appointment">
    </form>
</body>
</html>