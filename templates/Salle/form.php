<?php
ob_start();
$types = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];
?>

<div class="page-header">
    <a href="/salles" class="btn btn-secondary" style="margin-bottom: 12px; display: inline-flex; font-size: 0.85rem; padding: 6px 12px;">
        ← Retour
    </a>
    <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Renseignez les caractéristiques de l'espace.</p>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" novalidate>
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="grid">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="nom">Nom de la salle</label>
                <input class="form-control" id="nom" name="nom" value="<?= htmlspecialchars((string) ($old['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex. Salle B12" required>
                <?php if (isset($errors['nom'])): ?>
                    <span class="badge badge-danger" style="margin-top: 6px;"><?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="batiment">Bâtiment</label>
                <input class="form-control" id="batiment" name="batiment" value="<?= htmlspecialchars((string) ($old['batiment'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex. Bâtiment A" required>
                <?php if (isset($errors['batiment'])): ?>
                    <span class="badge badge-danger" style="margin-top: 6px;"><?= htmlspecialchars($errors['batiment'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="capacite">Capacité</label>
                <input type="number" min="1" max="1000" class="form-control" id="capacite" name="capacite" value="<?= htmlspecialchars((string) ($old['capacite'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="40" required>
                <?php if (isset($errors['capacite'])): ?>
                    <span class="badge badge-danger" style="margin-top: 6px;"><?= htmlspecialchars($errors['capacite'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="type">Type d'espace</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Sélectionner</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type ?>" <?= ($old['type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($type), ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['type'])): ?>
                    <span class="badge badge-danger" style="margin-top: 6px;"><?= htmlspecialchars($errors['type'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 25px;">
                <input type="checkbox" id="active" name="active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);" <?= in_array((string) ($old['active'] ?? '1'), ['1', 'true'], true) ? 'checked' : '' ?>>
                <label for="active" style="margin-bottom: 0; cursor: pointer;">Salle active</label>
                <?php if (isset($errors['active'])): ?>
                    <span class="badge badge-danger"><?= htmlspecialchars($errors['active'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
            <a class="btn btn-secondary" href="/salles">Annuler</a>
            <button class="btn btn-primary" type="submit"><?= htmlspecialchars($formMethodLabel, ENT_QUOTES, 'UTF-8') ?> →</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';