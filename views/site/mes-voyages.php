<?php
/** @var $user app\models\Internaute */
?>

<div class="page-shell">
    <?= $this->render('_mes_voyages', [
        'user' => $user,
        'embedded' => false,
    ]) ?>
</div>
