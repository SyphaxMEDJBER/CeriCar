<?php
namespace app\models; // Espace de noms du modèle.

use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.
use app\models\reservation; // Modèle Reservation (relation).
use app\models\voyage; // Modèle Voyage (relation).
use yii\web\IdentityInterface; // Interface d'identité Yii.

/**
 * Modèle Internaute (identité utilisateur + relations).
 */
class internaute extends ActiveRecord implements IdentityInterface{ // Classe utilisateur principale.

  /**
   * Associe ce ActiveRecord à la table SQL.
   *
   * @return string
   */
    public static function tableName(){ // Nom de table.
      return 'fredouil.internaute'; // Table SQL cible.
    }


    /**
     * Réservations effectuées par cet utilisateur.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReservations(){ // Relation 1->N vers reservations.
      return $this->hasMany(reservation::class ,['voyageur'=>'id']); // FK reservation.voyageur => internaute.id
    }


    /**
     * Voyages proposés par cet utilisateur en tant que conducteur.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVoyages(){ // Relation 1->N vers voyages.
      return $this->hasMany(voyage::class,['conducteur'=>'id']); // FK voyage.conducteur => internaute.id
    }



  /**
   * Trouver un utilisateur par pseudo.
   *
   * @param string $pseudo
   * @return static|null
   */
  public static function getUserByIdentifiant($pseudo){ // Recherche par pseudo.
    return self::findOne(['pseudo'=>$pseudo]); // Premier résultat correspondant.
  }


  /**
   * Trouver un utilisateur par id.
   *
   * @param int $id
   * @return static|null
   */
  public static function getUserById($id) // Recherche par identifiant.
    {
        return self::findOne($id); // Retourne l'utilisateur ou null.

    }


    /**
     * Alias pour récupérer le profil par id.
     *
     * @param int $id
     * @return static|null
     */
    public static function getProfilById($id) // Alias sémantique.
    {
         return self::findOne($id); // Même logique que getUserById.

    }

    /**
     * IdentityInterface : trouver l’identité par id.
     *
     * @param string|int $id
     * @return static|null
     */
    public static function findIdentity($id){ // Requis par IdentityInterface.
        return self::findOne($id); // Retourne l'identité.
    }

    /**
     * IdentityInterface : les tokens d’accès ne sont pas utilisés ici.
     *
     * @param string $token
     * @param mixed $type
     * @return null
     */
    public static function findIdentityByAccessToken($token, $type = null){ // Non utilisé.
        return null; // Pas d'auth par token.
    }

    /**
     * IdentityInterface : retourne la clé primaire.
     *
     * @return int|string|null
     */
    public function getId(){ // ID utilisateur.
        return $this->id; // Valeur de la clé primaire.
    }

    /**
     * IdentityInterface : auth key non utilisé.
     *
     * @return null
     */
    public function getAuthKey(){ // Non utilisé.
        return null; // Pas de clé d'auth persistante.
    }

    /**
     * IdentityInterface : validation d’auth key non utilisée.
     *
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey){ // Non utilisé.
        return true; // Toujours valide.
    }

    /**
     * Trouver par pseudo (utilisé par LoginForm).
     *
     * @param string $pseudo
     * @return static|null
     */
    public static function findByPseudo($pseudo){ // Recherche par pseudo.
        return self::findOne(['pseudo' => $pseudo]); // Retourne l'utilisateur.
    }

    /**
     * Valide un mot de passe en clair contre la valeur stockée.
     * Supporte les valeurs héritées en md5.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password){ // Vérifie le mot de passe.
        $stored = trim((string)$this->pass); // Mot de passe stocké.
        $plain = trim((string)$password); // Mot de passe saisi.
        if ($stored === '') { // Aucun mot de passe enregistré.
            return false; // Refus.
        }
        if ($stored === $plain) { // Cas texte en clair (legacy).
            return true; // Ok.
        }
        return md5($plain) === $stored; // Compare au hash md5.
    }
    
    








}























?>
