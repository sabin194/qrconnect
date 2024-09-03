<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";  
$password = "";     
$dbname = "qrsignup";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginMessage = ""; 

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // Query to fetch user data
    $sql = "SELECT id, name, password FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: qrcode.php"); 
            exit(); 
        } else {
            $loginMessage = "Invalid email or password.";
        }
    } else {
        $loginMessage = "No user found with that email.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./login.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login1.php" method="POST">
            <div class="inputBox">
                <input type="email" name="email" required>
                <label for="email">Email</label>
            </div>
            <div class="inputBox">
                <input type="password" name="password" required>
                <label for="password">Password</label>
            </div>
            <div class="inputBox">
                <input type="submit" value="Login">
            </div>
        </form>
        <?php if (!empty($loginMessage)): ?>
            <p class="message"><?php echo $loginMessage; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
