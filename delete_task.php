<?php
require "connect.php";

if (!$con) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection error'
    ]);
    exit;
}

$task_id = intval($_POST['task_id']);
$user_id = intval($_POST['user_id']);

$check_stmt = $con->prepare("SELECT task_id FROM task WHERE task_id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $task_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Task not found or access denied'
    ]);
    exit;
}
$check_stmt->close();

$stmt = $con->prepare("UPDATE task SET visible = 0 WHERE task_id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Task deleted successfully',
            'data' => [
                'deleted_task_id' => $task_id
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No task was deleted'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to execute: ' . $stmt->error
    ]);
}
$stmt->close();
$con->close();
