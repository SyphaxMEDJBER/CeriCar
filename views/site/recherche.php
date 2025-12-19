<?php
use yii\helpers\Html;
$this->title = "Recherche de voyage";
?>

<div class="container">
<?= Html::beginForm(['site/recherche'], 'post', [
    'id' => 'formRecherche',
    'class' => 'search-bar'
]) ?>

<input type="text" name="depart" list="villesDepart"
       class="form-control search-input"
       placeholder="Ville de départ">

<datalist id="villesDepart">
<?php foreach ($vdep as $v): ?>
<option value="<?= Html::encode($v) ?>">
<?php endforeach; ?>
</datalist>

<input type="text" name="arrivee" list="villesArrivee"
       class="form-control search-input"
       placeholder="Ville d’arrivée">

<datalist id="villesArrivee">
<?php foreach ($varr as $v): ?>
<option value="<?= Html::encode($v) ?>">
<?php endforeach; ?>
</datalist>

<input type="number" name="voyageurs"
       class="form-control search-input"
       min="1" value="1">

<button type="submit" class="btn btn-primary search-btn">
    Rechercher
</button>

<?= Html::endForm() ?>
</div>

<!-- CONTENEUR RÉSULTATS (OBLIGATOIRE) -->
<div id="resultats" class="row g-4 mt-4"></div>
