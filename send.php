<?php
// send.php - handles contact form submissions with redirect

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize form data
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        header("Location: index.html?status=error&msg=empty");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.html?status=error&msg=invalid_email");
        exit;
    }

    // Email settings
    $to = "info@infinity8.com"; // Change this to your real email address
    $subject = "New Contact Form Message from $name";
    $headers  = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body = "You have received a new message from your website contact form.\n\n" .
            "Here are the details:\n" .
            "Name: $name\n" .
            "Email: $email\n" .
            "Phone: $phone\n" .
            "Message:\n$message\n";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
        header("Location: index.html?status=success");
    } else {
        header("Location: index.html?status=error&msg=send_fail");
    }
} else {
    header("Location: index.html?status=error&msg=invalid_request");
}
?>