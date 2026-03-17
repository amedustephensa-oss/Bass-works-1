<?php
require_once __DIR__ . '/_bootstrap.php';
$user = requireAuth($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    jsonResponse(['ok' => true, 'user' => $user]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse(['ok' => false, 'message' => 'Method not allowed'], 405);
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$fullName = trim($payload['full_name'] ?? '');

if ($fullName === '') {
    jsonResponse(['ok' => false, 'message' => 'Full name is required'], 422);
}

$stmt = $mysqli->prepare('UPDATE users SET full_name = ? WHERE id = ?');
$stmt->bind_param('si', $fullName, $user['id']);
$stmt->execute();
$_SESSION['full_name'] = $fullName;

jsonResponse(['ok' => true, 'message' => 'Profile updated']);
