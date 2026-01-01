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
        <?php else: ?>
            <?php
                $voyages = $user->voyages ?: [];
                usort($voyages, function ($a, $b) {
                    $aTime = $a->heuredepart ?? 0;
                    $bTime = $b->heuredepart ?? 0;
                    return $aTime <=> $bTime;
                });
            ?>
        <?php endif; ?>

        <?php if ($user->permis && !empty($voyages)): ?>
            <div class="row g-3">
                <?php foreach ($voyages as $v): ?>
                    <?php $trajet = $v->trajetObj; ?>
                    <div class="col-md-6">
                        <div class="card search-card h-100">
                            <div class="card-body">
                                <div class="result-header">
                                    <div class="result-title">
                                        <?= Html::encode($trajet->depart ?? '') ?> → <?= Html::encode($trajet->arrivee ?? '') ?>
                                    </div>
                                    <span class="result-badge badge-dispo">Publié</span>
                                </div>
                                <div class="result-meta">
                                    <div class="meta-row">
                                        <span class="meta-label">Places dispo</span>
                                        <span class="meta-value"><?= Html::encode($v->nbplacedispo) ?></span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">Bagages</span>
                                        <span class="meta-value"><?= Html::encode($v->nbbagage) ?></span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">Heure depart</span>
                                        <span class="meta-value"><?= Html::encode($v->heuredepart) ?> h</span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">Contraintes</span>
                                        <span class="meta-value"><?= Html::encode($v->contraintes) ?></span>
                                    </div>
                                    <div class="meta-row">
                                        <span class="meta-label">Vehicule</span>
                                        <span class="meta-value"><?= Html::encode($v->marqueVehicule->marquev ?? '') ?> – <?= Html::encode($v->typeVehicule->typev ?? '') ?></span>
                                    </div>
                                </div>
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
