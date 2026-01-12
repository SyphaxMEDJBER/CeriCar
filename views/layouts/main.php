<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use app\assets\AppAsset;

// Layout principal : navbar + notification globale + contenu + footer.
// Enregistre les assets CSS/JS globaux.
AppAsset::register($this);
JqueryAsset::register($this);
$this->registerJsFile('@web/js/recherche.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('@web/js/auth.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('@web/js/profil.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('@web/js/navigation.js', ['depends' => [JqueryAsset::class]]);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Meta CSRF pour les formulaires -->
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> – CeriCar</title>
    <!-- Head Yii (assets injectés) -->
    <?php $this->head() ?>

    <!-- CSS externes -->
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

    <!-- Liens principaux -->
    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">

            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['site/index']) ?>">Accueil</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="<?= Url::to(['site/recherche']) ?>">Rechercher</a>
            </li>

            <?php if (Yii::$app->user->isGuest): ?>

                <!-- Liens visiteurs -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/login']) ?>" data-ajax="false">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/signup']) ?>">Inscription</a>
                </li>

            <?php else: ?>

                <!-- Liens utilisateur connecté -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/profil']) ?>">Mon compte</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['site/faq']) ?>">FAQ</a>
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

<!-- CONTENU -->
<main class="container" style="margin-top:30px; min-height:70vh;">
    <!-- BANDEAU NOTIFICATION -->
    <div class="notif-wrap">
        <div id="notif" class="alert text-center m-0 d-none"></div>
    </div>
    <!-- Contenu dynamique remplacé par la navigation AJAX -->
    <div id="page-content">
        <?= $content ?>
    </div>
</main>

<!-- FOOTER -->
<footer class="footer text-center py-4">
    <p>CeriCar © <?= date('Y') ?> — Mobilité nouvelle génération</p>
</footer>

<!-- JS externes -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
