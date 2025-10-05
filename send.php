<?php
// send.php - Ultra permissive CORS headers

// Comprehensive CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Allow any origin during development
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Credentials: true");
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = [];

// Try to parse as JSON first, then fall back to form data
if (!empty($input)) {
    $data = json_decode($input, true);
}

// If no JSON data, use regular POST
if (empty($data)) {
    $data = $_POST;
}

// Extract values with null coalescing
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required";
}

if (empty($email)) {
    $errors[] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (empty($message)) {
    $errors[] = "Message is required";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit;
}

// Send email
$to = "maybaranikoko30@gmail.com";
$subject = "New Contact Form Message from " . htmlspecialchars($name);
$email_message = "
Name: " . htmlspecialchars($name) . "
Email: " . htmlspecialchars($email) . "
Phone: " . htmlspecialchars($phone) . "
Message: " . htmlspecialchars($message) . "
";

$headers = "From: " . $email . "\r\n" .
           "Reply-To: " . $email . "\r\n" .
           "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $email_message, $headers)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Your message has been sent successfully!'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to send email. Please try again later.'
    ]);
}
?>
