<?php
ob_start();
?>
<div class="error-page"><span class="error-code">405</span><span class="eyebrow">ACTION BLOQUÉE</span><h1>Méthode non autorisée</h1><p>Cette action HTTP n'est pas disponible pour cette ressource.</p><a class="btn btn-primary" href="/">Retour à l'accueil</a></div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
