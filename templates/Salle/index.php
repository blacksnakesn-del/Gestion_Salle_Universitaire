<?php
ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
    <div>
        <h1>Les salles universitaires</h1>
        <p>Explorez les espaces disponibles et gérez leur état opérationnel.</p>
    </div>
    <a class="btn btn-primary" href="/salles/create">＋ Nouvelle salle</a>
</div>

<?php if ($success): ?>
    <div class="alert" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #86efac; margin-bottom: 20px;">
        ✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="grid" style="margin-bottom: 30px;">
    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 5px;">TOTAL</span>
        <strong style="font-size: 2rem; color: var(--primary); display: block;"><?= $salles->count() ?></strong>
        <small style="color: var(--text-muted);">Salles enregistrées</small>
    </div>
    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 5px;">ACTIVES</span>
        <strong style="font-size: 2rem; color: var(--success); display: block;"><?= $salles->where('active', true)->count() ?></strong>
        <small style="color: var(--text-muted);">Disponibles</small>
    </div>
    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 5px;">CAPACITÉ</span>
        <strong style="font-size: 2rem; color: var(--text); display: block;"><?= $salles->sum('capacite') ?></strong>
        <small style="color: var(--text-muted);">Places cumulées</small>
    </div>
</div>

<?php if ($salles->isEmpty()): ?>
    <div class="card" style="text-align: center; padding: 50px 20px;">
        <div style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 10px;">◇</div>
        <h2>Aucune salle</h2>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Commencez par enregistrer votre première salle.</p>
        <a class="btn btn-primary" href="/salles/create">Créer une salle</a>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($salles as $salle): ?>
            <article class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); letter-spacing: 1px;">ROOM-<?= str_pad((string) $salle->id, 3, '0', STR_PAD_LEFT) ?></span>
                        <?php if ($salle->active): ?>
                            <span class="badge badge-success">● Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">● Inactive</span>
                        <?php endif; ?>
                    </div>

                    <h2 style="font-size: 1.25rem; margin-bottom: 4px;"><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 15px;">⌖ <?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></p>

                    <div style="display: flex; gap: 15px; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
                        <span>👥 <?= (int) $salle->capacite ?> places</span>
                        <span>•</span>
                        <span><?= htmlspecialchars(ucfirst($salle->type), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; border-top: 1px solid var(--border); padding-top: 15px;">
                    <a class="btn btn-secondary" style="flex: 1; text-align: center;" href="/salles/<?= (int) $salle->id ?>">Détails</a>
                    <a class="btn btn-secondary" style="flex: 1; text-align: center; background: transparent; border: 1px solid var(--border);" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';