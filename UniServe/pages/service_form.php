<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireLogin();

$userId = (int) $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;

$errors = [];
$title = '';
$description = '';
$price = '';
$currentImage = null;

// Mod Edit: ambil data service dan semak kebenaran
if ($isEdit) {
    $stmt = $conn->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$service) {
        header('Location: dashboard.php?error=' . urlencode('Service tidak dijumpai.'));
        exit;
    }

    if (!isAdmin() && (int) $service['user_id'] !== $userId) {
        header('Location: dashboard.php?error=' . urlencode('Anda tidak dibenarkan mengedit service ini.'));
        exit;
    }

    $title = $service['title'];
    $description = $service['description'];
    $price = $service['price'];
    $currentImage = $service['image'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // Validation server-side
    if ($title === '') {
        $errors[] = 'Tajuk diperlukan.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Tajuk terlalu panjang (maksimum 150 aksara).';
    }

    if ($description === '') {
        $errors[] = 'Penerangan diperlukan.';
    }

    if ($price === '' || !is_numeric($price) || (float) $price < 0 || (float) $price > 999999) {
        $errors[] = 'Harga mesti nombor yang sah.';
    }

    // Upload gambar (pilihan)
    $newImageName = null;
    $hasFile = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($hasFile) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Muat naik gambar gagal. Sila cuba lagi.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES['image']['tmp_name']);

            if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
                $errors[] = 'Gambar mesti JPG atau PNG.';
            }
            if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Saiz gambar maksimum 2MB.';
            }

            if (empty($errors)) {
                $ext = $mime === 'image/png' ? 'png' : 'jpg';
                $newImageName = uniqid('svc_', true) . '.' . $ext;
                $dest = __DIR__ . '/../uploads/' . $newImageName;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $errors[] = 'Gagal menyimpan gambar.';
                    $newImageName = null;
                }
            }
        }
    }

    if (empty($errors)) {
        $priceVal = (float) $price;

        if ($isEdit) {
            $imageToSave = $newImageName ?? $currentImage;

            $stmt = $conn->prepare('UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?');
            $stmt->bind_param('ssdsi', $title, $description, $priceVal, $imageToSave, $id);
            $ok = $stmt->execute();
            $stmt->close();

            // Padam gambar lama kalau diganti
            if ($ok && $newImageName && $currentImage) {
                $old = __DIR__ . '/../uploads/' . basename($currentImage);
                if (is_file($old)) unlink($old);
            }
            $msg = 'Service berjaya dikemaskini.';
        } else {
            $stmt = $conn->prepare('INSERT INTO services (user_id, title, description, price, image) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('issds', $userId, $title, $description, $priceVal, $newImageName);
            $ok = $stmt->execute();
            $stmt->close();
            $msg = 'Service berjaya ditambah.';
        }

        if ($ok) {
            header('Location: dashboard.php?success=' . urlencode($msg));
            exit;
        } else {
            $errors[] = 'Gagal menyimpan service. Sila cuba lagi.';
        }
    }
}

$pageTitle = $isEdit ? 'Edit Service' : 'Tambah Service';
require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title mb-4"><?= $isEdit ? 'Edit Service' : 'Tambah Service' ?></h3>

                <div id="clientErrors" class="alert alert-danger d-none"></div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="serviceForm" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label for="title" class="form-label">Tajuk</label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?= htmlspecialchars($title) ?>" maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Penerangan</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="4"><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Harga (RM)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="price" name="price"
                               value="<?= htmlspecialchars((string) $price) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar (JPG/PNG, maksimum 2MB)</label>
                        <?php if ($currentImage): ?>
                            <div class="mb-2">
                                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($currentImage) ?>"
                                     class="img-thumbnail" style="max-width:150px" alt="">
                                <div class="form-text">Pilih fail baru untuk menggantikan gambar ini.</div>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image"
                               accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? 'Kemaskini' : 'Simpan' ?>
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>assets/js/validation.js"></script>
<?php require_once '../includes/footer.php'; ?>