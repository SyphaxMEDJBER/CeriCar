<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Recherche de voyage";
?>

<!-- BARRE DE RECHERCHE (IDENTIQUE À LA HOME) -->
<div class="container">
    <?= Html::beginForm(['site/recherche'], 'post', ['class' => 'search-bar']) ?>

        <input type="text"
               name="depart"
               list="villesDepart"
               class="form-control search-input"
               placeholder="Ville de départ"
               value="<?= Html::encode($depart ?? '') ?>">

        <datalist id="villesDepart">
            <?php foreach ($vdep as $v): ?>
                <option value="<?= Html::encode($v) ?>">
            <?php endforeach; ?>
        </datalist>

        <input type="text"
               name="arrivee"
               list="villesArrivee"
               class="form-control search-input"
               placeholder="Ville d’arrivée"
               value="<?= Html::encode($arrivee ?? '') ?>">

        <datalist id="villesArrivee">
            <?php foreach ($varr as $v): ?>
                <option value="<?= Html::encode($v) ?>">
            <?php endforeach; ?>
        </datalist>

        <input type="number"
               name="voyageurs"
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
<div class="container mt-4">

<?php if (!empty($resultats)): ?>

    <h2>Résultats pour <?= $nb ?> voyageurs</h2>

    <?php foreach ($resultats as $r): ?>
        <div class="result-card <?= $r['complet'] ? 'complet' : 'disponible' ?>">

            <p><strong>Conducteur :</strong> <?= Html::encode($r['conducteur']) ?></p>

            <p>
                <?php if ($r['complet']): ?>
                    <span class="badge bg-danger">COMPLET</span>
                <?php else: ?>
                    Places restantes : <?= $r['places'] ?>
                <?php endif; ?>
            </p>

            <p><strong>Prix total :</strong> <?= $r['prix'] ?> €</p>

        </div>
    <?php endforeach; ?>

<?php elseif ($depart): ?>

    <div class="alert alert-warning">
        Aucun voyage trouvé pour <?= Html::encode("$depart → $arrivee") ?>
    </div>

<?php endif; ?>

</div>
