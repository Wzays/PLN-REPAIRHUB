<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}

$pdo = getDB();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Ambil semua data booking dengan join ke seluruh tabel tanpa filter email
        $sql = "
            SELECT 
                b.id AS b_id, b.bookingDate, b.bookingTime, b.address AS b_address, 
                b.notes, b.status AS b_status, b.difficulty, b.finalPrice, b.createdAt,
                c.id AS c_id, c.name AS c_name, c.email AS c_email, c.phone AS c_phone, c.address AS c_address,
                s.id AS s_id, s.name AS s_name, s.price AS s_price, s.duration AS s_duration, 
                s.description AS s_description, s.category AS s_category,
                t.id AS t_id, t.name AS t_name, t.specialty AS t_specialty, t.phone AS t_phone, 
                t.avatar AS t_avatar, t.status AS t_status, t.rating AS t_rating,
                d.id AS d_id, d.deviceName, d.issueType, d.issueDescription, d.photoUrl
            FROM bookings b
            LEFT JOIN customers c ON b.customerId = c.id
            LEFT JOIN services s ON b.serviceId = s.id
            LEFT JOIN technicians t ON b.technicianId = t.id
            LEFT JOIN device_issues d ON d.bookingId = b.id
            ORDER BY b.createdAt DESC
        ";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'booking' => [
                    'id' => $r['b_id'],
                    'bookingDate' => $r['bookingDate'],
                    'bookingTime' => $r['bookingTime'],
                    'address' => $r['b_address'],
                    'notes' => $r['notes'],
                    'status' => $r['b_status'],
                    'difficulty' => $r['difficulty'],
                    'finalPrice' => (int)$r['finalPrice'],
                    'createdAt' => $r['createdAt'],
                ],
                'customer' => [
                    'id' => $r['c_id'],
                    'name' => $r['c_name'],
                    'email' => $r['c_email'],
                    'phone' => $r['c_phone'],
                    'address' => $r['c_address']
                ],
                'service' => [
                    'id' => $r['s_id'],
                    'name' => $r['s_name'],
                    'price' => (int)$r['s_price'],
                    'duration' => $r['s_duration'],
                    'description' => $r['s_description'],
                    'category' => $r['s_category']
                ],
                'technician' => $r['t_id'] ? [
                    'id' => $r['t_id'],
                    'name' => $r['t_name'],
                    'specialty' => $r['t_specialty'],
                    'phone' => $r['t_phone'],
                    'avatar' => $r['t_avatar'],
                    'status' => $r['t_status'],
                    'rating' => (float)$r['t_rating']
                ] : null,
                'deviceIssue' => $r['d_id'] ? [
                    'id' => $r['d_id'],
                    'deviceName' => $r['deviceName'],
                    'issueType' => $r['issueType'],
                    'issueDescription' => $r['issueDescription'],
                    'photoUrl' => $r['photoUrl']
                ] : null
            ];
        }

        jsonResponse($result);
    } 
    elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception("ID required");

        // Admin bisa mengubah status atau technicianId
        $stmt = $pdo->prepare("UPDATE bookings SET status=?, technicianId=? WHERE id=?");
        $stmt->execute([
            $data['status'],
            $data['technicianId'],
            $id
        ]);
        jsonResponse(['message' => 'Booking updated successfully']);
    } 
    elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id) throw new Exception("ID required");

        // Opsional: hapus record device_issues terkait
        $pdo->prepare('DELETE FROM device_issues WHERE bookingId=?')->execute([$id]);
        $pdo->prepare('DELETE FROM bookings WHERE id=?')->execute([$id]);
        
        jsonResponse(['message' => 'Booking deleted successfully']);
    } 
    else {
        jsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Throwable $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
