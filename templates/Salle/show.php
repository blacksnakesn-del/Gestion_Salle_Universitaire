<?php

use App\Model\Salle;

ob_start();
?>
<section class="page-head">
    <div>
        <a class="back-link" href="/salles">← Retour aux salles</a>
        <span class="eyebrow">FICHE SALLE</span>
        <h1><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="action-row">
        <a class="btn btn-secondary" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
        <?php if ($salle->active): ?><a class="btn btn-primary" href="/reservations/create?salle_id=<?= (int) $salle->id ?>">Réserver cette salle</a><?php endif; ?>
    </div>
</section>

<?php if (!empty($success)): ?><div class="alert alert-success">✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="detail-grid">
    <section class="card detail-hero">
        <div class="hero-symbol">U</div>
        <div><span class="eyebrow">ROOM-<?= str_pad((string) $salle->id, 3, '0', STR_PAD_LEFT) ?></span><h2><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h2><p class="muted">Espace universitaire</p></div>
        <span class="status-dot <?= $salle->active ? 'online' : 'offline' ?>"></span>
    </section>

    <section class="detail-info card">
        <div class="info-item"><span>BATIMENT</span><strong><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></strong></div>
        <div class="info-item"><span>CAPACITÉ</span><strong><?= (int) $salle->capacite ?> places</strong></div>
        <div class="info-item"><span>TYPE</span><strong><?= htmlspecialchars(ucfirst($salle->type), ENT_QUOTES, 'UTF-8') ?></strong></div>
        <div class="info-item"><span>STATUT</span><strong><?= $salle->active ? 'Active' : 'Inactive' ?></strong></div>
    </section>
</div>

<section class="section-block">
    <div class="section-title"><div><span class="eyebrow">PLANNING</span><h2>Réservations de la salle</h2></div></div>
    <?php if ($reservations->isEmpty()): ?>
        <div class="card empty-inline">Aucune réservation pour cette salle.</div>
    <?php else: ?>
        <div class="table-card card"><div class="table-wrapper"><table class="data-table"><thead><tr><th>Responsable</th><th>Motif</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead><tbody>
        <?php foreach ($reservations as $reservation): ?>
            <tr><td><strong><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></small></td><td><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($reservation->date_debut->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($reservation->date_fin->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td><td><span class="badge <?= $reservation->statut === 'confirmée' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></span></td></tr>
        <?php endforeach; ?>
        </tbody></table></div></div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
