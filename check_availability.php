<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Retrieve the posted values from the form
$date = $_POST['date'];
$time = $_POST['time'];

// Include PHPMailer autoload file
require 'vendor/autoload.php';

// Database credentials
$host = 'localhost';   
$db = 'contact_form_db';   
$user = 'newuser';   
$pass = 'root123';   

// Create a new MySQLi connection
$mysqli = new mysqli($host, $user, $pass, $db);  

// Check connection
if ($mysqli->connect_error) {   
    $response = array('status' => 'error', 'message' => 'Connection failed: ' . $mysqli->connect_error);  
    echo json_encode($response);  
    exit;   
}
// $date = $_POST['date'] ?? null;
// $time = $_POST['time'] ?? null;
// Retrieve form data safely
$name = $_POST['name'] ?? null;   
$email = $_POST['email'] ?? null;   
$phone = $_POST['phone'] ?? null;   
$city = $_POST['city'] ?? null;   
$business = $_POST['business'] ?? null;   
$captcha = $_POST['captcha'] ?? null;  // User inputted captcha

// Validation
$errors = [];

// Check if date and time inputs are not empty
if (empty($date)) {
    $errors[] = 'Date is required.';
}
if (empty($time)) {
    $errors[] = 'Time is required.';
}

// // Check and format the date and time using expected format (Y-m-d for date and H:i for time)
// if (!empty($date) && !empty($time)) {
//     // Example: Expecting date in format Y-m-d (e.g., 2024-10-10) and time in H:i (e.g., 14:30)
//     $datetime = DateTime::createFromFormat('Y-m-d H:i', "$date $time", new DateTimeZone('Asia/Kolkata'));
    
//     // Check if the date and time format is valid
//     if ($datetime && $datetime->format('Y-m-d H:i') === "$date $time") {
//         $datetime->setTimezone(new DateTimeZone('Asia/Kolkata'));
//         $time_in_ist = $datetime->format('H:i');
//         $date_in_ist = $datetime->format('Y-m-d');
//     } else {
//         // Invalid date or time format
//         $errors[] = 'Invalid date or time format. Please use the correct format (Y-m-d for date, H:i for time).';
//     }
// }

// Check if name and email are provided
if (empty($name)) {   
    $errors[] = 'Name is required';   
}
if (empty($email)) {   
    $errors[] = 'Email is required';   
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {   
    $errors[] = 'Invalid email format';   
}
if (empty($phone)) {   
    $errors[] = 'Phone is required';   
}

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
    unset($_SESSION['captcha']);  // Clear captcha after successful validation to prevent reuse
}

// Check if there are any errors
if (!empty($errors)) {   
    $response = array('status' => 'error', 'message' => implode('<br>', $errors));  
 echo json_encode($response);  
    exit;   
}

// Proceed if there are no errors (valid captcha and form data)
if (empty($errors)) {  
    // Check if the selected time slot is already booked
    $check_query = "SELECT * FROM contact_form WHERE date = ? AND time = ? AND booked = 1";  
    $check_stmt = $mysqli->prepare($check_query);  
    $check_stmt->bind_param('ss', $date_in_ist, $time_in_ist);  
    $check_stmt->execute();  
    $check_result = $check_stmt->get_result();  

    if ($check_result->num_rows > 0) {  
        $response = array('status' => 'error', 'message' => 'The selected time slot is already booked. Please choose another time.');  
        echo json_encode($response);  
    } else {  
        // Insert appointment into the database
        $query = "INSERT INTO contact_form (date, time, name, email, phone, city, business, booked) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";  
        $stmt = $mysqli->prepare($query);  

        if ($stmt) {  
            $stmt->bind_param('sssssss', $date_in_ist, $time_in_ist, $name, $email, $phone, $city, $business);  

            if ($stmt->execute()) {  
                $response = array('status' => 'success', 'message' => 'Appointment booked successfully!');  
                echo json_encode($response);

                // Send confirmation email to host
                $to = "perfectiongeeks@gmail.com"; 
                $subject = "New Appointment Request";
                $message = '
                <html>
                <body>
                <p>Dear Host,</p>
                <p>' . $name . ' has booked an appointment. Details are:</p>
                <p>Date: ' . $date_in_ist . '</p>
                <p>Time: ' . $time_in_ist . '</p>
                <p>Phone: ' . $phone . '</p>
                <p>Business: ' . $business . '</p>
                </body>
                </html>';

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();                                      
                    $mail->Host = 'smtp.gmail.com';                       
                    $mail->SMTPAuth = true;                               
                    $mail->Username = 'perfectiongeeks@gmail.com';                    
                    $mail->Password = 'odloplohkpmuyqhq';             
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;                                  
                    $mail->setFrom($email, $name);
                    $mail->addAddress($to);
                    $mail->isHTML(true);                                  
                    $mail->Subject = $subject;
                    $mail->Body = $message;
                    $mail->send();
                } catch (Exception $e) {
                    // Handle mail error
                    $response = array('status' => 'error', 'message' => 'Mail could not be sent.');  
                    echo json_encode($response);  
                }
            } else {  
                $response = array('status' => 'error', 'message' => 'Error: ' . $stmt->error);  
                echo json_encode($response);  
            }  
        } else {  
            $response = array('status' => 'error', 'message' => 'Error: ' . $mysqli->error);  
            echo json_encode($response);  
        }  
    }

    $check_stmt->close();  
    $stmt->close();  
    $mysqli->close();  
}