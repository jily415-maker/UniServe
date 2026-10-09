<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireLogin();

$userId = (int) $_SESSION['user_id'];

// Admin boleh melihat semua service.
// User biasa hanya boleh melihat service sendiri.
if (isAdmin()) {
    $sql = 'SELECT s.id, s.title, s.price, s.image, s.created_at,
                   u.name AS owner
            FROM services s
            JOIN users u ON s.user_id = u.id
            ORDER BY s.created_at DESC';

    $stmt = $conn->prepare($sql);
} else {
    $sql = 'SELECT s.id, s.title, s.price, s.image, s.created_at,
                   u.name AS owner
            FROM services s
            JOIN users u ON s.user_id = u.id
            WHERE s.user_id = ?
            ORDER BY s.created_at DESC';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
}

$stmt->execute();
$services = $stmt->get_result();

$pageTitle = 'Dashboard';
require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">
        <?= isAdmin() ? 'Semua Service (Admin)' : 'Service Saya' ?>
    </h3>

    <a href="service_form.php" class="btn btn-primary">
        + Tambah Service
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<?php if ($services->num_rows === 0): ?>

    <div class="alert alert-info">
        Belum ada service. Klik "Tambah Service" untuk mula.
    </div>

<?php else: ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">

            <thead class="table-primary">
                <tr>
                    <th>Gambar</th>
                    <th>Tajuk</th>
                    <th>Harga (RM)</th>

                    <?php if (isAdmin()): ?>
                        <th>Pemilik</th>
                    <?php endif; ?>

                    <th>Tarikh</th>
                    <th>Tindakan</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $services->fetch_assoc()): ?>
                    <tr>
                        <td style="width: 90px">
                            <?php if (!empty($row['image'])): ?>
                                <img
                                    src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($row['image']) ?>"
                                    class="img-thumbnail"
                                    style="max-width: 80px"
                                    alt="Gambar service"
                                >
                            <?php else: ?>
                                <span class="text-muted small">
                                    Tiada
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['title']) ?>
                        </td>

                        <td>
                            <?= number_format((float) $row['price'], 2) ?>
                        </td>

                        <?php if (isAdmin()): ?>
                            <td>
                                <?= htmlspecialchars($row['owner']) ?>
                            </td>
                        <?php endif; ?>

                        <td>
                            <?= date(
                                'd/m/Y',
                                strtotime($row['created_at'])
                            ) ?>
                        </td>

                        <td>
                            <a
                                href="service_form.php?id=<?= (int) $row['id'] ?>"
                                class="btn btn-sm btn-warning"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="service_delete.php"
                                class="d-inline"
                                onsubmit="return confirm('Padam service ini?');"
                            >
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $row['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-danger"
                                >
                                    Padam
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

<?php endif; ?>

<?php
$stmt->close();
require_once '../includes/footer.php';
?>