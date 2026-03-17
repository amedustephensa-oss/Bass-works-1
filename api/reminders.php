<?php
require_once __DIR__ . '/_bootstrap.php';
$user = requireAuth($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $remindersStmt = $mysqli->prepare('SELECT id, message, remind_at FROM reminders WHERE user_id = ? ORDER BY remind_at ASC');
    $remindersStmt->bind_param('i', $user['id']);
    $remindersStmt->execute();
    $reminders = $remindersStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $alertsStmt = $mysqli->prepare('SELECT title, deadline FROM goals WHERE user_id = ? AND status = "pending" AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 DAY) ORDER BY deadline ASC');
    $alertsStmt->bind_param('i', $user['id']);
    $alertsStmt->execute();
    $alerts = $alertsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    jsonResponse(['ok' => true, 'reminders' => $reminders, 'alerts' => $alerts]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true) ?? [];
    $message = trim($payload['message'] ?? '');
    $remindAt = trim($payload['remind_at'] ?? '');

    if ($message === '' || $remindAt === '') {
        jsonResponse(['ok' => false, 'message' => 'Message and reminder time are required'], 422);
    }

    $stmt = $mysqli->prepare('INSERT INTO reminders (user_id, message, remind_at) VALUES (?, ?, ?)');
    $stmt->bind_param('iss', $user['id'], $message, $remindAt);
    $stmt->execute();

    jsonResponse(['ok' => true, 'message' => 'Reminder saved']);
}

jsonResponse(['ok' => false, 'message' => 'Method not allowed'], 405);
