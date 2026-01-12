<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "CeriCar – Trouvez votre voyage";

// Blocs marketing de la page d’accueil (hero + trajets populaires).
// Affiche les erreurs PHP en mode debug.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>

<!-- HERO : contenu d’intro -->
<div class="container hero">

    <!-- TEXTE À GAUCHE -->
    <div class="hero-text">
        <h1>Voyagez plus vite.<br>Voyagez mieux.</h1>
        <p>Le covoiturage nouvelle génération. Sécurisé, rapide et au meilleur prix.<br>
        Trouvez votre trajet dès maintenant.</p>
    </div>

    <!-- IMAGE À DROITE -->
    <div class="hero-img">
        <img src="<?= Url::to('@web/images/herooo.png') ?>" alt="car">

    </div>

</div>


<!-- Mise en avant des trajets populaires -->
<h2 id="tp">Trajets populaires</h2>

<div class="popular-container">

    <!-- Carte trajet populaire #1 -->
    <div class="popular-card">
        <img src=<?= Url::to('@web/images/paris.jpg') ?> alt="Paris">
        <div class="card-info">
            <h3>Marseille → Paris</h3>
            <span class="price">100 $</span>
        </div>
    </div>

    <!-- Carte trajet populaire #2 -->
    <div class="popular-card">
        <img src=<?= Url::to('@web/images/avignon.jpg') ?> alt="Avignon">
        <div class="card-info">
            <h3>Montpellier → Avignon</h3>
            <span class="price">100 $</span>
        </div>
    </div>

    <!-- Carte trajet populaire #3 -->
    <div class="popular-card">
        <img src=<?= Url::to('@web/images/toulouse.jpg') ?> alt="Toulouse">
        <div class="card-info">
            <h3>Lyon → Toulousee</h3>
            <span class="price">100 $</span>
        </div>
    </div>

</div>




<!-- Second bloc hero -->
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
