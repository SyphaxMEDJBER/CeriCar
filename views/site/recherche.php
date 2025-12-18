<?php
use yii\helpers\Html;

$this->title = "Recherche de voyage";
?>

<!-- BARRE DE RECHERCHE -->
<div class="container">
    <?= Html::beginForm(['site/recherche'], 'post', ['class' => 'search-bar']) ?>

        <input type="text" name="depart" list="villesDepart"
               class="form-control search-input"
               placeholder="Ville de départ"
               value="<?= Html::encode($depart ?? '') ?>">

        <datalist id="villesDepart">
            <?php foreach ($vdep as $v): ?>
                <option value="<?= Html::encode($v) ?>">
            <?php endforeach; ?>
        </datalist>

        <input type="text" name="arrivee" list="villesArrivee"
               class="form-control search-input"
               placeholder="Ville d’arrivée"
               value="<?= Html::encode($arrivee ?? '') ?>">

        <datalist id="villesArrivee">
            <?php foreach ($varr as $v): ?>
                <option value="<?= Html::encode($v) ?>">
            <?php endforeach; ?>
        </datalist>

        <input type="number" name="voyageurs"
               class="form-control search-input"
               min="1" max="10"
               placeholder="Voyageurs"
               value="<?= Html::encode($nb ?? '') ?>">

        <button type="submit" class="btn btn-primary search-btn">
            Rechercher
        </button>

    <?= Html::endForm() ?>
</div>

<!-- RÉSULTATS -->
<div class="row g-4 mt-4">
<?php foreach ($resultats as $r): ?>
    <div class="col-md-6 col-lg-4">

        <!-- LA CARTE EST FLEX -->
        <div class="card search-card h-100 d-flex flex-column <?= $r['complet'] ? 'card-complet' : '' ?>">

            <!-- CONTENU -->
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

                <p class="mb-1">🕒 Départ : <strong><?= Html::encode($r['heure']) ?> h</strong></p>
                <p class="mb-1">🚗 <?= Html::encode($r['marque']) ?> – <?= Html::encode($r['type']) ?></p>
                <p class="mb-2">🎒 Bagages : <?= Html::encode($r['bagages']) ?></p>

                <?php if (!empty($r['contraintes'])): ?>
                    <div class="contrainte-line mt-2">
                        ⚠️ <?= Html::encode($r['contraintes']) ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- FOOTER TOUJOURS EN BAS -->
            <div class="card-footer text-end mt-auto">
                <span class="price"><?= number_format($r['prix'], 2) ?> €</span>
            </div>

        </div>

    </div>
<?php endforeach; ?>
</div>
