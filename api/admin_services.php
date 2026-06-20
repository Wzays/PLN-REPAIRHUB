<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $pdo->query('SELECT * FROM services ORDER BY price ASC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['price'] = (int) $row['price'];
        }
        jsonResponse($rows);
    } 
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = 'srv_' . bin2hex(random_bytes(4));
        
        $stmt = $pdo->prepare('INSERT INTO services (id, name, price, duration, description, category) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $id,
            $data['name'] ?? 'New Service',
            $data['price'] ?? 0,
            $data['duration'] ?? '1 Jam',
            $data['description'] ?? '',
            $data['category'] ?? 'HARDWARE'
        ]);
        jsonResponse(['message' => 'Service created', 'id' => $id]);
    } 
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception("ID required");

        $stmt = $pdo->prepare('UPDATE services SET name=?, price=?, duration=?, description=?, category=? WHERE id=?');
        $stmt->execute([
            $data['name'],
            $data['price'],
            $data['duration'],
            $data['description'],
            $data['category'],
            $id
        ]);
        jsonResponse(['message' => 'Service updated']);
    } 
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception("ID required");

        $stmt = $pdo->prepare('DELETE FROM services WHERE id=?');
        $stmt->execute([$id]);
        jsonResponse(['message' => 'Service deleted']);
    } 
    else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Throwable $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
