<?php

use App\Model\Salle;

ob_start();
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
    <div>
        <a href="/salles" class="btn btn-secondary" style="margin-bottom: 12px; display: inline-flex; font-size: 0.85rem; padding: 6px 12px;">
            ← Retour aux salles
        </a>
        <h1><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a class="btn btn-secondary" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
        <?php if ($salle->active): ?>
            <a class="btn btn-primary" href="/reservations/create?salle_id=<?= (int) $salle->id ?>">Réserver cette salle</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #86efac; margin-bottom: 20px;">
        ✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<div class="grid" style="margin-bottom: 35px;">
    <div class="card" style="display: flex; align-items: center; justify-content: space-between; grid-column: 1 / -1;">
        <div>
            <span style="font-size: 0.75rem; color: var(--text-muted); letter-spacing: 1px; display: block; margin-bottom: 4px;">
                ROOM-<?= str_pad((string) $salle->id, 3, '0', STR_PAD_LEFT) ?>
            </span>
            <h2 style="font-size: 1.5rem; margin-bottom: 4px;"><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></h2>
            <p style="color: var(--text-muted); margin: 0;">Espace universitaire</p>
        </div>
        <div>
            <?php if ($salle->active): ?>
                <span class="badge badge-success">● Active</span>
            <?php else: ?>
                <span class="badge badge-danger">● Inactive</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 6px;">BÂTIMENT</span>
        <strong style="font-size: 1.1rem; color: var(--text);"><?= htmlspecialchars($salle->batiment, ENT_QUOTES, 'UTF-8') ?></strong>
    </div>

    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 6px;">CAPACITÉ</span>
        <strong style="font-size: 1.1rem; color: var(--primary);"><?= (int) $salle->capacite ?> places</strong>
    </div>

    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 6px;">TYPE</span>
        <strong style="font-size: 1.1rem; color: var(--text);"><?= htmlspecialchars(ucfirst($salle->type), ENT_QUOTES, 'UTF-8') ?></strong>
    </div>

    <div class="card" style="text-align: center;">
        <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 6px;">STATUT</span>
        <strong>
            <?php if ($salle->active): ?>
                <span class="badge badge-success">Active</span>
            <?php else: ?>
                <span class="badge badge-danger">Inactive</span>
            <?php endif; ?>
        </strong>
    </div>
</div>

<div style="margin-top: 40px;">
    <div style="margin-bottom: 20px;">
        <span style="font-size: 0.75rem; color: var(--primary); letter-spacing: 1px; font-weight: 700; text-transform: uppercase;">PLANNING</span>
        <h2 style="font-size: 1.4rem; margin-top: 4px;">Réservations de la salle</h2>
    </div>

    <?php if ($reservations->isEmpty()): ?>
        <div class="card" style="text-align: center; color: var(--text-muted); padding: 30px;">
            Aucune réservation enregistrée pour cette salle.
        </div>
    <?php else: ?>
        <div class="card" style="padding: 0; overflow: hidden;">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Responsable</th>
                            <th>Motif</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td>
                                    <strong style="display: block; color: var(--text);"><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></strong>
                                    <small style="color: var(--text-muted);"><?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($reservation->date_debut->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($reservation->date_fin->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="badge <?= $reservation->statut === 'confirmée' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';