<?php
/**
 * /api/booking_action.php
 * PUT    ?id=xxx  → update jadwal/catatan booking
 * DELETE ?id=xxx  → batalkan booking (status = 'cancelled')
 */
require_once __DIR__ . '/../config/database.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id     = $_GET['id'] ?? null;

if (!$id) {
    jsonResponse(['error' => 'Parameter id wajib diisi.'], 400);
}

// ─── DELETE: Batalkan booking ─────────────────────────────────────────────────
if ($method === 'DELETE') {
    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            jsonResponse(['error' => 'Booking tidak ditemukan.'], 404);
        }

        $upd = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $upd->execute([$id]);

        $booking['status'] = 'cancelled';
        jsonResponse(['success' => true, 'booking' => $booking]);
    } catch (Throwable $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

// ─── PUT: Update jadwal/catatan ──────────────────────────────────────────────
if ($method === 'PUT') {
    $body = getJsonBody();

    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt->execute([$id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            jsonResponse(['error' => 'Booking tidak ditemukan.'], 404);
        }

        $setParts = [];
        $params   = [];

        $allowed = ['status', 'bookingDate', 'bookingTime', 'notes', 'technicianId'];
        foreach ($allowed as $field) {
            if (isset($body[$field])) {
                $setParts[] = "`$field` = ?";
                $params[]   = $body[$field];
            }
        }

        if (count($setParts) > 0) {
            $params[] = $id;
            $sql = 'UPDATE bookings SET ' . implode(', ', $setParts) . ' WHERE id = ?';
            $pdo->prepare($sql)->execute($params);
        }

        $stmt2 = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
        $stmt2->execute([$id]);
        $updated = $stmt2->fetch();
        $updated['finalPrice'] = (int) $updated['finalPrice'];

        jsonResponse(['success' => true, 'booking' => $updated]);
    } catch (Throwable $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

jsonResponse(['error' => 'Method not allowed'], 405);
