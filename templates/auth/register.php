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
    <title>Inscription · UniReserve</title>
    <link rel="stylesheet" href="/assets/Css/app.css">
</head>
<body class="auth-page">
<main class="auth-shell">
    <section class="auth-intro">
        <span class="eyebrow">UNIRESERVE / INSCRIPTION</span>
        <h1>Créez votre accès à l’espace universitaire.</h1>
        <p>Inscrivez-vous pour gérer les salles et les réservations.</p>
    </section>

    <section class="card auth-card">
        <div class="brand auth-brand">
            <span class="brand-mark">U</span>
            <span><strong>UNI</strong>RESERVE</span>
        </div>
        <h2>Créer un compte</h2>
        <p class="muted">Vos informations restent nécessaires pour vous connecter.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/register" class="auth-form">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input class="form-control" type="text" id="name" name="name" value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="name" required autofocus>
            </div>
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input class="form-control" type="email" id="email" name="email" value="<?= htmlspecialchars((string) ($old['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input class="form-control" type="password" id="password" name="password" minlength="6" autocomplete="new-password" required>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" minlength="6" autocomplete="new-password" required>
            </div>
            <button class="btn btn-primary full-width" type="submit">Créer mon compte</button>
        </form>
        <p class="auth-switch">Vous avez déjà un compte ? <a href="/login">Se connecter</a></p>
    </section>
</main>
</body>
</html>
