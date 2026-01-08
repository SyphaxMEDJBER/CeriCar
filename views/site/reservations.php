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
            $segments = [];

            foreach ($reservations as $r) {
                $v = $r->voyageObj;
                $trajet = $v ? $v->trajetObj : null;
                $segments[] = [
                    'r' => $r,
                    'v' => $v,
                    't' => $trajet,
                    'heure' => $v ? (int)$v->heuredepart : null,
                    'nb' => $r->nbplaceresa,
                ];
            }

            usort($segments, function ($a, $b) {
                $aTime = $a['heure'] ?? PHP_INT_MAX;
                $bTime = $b['heure'] ?? PHP_INT_MAX;
                return $aTime <=> $bTime;
            });

            $used = [];
            foreach ($segments as $idx => $segment) {
                if (isset($used[$idx])) {
                    continue;
                }

                if (!$segment['v'] || !$segment['t'] || $segment['heure'] === null) {
                    $used[$idx] = true;
                    $direct[] = [
                        'r' => $segment['r'],
                        'v' => $segment['v'],
                        'trajet' => $segment['t'],
                    ];
                    continue;
                }

                $chain = [];
                $currentIdx = $idx;

                while (true) {
                    if (isset($used[$currentIdx])) {
                        break;
                    }
                    $current = $segments[$currentIdx];
                    if (!$current['v'] || !$current['t'] || $current['heure'] === null) {
                        break;
                    }

                    $used[$currentIdx] = true;
                    $chain[] = $current;

                    $nextIdx = null;
                    $nextHour = null;

                    foreach ($segments as $candIdx => $cand) {
                        if (isset($used[$candIdx])) {
                            continue;
                        }
                        if (!$cand['v'] || !$cand['t'] || $cand['heure'] === null) {
                            continue;
                        }
                        if ((int)$cand['nb'] !== (int)$current['nb']) {
                            continue;
                        }
                        if ($cand['t']->depart !== $current['t']->arrivee) {
                            continue;
                        }
                        if ($cand['heure'] <= $current['heure']) {
                            continue;
                        }
                        if ($nextIdx === null || $cand['heure'] < $nextHour) {
                            $nextIdx = $candIdx;
                            $nextHour = $cand['heure'];
                        }
                    }

                    if ($nextIdx === null) {
                        break;
                    }
                    $currentIdx = $nextIdx;
                }

                if (count($chain) >= 2) {
                    $ids = [];
                    foreach ($chain as $seg) {
                        if ($seg['v']) {
                            $ids[] = $seg['v']->id;
                        }
                    }
                    $corr[] = [
                        'segments' => $chain,
                        'ids' => $ids,
                        'nb' => $chain[0]['r']->nbplaceresa,
                    ];
                } else {
                    $direct[] = [
                        'r' => $segment['r'],
                        'v' => $segment['v'],
                        'trajet' => $segment['t'],
                    ];
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
                                        $segments = $item['segments'];
                                        $nbPlaces = $item['nb'];
                                        $ids = $item['ids'];
                                        $priceCorr = 0;
                                        $heures = [];
                                        $startTrajet = null;
                                        $endTrajet = null;
                                        $isValid = true;

                                        foreach ($segments as $segment) {
                                            $v = $segment['v'];
                                            $t = $segment['t'];
                                            if (!$v || !$t) {
                                                $isValid = false;
                                                break;
                                            }
                                            $heures[] = $v->heuredepart;
                                            $priceCorr += ($t->distance ?? 0) * $v->tarif * $nbPlaces;
                                            if ($startTrajet === null) {
                                                $startTrajet = $t;
                                            }
                                            $endTrajet = $t;
                                        }

                                        $nbCorr = max(count($segments) - 1, 1);
                                        $label = $nbCorr > 1 ? $nbCorr . ' correspondances' : 'Correspondance';
                                    ?>
                                    <div class="corr-item">
                                        <div class="card search-card h-100 d-flex flex-column card-correspondance"
                                             data-voyage-ids="<?= Html::encode(implode(',', $ids)) ?>"
                                             data-nb="<?= Html::encode($nbPlaces) ?>">
                                            <?php if ($isValid && $startTrajet && $endTrajet): ?>
                                                <div class="card-body">
                                                    <div class="result-header">
                                                        <div class="result-title">Trajet</div>
                                                        <span class="result-badge badge-dispo">Réservé</span>
                                                    </div>
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
                                                            <span><?= Html::encode($startTrajet->depart) ?></span>
                                                            <span><?= Html::encode($label) ?></span>
                                                            <span><?= Html::encode($endTrajet->arrivee) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="result-meta">
                                                        <div class="meta-row">
                                                            <span class="meta-label">Places reservees</span>
                                                            <span class="meta-value"><?= Html::encode($nbPlaces) ?></span>
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
