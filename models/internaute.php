<?php
namespace app\models; // Espace de noms du modèle.

use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.
use app\models\reservation; // Modèle Reservation (relation).
use app\models\voyage; // Modèle Voyage (relation).
use yii\web\IdentityInterface; // Interface d'identité Yii.

/**
 * Modèle Internaute (identité utilisateur + relations).
 */
class internaute extends ActiveRecord implements IdentityInterface{ //


    public static function tableName(){ // Nom de table.
      return 'fredouil.internaute'; // Table SQL cible.
    }


  
    public function getReservations(){ // Relation 1->N vers reservations.
      return $this->hasMany(reservation::class ,['voyageur'=>'id']); // FK reservation.voyageur => internaute.id
    }


 
    public function getVoyages(){ // Relation 1->N vers voyages.
      return $this->hasMany(voyage::class,['conducteur'=>'id']); // FK voyage.conducteur => internaute.id
    }



  public static function getUserByIdentifiant($pseudo){ // Recherche par pseudo.
    return self::findOne(['pseudo'=>$pseudo]); // Premier résultat correspondant.
  }



  public static function getUserById($id) // Recherche par identifiant.
    {
        return self::findOne($id); // Retourne l'utilisateur ou null.

    }



    public static function getProfilById($id) 
    {
         return self::findOne($id); // Même logique que getUserById.

    }

    //retrouve un utilisateur via son id
    public static function findIdentity($id){ // Requis par IdentityInterface.
        return self::findOne($id); // Retourne l'identité.
    }

  
    public static function findIdentityByAccessToken($token, $type = null){ // Non utilisé.
        return null; // Pas d'auth par token.
    }

    public function getId(){ // ID utilisateur.
        return $this->id; // Valeur de la clé primaire.
    }

    public function getAuthKey(){ // Non utilisé.
        return null; // Pas de clé d'auth persistante.
    }


    public function validateAuthKey($authKey){ // Non utilisé.
        return true; // Toujours valide.
    }


    public static function findByPseudo($pseudo){ // Recherche par pseudo.
        return self::findOne(['pseudo' => $pseudo]); // Retourne l'utilisateur.
    }

   
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
