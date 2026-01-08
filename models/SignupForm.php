<?php
namespace app\models;

use Yii;
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
            [['nom','prenom','pseudo','mail','pass'], 'required'],
            ['mail', 'email'],

            // IMPORTANT: permis = identifiant => string, pas number
            [['nom','prenom','pseudo','mail','photo','permis'], 'string', 'max' => 45],
            [['nom','prenom','pseudo','mail','photo','permis'], 'filter', 'filter' => 'trim'],

            ['pass', 'string', 'min' => 3],
        ];
    }

    public function signup()
    {
        if (!$this->load(Yii::$app->request->post()) || !$this->validate()) {
            return null;
        }

        $u = new internaute();
        $u->nom    = $this->nom;
        $u->prenom = $this->prenom;
        $u->pseudo = $this->pseudo;
        $u->mail   = $this->mail;

        // LE POINT CRITIQUE
        $u->permis = (string)$this->permis;

        // si vide => mets une image par défaut courte (<=45)
        $u->photo  = $this->photo ?: 'default.png';

        $u->pass   = md5($this->pass);

        return $u->save(false) ? $u : null;
    }
}
