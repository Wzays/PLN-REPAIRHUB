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
        $stmt = $pdo->query('SELECT * FROM technicians ORDER BY name ASC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['rating'] = (float) $row['rating'];
        }
        jsonResponse($rows);
    } 
    elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = 'tech_' . bin2hex(random_bytes(4));
        
        $stmt = $pdo->prepare('INSERT INTO technicians (id, name, specialty, phone, avatar, status, rating) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $id,
            $data['name'] ?? 'New Technician',
            $data['specialty'] ?? 'General Repair',
            $data['phone'] ?? '+62 000-0000-0000',
            $data['avatar'] ?? 'https://ui-avatars.com/api/?name=Tech',
            $data['status'] ?? 'available',
            $data['rating'] ?? 5.0
        ]);
        jsonResponse(['message' => 'Technician created', 'id' => $id]);
    } 
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception("ID required");

        $stmt = $pdo->prepare('UPDATE technicians SET name=?, specialty=?, phone=?, avatar=?, status=?, rating=? WHERE id=?');
        $stmt->execute([
            $data['name'],
            $data['specialty'],
            $data['phone'],
            $data['avatar'],
            $data['status'],
            $data['rating'],
            $id
        ]);
        jsonResponse(['message' => 'Technician updated']);
    } 
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception("ID required");

        $stmt = $pdo->prepare('DELETE FROM technicians WHERE id=?');
        $stmt->execute([$id]);
        jsonResponse(['message' => 'Technician deleted']);
    } 
    else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Throwable $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
