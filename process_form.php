<?php
// Turn on error reporting and display errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set the error handler function
set_error_handler('customErrorHandler');

// Custom error handler function
function customErrorHandler($errno, $errstr, $errfile, $errline)
{
    echo "Error on line $errline: $errstr";
}

// Include PHPMailer autoload file
require 'vendor/autoload.php';

/* //  Local Database configuration (ensure correct values)
$servername = "localhost";
$username_db = "root"; // Use appropriate username for database access
$password_db = ""; // Update with your database password if applicable
$database = "contact_form_db";
 */

// live Database configuration (ensure correct values)
$servername = "localhost";
$username_db = "user"; // Use appropriate username for database access
$password_db = "your_db_password"; // Update with your database password if applicable
$database = "userdb";


$conn = new mysqli($servername, $username_db, $password_db, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// SQL query to create user_visits table
$sql = "CREATE TABLE IF NOT EXISTS user_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(255) NOT NULL,
    user_agent TEXT,
    visit_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Execute SQL query
if ($conn->query($sql) === TRUE) {
    // echo "Table user_visits created successfully.<br>";
} else {
    //echo "Error creating table: " . $conn->error . "<br>";
}

// Initialize variables
$visit_count = 0;

// Check if the visit count cookie is set
if (isset($_COOKIE['visit_count'])) {
    $visit_count = $_COOKIE['visit_count'];
    $visit_count++;
} else {
    $visit_count = 1;
}

// Set the visit count cookie
setcookie('visit_count', $visit_count, time() + (86400 * 30), "/");

// Display the visit count
//echo "You have visited this website $visit_count times.<br>";

// Record user data in the database if available
$ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown'; // Get user's IP address
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown'; // Get user's user agent

// Insert user data into the database
$sql = "INSERT INTO user_visits (ip_address, user_agent) VALUES ('$ip_address', '$user_agent')";
if ($conn->query($sql) === TRUE) {
    //  echo "User data recorded successfully.";
} else {
    //echo "Error recording user data: " . $conn->error;
}

// Initialize variables
$errors = array();
$name = $email = $subject = $message = $capcha = "";

// Collect form data
$name = isset($_POST["name"]) ? $conn->real_escape_string($_POST["name"]) : "";
$email = isset($_POST["email"]) ? $conn->real_escape_string($_POST["email"]) : "";
$subject = isset($_POST["subject"]) ? $conn->real_escape_string($_POST["subject"]) : "";
$message = isset($_POST["message"]) ? $conn->real_escape_string($_POST["message"]) : "";
// Check if the captcha value is correct
$capcha = isset($_POST['capcha']) ? intval($_POST['capcha']) : ""; // Get the captcha value from the form

$num1 = isset($_POST['num1']) ? intval($_POST['num1']) : ""; // Get the first random number
$num2 = isset($_POST['num2']) ? intval($_POST['num2']) : ""; // Get the second random number

// Convert $num1 and $num2 to integers if they are not already
$num1 = intval($num1);
$num2 = intval($num2);

// Check if $num1 and $num2 are numeric
if (!is_numeric($num1) || !is_numeric($num2)) {
    // Handle the error (e.g., display an error message and exit)
    echo "Error: Invalid captcha numbers.";
    exit;
}

$correctAnswer = $num1 + $num2;
//echo "num1 </br>".$num1."num2 </br>".$num2."correct answer </br>".$correctAnswer;


if (isset($_POST['submit'])) {
    // Validate form fields
    if (empty($name)) {
        $errors[] = "Name is required.";
        echo "<script>alert('Name is required.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
        echo "<script>alert('Email is required.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
        echo "<script>alert('Invalid email format.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    }

    if (empty($subject)) {
        $errors[] = "Phone Number is required.";
        echo "<script>alert('Phone Number is required.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    }

    if (empty($message)) {
        $errors[] = "Message is required.";
        echo "<script>alert('Message is required.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    }

    if (empty($capcha)) {
        $errors[] = "Capcha is required.";
        echo "<script>alert('Capcha is required.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    } elseif ($capcha !== $correctAnswer) {
        // Display an alert if the captcha is incorrect
        echo "<script>alert('Incorrect captcha value! Please try again.');</script>";
        echo '<script>window.location.href = "index.html";</script>';
        exit;
    }

    // Proceed with the rest of the code if no errors are encountered
    if (!empty($errors)) {
        // Display validation errors
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    } else {
        // SQL query to insert data into the database
        $sql = "INSERT INTO contact_form (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

        if ($conn->query($sql) === TRUE) {
            // Data inserted successfully
            // Compose email content
            $to = "perfectiongeeks@gmail.com"; // Replace with your email address
            $fromEmail = $email;
            $fromName = $name;
            $message1 = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thank You</title>
            <style>
            / Add your CSS styles here /
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
            }
            .header img {
                max-width: 200px;
            }
            .content {
                text-align: center;
                margin-bottom: 20px;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                color: #888888;
            }
            </style>
            </head>
            <body>
            <div class="container">
                <div class="header">
                    <img src="/Users/shreybhardwaj/Desktop/logo(1).png" alt="PerfectionGeeks Logo">
                </div>
                <div class="content">
                    <p>Dear,Host ,</p>
                    <p>' . $name . ' want to contact to you.</p>
                    <p> Thease are the proper Information of the ' . $name . '
                    <p> Contact Number ' . $subject . '</p>
                    <p> Meassage :- ' . $message . '</p>
                    <p><strong>Thank You</strong></p>
                    <p>' . $name . '</p>
                </div>
                <div class="footer">
                    <p>Copyright &copy; 2020 PerfectionGeeks Technologies | All Rights Reserved</p>
                    <p><a href="mailto:sales@perfectiongeeks.com">sales@perfectiongeeks.com</a></p>
                </div>
            </div>
            </body>
            </html>
            ';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();                                      // Send using SMTP
                $mail->Host = 'smtp.gmail.com';                       // Set the SMTP server to send through
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'perfectiongeeks@gmail.com';                     //SMTP username
                $mail->Password = 'odloplohkpmuyqhq';             // SMTP password
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
                $mail->Port = 587;                                    // TCP port to connect to

                // Recipients
                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($to);                               // Add a recipient

                // Email content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body = $message1;

                $mail->send();
                //          echo "Your message has been sent successfully and saved to the database!";
            } catch (Exception $e) {
                //        echo "Error sending email: {$mail->ErrorInfo}";
            }

            // Thankyou Mail to user 
            if (isset($_POST['submit'])) {
                $to1 = $email; // Replace with your email address
                $fromEmail1 = "noreplyperfectiongeeks@gmail.com";
                $fromName1 = "Perfectiongeeks";
                $subject1 = "Thank you mail";
                $message2 = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Thank You</title>
                </head>
                <body>
                <div style="max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 5px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <div style="background-color:#00718c;width:100%;height:10px;margin-bottom:30px"></div>
                    <div style=" display: flex;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                    margin: 50px 0;">
                        <img src="img_talk.png" alt="" width="70%">
                    </div>
                    <div>
                        <div>
                            <h2 style="text-align:center">Thank you for showing your interest in <br> <span
                                    style="color:#00718c;font-size:40px">PerfectionGeeks</span></h2>
                            <p style="font-size:16px;margin-top:54px">Dear <span>' . $name . '</span></p>
                            <p style="font-size:16px;line-height:20px;letter-spacing:0.5px;">We have received your request. We will
                                be soon get back to you on your inquiry. </p>
                            <p style="font-size:16px;line-height:20px;letter-spacing:0.5px;"> However you can watch our videos and
                                stay updated about latest technology news from your Youtube Channel. </p>
                            <a href="https://www.youtube.com/channel/UCBxMeaaNowv1qr0j3OxgS-A"
                                style="color:blue;text-decoration:none" target="_blank">How PerfectionGeeks Work!</a>
                            <p style="font-size:16px;line-height:20px;letter-spacing:0.5px;"> We look forward to speaking with you
                                soon!</p>
                            <p style="font-size:16px;line-height:20px;letter-spacing:0.5px;" s>Thank You</p>
                            <p style="font-size:16px;line-height:20px;letter-spacing:0.5px;
                            font-weight:700;margin-bottom:54px;">
                                PerfectionGeeks</p>
                        </div>
                        <div style="background-color:rgba(0,0,0,0.9);padding:10px 0px">
                            <div style="margin-top:30px">
                                <img style="width:25%;margin-left:17vw" src="">
                            </div>
                            <div style="color:white;text-align:center;margin-top:20px">
                                <p>Copyright © 2020 PerfectionGeeks Technologies | All Rights Reserved</p>

                                <p style="color:#00718c"><a href="mailto:sales@perfectiongeeks.com"
                                        mailto:target="_blank">sales@perfectiongeeks.com</a></p>
                                <table style="margin:auto">
                                    <tbody>
                                        <tr>
                                            <td><a href="https://www.youtube.com/channel/UCBxMeaaNowv1qr0j3OxgS-A" target="_blank">
                                                    <img src="/Applications/Contact/youtube_mailer.png" style=" width:30px;margin-right:10px;"> </a></td>
                                            <td><a href="https://www.facebook.com/perfectiongeeks/" target="_blank">
                                                    <img src="facebook_mailer.png" style=" width:30px;margin-right:10px;"></a></td>
                                            <td><a href="https://in.linkedin.com/company/perfectiongeeks-technologies"
                                                    target="_blank">
                                                    <img src="linkedin_mailer.png" style=" width:30px;margin-right:10px;"></a></td>
                                            <td><a href="https://twitter.com/perfectiongeeks" target="_blank">
                                                    <img src="tiwiter_mailer.png" style=" width:30px;margin-right:10px;"></a></td>
                                            <td><a> <img src="email_mailer.png" style=" width:30px;margin-right:10px;"></a></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div style="  text-align: center;
                        margin-top: 20px;
                        color: #888888;"> 
                            <p>Copyright &copy; 2020 PerfectionGeeks Technologies | All Rights Reserved</p>
                            <p><a href="mailto:sales@perfectiongeeks.com">sales@perfectiongeeks.com</a></p>
                        </div>
                    </div>
                </body>
                </html>';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();                                      // Send using SMTP
                    $mail->Host = 'smtp.gmail.com';                       // Set the SMTP server to send through
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = 'perfectiongeeks@gmail.com';                     //SMTP username
                    $mail->Password = 'odloplohkpmuyqhq';              // SMTP password
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
                    $mail->Port = 587;                                    // TCP port to connect to

                    // Recipients
                    $mail->setFrom($fromEmail1, $fromName1);
                    $mail->addAddress($to1);                              // Add a recipient

                    // Email content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = $subject1;
                    $mail->Body = $message2;

                    $mail->send();
                    //          echo "Your message has been sent successfully and saved to the database!";
                    header("Location: https://www.perfectiongeeks.com/thank-you");
                } catch (Exception $e) {
                    //        echo "Error sending thank-you email: {$mail->ErrorInfo}";
                }
            } else {
                // Error inserting data
                //  echo "Unable to save data.";
            }
        }
    }
}

// Close connection
$conn->close();