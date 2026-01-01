<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $user app\models\Internaute */
?>

<div class="page-shell">
    <div class="section-card">
        <div class="section-header">
            <h2>Mes reservations</h2>
            <p class="section-subtitle">Toutes tes reservations en cours.</p>
        </div>

        <?php if (!empty($user->reservations)): ?>
            <div class="row g-3">
                <?php foreach ($user->reservations as $r): ?>
                    <?php $v = $r->voyageObj; ?>
                    <?php $trajet = $v ? $v->trajetObj : null; ?>
                    <div class="col-md-6">
                        <div class="info-card">
                            <?php if ($v && $trajet): ?>
                                <div class="info-title">
                                    <?= Html::encode($trajet->depart) ?> → <?= Html::encode($trajet->arrivee) ?>
                                </div>
                                <div class="info-row">
                                    <span>Places reservees</span>
                                    <strong><?= Html::encode($r->nbplaceresa) ?></strong>
                                </div>
                                <div class="info-row">
                                    <span>Conducteur</span>
                                    <strong><?= Html::encode(($v->conducteurObj->prenom ?? '') . ' ' . ($v->conducteurObj->nom ?? '')) ?></strong>
                                </div>
                                <div class="info-row">
                                    <span>Vehicule</span>
                                    <strong><?= Html::encode($v->marqueVehicule->marquev ?? '') ?> – <?= Html::encode($v->typeVehicule->typev ?? '') ?></strong>
                                </div>
                                <div class="info-row">
                                    <span>Tarif</span>
                                    <strong><?= Html::encode($v->tarif) ?> € / place</strong>
                                </div>
                            <?php else: ?>
                                <div>Voyage ou trajet introuvable pour cette reservation.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Aucune reservation pour le moment.</div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= Url::to(['site/profil']) ?>" class="btn btn-outline-light">Retour profil</a>
        </div>
    </div>
</div>
