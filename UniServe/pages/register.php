<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Kalau dah login, tak perlu daftar lagi
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation server-side
    if ($name === '') {
        $errors[] = 'Nama diperlukan.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'Nama terlalu panjang (maksimum 100 aksara).';
    }

    if ($email === '') {
        $errors[] = 'Emel diperlukan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format emel tidak sah.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Kata laluan mesti sekurang-kurangnya 6 aksara.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Kata laluan dan pengesahan tidak sama.';
    }

    // Semak emel dah wujud atau belum
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Emel ini sudah didaftarkan.';
        }
        $stmt->close();
    }

    // Simpan user baru
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "user")');
        $stmt->bind_param('sss', $name, $email, $hash);

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: login.php?registered=1');
            exit;
        } else {
            $errors[] = 'Pendaftaran gagal. Sila cuba lagi.';
            $stmt->close();
        }
    }
}

$pageTitle = 'Register';
require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Daftar Akaun</h3>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= htmlspecialchars($name) ?>" maxlength="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Emel</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($email) ?>" maxlength="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Laluan</label>
                        <input type="password" class="form-control" id="password" name="password"
                               minlength="6" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Sahkan Kata Laluan</label>
                        <input type="password" class="form-control" id="confirm_password"
                               name="confirm_password" minlength="6" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Dah ada akaun? <a href="login.php">Log masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>