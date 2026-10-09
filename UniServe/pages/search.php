<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$keyword = trim($_GET['keyword'] ?? '');
$services = null;

if ($keyword !== '') {
    $searchTerm = '%' . $keyword . '%';

    $sql = 'SELECT s.id, s.title, s.description, s.price,
                   s.image, s.created_at, u.name AS owner
            FROM services s
            JOIN users u ON s.user_id = u.id
            WHERE s.title LIKE ? OR s.description LIKE ?
            ORDER BY s.created_at DESC';

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $services = $stmt->get_result();
    $stmt->close();
}

$pageTitle = 'Search Services';
require_once '../includes/header.php';
?>

<div class="container py-4">
    <h3 class="mb-4">Cari Service</h3>

    <form method="GET" action="search.php" class="mb-4">
        <div class="input-group">
            <input
                type="text"
                name="keyword"
                class="form-control"
                placeholder="Masukkan tajuk atau penerangan service..."
                value="<?= htmlspecialchars($keyword) ?>"
            >

            <button type="submit" class="btn btn-primary">
                Cari
            </button>

            <a href="search.php" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>

    <?php if ($keyword === ''): ?>

        <div class="alert alert-info">
            Masukkan kata kunci untuk mencari service.
        </div>

    <?php elseif ($services && $services->num_rows > 0): ?>

        <div class="row g-3">
            <?php while ($row = $services->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">

                        <?php if (!empty($row['image'])): ?>
                            <img
                                src="<?= BASE_URL ?>uploads/<?= htmlspecialchars(basename($row['image'])) ?>"
                                class="card-img-top"
                                style="height:200px; object-fit:cover"
                                alt="Gambar service"
                            >
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title">
                                <?= htmlspecialchars($row['title']) ?>
                            </h5>

                            <p class="card-text">
                                <?= nl2br(htmlspecialchars($row['description'])) ?>
                            </p>

                            <p class="fw-bold text-primary">
                                RM <?= number_format((float)$row['price'], 2) ?>
                            </p>

                            <p class="text-muted small mb-0">
                                Pemilik: <?= htmlspecialchars($row['owner']) ?>
                            </p>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            Tiada service dijumpai untuk kata kunci
            "<?= htmlspecialchars($keyword) ?>".
        </div>

    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>