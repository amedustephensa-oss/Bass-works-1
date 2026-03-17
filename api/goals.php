<?php
require_once __DIR__ . '/_bootstrap.php';
$user = requireAuth($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $mysqli->prepare('SELECT id, title, details, deadline, status FROM goals WHERE user_id = ? ORDER BY deadline ASC');
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $goals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $total = count($goals);
    $completed = count(array_filter($goals, fn($goal) => $goal['status'] === 'completed'));
    $pending = $total - $completed;
    $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

    jsonResponse([
        'ok' => true,
        'goals' => $goals,
        'metrics' => compact('total', 'completed', 'pending', 'progress')
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    $title = trim($payload['title'] ?? '');
    $details = trim($payload['details'] ?? '');
    $deadline = $payload['deadline'] ?? '';

    if ($title === '' || $deadline === '') {
        jsonResponse(['ok' => false, 'message' => 'Goal and deadline are required'], 422);
    }

    $stmt = $mysqli->prepare('INSERT INTO goals (user_id, title, details, deadline, status) VALUES (?, ?, ?, ?, "pending")');
    $stmt->bind_param('isss', $user['id'], $title, $details, $deadline);
    $stmt->execute();

    jsonResponse(['ok' => true, 'message' => 'Goal created']);
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    $goalId = (int) ($payload['goal_id'] ?? 0);

    $stmt = $mysqli->prepare('UPDATE goals SET status = IF(status = "pending", "completed", "pending") WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $goalId, $user['id']);
    $stmt->execute();

    jsonResponse(['ok' => true, 'message' => 'Goal status updated']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    $goalId = (int) ($payload['goal_id'] ?? 0);

    $stmt = $mysqli->prepare('DELETE FROM goals WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $goalId, $user['id']);
    $stmt->execute();

    jsonResponse(['ok' => true, 'message' => 'Goal deleted']);
}

jsonResponse(['ok' => false, 'message' => 'Method not allowed'], 405);
