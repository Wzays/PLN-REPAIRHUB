<?php
/**
 * /api/bookings.php
 * GET  → list semua bookings
 * POST → buat booking baru + upsert customer + simpan device_issue
 */
require_once __DIR__ . '/../config/database.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$method = $_SERVER['REQUEST_METHOD'];

// ─── GET: list bookings ───────────────────────────────────────────────────────
if ($method === 'GET') {
    try {
        $pdo  = getDB();
        $stmt = $pdo->query('SELECT * FROM bookings ORDER BY createdAt DESC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['finalPrice'] = (int) $r['finalPrice'];
        }
        jsonResponse($rows);
    } catch (Throwable $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

// ─── POST: buat booking baru ─────────────────────────────────────────────────
if ($method === 'POST') {
    $body = getJsonBody();

    $customer      = $body['customer']     ?? null;
    $serviceId     = $body['serviceId']    ?? null;
    $technicianId  = $body['technicianId'] ?? 'tech_1';
    $bookingDate   = $body['bookingDate']  ?? date('Y-m-d');
    $bookingTime   = $body['bookingTime']  ?? '09:00';
    $address       = $body['address']      ?? '';
    $notes         = $body['notes']        ?? '';
    $difficulty    = $body['difficulty']   ?? 'Standar';
    $finalPrice    = $body['finalPrice']   ?? 150000;
    $deviceIssue   = $body['deviceIssue']  ?? null;

    if (!$customer || !$serviceId) {
        jsonResponse(['error' => 'customer data dan serviceId wajib diisi.'], 400);
    }

    try {
        $pdo = getDB();
        $pdo->beginTransaction();

        // Upsert customer
        $stmt = $pdo->prepare('SELECT * FROM customers WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $stmt->execute([$customer['email']]);
        $existingCust = $stmt->fetch();

        if ($existingCust) {
            $custId = $existingCust['id'];
            // Perbarui data pelanggan yang sudah ada (misal ganti nama / alamat)
            $updateCust = $pdo->prepare('UPDATE customers SET name=?, phone=?, address=? WHERE id=?');
            $updateCust->execute([
                $customer['name'] ?? $existingCust['name'],
                $customer['phone'] ?? $existingCust['phone'],
                $address ?: ($customer['address'] ?? $existingCust['address']),
                $custId
            ]);
        } else {
            $custId = genId('cust');
            $ins = $pdo->prepare(
                'INSERT INTO customers (id, name, email, phone, address) VALUES (?, ?, ?, ?, ?)'
            );
            $ins->execute([
                $custId,
                $customer['name']    ?? '',
                $customer['email']   ?? '',
                $customer['phone']   ?? '',
                $address ?: ($customer['address'] ?? ''),
            ]);
        }

        // Buat booking
        $bookingId = genId('book');
        $createdAt = date('c'); // ISO 8601
        $ins = $pdo->prepare(
            'INSERT INTO bookings
             (id, customerId, serviceId, technicianId, bookingDate, bookingTime, address, status, notes, difficulty, finalPrice, createdAt)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $ins->execute([
            $bookingId, $custId, $serviceId, $technicianId,
            $bookingDate, $bookingTime, $address, 'pending',
            $notes, $difficulty, $finalPrice, $createdAt,
        ]);

        // Simpan device issue
        $savedIssue = null;
        if ($deviceIssue) {
            $issueId = genId('issue');
            $ins2 = $pdo->prepare(
                'INSERT INTO device_issues (id, bookingId, deviceName, issueType, issueDescription, photoUrl)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $ins2->execute([
                $issueId, $bookingId,
                $deviceIssue['deviceName']       ?? 'General PC/Laptop',
                $deviceIssue['issueType']        ?? 'Unspecified',
                $deviceIssue['issueDescription'] ?? '',
                $deviceIssue['photoUrl']         ?? '',
            ]);
            $savedIssue = [
                'id'               => $issueId,
                'bookingId'        => $bookingId,
                'deviceName'       => $deviceIssue['deviceName']       ?? '',
                'issueType'        => $deviceIssue['issueType']        ?? '',
                'issueDescription' => $deviceIssue['issueDescription'] ?? '',
                'photoUrl'         => $deviceIssue['photoUrl']         ?? '',
            ];
        }

        $pdo->commit();

        // Ambil service & technician untuk response
        $srv  = $pdo->prepare('SELECT * FROM services    WHERE id = ?'); $srv->execute([$serviceId]);
        $tech = $pdo->prepare('SELECT * FROM technicians WHERE id = ?'); $tech->execute([$technicianId]);

        $newBooking = [
            'id'           => $bookingId,
            'customerId'   => $custId,
            'serviceId'    => $serviceId,
            'technicianId' => $technicianId,
            'bookingDate'  => $bookingDate,
            'bookingTime'  => $bookingTime,
            'address'      => $address,
            'status'       => 'pending',
            'notes'        => $notes,
            'difficulty'   => $difficulty,
            'finalPrice'   => (int) $finalPrice,
            'createdAt'    => $createdAt,
        ];

        jsonResponse([
            'booking'     => $newBooking,
            'customer'    => $existingCust ?: ['id' => $custId, 'name' => $customer['name'], 'email' => $customer['email']],
            'service'     => $srv->fetch()  ?: null,
            'technician'  => $tech->fetch() ?: null,
            'deviceIssue' => $savedIssue,
        ], 201);

    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

jsonResponse(['error' => 'Method not allowed'], 405);
