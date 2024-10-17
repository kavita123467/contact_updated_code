<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include PHPMailer autoload file
require 'vendor/autoload.php';

// Database credentials
$host = 'localhost';
$db = 'contact_form_db';
$user = 'newuser';
$pass = 'root123';

// Retrieve the posted values from the form
if (isset($_POST['date']) && isset($_POST['selected_time'])) {
    $date_in_ist = $_POST['date'] ?? null;
    $time_in_ist = $_POST['selected_time'] ?? null;
$date = $_POST['date'] ?? null;
$time = $_POST['selected_time'] ?? null;
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$phone = $_POST['phone'] ?? null;
$city = $_POST['city'] ?? null;
$business = $_POST['business'] ?? null;
$captcha = $_POST['captcha'] ?? null;

// Validation
$errors = [];

// Check if required fields are not empty
if (empty($date)) $errors[] = 'Date is required.';
if (empty($time)) $errors[] = 'Time is required.';
if (empty($name)) $errors[] = 'Name is required';
if (empty($email)) $errors[] = 'Email is required';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
if (empty($phone)) $errors[] = 'Phone is required';
if (empty($captcha)) $errors[] = 'captcha is required';

// Format date and time
// if (!empty($date) && !empty($time)) {
//     $datetime = DateTime::createFromFormat('Y-m-d H:i', "$date $time", new DateTimeZone('Asia/Kolkata'));
//     if ($datetime && $datetime->format('Y-m-d H:i') === "$date $time") {
//         $time_in_ist = $datetime->format('H:i');
//         $date_in_ist = $datetime->format('Y-m-d');
//     } else {
//         $errors[] = 'Invalid date or time format.';
//     }
// } else {
//     $date_in_ist = $date;
//     $time_in_ist = $time;
// }

// Disable past date selection
$today = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
$today_date = $today->format('Y-m-d');
if (!empty($date_in_ist) && $date_in_ist < $today_date) {
    $errors[] = 'Cannot select a past date.';
}

// Captcha Validation
if (empty($captcha)) {
    $errors[] = 'Captcha is required.';
} elseif (!isset($_SESSION['captcha'])) {
    $errors[] = 'Captcha session is not set. Please reload the page and try again.';
} elseif (strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
    $errors[] = 'Invalid captcha. Please try again.';
} else {
    unset($_SESSION['captcha']); // Clear captcha after successful validation to prevent reuse
}

// Check if there are any errors
if (!empty($errors)) {
    $response = array('status' => 'error', 'message' => implode('<br>', $errors));
    echo json_encode($response);
    exit;
}

// Proceed if there are no errors (valid captcha and form data)
$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    $response = array('status' => 'error', 'message' => 'Connection failed: ' . $mysqli->connect_error);
    echo json_encode($response);
    exit;
}

// Check if the selected time slot is already booked
$check_query = "SELECT * FROM contact_form WHERE date = ? AND time = ? AND booked = 1";
$check_stmt = $mysqli->prepare($check_query);
$check_stmt->bind_param('ss', $date_in_ist, $time_in_ist);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $response = array('status' => 'error', 'message' => 'The selected time slot is already booked. Please choose another time.');
    echo json_encode($response);
    exit;
}

// Insert appointment into the database
$query = "INSERT INTO contact_form (date, time, name, email, phone, city, business, booked) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
$stmt = $mysqli->prepare($query);

if ($stmt) {
    $stmt->bind_param('sssssss', $date_in_ist, $time_in_ist, $name, $email, $phone, $city, $business);
    echo "inserted";
    var_dump($date_in_ist, $time_in_ist, $name, $email, $phone, $city, $business);



    if ($stmt->execute()) {
        // Send confirmation email to host
        echo "mail";
        $to = "perfectiongeeks@gmail.com";
        $fromEmail = $email;
        $fromName = $name;
            $message1 = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>New Appointment Request</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { text-align: center; margin-bottom: 20px; }
                    .content { margin-bottom: 20px; }
                    .footer { text-align: center; margin-top: 20px; color: #888888; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <img src="https://www.example.com/logo.png" alt="PerfectionGeeks Logo">
                    </div>
                    <div class="content">
                        <p>Dear Host,</p>
                        <p>' . htmlspecialchars($name) . ' has booked an appointment. Details are:</p>
                        <p>Date: ' . htmlspecialchars($date_in_ist) . '</p>
                        <p>Time: ' . htmlspecialchars($time_in_ist) . '</p>
                        <p>Phone: ' . htmlspecialchars($phone) . '</p>
                        <p>Business: ' . htmlspecialchars($business) . '</p>
                    </div>
                    <div class="footer">
                        <p>Copyright &copy; 2024 PerfectionGeeks Technologies | All Rights Reserved</p>
                        <p><a href="mailto:sales@perfectiongeeks.com">sales@perfectiongeeks.com</a></p>
                    </div>
                </div>
            </body>
            </html>
            ';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'kavitaperfectiongeeks@gmail.com';
                $mail->Password = 'vtghjxkjqqimrbqn';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
    
                $mail->setFrom($fromEmail, $fromName);
                $mail->addAddress($to);
    
                $mail->isHTML(true);
                $mail->Subject = "New Appointment Request";
                $mail->Body = $message1;
    
                $mail->send();
                echo "mailsend";
            } catch (Exception $e) {
                error_log("Error sending email to host: " . $mail->ErrorInfo);
            }
    
            // Send thank you email to user
            $to1 = $email;
            $fromEmail1 = "noreplyperfectiongeeks@gmail.com";
            $fromName1 = "Perfectiongeeks";
            $subject1 = "Thank you for your appointment request";
            $message2 = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Thank You</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #00718c; height: 10px; margin-bottom: 30px; }
                    .content { text-align: center; }
                    .footer { background-color: rgba(0,0,0,0.9); padding: 20px 0; color: white; text-align: center; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header"></div>
                    <div class="content">
                        <h2>Thank you for showing your interest in <br> <span style="color:#00718c;font-size:40px">PerfectionGeeks</span></h2>
                        <p>Dear ' . htmlspecialchars($name) . ',</p>
                        <p>We have received your appointment request. We will get back to you soon regarding your inquiry.</p>
                        <p>You can watch our videos and stay updated about the latest technology news from our YouTube Channel.</p>
                        <a href="https://www.youtube.com/channel/UCBxMeaaNowv1qr0j3OxgS-A" style="color:blue;text-decoration:none" target="_blank">How PerfectionGeeks Work!</a>
                        <p>We look forward to speaking with you soon!</p>
                        <p>Thank You</p>
                        <p><strong>PerfectionGeeks</strong></p>
                    </div>
                    <div class="footer">
                        <p>Copyright © 2024 PerfectionGeeks Technologies | All Rights Reserved</p>
                        <p><a href="mailto:sales@perfectiongeeks.com" style="color:#00718c;">sales@perfectiongeeks.com</a></p>
                        <!-- Add social media icons here -->
                    </div>
                </div>
            </body>
            </html>
            ';

            $mail2 = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail2->isSMTP();
            $mail2->Host = 'smtp.gmail.com';
            $mail2->SMTPAuth = true;
            $mail2->Username = 'perfectiongeeks@gmail.com';
            $mail2->Password = 'odloplohkpmuyqhq';
            $mail2->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail2->Port = 587;

            $mail2->setFrom($fromEmail1, $fromName1);
            $mail2->addAddress($to1);

            $mail2->isHTML(true);
            $mail2->Subject = $subject1;
            $mail2->Body = $message2;

            $mail2->send();

            // Success response sent after both emails
            $response = array('status' => 'success', 'message' => 'Appointment booked successfully!');
            echo json_encode($response);

            // Redirect to thank you page
            header("Location: https://www.perfectiongeeks.com/thank-you");
            exit(); // Ensure no further script execution
        } catch (Exception $e) {
            error_log("Error sending thank you email: " . $mail2->ErrorInfo);
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Error: ' . $stmt->error);
        echo json_encode($response);
        exit;
    }
} else {
    $response = array('status' => 'error', 'message' => 'Error: ' . $mysqli->error);
    echo json_encode($response);
    exit;
}
}
else {
    die(json_encode(array('status' => 'error', 'message' => 'Required fields are missing')));
}

$check_stmt->close();
if (isset($stmt)) $stmt->close();
$mysqli->close();