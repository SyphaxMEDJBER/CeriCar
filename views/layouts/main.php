<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use app\assets\AppAsset;

AppAsset::register($this);
JqueryAsset::register($this);
$this->registerJsFile('@web/js/recherche.js', ['depends' => [JqueryAsset::class]]);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?> – CeriCar</title>
    <?php $this->head() ?>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Theme -->
    <link rel="stylesheet" href="<?= Url::to('@web/css/dark.css') ?>">
</head>

<body>
<?php $this->beginBody() ?>

<!-- BANDEAU NOTIFICATION -->
<div id="notif" class="alert text-center m-0 d-none"></div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-4 fixed-top">
    <a class="navbar-brand" href="<?= Url::to(['site/index']) ?>">CeriCar</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['site/index']) ?>">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['site/recherche']) ?>">Rechercher</a>
            </li>

            <?php if (Yii::$app->user->isGuest): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/login']) ?>">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/signup']) ?>">Inscription</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/profil']) ?>">Mon Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/logout']) ?>" data-method="post">Déconnexion</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- CONTENU -->
<main class="container py-5" style="margin-top:80px;">
    <?= $content ?>
</main>

<!-- FOOTER -->
<footer class="footer text-center py-4">
    <p>CeriCar © <?= date('Y') ?> — Mobilité nouvelle génération</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
