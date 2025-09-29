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

    $update_fields = [];
    $params = [];
    $types = "";

    if (isset($_POST['title'])) {
        $update_fields[] = "title = ?";
        $params[] = trim($_POST['title']);
        $types .= "s";
    }

    if (isset($_POST['description'])) {
        $update_fields[] = "description = ?";
        $params[] = trim($_POST['description']);
        $types .= "s";
    }

    if (isset($_POST['status'])) {
        $update_fields[] = "status = ?";
        $params[] = intval($_POST['status']);
        $types .= "i";
    }

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

    $sql = "UPDATE task SET " . implode(", ", $update_fields) . " WHERE task_id = ? AND user_id = ?";
    $stmt = $con->prepare($sql);

    $params[] = $task_id;
    $params[] = $user_id;
    $types .= "ii";

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $select_stmt = $con->prepare("SELECT task_id, title, description, status FROM task WHERE task_id = ? AND user_id = ?");
        $select_stmt->bind_param("ii", $task_id, $user_id);
        
        if ($select_stmt->execute()) {
            $result = $select_stmt->get_result();
            $task = $result->fetch_assoc();
            $select_stmt->close();

            echo json_encode([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => [
                    'task_id' => intval($task['task_id']),
                    'title' => $task['title'],
                    'description' => $task['description'],
                    'complete' => intval($task['status']) === 1
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to updated task'
            ]);
        }
        
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No changes made'
        ]);
    }
    $stmt->close();
    $con->close();
    ?>