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
$user_id = $_SESSION['user_id']; // Assuming you have this in session as well

// Generate the URL for the QR code, which will trigger the notification
$qr_url = "https://your-domain.com/notification.php?user_id=" . $user_id;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QR Code Generator & Decoder</title>
    <link rel="stylesheet" href="qrcode.css">
    <!-- Load qrcode.min.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Load jsQR library for QR code decoding -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsqr/1.4.0/jsqr.min.js"></script>
</head>

<body>
    <section>
        <!-- Grid of spans for background animation -->
        <!-- You can remove some spans if not needed for performance -->
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
            <h2 style="color:green;" >Enter your details to generate a QR code</h2>
            <form id="qr-generation-form">
                <input type="text" id="name" placeholder="Name" required /><br />
                <input type="text" id="address" placeholder="Address" required /><br />
                <input type="text" id="contact" placeholder="Contact" required /><br />
                <input type="email" id="email" placeholder="Email" required /><br />
                <input type="tel" id="phone" placeholder="Phone Number" required /><br />
                <input type="submit" value="Generate QR Code" />
            </form><br />
            <div id="qr-code"></div>

            <!-- QR Code Decoding -->
            <h2 style="color:green;" >Upload a QR code image to decode</h2>
            <!-- Custom file input -->
                <div class="custom-file-input">
                    <input type="file" id="qr-image" accept="image/*" />
                    <label for="qr-image">Choose Image</label>
                </div>
                <canvas id="qr-canvas" style="display:none;"></canvas>

        </main>
    </section>

    <script>
        // QR Code Generation
        document.getElementById('qr-generation-form').addEventListener('submit', function(event) {
            event.preventDefault(); 

            // Get user input
            const name = document.getElementById('name').value;
            const address = document.getElementById('address').value;
            const contact = document.getElementById('contact').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;

            // Combine details into a single string
            const data = `Name: ${name}\nAddress: ${address}\nContact: ${contact}\nEmail: ${email}\nPhone: ${phone}`;

            // Clear previous QR code
            document.getElementById('qr-code').innerHTML = '';

            // Generate new QR code
            const qrcode = new QRCode(document.getElementById('qr-code'), {
                text: data,
                width: 128,
                height: 128,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.L
            });
        });

        // QR Code Decoding
        document.getElementById('qr-image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.getElementById('qr-canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0, img.width, img.height);

                        // Decode the QR code
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, canvas.width, canvas.height);

                        if (code) {
                            document.getElementById('qr-result').textContent = 'Decoded data: ' + code.data;
                        } else {
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
