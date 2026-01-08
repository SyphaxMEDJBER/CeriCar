<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use app\assets\AppAsset;

AppAsset::register($this);
JqueryAsset::register($this);
$this->registerJsFile('@web/js/recherche.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('@web/js/auth.js', ['depends' => [JqueryAsset::class]]);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> – CeriCar</title>
    <?php $this->head() ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= Url::to('@web/css/dark.css') ?>">
</head>

<body>
<?php $this->beginBody() ?>

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
                    <a class="nav-link" href="<?= Url::to(['site/profil']) ?>">Mon compte</a>
                </li>

                <li class="nav-item">
                    <?= Html::beginForm(['site/logout'], 'post', ['class' => 'd-inline']) ?>
                        <?= Html::submitButton(
                            'Déconnexion',
                            ['class' => 'nav-link btn btn-link p-0']
                        ) ?>
                    <?= Html::endForm() ?>
                </li>

            <?php endif; ?>

        </ul>
    </div>
</nav>

<!-- BANDEAU NOTIFICATION -->
<div class="notif-wrap">
    <div id="notif" class="alert text-center m-0 d-none"></div>
</div>

<!-- CONTENU -->
<main class="container" style="margin-top:30px; min-height:70vh;">
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
