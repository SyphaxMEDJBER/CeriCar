<?php
use yii\helpers\Html;
?>

<?php foreach ($resultats as $r): ?>
<div class="col-md-6 col-lg-4">
    <div class="card search-card h-100 d-flex flex-column <?= $r['complet'] ? 'card-complet' : '' ?>">

        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0"><?= Html::encode($r['conducteur']) ?></h5>

                <?php if ($r['complet']): ?>
                    <span class="badge badge-complet">COMPLET</span>
                <?php else: ?>
                    <span class="badge badge-dispo">
                        DISPONIBLE (<?= $r['places'] ?>)
                    </span>
                <?php endif; ?>
            </div>

            <p class="trajet mb-2">
                <?= Html::encode($depart) ?> → <?= Html::encode($arrivee) ?>
            </p>

            <p class="mb-1">🕒 <?= Html::encode($r['heure']) ?> h</p>
            <p class="mb-1">🚗 <?= Html::encode($r['marque']) ?> – <?= Html::encode($r['type']) ?></p>
            <p class="mb-2">🎒 Bagages : <?= Html::encode($r['bagages']) ?></p>

            <?php if (!empty($r['contraintes'])): ?>
                <div class="contrainte-line mt-2">
                    ⚠️ <?= Html::encode($r['contraintes']) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-footer text-end mt-auto">
            <span class="price"><?= number_format($r['prix'], 2) ?> €</span>
        </div>

    </div>
</div>
<?php endforeach; ?>
