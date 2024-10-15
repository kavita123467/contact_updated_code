<?php
session_start();

// Initialize variables
$date = '';
$time = '';
$captcha = '';
$error = '';
$success = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set the variables
    $date = $_POST['date'] ?? '';
    $time = $_POST['selected_time'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    $session_captcha = $_SESSION['captcha'] ?? '';
    $captcha_expires = $_SESSION['captcha_expires'] ?? 0;
    
    // Debug output
    echo "Date: " . $date . "<br>Time: " . $time . "<br>Captcha: " . $captcha . 
         "<br>Session Captcha: " . $session_captcha . "<br>Captcha Expired: " . $captcha_expires;
    
    // Validate the captcha
    if ($captcha !== $session_captcha || time() > $captcha_expires) {
        $error = "Invalid or expired CAPTCHA. Please try again.";
    } else {
        // Process the form data
        // TODO: Add your form processing logic here
        $success = "Appointment saved successfully. Date: " . htmlspecialchars($date) . ", Time: " . htmlspecialchars($time);
        
        // Clear the used CAPTCHA
        unset($_SESSION['captcha']);
        unset($_SESSION['captcha_expires']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <style>
        .circle {
            width: 100px;
            height: 45px;
            margin: 8px 0;
        }
        .refresh {
            width: 60px;
            cursor: pointer;
        }
        .rotateImage {
            transform: rotate(360deg);
            transition: 0.6s ease-in-out;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form action="check_availability.php" method="post" id="appointment-form">
        <!-- Hidden fields for date and time -->
        <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="selected_time" value="<?php echo htmlspecialchars($time); ?>">

        <!-- Name -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <!-- Phone -->
        <label for="phone">Phone:</label>
        <input type="tel" id="phone" name="phone" required pattern="\d{10}" placeholder="Enter 10 digit phone number"><br><br>

        <!-- Captcha -->
        <label for="captcha">Captcha:</label>
        <input type="text" id="captcha" name="captcha" required><br><br>
        <img id="captcha-image" src="captcha.php" alt="CAPTCHA Image" />

        <!-- Timer Display -->
        <div id="timer">Time remaining: 2:00</div>
        <div class="circle">
            <img class="refresh" src="https://www.svgrepo.com/show/533701/refresh-cw.svg" alt="Refresh CAPTCHA" id="refresh-captcha">
        </div>

        <!-- City -->
        <label for="city">City:</label>
        <input type="text" id="city" name="city" required><br><br>

        <!-- Business -->
        <label for="business">Business:</label>
        <input type="text" id="business" name="business"><br><br>

        <!-- Submit Button -->
        <input type="submit" class="next-button" value="Save Appointment">
    </form>

    <script>
        let timeLeft = 120; // Set to 2 minutes in seconds
        let interval;

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timer').textContent = `Time remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (timeLeft <= 0) {
                clearInterval(interval);
                refreshCaptcha();
            } else {
                timeLeft--;
            }
        }

        function refreshCaptcha() {
            const captchaImage = document.getElementById('captcha-image');
            captchaImage.src = 'captcha.php?t=' + new Date().getTime();
            timeLeft = 120; // Reset timer to 2 minutes
            clearInterval(interval);
            interval = setInterval(updateTimer, 1000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            interval = setInterval(updateTimer, 1000);
            document.getElementById('refresh-captcha').addEventListener('click', function() {
                this.classList.add('rotateImage');
                refreshCaptcha();
                setTimeout(() => this.classList.remove('rotateImage'), 600);
            });
        });
    </script>
</body>
</html>