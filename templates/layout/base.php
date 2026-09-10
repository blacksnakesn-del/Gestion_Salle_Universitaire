<?php

declare(strict_types=1);

$title = $title ?? 'Gestion Universitaire';
$content = $content ?? '';
$success = $success ?? null;
$error = $error ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des réservations de salles universitaires">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> · UniReserve</title>
    <link rel="stylesheet" href="/assets/Css/app.css">
</head>
<body>
<div class="ambient ambient-one"></div>
<div class="ambient ambient-two"></div>

<header class="navbar">
    <a class="brand" href="/">
        <span class="brand-mark">U</span>
        <span><strong>UNI</strong>RESERVE</span>
    </a>

    <nav class="nav-links" aria-label="Navigation principale">
        <a href="/">Accueil</a>
        <a href="/salles">Salles</a>
        <a href="/reservations">Réservations</a>
        <a class="nav-cta" href="/reservations/create">+ Réserver</a>
    </nav>
</header>

<main class="container">
    <?php if ($success): ?>
        <div class="alert alert-success">✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">! <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="footer">
    <span>UNIRESERVE</span>
    <span>Système de gestion universitaire · PHP <?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?></span>
</footer>
</body>
</html>
