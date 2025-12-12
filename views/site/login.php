<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = Html::encode('Se connecter');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-wrapper">

    <div class="login-card">

        <h1 class="login-title"><?= Html::encode($this->title) ?></h1>
        <p class="login-sub">Veuillez remplir les champs ci-dessous :</p>

        <?php $form = ActiveForm::begin([ //un tableau d'option que yii utilise pour configurer le formulaire 
            'id' => 'login-form',//un identifiant 
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{error}",
                'labelOptions' => ['class' => 'form-label login-label'],
                'inputOptions' => ['class' => 'form-control input-dark'],
                'errorOptions' => ['class' => 'invalid-feedback'],
            ],
           ]); 
        ?>

            <?= $form->field($model, 'username')
                ->textInput(['autofocus' => true, 'placeholder' => "Nom d'utilisateur"]) ?>

            <?= $form->field($model, 'password')
                ->passwordInput(['placeholder' => "Mot de passe"]) ?>

            <?= $form->field($model, 'rememberMe')
                ->checkbox(['label' => 'Se souvenir de moi', 'class' => 'remember-check']) ?>

            <div class="form-group mt-3">
                <?= Html::submitButton('Se connecter', [
                    'class' => 'btn btn-login',
                    'name' => 'login-button'
                ]) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
