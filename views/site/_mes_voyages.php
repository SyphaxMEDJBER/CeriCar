<?php
// Partiel : liste des voyages proposés par l’utilisateur courant.
use yii\helpers\Html;
use yii\helpers\Url;

// Calcule l'heure d'arrivée estimée.
$formatArrivee = function ($heureDepart, $distanceKm) {
    $departMin = (int)$heureDepart * 60;
    $dureeMin = (int)round((float)$distanceKm);
    $arriveeMin = $departMin + $dureeMin;
    $h = (int)floor($arriveeMin / 60) % 24;
    $m = $arriveeMin % 60;
    return sprintf('%02d:%02d', $h, $m);
};

/** @var $user app\models\Internaute */
/** @var bool $embedded */
?>

<?php $embedded = !empty($embedded); ?>

<div class="section-card">
    <div class="section-header">
        <h2>Mes voyages</h2>
        <p class="section-subtitle">Tes voyages proposes et leurs details.</p>
    </div>

    <?php if (!$user->permis): ?>
        <!-- Permis manquant -->
        <div class="empty-state">Permis requis pour proposer et afficher vos voyages.</div>
    <?php elseif (!empty($user->voyages)): ?>
        <!-- Liste des voyages -->
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
                            <span>Arrivee</span>
                            <strong><?= Html::encode($formatArrivee($v->heuredepart ?? 0, $trajet->distance ?? 0)) ?></strong>
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
        <!-- Aucun voyage -->
        <div class="empty-state">Aucun voyage propose pour le moment.</div>
    <?php endif; ?>

    <?php if (!$embedded): ?>
        <!-- Retour profil si page complète -->
        <div class="mt-4">
            <a href="<?= Url::to(['site/profil']) ?>" class="btn btn-outline-light">Retour profil</a>
        </div>
    <?php endif; ?>
</div>
