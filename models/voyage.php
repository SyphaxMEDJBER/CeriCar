<?php
namespace app\models; // Espace de noms du modèle.

use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.
use app\models\internaute; // Modèle Internaute (relation).
use app\models\reservation; // Modèle Reservation (relation).
use app\models\trajet; // Modèle Trajet (relation).
use app\models\typevehicule; // Modèle TypeVehicule (relation).
use app\models\marquevehicule; // Modèle MarqueVehicule (relation).

/**
 * Modèle Voyage (instance de trajet).
 */
class voyage extends ActiveRecord{ // Classe Voyage.

  /**
   * @return string
   */
  public static function tableName(){ // Nom de table.
    return 'fredouil.voyage'; // Table SQL cible.
  }

  /**
   * Conducteur (utilisateur) pour ce voyage.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getConducteurObj(){ // Relation vers conducteur.
    return $this->hasOne(internaute::class,['id'=>'conducteur']); // FK voyage.conducteur => internaute.id
  }

  /**
   * Réservations pour ce voyage.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getReservations(){ // Relation vers réservations.
    return $this->hasMany(reservation::class,['voyage'=>'id']); // FK reservation.voyage => voyage.id
  }

  // public function getVoyageurs(){
  //   return $this->hasMany(internaute::class,[''])
  // }

  public function getTrajetObj(){ // Relation vers trajet.
    return $this->hasOne(trajet::class,['id'=>'trajet']); // FK voyage.trajet => trajet.id
  }

  /**
   * Type de véhicule pour ce voyage.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getTypeVehicule(){ // Relation vers type véhicule.
    return $this->hasOne(typevehicule::class,['id'=>'idtypev']); // FK voyage.idtypev => typevehicule.id
  }

  /**
   * Marque du véhicule pour ce voyage.
   *
   * @return \yii\db\ActiveQuery
   */
    public function getMarqueVehicule(){ // Relation vers marque véhicule.
    return $this->hasOne(marquevehicule::class,['id'=>'idmarquev']); // FK voyage.idmarquev => marquevehicule.id
  }

  /**
   * Trouver les voyages par id de trajet.
   *
   * @param int $idTrajet
   * @return static[]
   */
  public static function getVoyagesByTrajetId($idTrajet){ // Voyages par trajet.
    return self::findAll(['trajet'=>$idTrajet]); // Tous les voyages liés.

  }






  /**
   * Places restantes en tenant compte des réservations.
   *
   * @return int
   */
  public function getPlacesRestantes() // Calcul des places restantes.
  {
      // Somme des places réservées pour ce voyage.
      $reservations = reservation::find() // Démarre la requête.
          ->where(['voyage' => $this->id]) // Filtre par voyage.
          ->sum('nbplaceresa'); // Somme des réservations.

      if ($reservations === null) { // Si aucune réservation.
          $reservations = 0; // Initialise à 0.
      }

      return $this->nbplacedispo - $reservations; // Places restantes.
  }

  /**
   * Heure d'arrivee approximative (1 km = 1 minute).
   *
   * @param float|int $distanceKm
   * @return string
   */
  public function getHeureArrivee($distanceKm)
  {
      $departMin = (int)$this->heuredepart * 60;
      $dureeMin = (int)round((float)$distanceKm);
      $arriveeMin = $departMin + $dureeMin;
      $h = (int)floor($arriveeMin / 60) % 24;
      $m = $arriveeMin % 60;
      return sprintf('%02d:%02d', $h, $m);
  }





}














?>
