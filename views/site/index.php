<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "CeriCar – Trouvez votre voyage";
?>

<div class="container hero">

    <!-- TEXTE À GAUCHE -->
    <div class="hero-text">
        <h1>Voyagez plus vite.<br>Voyagez mieux.</h1>
        <p>Le covoiturage nouvelle génération. Sécurisé, rapide et au meilleur prix.<br>
        Trouvez votre trajet dès maintenant.</p>
    </div>

    <!-- IMAGE À DROITE -->
    <div class="hero-img">
        <img src="<?= Url::to('@web/images/hero2.png') ?>" alt="car">

    </div>

</div>

<!-- BARRE DE RECHERCHE HORIZONTALE -->
<div class="container">
    <?= Html::beginForm('#', 'post', ['class' => 'search-bar']) ?>

        <!-- Ville de départ -->
        <input type="text" 
               list="villesDepart" 
               class="form-control search-input" 
               placeholder="Ville de départ">
        <datalist id="villesDepart">
            <option value="Toulouse">
            <option value="Montpellier">
            <option value="Marseille">
            <option value="Paris">
            <option value="Nice">
        </datalist>

        <!-- Ville d'arrivée -->
        <input type="text" 
               list="villesArrivee" 
               class="form-control search-input" 
               placeholder="Ville d’arrivée">
        <datalist id="villesArrivee">
            <option value="Paris">
            <option value="Lyon">
            <option value="Nice">
            <option value="Marseille">
            <option value="Toulouse">
        </datalist>

        <!-- Nombre de voyageurs -->
        <input type="number" 
               class="form-control search-input" 
               min="1" max="10" 
               placeholder="Voyageurs">

        <!-- Bouton -->
        <button type="submit" class="btn btn-primary search-btn">
            Rechercher
        </button>

    <?= Html::endForm() ?>
</div>



<h2 id="tp">Trajets populaires</h2>

<div class="popular-container">

    <div class="popular-card">
        <img src=<?= Url::to('@web/images/paris.jpg') ?> alt="Paris">
        <div class="card-info">
            <h3>Marseille → Paris</h3>
            <span class="price">100 $</span>
        </div>
    </div>

    <div class="popular-card">
        <img src=<?= Url::to('@web/images/avignon.jpg') ?> alt="Avignon">
        <div class="card-info">
            <h3>Montpellier → Avignon</h3>
            <span class="price">100 $</span>
        </div>
    </div>

    <div class="popular-card">
        <img src=<?= Url::to('@web/images/toulouse.jpg') ?> alt="Toulouse">
        <div class="card-info">
            <h3>Lyon → Toulousee</h3>
            <span class="price">100 $</span>
        </div>
    </div>

</div>




<div class="container hero">

    <!-- TEXTE À GAUCHE -->
    <div class="hero-text">
        <h1>Le réseau du covoiturage<br> nouvelle génération</h1>
        <p>Chaque jour, des milliers de routes s’illuminent.<br>
            Nous connectons les voyageurs sur tout le territoire pour des trajets sûrs, rapides et économiques.</p>
    </div>

    <!-- IMAGE À DROITE -->
    <div class="hero-img">
        <img src="<?= Url::to('@web/images/france.png') ?>" alt="car">

    </div>

</div>