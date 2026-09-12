<?php

declare(strict_types=1);

$error = $error ?? null;
$old = $old ?? [];
$csrfToken = $csrfToken ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion · UniReserve</title>
    <link rel="stylesheet" href="/assets/Css/app.css">
</head>
<body class="auth-page">
<main class="auth-shell">
    <section class="auth-intro">
        <span class="eyebrow">UNIRESERVE / ACCÈS</span>
        <h1>Votre espace de réservation universitaire.</h1>
        <p>Connectez-vous pour gérer les salles et les créneaux disponibles.</p>
    </section>

    <section class="card auth-card">
        <div class="brand auth-brand">
            <span class="brand-mark">U</span>
            <span><strong>UNI</strong>RESERVE</span>
        </div>
        <h2>Connexion</h2>
        <p class="muted">Accédez à votre tableau de gestion.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/login" class="auth-form">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input class="form-control" type="email" id="email" name="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary full-width" type="submit">Se connecter</button>
        </form>
        <p class="auth-switch">Pas encore de compte ? <a href="/register">Créer un compte</a></p>
    </section>
</main>
</body>
</html>
