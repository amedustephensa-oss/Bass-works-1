<?php
require_once __DIR__ . '/_bootstrap.php';

session_unset();
session_destroy();
jsonResponse(['ok' => true]);
