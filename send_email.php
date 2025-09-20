<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if PHPMailer is installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    $reason = isset($_POST['reason']) ? filter_var($_POST['reason'], FILTER_SANITIZE_STRING) : 'Not specified';

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill in all required fields.";
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings for Mailtrap
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io'; // Mailtrap SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'a20a921b57d769'; // Replace with your Mailtrap SMTP username
        $mail->Password = '73fefad831a672'; // Replace with your Mailtrap SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525; // Mailtrap SMTP port (alternatives: 25, 465, or 587)

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('cg1262171@gmail.com'); // Replace with the recipient's email for testing

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject ?: 'Contact Form Submission';
        $mail->Body = "<h3>Contact Form Submission</h3>
                       <p><strong>Name:</strong> $name</p>
                       <p><strong>Email:</strong> $email</p>
                       <p><strong>Reason:</strong> $reason</p>
                       <p><strong>Message:</strong> $message</p>";
        $mail->AltBody = "Name: $name\nEmail: $email\nReason: $reason\nMessage: $message";

        $mail->send();
        echo "Message sent successfully! Check your Mailtrap inbox.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Invalid request method.";
}
?>