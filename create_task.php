<?php
require "connect.php";

if (!$con) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection error'
    ]);
    exit;
}

$user_id = intval($_POST['user_id']);
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$status = isset($_POST['status']) ? intval($_POST['status']) : 0;

$stmt = $con->prepare("INSERT INTO task (user_id, title, description, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issi", $user_id, $title, $description, $status);

if ($stmt->execute()) {
    $task_id = $con->insert_id;
    echo json_encode([
        'status' => 'success',
        'message' => 'Task created successfully',
        'data' => [
            'task_id' => $task_id,
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create task: ' . $stmt->error
    ]);
}
$stmt->close();
$con->close();
