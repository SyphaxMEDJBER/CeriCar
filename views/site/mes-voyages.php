<?php
/** @var $user app\models\Internaute */
// Enveloppe page complète pour la liste des voyages.
?>

<div class="page-shell">
    <!-- Inclusion du partiel en mode page complète -->
    <?= $this->render('_mes_voyages', [
        'user' => $user,
        'embedded' => false,
    ]) ?>
</div>
