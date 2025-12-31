<?php
use yii\helpers\Html;
?>

<?php foreach ($resultats as $r): ?>
<div class="col-md-6 col-lg-4">
    <div
        class="card search-card h-100 d-flex flex-column <?= $r['complet'] ? 'card-complet' : '' ?> <?= $r['type'] === 'correspondance' ? 'card-correspondance' : '' ?>"
        <?php if ($r['type'] === 'correspondance'): ?>
            data-voyage-ids="<?= Html::encode(implode(',', $r['voyage_ids'])) ?>"
            data-nb="<?= Html::encode((int)($nb ?? 1)) ?>"
        <?php endif; ?>
    >

        <div class="card-body">

            <div class="result-header">
                <div class="result-title">Trajet</div>
                <?php if ($r['complet']): ?>
                    <span class="result-badge badge-complet">Complet</span>
                <?php else: ?>
                    <span class="result-badge badge-dispo">
                        Disponible (<?= $r['places'] ?>)
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($r['type'] === 'direct'): ?>
                <div class="bb-time">
                    <div class="bb-track">
                        <span class="bb-bar"></span>
                        <span class="bb-duration"><?= Html::encode($r['heure']) ?> h</span>
                        <span class="bb-bar"></span>
                    </div>
                </div>

                <div class="bb-cities">
                    <span><?= Html::encode($depart) ?></span>
                    <span><?= Html::encode($arrivee) ?></span>
                </div>

                <div class="result-meta">
                    <div class="meta-row">
                        <span class="meta-label">Vehicule</span>
                        <span class="meta-value"><?= Html::encode($r['marque']) ?> – <?= Html::encode($r['typev']) ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Bagages</span>
                        <span class="meta-value"><?= Html::encode($r['bagages']) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <?php
                    [$h1, $h2] = explode(' → ', $r['heure']);
                ?>

                <div class="bb-time">
                    <div class="bb-track">
                        <span class="bb-bar"></span>
                        <span class="bb-duration"><?= Html::encode($h1) ?> h</span>
                        <span class="bb-bar"></span>
                        <span class="bb-duration"><?= Html::encode($h2) ?> h</span>
                        <span class="bb-bar"></span>
                    </div>
                </div>

                <div class="bb-cities">
                    <span><?= Html::encode($depart) ?></span>
                    <span>Correspondance</span>
                    <span><?= Html::encode($arrivee) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($r['type'] === 'direct' && !empty($r['contraintes'])): ?>
                <div class="contrainte-line mt-2">
                    <?= Html::encode($r['contraintes']) ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="card-footer mt-auto d-flex justify-content-between align-items-center px-3 py-2 result-footer">
            <div class="result-price">
                <?= number_format($r['prix'], 2) ?> €
            </div>

            <button
                class="btn btn-sm btn-reserver"
                data-voyage-ids="<?= implode(',', $r['voyage_ids']) ?>"
                <?= $r['complet'] ? 'disabled' : '' ?>
            >
                Reserver
            </button>
        </div>

    </div>
</div>
<?php endforeach; ?>
