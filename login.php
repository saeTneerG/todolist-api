<?php
require "connect.php";

if (!$con) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection error'
    ]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $con->prepare("SELECT user_id, name, password, account_activation_hash FROM user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['account_activation_hash'] !== null) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Account not activated. Please check your email.'
    ]);
    exit;
}

if ($result->num_rows === 1) {

    if (password_verify($password, $user['password'])) {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'email' => $email
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Wrong password or email'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Wrong password or email'
    ]);
}

$stmt->close();
$con->close();
