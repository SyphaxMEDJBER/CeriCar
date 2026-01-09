<?php
namespace app\models; // Espace de noms du modèle.

use Yii; // Accès aux composants Yii.
use yii\base\Model; // Base des formulaires Yii.
use app\models\internaute; // Modèle utilisateur.

/**
 * SignupForm gère l’inscription et la création d’utilisateur.
 */
class SignupForm extends Model // Formulaire d'inscription.
{
    public $nom; // Nom.
    public $prenom; // Prénom.
    public $pseudo; // Pseudo.
    public $mail; // Email.
    public $permis; // Permis.
    public $photo; // URL photo.
    public $pass; // Mot de passe.


    public function rules() // Règles de validation.
    {
        return [
            [['nom','prenom','pseudo','mail','pass'], 'required'], // Champs requis.
            ['mail', 'email'], // Format email.

            // IMPORTANT : permis = identifiant => chaîne, pas nombre
            [['nom','prenom','pseudo','mail','photo','permis'], 'string', 'max' => 45], // Longueur max.
            [['nom','prenom','pseudo','mail','photo','permis'], 'filter', 'filter' => 'trim'], // Nettoyage.
            ['pass', 'filter', 'filter' => 'trim'], // Nettoyage mot de passe.

            ['pass', 'string', 'min' => 3], // Longueur min du mot de passe.
            ['mail', 'validateMailUnique'], // Email unique.
            ['pseudo', 'validatePseudoUnique'], // Pseudo unique.
        ];
    }


    public function signup() // Crée l'utilisateur.
    {
        if (!$this->load(Yii::$app->request->post()) || !$this->validate()) { // Charge + valide.
            return null; // Échec si invalide.
        }

        $u = new internaute(); // Nouvelle entité.
        // Champs de profil de base.
        $u->nom    = $this->nom; // Affecte le nom.
        $u->prenom = $this->prenom; // Affecte le prénom.
        $u->pseudo = $this->pseudo; // Affecte le pseudo.
        $u->mail   = $this->mail; // Affecte l'email.

        // Le permis est stocké comme chaîne dans ce schéma.
        $u->permis = (string)$this->permis; // Cast en string.

        // Photo par défaut si non fournie.
        $u->photo  = $this->photo ?: 'default.png'; // Valeur par défaut.

        // Stocke en md5 pour compatibilité héritée.
        $u->pass   = md5($this->pass); // Hash md5.

        return $u->save(false) ? $u : null; // Sauve sans revalider.
    }

    //verifie que l'email n'existe pas déjà dans la table internaute
    public function validateMailUnique($attribute, $params)
    {
        if ($this->hasErrors()) { // Stop si erreurs existantes.
            return;
        }

        $mail = trim((string)$this->$attribute); // Normalise l'email.
        if ($mail === '') { // Laisse la règle required gérer le vide.
            return;
        }

        if (internaute::find()->where(['mail' => $mail])->exists()) { // Vérifie l'existence.
            $this->addError($attribute, 'Email déjà utilisé.'); // Ajoute l'erreur.
        }
    }


    public function validatePseudoUnique($attribute, $params)
    {
        if ($this->hasErrors()) { // Stop si erreurs existantes.
            return;
        }

        $pseudo = trim((string)$this->$attribute); // Normalise le pseudo.
        if ($pseudo === '') { // Laisse la règle required gérer le vide.
            return;
        }

        if (internaute::find()->where(['pseudo' => $pseudo])->exists()) { // Vérifie l'existence.
            $this->addError($attribute, 'Pseudo déjà utilisé.'); // Ajoute l'erreur.
        }
    }
}
