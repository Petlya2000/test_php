<?php

require_once __DIR__ . '/../init.php';

$db = getDatabaseConnection();

// Получение метода и URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

if ($uri[0] === 'tasks') {
    // Создание задачи: POST /tasks
    if ($method === 'POST' && count($uri) === 1) {
        $data = getRequestBody();

        if (empty($data['title'])) {
            sendJsonResponse(['error' => 'Title is required'], 400);
        }

        $stmt = $db->prepare("INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status)");
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':status' => $data['status'] ?? 'pending',
        ]);

        sendJsonResponse(['id' => $db->lastInsertId()], 201);
    }

    // Просмотр списка задач: GET /tasks
    elseif ($method === 'GET' && count($uri) === 1) {
        $stmt = $db->query("SELECT * FROM tasks");
        $tasks = $stmt->fetchAll();

        sendJsonResponse($tasks);
    }

    // Просмотр одной задачи: GET /tasks/{id}
    elseif ($method === 'GET' && count($uri) === 2) {
        $id = (int)$uri[1];
        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $task = $stmt->fetch();

        if ($task) {
            sendJsonResponse($task);
        } else {
            sendJsonResponse(['error' => 'Task not found'], 404);
        }
    }

    // Обновление задачи: PUT /tasks/{id}
    elseif ($method === 'PUT' && count($uri) === 2) {
        $id = (int)$uri[1];
        $data = getRequestBody();

        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $task = $stmt->fetch();

        if (!$task) {
            sendJsonResponse(['error' => 'Task not found'], 404);
        }

        $stmt = $db->prepare("UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id");
        $stmt->execute([
            ':title' => $data['title'] ?? $task['title'],
            ':description' => $data['description'] ?? $task['description'],
            ':status' => $data['status'] ?? $task['status'],
            ':id' => $id,
        ]);

        sendJsonResponse(['message' => 'Task updated']);
    }

    // Удаление задачи: DELETE /tasks/{id}
    elseif ($method === 'DELETE' && count($uri) === 2) {
        $id = (int)$uri[1];

        $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);

        sendJsonResponse(['message' => 'Task deleted']);
    }

    // Если маршрут не найден
    else {
        sendJsonResponse(['error' => 'Not Found'], 404);
    }
} else {
    sendJsonResponse(['error' => 'Not Found'], 404);
}
