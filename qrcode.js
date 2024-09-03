document.getElementById('qr-generation-form').addEventListener('submit', function(event) {
    event.preventDefault();

    // Get the input values
    const name = document.getElementById('name').value;
    const address = document.getElementById('address').value;
    const contact = document.getElementById('contact').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;

    // Construct the QR code content
    const qrContent = `Name: ${name}\nAddress: ${address}\nContact: ${contact}\nEmail: ${email}\nPhone: ${phone}`;

    // Clear any previous QR code
    document.getElementById('qr-code').innerHTML = '';

    // Generate the QR code
    new QRCode(document.getElementById('qr-code'), {
        text: qrContent,
        width: 128,
        height: 128
    });
});
document.getElementById('qr-generation-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var name = document.getElementById('name').value;
    var address = document.getElementById('address').value;
    var contact = document.getElementById('contact').value;
    var email = document.getElementById('email').value;
    var phone = document.getElementById('phone').value;

    var qrData = `Name: ${name}\nAddress: ${address}\nContact: ${contact}\nEmail: ${email}\nPhone: ${phone}`;

    var qrCodeContainer = document.getElementById('qr-code');
    qrCodeContainer.innerHTML = ""; // Clear previous QR code
    new QRCode(qrCodeContainer, {
        text: qrData,
        width: 400,
        height: 400
    });
});