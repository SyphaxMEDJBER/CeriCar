<?php
use yii\helpers\Html;
?>

<?php foreach ($resultats as $r): ?>
<div class="col-md-6 col-lg-4">
    <div class="card search-card h-100 d-flex flex-column <?= $r['complet'] ? 'card-complet' : '' ?>">

    <div class="card-body">

        <!-- Conducteur + badge -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="card-title mb-0"><?= Html::encode($r['conducteur']." ".$r['conducteurnom']) ?></h5>

            <?php if ($r['complet']): ?>
                <span class="badge badge-complet">COMPLET</span>
            <?php else: ?>
                <span class="badge badge-dispo">
                    DISPONIBLE (<?= $r['places'] ?>)
                </span>
            <?php endif; ?>
        </div>


        
        <!-- TIMELINE HEURES-->
        <div class="bb-time">

            <div class="bb-track">
                <span class="bb-bar"></span>
                <span class="bb-duration"><?="Départ: ".Html::encode($r['heure']).'h'?></span>
                <span class="bb-bar"></span>
            </div>

        </div>

        <div class="bb-cities">
            <span><?= Html::encode($depart) ?></span>
            <span><?= Html::encode($arrivee) ?></span>
        </div>


     

        <!-- INFOS -->
        <p class="mb-1">🚗 <?= Html::encode($r['marque']) ?> – <?= Html::encode($r['type']) ?></p>
        <p class="mb-2">🎒 Bagages : <?= Html::encode($r['bagages']) ?></p>

        <?php if (!empty($r['contraintes'])): ?>
            <div class="contrainte-line mt-2">
                ⚠️ <?= Html::encode($r['contraintes']) ?>
            </div>
        <?php endif; ?>

    </div>


    <div class="card-footer mt-auto d-flex justify-content-between align-items-center px-3 py-2">

        <div class="fw-bold fs-5 text-info">
            <?= number_format($r['prix'], 2) ?> €
        </div>
        <button
            class="btn btn-outline-info btn-sm btn-reserver"
            data-voyage-id="<?= $r['voyage_id'] ?? '' ?>"
            <?= $r['complet'] ? 'disabled' : '' ?>
        >
            Réserver
        </button>
    </div>


    </div>
</div>
<?php endforeach; ?>










