<?php
use yii\helpers\Html;
?>

<?php
$directs = array_filter($resultats, function ($r) {
    return isset($r['type']) && $r['type'] === 'direct';
});
$correspondances = array_filter($resultats, function ($r) {
    return isset($r['type']) && $r['type'] === 'correspondance';
});
?>

<?php if (!empty($directs)): ?>
<div class="col-12">
    <h3 class="result-section-title">Voyages directs</h3>
</div>
<?php foreach ($directs as $r): ?>
<div class="col-md-6 col-lg-4 corr-col">
    <div class="card search-card h-100 d-flex flex-column <?= $r['complet'] ? 'card-complet' : '' ?>">

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
                    $heures = preg_split('/\s*→\s*/', (string)($r['heure'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                    if (empty($heures)) {
                        $heures = [''];
                    }
                    $nbCorr = max(count($heures) - 1, 1);
                    $label = $nbCorr > 1 ? $nbCorr . ' correspondances' : 'Correspondance';
                ?>

                <div class="corr-summary-block">
                    <div class="bb-time">
                        <div class="bb-track">
                            <span class="bb-bar"></span>
                            <?php foreach ($heures as $heure): ?>
                                <span class="bb-duration"><?= Html::encode($heure) ?> h</span>
                                <span class="bb-bar"></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="bb-cities">
                        <span><?= Html::encode($depart) ?></span>
                        <span><?= Html::encode($label) ?></span>
                        <span><?= Html::encode($arrivee) ?></span>
                    </div>
                </div>

                <div class="result-toggle">Voir les details</div>

                <div class="correspondance-inline d-none"></div>
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
<?php endif; ?>

<?php if (!empty($correspondances)): ?>
<div class="col-12">
    <h3 class="result-section-title">Voyages avec correspondance</h3>
</div>
<div class="col-12">
    <div class="correspondance-grid">
        <?php foreach ($correspondances as $r): ?>
            <div class="corr-item">
                <div
                    class="card search-card h-100 d-flex flex-column <?= $r['complet'] ? 'card-complet' : '' ?> card-correspondance"
                    data-voyage-ids="<?= Html::encode(implode(',', $r['voyage_ids'])) ?>"
                    data-nb="<?= Html::encode((int)($nb ?? 1)) ?>"
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

                        <?php
                            $heures = preg_split('/\s*→\s*/', (string)($r['heure'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                            if (empty($heures)) {
                                $heures = [''];
                            }
                            $nbCorr = max(count($heures) - 1, 1);
                            $label = $nbCorr > 1 ? $nbCorr . ' correspondances' : 'Correspondance';
                        ?>

                        <div class="corr-summary-block">
                            <div class="bb-time">
                                <div class="bb-track">
                                    <span class="bb-bar"></span>
                                    <?php foreach ($heures as $heure): ?>
                                        <span class="bb-duration"><?= Html::encode($heure) ?> h</span>
                                        <span class="bb-bar"></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="bb-cities">
                                <span><?= Html::encode($depart) ?></span>
                                <span><?= Html::encode($label) ?></span>
                                <span><?= Html::encode($arrivee) ?></span>
                            </div>
                        </div>

                        <div class="result-toggle">Voir les details</div>

                        <div class="correspondance-inline d-none"></div>

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
    </div>
</div>
<?php endif; ?>
