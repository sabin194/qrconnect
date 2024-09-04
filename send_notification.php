<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit();
}

// Get the user data from the session
$user_email = $_SESSION['user_email']; // Assuming user's email is stored in the session
$user_name = $_SESSION['user_name'];

$qrData = json_decode(file_get_contents('php://input'), true)['qrData'];

// Set email parameters
$to = $user_email;
$subject = "QR Code Scanned Notification";
$message = "Hello $user_name,\n\nYour QR code was just scanned. Here are the details:\n\n$qrData\n\nBest regards,\nQRConnect Team";
$headers = "From: no-reply@qrconnect.com";

// Send the email
if (mail($to, $subject, $message, $headers)) {
    echo "Notification sent successfully.";
} else {
    echo "Failed to send notification.";
}
?>
