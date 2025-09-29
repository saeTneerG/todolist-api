<?php
require "connect.php";

if (!$con) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection error'
    ]);
    exit;
}

if (!isset($_POST['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'user_id is required'
    ]);
    exit;
}
$user_id = intval($_POST['user_id']);
$stmt = $con->prepare("UPDATE task SET visible = 0 WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $deleted_count = $stmt->affected_rows;
    if (!$deleted_count == 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Task deleted successfully',
            'data' => [
                'Delete count' => $deleted_count
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No tasks to delete'
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
?>