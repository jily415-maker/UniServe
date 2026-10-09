<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header('Location: dashboard.php?error=' . urlencode('ID service tidak sah.'));
    exit;
}

// Semak service wujud dan pengguna mempunyai kebenaran
$stmt = $conn->prepare('SELECT user_id, image FROM services WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$service) {
    header('Location: dashboard.php?error=' . urlencode('Service tidak dijumpai.'));
    exit;
}

if (!isAdmin() && (int) $service['user_id'] !== $userId) {
    header('Location: dashboard.php?error=' . urlencode('Anda tidak dibenarkan memadam service ini.'));
    exit;
}

// Padam rekod service
$stmt = $conn->prepare('DELETE FROM services WHERE id = ?');
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$stmt->close();

// Padam gambar berkaitan jika ada
if ($ok && !empty($service['image'])) {
    $imagePath = __DIR__ . '/../uploads/' . basename($service['image']);
    if (is_file($imagePath)) {
        unlink($imagePath);
    }
}

if ($ok) {
    header('Location: dashboard.php?success=' . urlencode('Service berjaya dipadam.'));
} else {
    header('Location: dashboard.php?error=' . urlencode('Gagal memadam service.'));
}
exit;
?>