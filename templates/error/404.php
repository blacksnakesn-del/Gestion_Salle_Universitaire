<?php
ob_start();
?>
<div class="error-page"><span class="error-code">404</span><span class="eyebrow">SIGNAL PERDU</span><h1>Page introuvable</h1><p>La ressource demandée n'existe pas ou a été déplacée.</p><a class="btn btn-primary" href="/">Retour à l'accueil</a></div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout/base.php';
