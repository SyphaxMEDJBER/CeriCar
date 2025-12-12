<?php

namespace app\models;

use yii\base\Model;
use app\models\internaute;

class SignupForm extends Model
{
    public $nom;
    public $prenom;
    public $pseudo;
    public $mail;
    public $permis;
    public $photo;
    public $pass;

    public function rules()
    {
        return [
            // on remplira après
        ];
    }

    public function signup()
    {
        // on remplira après
    }
}
