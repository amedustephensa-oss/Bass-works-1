<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function currentUser(mysqli $mysqli): ?array {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $mysqli->prepare('SELECT id, full_name, email, created_at FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    return $user ?: null;
}

function requireAuth(mysqli $mysqli): array {
    $user = currentUser($mysqli);
    if (!$user) {
        jsonResponse(['ok' => false, 'message' => 'Unauthorized'], 401);
    }
    return $user;
}
