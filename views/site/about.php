<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

// Page À propos (contenu statique).
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <!-- Titre -->
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Texte statique -->
    <p>
        This is the About page. You may modify the following file to customize its content:
    </p>

    <!-- Chemin du fichier -->
    <code><?= __FILE__ ?></code>
</div>
