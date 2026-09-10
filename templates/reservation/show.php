<?php
ob_start();
?>
<section class="page-head"><div><a class="back-link" href="/reservations">← Retour aux réservations</a><span class="eyebrow">RESERVATION / #<?= (int) $reservation->id ?></span><h1>Détail de la réservation</h1><p><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></p></div></section>

<div class="reservation-layout">
    <section class="card reservation-main"><div class="reservation-status"><span class="badge <?= $reservation->statut === 'confirmée' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></span></div><h2><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></h2><p class="muted">Salle <strong><?= htmlspecialchars($reservation->salle?->nom ?? 'Inconnue', ENT_QUOTES, 'UTF-8') ?></strong></p><div class="timeline"><div><span>Début</span><strong><?= htmlspecialchars($reservation->date_debut->format('d/m/Y · H:i'), ENT_QUOTES, 'UTF-8') ?></strong></div><div class="timeline-line"></div><div><span>Fin</span><strong><?= htmlspecialchars($reservation->date_fin->format('d/m/Y · H:i'), ENT_QUOTES, 'UTF-8') ?></strong></div></div></section>
    <aside class="card reservation-side"><span class="eyebrow">RESPONSABLE</span><h3><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></p><hr><span class="eyebrow">SALLE</span><h3><?= htmlspecialchars($reservation->salle?->nom ?? 'Inconnue', ENT_QUOTES, 'UTF-8') ?></h3><p><?= htmlspecialchars($reservation->salle?->batiment ?? '', ENT_QUOTES, 'UTF-8') ?></p><?php if ($reservation->statut === 'confirmée'): ?><form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel" onsubmit="return confirm('Annuler cette réservation ?');"><input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><button class="btn btn-danger full-width" type="submit">Annuler la réservation</button></form><?php endif; ?></aside>
</div>
<?php if (!empty($success)): ?><div class="alert alert-success">✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger">! <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
