<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    $pdo = getDB();
    jsonResponse([
        'mode'           => 'MySQL Connected',
        'mysqlConnected' => true,
        'mysqlHost'      => DB_HOST,
        'database'       => DB_NAME,
    ]);
} catch (Throwable $e) {
    jsonResponse([
        'mode'           => 'MySQL Error: ' . $e->getMessage(),
        'mysqlConnected' => false,
        'mysqlHost'      => DB_HOST,
    ], 500);
}
