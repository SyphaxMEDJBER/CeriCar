<?php
/** @var $user app\models\Internaute */
// Enveloppe page complète pour la liste des réservations.
?>

<div class="page-shell">
    <!-- Inclusion du partiel en mode page complète -->
    <?= $this->render('_reservations', [
        'user' => $user,
        'embedded' => false,
    ]) ?>
</div>
