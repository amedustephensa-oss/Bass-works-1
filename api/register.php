<?php
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'message' => 'Method not allowed'], 405);
}

$payload = json_decode(file_get_contents('php://input'), true) ?? [];
$fullName = trim($payload['full_name'] ?? '');
$email = trim($payload['email'] ?? '');
$password = $payload['password'] ?? '';

if ($fullName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    jsonResponse(['ok' => false, 'message' => 'Invalid input data'], 422);
}

$check = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
$check->bind_param('s', $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    jsonResponse(['ok' => false, 'message' => 'Email already exists'], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare('INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $fullName, $email, $hash);
$stmt->execute();

jsonResponse(['ok' => true, 'message' => 'Registration successful']);
