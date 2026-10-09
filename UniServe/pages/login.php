<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Kalau dah login, terus ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation server-side
    if ($email === '') {
        $errors[] = 'Emel diperlukan.';
    }
    if ($password === '') {
        $errors[] = 'Kata laluan diperlukan.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            // Login berjaya
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Emel atau kata laluan salah.';
        }
    }
}

$pageTitle = 'Login';
require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">Log Masuk</h3>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">Pendaftaran berjaya! Sila log masuk.</div>
                <?php endif; ?>

                <?php if (isset($_GET['loggedout'])): ?>
                    <div class="alert alert-info">Anda telah log keluar.</div>
                <?php endif; ?>

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
                        <label for="email" class="form-label">Emel</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?= htmlspecialchars($email) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Laluan</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Log Masuk</button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Belum ada akaun? <a href="register.php">Daftar</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>