<?php
ob_start();
?>
<section class="page-head"><div><span class="eyebrow">CAMPUS / PLANNING</span><h1>Réservations <span class="gradient-text">enregistrées</span></h1><p>Visualisez les réservations et filtrez-les par salle.</p></div><a class="btn btn-primary" href="/reservations/create">＋ Nouvelle réservation</a></section>

<form class="filter-bar card" method="get" action="/reservations"><div><label for="salle_id">Filtrer par salle</label><select class="form-control" id="salle_id" name="salle_id"><option value="">Toutes les salles</option><?php foreach ($salles as $salle): ?><option value="<?= (int) $salle->id ?>" <?= isset($selectedSalle) && $selectedSalle && $selectedSalle->id === $salle->id ? 'selected' : '' ?>><?= htmlspecialchars($salle->nom, ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div><button class="btn btn-secondary" type="submit">Filtrer</button></form>

<?php if ($success): ?><div class="alert alert-success">✓ <?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger">! <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="table-card card"><div class="table-wrapper"><table class="data-table"><thead><tr><th>Responsable</th><th>Salle</th><th>Motif</th><th>Créneau</th><th>Statut</th><th></th></tr></thead><tbody>
<?php if ($reservations->isEmpty()): ?><tr><td colspan="6" class="empty-table">Aucune réservation trouvée.</td></tr><?php else: ?>
<?php foreach ($reservations as $reservation): ?><tr><td><strong><?= htmlspecialchars($reservation->responsable, ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($reservation->email, ENT_QUOTES, 'UTF-8') ?></small></td><td><?= htmlspecialchars($reservation->salle?->nom ?? 'Salle supprimée', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($reservation->motif, ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($reservation->date_debut->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?><br><span class="muted">→ <?= htmlspecialchars($reservation->date_fin->format('H:i'), ENT_QUOTES, 'UTF-8') ?></span></td><td><span class="badge <?= $reservation->statut === 'confirmée' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($reservation->statut, ENT_QUOTES, 'UTF-8') ?></span></td><td><a class="table-link" href="/reservations/<?= (int) $reservation->id ?>">Ouvrir →</a></td></tr><?php endforeach; ?>
<?php endif; ?></tbody></table></div></div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
