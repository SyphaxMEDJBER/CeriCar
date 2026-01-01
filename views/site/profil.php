<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $user app\models\Internaute */

$photo = trim((string)($user->photo ?? ''));
$hasPhoto = $photo !== '' && $photo !== 'default.png';
$initials = strtoupper(substr((string)$user->prenom, 0, 1) . substr((string)$user->nom, 0, 1));
?>

<div class="page-shell">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if ($hasPhoto): ?>
                    <img src="<?= Html::encode($photo) ?>" alt="Photo profil">
                <?php else: ?>
                    <div class="avatar-fallback"><?= Html::encode($initials) ?></div>
                <?php endif; ?>
            </div>
            <div class="profile-title">
                <h2><?= Html::encode($user->prenom . ' ' . $user->nom) ?></h2>
                <span class="profile-handle">@<?= Html::encode($user->pseudo) ?></span>
            </div>
        </div>

        <div class="profile-infos">
            <div class="profile-info">
                <span>Email</span>
                <strong><?= Html::encode($user->mail) ?></strong>
            </div>
            <div class="profile-info">
                <span>Permis</span>
                <strong><?= $user->permis ? 'Oui' : 'Non' ?></strong>
            </div>
        </div>

        <div class="profile-actions">
            <a href="<?= Url::to(['site/reservations']) ?>" class="btn btn-primary btn-lg">Mes réservations</a>
            <a href="<?= Url::to(['site/mes-voyages']) ?>" class="btn btn-outline-light btn-lg">Mes voyages</a>
            <?php if ($user->permis): ?>
                <a href="<?= Url::to(['site/proposer']) ?>" class="btn btn-outline-info btn-lg">Proposer un voyage</a>
            <?php else: ?>
                <button class="btn btn-outline-secondary btn-lg" disabled>Proposer un voyage (permis requis)</button>
            <?php endif; ?>
        </div>
    </div>
</div>
