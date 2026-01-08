<?php
/** @var $user app\models\Internaute */
?>

<div class="page-shell">
    <?= $this->render('_reservations', [
        'user' => $user,
        'embedded' => false,
    ]) ?>
</div>
