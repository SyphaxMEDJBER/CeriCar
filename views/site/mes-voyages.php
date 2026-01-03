<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $user app\models\Internaute */
?>

<div class="page-shell">
    <div class="section-card">
        <div class="section-header">
            <h2>Mes voyages</h2>
            <p class="section-subtitle">Tes voyages proposes et leurs details.</p>
        </div>

        <?php if (!$user->permis): ?>
            <div class="empty-state">Permis requis pour proposer et afficher vos voyages.</div>
        <?php elseif (!empty($user->voyages)): ?>
            <div class="row g-3">
                <?php foreach ($user->voyages as $v): ?>
                    <?php $trajet = $v->trajetObj; ?>
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-title">
                                <?= Html::encode($trajet->depart ?? '') ?> → <?= Html::encode($trajet->arrivee ?? '') ?>
                            </div>
                            <div class="info-row">
                                <span>Places dispo</span>
                                <strong><?= Html::encode($v->nbplacedispo) ?></strong>
                            </div>
                            <div class="info-row">
                                <span>Bagages</span>
                                <strong><?= Html::encode($v->nbbagage) ?></strong>
                            </div>
                            <div class="info-row">
                                <span>Heure depart</span>
                                <strong><?= Html::encode($v->heuredepart) ?> h</strong>
                            </div>
                            <div class="info-row">
                                <span>Contraintes</span>
                                <strong><?= Html::encode($v->contraintes) ?></strong>
                            </div>
                            <div class="info-row">
                                <span>Vehicule</span>
                                <strong><?= Html::encode($v->marqueVehicule->marquev ?? '') ?> – <?= Html::encode($v->typeVehicule->typev ?? '') ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Aucun voyage propose pour le moment.</div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= Url::to(['site/profil']) ?>" class="btn btn-outline-light">Retour profil</a>
        </div>
    </div>
</div>
