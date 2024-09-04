//final ko final
<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user's name from session
$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

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

// Handle form submission for QR code generation
$qr_generated = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);

    // Combine details into a single string for QR code data, each on a new line
    $qrcode_data = $name . "\n" . $address . "\n" . $contact . "\n" . $email . "\n" . $phone;

    // Insert the QR code data into the database
    $sql = "UPDATE users SET qr_data='$qrcode_data' WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        $qr_generated = true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR Code Generator & Decoder</title>
    <link rel="stylesheet" href="qrcode.css">
    <!-- Load QRious library -->
    <script src="https://cdn.jsdelivr.net/npm/qrious@latest/dist/qrious.min.js"></script>
    <!-- Load jsQR library for QR code decoding -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
</head>
<body>
    <section>
        <!-- Grid of spans for background animation -->
        <?php for ($i = 0; $i < 100; $i++): ?>
            <span></span>
        <?php endfor; ?>

        <header>
            <h1>QR Code Generator</h1>
            <div class="username">Welcome, <?php echo htmlspecialchars($user_name); ?>! </div>
            <div> <a class="logout" href="logout.php">Logout</a> </div>
        </header>

        <main>
            <!-- QR Code Generation -->
            <h2 style="color:green;">Enter your details to generate a QR code</h2>
            <form id="qr-generation-form" method="POST">
                <input type="text" id="name" name="name" placeholder="Name" required /><br />
                <input type="text" id="address" name="address" placeholder="Address" required /><br />
                <input type="text" id="contact" name="contact" placeholder="Contact" required /><br />
                <input type="email" id="email" name="email" placeholder="Email" required /><br />
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" required /><br />
                <input type="submit" value="Generate QR Code" />
            </form><br />
            
            <div id="qr-container" style="text-align: center;">
                <canvas id="qr-code" width="150" height="150"></canvas>
            </div>

            <!-- Display QR code if generated -->
            <?php if ($qr_generated): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const qrData = <?php echo json_encode($qrcode_data); ?>;
                        const qr = new QRious({
                            element: document.getElementById('qr-code'),
                            value: qrData,
                            size: 150, // Adjusted size for better appearance
                            background: '#ffffff',
                            foreground: '#000000',
                            level: 'L'
                        });
                    });
                </script>
            <?php endif; ?>

            <!-- QR Code Decoding -->
            <h2 style="color:green;">Upload a QR code image to decode</h2>
            <div class="custom-file-input">
                <input type="file" id="qr-image" accept="image/*" />
                <label for="qr-image">Choose Image</label>
            </div>
            <canvas id="qr-canvas" style="display:none;"></canvas>
            
            <!-- This is where the decoded QR code data will be displayed -->
            <div id="qr-result" style="margin-top: 20px; font-weight: bold; color: blue;"></div>
        </main>
    </section>

    <script>
        // QR Code Decoding
        document.getElementById('qr-image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                console.log('File selected:', file);
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = new Image();
                    img.onload = function() {
                        console.log('Image loaded and drawn on canvas.');
                        const canvas = document.getElementById('qr-canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0, img.width, img.height);

                        // Decode the QR code
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        console.log('Image data captured.');
                        const code = jsQR(imageData.data, canvas.width, canvas.height);

                        if (code) {
                            console.log('QR code found:', code.data);
                            const dataLines = code.data.split('\n');
                            const labels = ['Name', 'Address', 'Contact', 'Email', 'Phone Number'];
                            let formattedResult = '';
                            for (let i = 0; i < dataLines.length; i++) {
                                formattedResult += `${labels[i]}: ${dataLines[i]}<br>`;
                            }
                            document.getElementById('qr-result').innerHTML = 'Decoded data:<br>' + formattedResult;
                        } else {
                            console.log('No QR code found.');
                            document.getElementById('qr-result').textContent = 'No QR code found.';
                        }
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
