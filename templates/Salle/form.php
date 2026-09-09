<?php
ob_start();
$types = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];
?>
<section class="page-head compact"><div><a class="back-link" href="/salles">← Retour</a><span class="eyebrow">CONFIGURATION / SALLE</span><h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1><p>Renseignez les caractéristiques de l'espace.</p></div></section>

<div class="form-shell card">
<form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" novalidate>
    <div class="form-grid">
        <div class="form-group full"><label for="nom">Nom de la salle</label><input class="form-control <?= isset($errors['nom']) ? 'input-error' : '' ?>" id="nom" name="nom" value="<?= htmlspecialchars((string) ($old['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex. Salle B12" required><?php if (isset($errors['nom'])): ?><span class="field-error"><?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></div>
        <div class="form-group"><label for="batiment">Bâtiment</label><input class="form-control <?= isset($errors['batiment']) ? 'input-error' : '' ?>" id="batiment" name="batiment" value="<?= htmlspecialchars((string) ($old['batiment'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex. Bâtiment A" required><?php if (isset($errors['batiment'])): ?><span class="field-error"><?= htmlspecialchars($errors['batiment'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></div>
        <div class="form-group"><label for="capacite">Capacité</label><input type="number" min="1" max="1000" class="form-control <?= isset($errors['capacite']) ? 'input-error' : '' ?>" id="capacite" name="capacite" value="<?= htmlspecialchars((string) ($old['capacite'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="40" required><?php if (isset($errors['capacite'])): ?><span class="field-error"><?= htmlspecialchars($errors['capacite'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></div>
        <div class="form-group"><label for="type">Type</label><select class="form-control <?= isset($errors['type']) ? 'input-error' : '' ?>" id="type" name="type" required><option value="">Sélectionner</option><?php foreach ($types as $type): ?><option value="<?= $type ?>" <?= ($old['type'] ?? '') === $type ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($type), ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select><?php if (isset($errors['type'])): ?><span class="field-error"><?= htmlspecialchars($errors['type'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></div>
        <div class="form-group switch-group"><label class="switch-label"><input type="checkbox" name="active" value="1" <?= in_array((string) ($old['active'] ?? '1'), ['1', 'true'], true) ? 'checked' : '' ?>><span class="switch"></span><span>Salle active</span></label><?php if (isset($errors['active'])): ?><span class="field-error"><?= htmlspecialchars($errors['active'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></div>
    </div>
    <div class="form-actions"><a class="btn btn-secondary" href="/salles">Annuler</a><button class="btn btn-primary" type="submit"><?= htmlspecialchars($formMethodLabel, ENT_QUOTES, 'UTF-8') ?> →</button></div>
</form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
