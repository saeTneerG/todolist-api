<?php
require "connect.php";

if (!$con) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection error'
    ]);
    exit;
}

$activation_token = bin2hex(random_bytes(16));
$activation_token_hash = hash("sha256", $activation_token);

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'All fields are required'
    ]);
    exit;
}

$check_stmt = $con->prepare("SELECT user_id FROM user WHERE email = ?");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email already exists'
    ]);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $con->prepare("INSERT INTO user (name, email, password, account_activation_hash) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $activation_token_hash);

if ($stmt->execute()) {
    require "email_confirm/email.php";
    $user_id = $con->insert_id;

    echo json_encode([
        'status' => 'success',
        'message' => 'User registered successfully',
        'data' => [
            'user_id' => $user_id,
            'name' => $name,
            'email' => $email
        ]
    ]);

    $send_email = sendMail(
        $email,
        $name,
        $activation_token
    );
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Registration failed'
    ]);
}
$stmt->close();

if ($send_email === true) {
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to send confirmation email.'
    ]);
}

$con->close();
