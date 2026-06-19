<?php
/**
 * GET /api/booking_detail.php
 * 5-table JOIN: bookings + customers + services + technicians + device_issues
 */
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
    $pdo = getDB();

    $sql = "
        SELECT
            b.id             AS booking_id,
            b.customerId,
            b.serviceId,
            b.technicianId,
            b.bookingDate,
            b.bookingTime,
            b.address        AS booking_address,
            b.status,
            b.notes,
            b.difficulty,
            b.finalPrice,
            b.createdAt,

            c.id             AS cust_id,
            c.name           AS cust_name,
            c.email          AS cust_email,
            c.phone          AS cust_phone,
            c.address        AS cust_address,

            s.id             AS srv_id,
            s.name           AS srv_name,
            s.price          AS srv_price,
            s.duration       AS srv_duration,
            s.description    AS srv_description,
            s.category       AS srv_category,

            t.id             AS tech_id,
            t.name           AS tech_name,
            t.specialty      AS tech_specialty,
            t.phone          AS tech_phone,
            t.avatar         AS tech_avatar,
            t.status         AS tech_status,
            t.rating         AS tech_rating,

            di.id            AS issue_id,
            di.deviceName,
            di.issueType,
            di.issueDescription,
            di.photoUrl

        FROM bookings b
        JOIN customers   c  ON b.customerId   = c.id
        JOIN services    s  ON b.serviceId    = s.id
        LEFT JOIN technicians  t  ON b.technicianId  = t.id
        LEFT JOIN device_issues di ON di.bookingId   = b.id
        ORDER BY b.createdAt DESC
    ";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    $details = [];
    foreach ($rows as $row) {
        $details[] = [
            'booking' => [
                'id'          => $row['booking_id'],
                'customerId'  => $row['customerId'],
                'serviceId'   => $row['serviceId'],
                'technicianId'=> $row['technicianId'],
                'bookingDate' => $row['bookingDate'],
                'bookingTime' => $row['bookingTime'],
                'address'     => $row['booking_address'],
                'status'      => $row['status'],
                'notes'       => $row['notes'],
                'difficulty'  => $row['difficulty'],
                'finalPrice'  => (int) $row['finalPrice'],
                'createdAt'   => $row['createdAt'],
            ],
            'customer' => [
                'id'      => $row['cust_id'],
                'name'    => $row['cust_name'],
                'email'   => $row['cust_email'],
                'phone'   => $row['cust_phone'],
                'address' => $row['cust_address'],
            ],
            'service' => [
                'id'          => $row['srv_id'],
                'name'        => $row['srv_name'],
                'price'       => (int) $row['srv_price'],
                'duration'    => $row['srv_duration'],
                'description' => $row['srv_description'],
                'category'    => $row['srv_category'],
            ],
            'technician' => $row['tech_id'] ? [
                'id'        => $row['tech_id'],
                'name'      => $row['tech_name'],
                'specialty' => $row['tech_specialty'],
                'phone'     => $row['tech_phone'],
                'avatar'    => $row['tech_avatar'],
                'status'    => $row['tech_status'],
                'rating'    => (float) $row['tech_rating'],
            ] : null,
            'deviceIssue' => $row['issue_id'] ? [
                'id'               => $row['issue_id'],
                'bookingId'        => $row['booking_id'],
                'deviceName'       => $row['deviceName'],
                'issueType'        => $row['issueType'],
                'issueDescription' => $row['issueDescription'],
                'photoUrl'         => $row['photoUrl'],
            ] : null,
        ];
    }

    jsonResponse($details);
} catch (Throwable $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}
