<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Test</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/dist/qrcode.min.js"></script>
</head>
<body>
    <div id="test-qr"></div>
    <script>
        const qrData = 'Hello, world!';
        QRCode.toCanvas(document.getElementById('test-qr'), qrData, { width: 180 }, function (error) {
            if (error) console.error(error);
            console.log('QR code generated!');
        });
    </script>
</body>
</html>
