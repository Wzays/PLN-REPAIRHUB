<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

try {
    $pdo  = getDB();
    $stmt = $pdo->query('SELECT * FROM services ORDER BY price ASC');
    $rows = $stmt->fetchAll();
    // Cast price ke integer
    foreach ($rows as &$row) {
        $row['price'] = (int) $row['price'];
    }
    jsonResponse($rows);
} catch (Throwable $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
