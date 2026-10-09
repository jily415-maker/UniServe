<?php
require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$keyword = trim($_GET['keyword'] ?? '');

if ($keyword === '') {
    echo json_encode([
        'success' => true,
        'services' => []
    ]);
    exit;
}

$searchTerm = '%' . $keyword . '%';

$sql = 'SELECT s.id, s.title, s.description, s.price,
               s.image, u.name AS owner
        FROM services s
        JOIN users u ON s.user_id = u.id
        WHERE s.title LIKE ? OR s.description LIKE ?
        ORDER BY s.created_at DESC';

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();

$result = $stmt->get_result();
$services = [];

while ($row = $result->fetch_assoc()) {
    $services[] = $row;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'services' => $services
]);