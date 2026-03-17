<?php
require_once __DIR__ . '/_bootstrap.php';

$user = currentUser($mysqli);
jsonResponse(['ok' => true, 'user' => $user]);
