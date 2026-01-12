<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = "Recherche de voyage";
?>

<!-- Formulaire de recherche (soumis en AJAX dans recherche.js) -->
<div class="container">
<?= Html::beginForm(['site/recherche'], 'post', [
    'id' => 'formRecherche',//l'id de formulaire de recherche 
    'class' => 'search-bar'
]) ?>

<!-- Champ départ + suggestions -->
<input type="text" name="depart" list="villesDepart"
       class="form-control search-input"
       placeholder="Ville de départ">

<datalist id="villesDepart">
<?php foreach ($vdep as $v): ?>
<option value="<?= Html::encode($v) ?>">
<?php endforeach; ?>
</datalist>

<!-- Champ arrivée + suggestions -->
<input type="text" name="arrivee" list="villesArrivee"
       class="form-control search-input"
       placeholder="Ville d’arrivée">

<datalist id="villesArrivee">
<?php foreach ($varr as $v): ?>
<option value="<?= Html::encode($v) ?>">
<?php endforeach; ?>
</datalist>

<!-- Nombre de voyageurs -->
<input type="number" name="voyageurs"
       class="form-control search-input"
       min="1" value="1">

<!-- Option correspondances -->
<div class="d-flex align-items-center ms-3 correspondance-wrapper">
    <input
        type="checkbox"
        id="correspondance"
        name="correspondance"
        value="1"
        class="correspondance-checkbox"
    >
    <label for="correspondance" class="ms-2">
        correspondances
    </label>
</div>


<!-- Bouton de soumission -->
<button type="submit" class="btn btn-primary search-btn">
    Rechercher
</button>

<?= Html::endForm() ?>
</div>

<!-- Conteneur des résultats (remplacé par la réponse AJAX) -->
<!-- Quand on clk sur rechercher ce div est remplace par le html envoye par le serveur -->
<div
    id="resultats"
    class="row g-4 mt-4"
    data-details-url="<?= Url::to(['site/correspondance-details']) ?>"
    data-reserver-url="<?= \yii\helpers\Url::to(['site/reserver']) ?>">
    <?php if (!empty($resultats)): ?>
        <!-- Rendu initial côté serveur -->
        <?= $this->render('_resultats', [ 
            'resultats' => $resultats,
            'depart' => $depart ?? null,
            'arrivee' => $arrivee ?? null,
            'nb' => $nb ?? null,
        ]) ?>
    <?php endif; ?>
</div>
