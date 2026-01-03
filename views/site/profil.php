<?php
use yii\helpers\Html;

/** @var $user app\models\Internaute */
?>

<div class="d-flex justify-content-center mt-5">
    <div class="profile-card shadow-lg">

        <!-- HEADER -->
        <div class="profile-header d-flex align-items-center gap-4">
            <div class="profile-avatar">
                <img src="<?= Html::encode($user->photo) ?>" alt="Photo profil">
            </div>
            <div>
                <h2 class="mb-0"><?= Html::encode($user->prenom . ' ' . $user->nom) ?></h2>
                <span class="text-muted">@<?= Html::encode($user->pseudo) ?></span>
            </div>
        </div>

        <hr>

        <!-- INFOS -->
        <div class="profile-infos">
            <p><strong>Email :</strong> <?= Html::encode($user->mail) ?></p>
            <p><strong>Permis :</strong> <?= $user->permis ? 'Oui' : 'Non' ?></p>
        </div>

        <hr>

        <div class="profile-actions d-grid gap-3">
            <a href="<?= \yii\helpers\Url::to(['site/reservations']) ?>" class="btn btn-primary btn-lg">Mes réservations</a>
            <a href="<?= \yii\helpers\Url::to(['site/mes-voyages']) ?>" class="btn btn-outline-dark btn-lg">Mes voyages</a>
            <?php if ($user->permis): ?>
                <a href="<?= \yii\helpers\Url::to(['site/proposer']) ?>" class="btn btn-outline-info btn-lg">Proposer un voyage</a>
            <?php else: ?>
                <button class="btn btn-outline-secondary btn-lg" disabled>Proposer un voyage (permis requis)</button>
            <?php endif; ?>
        </div>

    </div>
</div>
