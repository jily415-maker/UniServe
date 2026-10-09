<?php
require_once '../includes/auth.php';

$pageTitle = 'Home';
require_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="p-5 mb-4 text-center bg-light rounded-4">
        <h1 class="display-5 fw-bold">
            Welcome to Campus Service Hub
        </h1>

        <p class="fs-4 mt-3">
            Student Skills &amp; Services Platform
        </p>

        <p class="text-muted">
            Platform perkhidmatan dan kemahiran pelajar kampus.
        </p>

        <a
            href="<?= BASE_URL ?>pages/search.php"
            class="btn btn-primary btn-lg mt-3"
        >
            Cari Perkhidmatan
        </a>

        <?php if (!isLoggedIn()): ?>
            <div class="mt-4">
                <span>Belum mempunyai akaun?</span>
                <a href="<?= BASE_URL ?>pages/register.php">
                    Daftar sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a
                    href="<?= BASE_URL ?>pages/dashboard.php"
                    class="btn btn-outline-dark"
                >
                    Pergi ke Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="row g-4 mt-2">

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <h4>Discover Services</h4>
                    <p class="text-muted">
                        Explore services and skills offered by campus students.
                    </p>
                    <a
                        href="<?= BASE_URL ?>pages/search.php"
                        class="btn btn-outline-dark"
                    >
                        Search Services
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <h4>Share Your Skills</h4>
                    <p class="text-muted">
                        Publish your services and let other students discover them.
                    </p>

                    <?php if (isLoggedIn()): ?>
                        <a
                            href="<?= BASE_URL ?>pages/service_form.php"
                            class="btn btn-outline-dark"
                        >
                            Add Service
                        </a>
                    <?php else: ?>
                        <a
                            href="<?= BASE_URL ?>pages/login.php"
                            class="btn btn-outline-dark"
                        >
                            Login to Continue
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <h4>Manage Services</h4>
                    <p class="text-muted">
                        Manage your service listings through your dashboard.
                    </p>

                    <?php if (isLoggedIn()): ?>
                        <a
                            href="<?= BASE_URL ?>pages/dashboard.php"
                            class="btn btn-outline-dark"
                        >
                            Open Dashboard
                        </a>
                    <?php else: ?>
                        <a
                            href="<?= BASE_URL ?>pages/login.php"
                            class="btn btn-outline-dark"
                        >
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>