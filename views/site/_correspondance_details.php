<?php
use yii\helpers\Html;
?>

<?php if (empty($segments)): ?>
    <div class="details-empty">Aucun detail disponible.</div>
<?php else: ?>
    <?php
        $startCity = $segments[0]['depart'] ?? '';
        $endCity = $segments[count($segments) - 1]['arrivee'] ?? '';
    ?>
    <div class="details-card">
        <div class="details-header">
            <div class="details-title">Details du trajet</div>
            <div class="details-summary">
                <span><?= Html::encode($startCity) ?></span>
                <span class="route-sep">→</span>
                <span><?= Html::encode($endCity) ?></span>
            </div>
            <div class="details-price"><?= number_format($total, 2) ?> €</div>
        </div>

        <div class="details-body">
            <?php foreach ($segments as $index => $segment): ?>
                <div class="details-row">
                    <div class="details-time"><?= Html::encode($segment['heure']) ?> h</div>
                    <div class="details-line">
                        <span class="details-dot"></span>
                        <?php if ($index < count($segments) - 1): ?>
                            <span class="details-rail"></span>
                        <?php endif; ?>
                    </div>
                    <div class="details-content">
                        <div class="details-city"><?= Html::encode($segment['depart']) ?></div>
                        <div class="details-route">
                            <?= Html::encode($segment['depart']) ?> <span class="route-sep">→</span> <?= Html::encode($segment['arrivee']) ?>
                        </div>
                        <div class="details-meta">
                            <span class="meta-label">Vehicule</span>
                            <span class="meta-value"><?= Html::encode($segment['marque']) ?> – <?= Html::encode($segment['typev']) ?></span>
                        </div>
                        <div class="details-meta">
                            <span class="meta-label">Bagages</span>
                            <span class="meta-value"><?= Html::encode($segment['bagages']) ?></span>
                        </div>
                        <div class="details-meta">
                            <span class="meta-label">Places</span>
                            <span class="meta-value"><?= Html::encode($segment['places']) ?></span>
                        </div>
                        <?php if (!empty($segment['prix'])): ?>
                            <div class="details-meta">
                                <span class="meta-label">Prix</span>
                                <span class="meta-value"><?= number_format($segment['prix'], 2) ?> €</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($segment['contraintes'])): ?>
                            <div class="contrainte-line mt-2">
                                <?= Html::encode($segment['contraintes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($index < count($segments) - 1): ?>
                    <div class="details-connector">
                        Correspondance a <?= Html::encode($segment['arrivee']) ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="details-row details-row-end">
                <div class="details-time">Arrivee</div>
                <div class="details-line">
                    <span class="details-dot"></span>
                </div>
                <div class="details-content">
                    <div class="details-city"><?= Html::encode($endCity) ?></div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
