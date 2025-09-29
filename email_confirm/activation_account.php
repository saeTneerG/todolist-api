<?php
require "../connect.php";
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
$token = $_GET['token'];
$token_hash = hash("sha256", $token);

$stmt = $con->prepare("SELECT * FROM user WHERE account_activation_hash = ?");
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if ($user === null) {
    die("Token not found");
}
$stmt = $con->prepare("UPDATE user SET account_activation_hash = NULL WHERE user_id = ?");
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Account Activated</title>
    <meta charset="UTF-8">
</head>

<body>

    <h1>Account Activated</h1>

    <p>Account activated successfully. You can return to app and login.</p>

</body>
</html>