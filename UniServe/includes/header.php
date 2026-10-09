<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Campus Service Hub
    </title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Campus Service Hub CSS -->
    <link
        rel="stylesheet"
        href="<?= BASE_URL ?>assets/style.css"
    >
</head>

<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark black-navbar">
    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="<?= BASE_URL ?>pages/index.php"
        >
            Campus Service Hub
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNav"
            aria-controls="mainNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= BASE_URL ?>pages/index.php"
                    >
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="<?= BASE_URL ?>pages/search.php"
                    >
                        Search Services
                    </a>
                </li>

                <?php if (isLoggedIn()): ?>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>pages/dashboard.php"
                        >
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>pages/service_form.php"
                        >
                            Add Service
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

            <ul class="navbar-nav align-items-lg-center">

                <?php if (isLoggedIn()): ?>

                    <li class="nav-item">
                        <span class="navbar-text me-lg-3">
                            Hi,
                            <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>

                            <?php if (isAdmin()): ?>
                                <span class="badge admin-badge">
                                    Admin
                                </span>
                            <?php endif; ?>
                        </span>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>pages/logout.php"
                        >
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="<?= BASE_URL ?>pages/login.php"
                        >
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link register-link"
                            href="<?= BASE_URL ?>pages/register.php"
                        >
                            Register
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>

<main class="container my-4 flex-grow-1"><?php /* Page content starts here */ ?>