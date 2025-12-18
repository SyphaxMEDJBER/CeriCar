<?php
use yii\helpers\Html;
use yii\helpers\Url;
app\assets\AppAsset::register($this);
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

    <!-- DARK THEME -->
    <link rel="stylesheet" href="<?= Url::to('@web/css/dark.css') ?>">
</head>

<body class="layout-root">
<?php $this->beginBody() ?>

    <!-- NOTIFICATION -->
        <?php if (Yii::$app->session->hasFlash('notif')): 
            $notif = Yii::$app->session->getFlash('notif');
        ?>
        <div id="notif" class="alert alert-<?= $notif['type'] ?> text-center m-0">
            <?= Html::encode($notif['message']) ?>
        </div>
        <?php endif; ?>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg px-4">
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

    <!-- CONTENU (IMPORTANT) -->
    <main class="content container py-5">
        <?= $content ?>
    </main>

    <!-- FOOTER TOUJOURS EN BAS -->
    <footer class="footer">
        <p>CeriCar © <?= date('Y') ?> — Mobilité nouvelle génération</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
