<?php


namespace app\models; // Espace de noms du modèle.

use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.
use app\models\voyage; // Modèle Voyage (relation).
use app\models\internaute; // Modèle Internaute (relation).



/**
 * Modèle Reservation (réservation d’un voyage).
 */
class reservation extends ActiveRecord{ // Classe Reservation.
  /**
   * @return string
   */
  public static function tableName(){ // Nom de table.
    return 'fredouil.reservation'; // Table SQL cible.
  }

  /**
   * Voyage lié à cette réservation.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getVoyageObj(){ // Relation vers voyage.
    return $this->hasOne(voyage::class,['id'=>'voyage']); // FK reservation.voyage => voyage.id
  }

  /**
   * Utilisateur qui a fait la réservation.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getReserveur(){ // Relation vers utilisateur.
    return $this-> hasOne(internaute::class,['id'=>'voyageur']); // FK reservation.voyageur => internaute.id
  }


  /**
   * Trouver les réservations pour un voyage.
   *
   * @param int $idVoyage
   * @return static[]
   */
  public static function getReservationsByVoyageId($idVoyage){ // Réservations par voyage.
    return self::findAll(['voyage'=>$idVoyage]); // Toutes les réservations liées.
  }



}




?>
