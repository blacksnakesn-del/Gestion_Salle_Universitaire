<?php
ob_start();
?>
<section class="page-head">
    <div>
        <span class="eyebrow">CAMPUS / INFRASTRUCTURE</span>
        <h1>Les salles <span class="gradient-text">universitaires</span></h1>
        <p>Explorez les espaces disponibles et gérez leur état opérationnel.</p>
    </div>
    <a class="btn btn-primary" href="/salles/create">＋ Nouvelle salle</a>
</section>

<?php if ($success): ?>
    <div class="alert alert-success">✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-card"><span>Total</span><strong><?= $salles->count() ?></strong><small>Salles enregistrées</small></div>
    <div class="stat-card"><span>Actives</span><strong><?= $salles->where('active', true)->count() ?></strong><small>Disponibles</small></div>
    <div class="stat-card"><span>Capacité</span><strong><?= $salles->sum('capacite') ?></strong><small>Places cumulées</small></div>
</div>

<?php if ($salles->isEmpty()): ?>
    <div class="empty-state card"><div class="empty-icon">◇</div><h2>Aucune salle</h2><p>Commencez par enregistrer votre première salle.</p><a class="btn btn-primary" href="/salles/create">Créer une salle</a></div>
<?php else: ?>
    <div class="room-grid">
        <?php foreach ($salles as $salle): ?>
            <article class="room-card card">
                <div class="room-top">
                    <span class="room-id">ROOM-<?= str_pad((string) $salle->id, 3, '0', STR_PAD_LEFT) ?></span>
                    <?php if ($salle->active): ?>
                        <span class="badge badge-success">● Active</span>
                    <?php else: ?>
                        <span class="badge badge-danger">● Inactive</span>
                    <?php endif; ?>
                </div>
                <h2><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="muted">⌖ <?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></p>
                <div class="room-meta">
                    <span>👥 <?= (int) $salle->capacite ?> places</span>
                    <span><?= htmlspecialchars(ucfirst($salle->type), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="card-actions">
                    <a class="btn btn-secondary" href="/salles/<?= (int) $salle->id ?>">Détails</a>
                    <a class="btn btn-ghost" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
