<?php
require "connect.php";

if (!$con) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection error'
    ]);
    exit;
}

$user_id = intval($_GET['user_id']);

$stmt = $con->prepare("SELECT task_id, title, description, status, visible FROM task WHERE user_id = ? ORDER BY task_id ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    if ($row['visible'] === 1) {
        $tasks[] = [
            'task_id' => intval($row['task_id']),
            'title' => $row['title'],
            'description' => $row['description'],
            'complete' => intval($row['status']) === 1
        ];
    }
}

echo json_encode([
    'status' => 'success',
    'message' => 'Tasks retrieved successfully',
    'data' => $tasks,
    'count' => count($tasks)
]);

$stmt->close();
$con->close();
