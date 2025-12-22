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
            [['nom','prenom','pseudo','mail','permis','pass'], 'required'],
            ['mail', 'email'],

            // IMPORTANT: permis = identifiant => string, pas number
            [['nom','prenom','pseudo','mail','photo','permis'], 'string', 'max' => 45],

            // hash bcrypt > 45, donc ta colonne pass doit accepter (sinon tu dois rester en md5)
            // Ici on reste en md5 pour être compatible si ta colonne pass est courte
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

        // COMPAT BDD (si pass varchar(45)): md5 = 32 chars
        $u->pass   = md5($this->pass);

        return $u->save(false) ? $u : null;
    }
}
