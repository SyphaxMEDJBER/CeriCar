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

        <?php
            $reservations = $user->reservations ?: [];
            usort($reservations, function ($a, $b) {
                $aTime = $a->voyageObj->heuredepart ?? 0;
                $bTime = $b->voyageObj->heuredepart ?? 0;
                return $aTime <=> $bTime;
            });
        ?>

        <?php
            $direct = [];
            $corr = [];
            foreach ($reservations as $r) {
                $v = $r->voyageObj;
                $trajet = $v ? $v->trajetObj : null;
                $ids = $v ? [$v->id] : [];
                $hasMatch = false;
                $seg1 = $v;
                $seg2 = null;
                $t1 = $trajet;
                $t2 = null;

                if ($v && $trajet) {
                    $match = null;
                    $matchTrajet = null;
                    $matchType = null;
                    foreach ($reservations as $other) {
                        if ($other === $r) {
                            continue;
                        }
                        $ov = $other->voyageObj;
                        $ot = $ov ? $ov->trajetObj : null;
                        if (!$ov || !$ot) {
                            continue;
                        }
                        if ($trajet->arrivee === $ot->depart && $v->heuredepart < $ov->heuredepart) {
                            $match = $ov;
                            $matchTrajet = $ot;
                            $matchType = 'next';
                            break;
                        }
                        if ($ot->arrivee === $trajet->depart && $ov->heuredepart < $v->heuredepart) {
                            $match = $ov;
                            $matchTrajet = $ot;
                            $matchType = 'prev';
                            break;
                        }
                    }

                    if ($match) {
                        $hasMatch = true;
                        if ($matchType === 'prev') {
                            $seg1 = $match;
                            $t1 = $matchTrajet;
                            $seg2 = $v;
                            $t2 = $trajet;
                            $ids = [$match->id, $v->id];
                        } else {
                            $seg1 = $v;
                            $t1 = $trajet;
                            $seg2 = $match;
                            $t2 = $matchTrajet;
                            $ids = [$v->id, $match->id];
                        }
                    }
                }

                $entry = [
                    'r' => $r,
                    'v' => $v,
                    'trajet' => $trajet,
                    'ids' => $ids,
                    'seg1' => $seg1,
                    'seg2' => $seg2,
                    't1' => $t1,
                    't2' => $t2,
                ];

                if ($hasMatch) {
                    $corr[] = $entry;
                } else {
                    $direct[] = $entry;
                }
            }
        ?>

        <?php if (!empty($direct) || !empty($corr)): ?>
            <div id="resultats" class="row g-4 mt-4" data-details-url="<?= Url::to(['site/correspondance-details']) ?>">
                <?php if (!empty($direct)): ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <h3 class="result-section-title">Réservations directes</h3>
                        </div>
                        <?php foreach ($direct as $item): ?>
                            <?php $v = $item['v']; ?>
                            <?php $trajet = $item['trajet']; ?>
                            <?php $r = $item['r']; ?>
                            <div class="col-md-6">
                                <div class="card search-card h-100 d-flex flex-column">
                                    <?php if ($v && $trajet): ?>
                                        <div class="card-body">
                                            <div class="result-header">
                                                <div class="result-title">Trajet</div>
                                                <span class="result-badge badge-dispo">Réservé</span>
                                            </div>
                                            <div class="bb-time">
                                                <div class="bb-track">
                                                    <span class="bb-bar"></span>
                                                    <span class="bb-duration"><?= Html::encode($v->heuredepart) ?> h</span>
                                                    <span class="bb-bar"></span>
                                                </div>
                                            </div>
                                            <div class="bb-cities">
                                                <span><?= Html::encode($trajet->depart) ?></span>
                                                <span><?= Html::encode($trajet->arrivee) ?></span>
                                            </div>
                                            <div class="result-meta">
                                                <div class="meta-row">
                                                    <span class="meta-label">Places reservees</span>
                                                    <span class="meta-value"><?= Html::encode($r->nbplaceresa) ?></span>
                                                </div>
                                                <div class="meta-row">
                                                    <span class="meta-label">Conducteur</span>
                                                    <span class="meta-value"><?= Html::encode(($v->conducteurObj->prenom ?? '') . ' ' . ($v->conducteurObj->nom ?? '')) ?></span>
                                                </div>
                                                <div class="meta-row">
                                                    <span class="meta-label">Vehicule</span>
                                                    <span class="meta-value"><?= Html::encode($v->marqueVehicule->marquev ?? '') ?> – <?= Html::encode($v->typeVehicule->typev ?? '') ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer mt-auto d-flex justify-content-between align-items-center px-3 py-2 result-footer">
                                            <div class="result-price">
                                                <?= Html::encode(number_format(($trajet->distance ?? 0) * $v->tarif * $r->nbplaceresa, 2)) ?> €
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="card-body">Voyage ou trajet introuvable pour cette reservation.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($corr)): ?>
                    <div class="row g-3 mt-3">
                        <div class="col-12">
                            <h3 class="result-section-title">Réservations avec correspondance</h3>
                        </div>
                        <div class="col-12">
                            <div class="correspondance-grid">
                                <?php foreach ($corr as $item): ?>
                                    <?php
                                        $r = $item['r'];
                                        $seg1 = $item['seg1'];
                                        $seg2 = $item['seg2'];
                                        $t1 = $item['t1'];
                                        $t2 = $item['t2'];
                                        $priceCorr = 0;
                                        if ($seg1 && $seg2 && $t1 && $t2) {
                                            $priceCorr = (($t1->distance ?? 0) * $seg1->tarif + ($t2->distance ?? 0) * $seg2->tarif) * $r->nbplaceresa;
                                        }
                                    ?>
                                    <div class="corr-item">
                                        <div class="card search-card h-100 d-flex flex-column card-correspondance"
                                             data-voyage-ids="<?= Html::encode(implode(',', $item['ids'])) ?>"
                                             data-nb="<?= Html::encode($r->nbplaceresa) ?>">
                                            <?php if ($seg1 && $seg2 && $t1 && $t2): ?>
                                                <div class="card-body">
                                                    <div class="result-header">
                                                        <div class="result-title">Trajet</div>
                                                        <span class="result-badge badge-dispo">Réservé</span>
                                                    </div>
                                                    <div class="corr-summary-block">
                                                        <div class="bb-time">
                                                            <div class="bb-track">
                                                                <span class="bb-bar"></span>
                                                                <span class="bb-duration"><?= Html::encode($seg1->heuredepart) ?> h</span>
                                                                <span class="bb-bar"></span>
                                                                <span class="bb-duration"><?= Html::encode($seg2->heuredepart) ?> h</span>
                                                                <span class="bb-bar"></span>
                                                            </div>
                                                        </div>
                                                        <div class="bb-cities">
                                                            <span><?= Html::encode($t1->depart) ?></span>
                                                            <span>Correspondance</span>
                                                            <span><?= Html::encode($t2->arrivee) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="result-meta">
                                                        <div class="meta-row">
                                                            <span class="meta-label">Places reservees</span>
                                                            <span class="meta-value"><?= Html::encode($r->nbplaceresa) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="result-toggle">Voir les details</div>
                                                    <div class="correspondance-inline d-none"></div>
                                                </div>
                                                <div class="card-footer mt-auto d-flex justify-content-between align-items-center px-3 py-2 result-footer">
                                                    <div class="result-price">
                                                        <?= Html::encode(number_format($priceCorr, 2)) ?> €
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="card-body">Voyage ou trajet introuvable pour cette reservation.</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">Aucune reservation pour le moment.</div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= Url::to(['site/profil']) ?>" class="btn btn-outline-light">Retour profil</a>
        </div>
    </div>
</div>
