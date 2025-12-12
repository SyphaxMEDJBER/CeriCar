<?php


/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */


use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;


$this->title = Html::encode('Créer un compte');

?>




<div class="login-wrapper">

    <div class="login-card">
      <h1 class="login-title"><?= Html::encode($this->title)?></h1>
      <p class="login-sub">Veuillez remplir les champs ci-dessous :</p>
      <?php $form=ActiveForm::begin([
        'id'=>'signup-form',
        'fieldConfig'=> [
                'template' => "{input}\n{error}",
                'inputOptions' => ['class' => 'form-control input-dark'],
                'errorOptions' => ['class' => 'invalid-feedback'],

          ],
        ]); 
      ?>
      <?= $form->field($model, 'nom')
          ->textInput(['placeholder' => "Nom"]) ?>
      <?= $form->field($model, 'prenom')
          ->textInput(['placeholder' => "Prénom"]) ?>
      <?= $form->field($model, 'pseudo')
          ->textInput(['placeholder' => "Pseudo"]) ?>
      <?= $form->field($model, 'mail')
          ->textInput(['placeholder' => "Mail"]) ?>

      <?= $form->field($model, 'permis')
          ->input('number',['placeholder' => "Numéro De Permis"]) ?>

      <?= $form->field($model, 'photo')
            ->textInput(['placeholder' => "URL de la photo"]) ?>

      <?= $form->field($model, 'pass')
          ->passwordInput(['placeholder' => "Password"]) ?>

      <div class="form-group mt-3">
                <?= Html::submitButton('Créer un compte', [
                    'class' => 'btn btn-login',
                    'name' => 'login-button'
                ]) ?>
      </div>
      <?php ActiveForm::end();  ?>


    </div>

</div>
