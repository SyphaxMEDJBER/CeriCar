<?php

namespace app\models; // Espace de noms du modèle.

use Yii; // Accès aux composants Yii.
use yii\base\Model; // Base des formulaires Yii.

/**
 * LoginForm gère les entrées d’authentification et la validation.
 *
 * @property-read internaute|null $user
 */
class LoginForm extends Model // Formulaire de connexion.
{
    public $username; // Champ identifiant (pseudo ou email).
    public $password; // Champ mot de passe.
    public $rememberMe = true; // Option "se souvenir".

    private $_user = false; // Cache utilisateur trouvé.


    /**
     * Règles de validation du formulaire de connexion.
     *
     * @return array
     */
    public function rules() // Règles de validation.
    {
        return [
            ['username', 'filter', 'filter' => 'trim'], // Nettoie l'identifiant.
            // identifiant et mot de passe sont obligatoires
            [['username', 'password'], 'required'], // Champs obligatoires.
            // rememberMe doit être un booléen
            ['rememberMe', 'boolean'], // Type booléen requis.
            // mot de passe validé par validatePassword()
            ['password', 'validatePassword'], // Validation custom du mot de passe.
        ];
    }


    public function validatePassword($attribute, $params) // Vérifie le mot de passe.
    {
        if (!$this->hasErrors()) { // Continue si pas d'autres erreurs.
            $user = $this->getUser(); // Récupère l'utilisateur.

            if (!$user || !$user->validatePassword($this->password)) { // Mot de passe invalide.
                $this->addError($attribute, 'Incorrect username or password.'); // Ajoute l'erreur.
            }
        }
    }


    public function login() // Tente la connexion.
    {
        if ($this->validate()) { // Valide d'abord.
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0); // Login Yii.
        }
        return false; // Échec de validation.
    }

   
    public function getUser() // Cherche l'utilisateur.
    {
        if ($this->_user === false) { // Si pas encore chargé.
            $login = trim((string)$this->username); // Normalise l'entrée.
            // Autorise la connexion par pseudo OU par email.
            $this->_user = internaute::find() // Démarre la requête.
                ->where(['pseudo' => $login]) // Condition pseudo.
                ->orWhere(['mail' => $login]) // Condition email.
                ->one(); // Récupère un utilisateur.
        }

        return $this->_user; // Retourne l'utilisateur (ou null).
    }
}
