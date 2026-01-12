<?php
// Partiel : détails des correspondances (chargés via AJAX).
use yii\helpers\Html;
?>

<?php if (empty($segments)): ?>
    <!-- Aucun segment -->
    <div class="details-empty">Aucun detail disponible.</div>
<?php else: ?>
    <?php
        // Calcule le résumé global du trajet.
        $startCity = $segments[0]['depart'] ?? '';
        $endCity = $segments[count($segments) - 1]['arrivee'] ?? '';
    ?>
    <div class="details-container">
        <div class="details-header">
            <div class="details-title">Details correspondance</div>
            <div class="details-summary">
                <span><?= Html::encode($startCity) ?></span>
                <span class="route-sep">→</span>
                <span><?= Html::encode($endCity) ?></span>
            </div>
            <!-- Prix total -->
            <div class="details-price"><?= number_format($total, 2) ?> €</div>
        </div>

        <!-- Cartes des segments -->
        <div class="row g-3 equal-height">
            <?php foreach ($segments as $segment): ?>
                <div class="col-md-6">
                    <div class="card search-card detail-card h-100">
                        <div class="card-body">
                            <div class="result-header">
                                <div class="result-title">Trajet</div>
                            </div>

                            <div class="bb-time">
                                <div class="bb-track">
                                    <span class="bb-bar"></span>
                                    <span class="bb-duration"><?= Html::encode($segment['heure']) ?> h</span>
                                    <span class="bb-bar"></span>
                                </div>
                            </div>

                            <!-- Villes départ/arrivée -->
                            <div class="bb-cities">
                                <span><?= Html::encode($segment['depart']) ?></span>
                                <span><?= Html::encode($segment['arrivee']) ?></span>
                            </div>

                            <!-- Métadonnées -->
                            <div class="result-meta">
                                <div class="meta-row">
                                    <span class="meta-label">Vehicule</span>
                                    <span class="meta-value"><?= Html::encode($segment['marque']) ?> – <?= Html::encode($segment['typev']) ?></span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-label">Bagages</span>
                                    <span class="meta-value"><?= Html::encode($segment['bagages']) ?></span>
                                </div>
                                <div class="meta-row">
                                    <span class="meta-label">Places</span>
                                    <span class="meta-value"><?= Html::encode($segment['places']) ?></span>
                                </div>
                                <div class="meta-row">
                                <span class="meta-label">Arrivee</span>
                                <span class="meta-value">
                                    <?= Html::encode($segment['arrivee_heure'] ?? '') ?>
                                </span>
                            </div>
                            </div>

                            <?php if (!empty($segment['contraintes'])): ?>
                                <!-- Contraintes -->
                                <div class="contrainte-line mt-2">
                                    <?= Html::encode($segment['contraintes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer mt-auto d-flex justify-content-between align-items-center px-3 py-2 result-footer">
                            <!-- Prix du segment -->
                            <div class="result-price">
                                <?= number_format($segment['prix'], 2) ?> €
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
