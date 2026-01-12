<?php
use yii\helpers\Html;

/** @var $user app\models\Internaute */

// Page profil : résumé utilisateur + sections embarquées.
// Prépare l'avatar et les initiales de secours.
$photo = trim((string)($user->photo ?? ''));
$prenom = trim((string)($user->prenom ?? ''));
$nom = trim((string)($user->nom ?? ''));
$initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
if ($initials === '') {
    $initials = '?';
}
?>

<div class="d-flex justify-content-center mt-5">
    <div class="profile-card shadow-lg">

        <!-- HEADER -->
        <!-- En-tête avec avatar + identité -->
        <div class="profile-header d-flex align-items-center gap-4">
            <div class="profile-avatar" role="img" aria-label="Photo profil">
                <?php if ($photo !== ''): ?>
                    <img
                        src="<?= Html::encode($photo) ?>"
                        alt=""
                        onload="this.nextElementSibling.classList.add('d-none');"
                        onerror="this.style.display='none';this.nextElementSibling.classList.remove('d-none');"
                    >
                    <!-- Fallback affiché uniquement si l'image ne charge pas -->
                    <div class="profile-avatar-fallback" aria-hidden="true">
                        <?= Html::encode($initials) ?>
                    </div>
                <?php else: ?>
                    <div class="profile-avatar-fallback" aria-hidden="true">
                        <?= Html::encode($initials) ?>
                    </div>
                <?php endif; ?>
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

        <!-- Actions -->
        <div class="profile-actions d-grid gap-3">
            <a href="<?= \yii\helpers\Url::to(['site/reservations']) ?>" class="btn btn-primary btn-lg js-profile-load">Mes réservations</a>
            <a href="<?= \yii\helpers\Url::to(['site/mes-voyages']) ?>" class="btn btn-accent btn-lg js-profile-load">Mes voyages</a>
            <?php if ($user->permis): ?>
                <a href="<?= \yii\helpers\Url::to(['site/proposer']) ?>" class="btn btn-outline-info btn-lg js-profile-load">Proposer un voyage</a>
            <?php else: ?>
                <button class="btn btn-outline-secondary btn-lg" disabled>Proposer un voyage (permis requis)</button>
            <?php endif; ?>
        </div>

        <!-- Contenu chargé en AJAX -->
        <div id="profile-content" class="profile-embed mt-4"></div>

    </div>
</div>
