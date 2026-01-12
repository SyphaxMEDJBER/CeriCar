<?php


/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */


use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

// Formulaire d’inscription rendu par ActiveForm et géré par auth.js en AJAX.
$this->title = Html::encode('Créer un compte');

?>




<div class="login-wrapper">

    <div class="login-card">
      <h1 class="login-title"><?= Html::encode($this->title)?></h1>
      <p class="login-sub">Veuillez remplir les champs ci-dessous :</p>
      <!-- Zone de notification (AJAX) -->
      <div class="alert auth-notif d-none"></div>
      <!-- Début du formulaire -->
      <?php $form=ActiveForm::begin([ //début du formulaire
        'id'=>'signup-form', // identifiant du formulaire   
        'fieldConfig'=> [
                'template' => "{input}\n{error}",
                'inputOptions' => ['class' => 'form-control input-dark'],
                'errorOptions' => ['class' => 'invalid-feedback'],
          ],
        ]); 
      ?>
      <!-- Identité -->
      <?= $form->field($model, 'nom')// champ nom
          ->textInput(['placeholder' => "Nom"]) ?> 
      <?= $form->field($model, 'prenom')
          ->textInput(['placeholder' => "Prénom"]) ?>
      <!-- Profil -->
      <?= $form->field($model, 'pseudo')
          ->textInput(['placeholder' => "Pseudo"]) ?>
      <?= $form->field($model, 'mail')
          ->textInput(['placeholder' => "Mail"]) ?>    
      <?= $form->field($model, 'permis')
          ->textInput(['placeholder' => "Numéro de permis"]) ?>



      <!-- Photo + mot de passe -->
      <?= $form->field($model, 'photo')
            ->textInput(['placeholder' => "URL de la photo"]) ?>

      <?= $form->field($model, 'pass')
          ->passwordInput(['placeholder' => "Password"]) ?>

      <div class="form-group mt-3">
                <!-- Bouton d'inscription -->
                <?= Html::submitButton('Créer un compte', [//bouton de soumission
                    'class' => 'btn btn-login',
                    'name' => 'login-button'
                ]) ?>
      </div>
      <?php ActiveForm::end();  ?>//fin du formulaire


    </div>

</div>
