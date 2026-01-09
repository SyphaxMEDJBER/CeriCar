<?php

namespace app\models; // Espace de noms du modèle.

use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.
use app\models\voyage; // Modèle Voyage (relation).


/**
 * Modèle Trajet (route entre deux villes).
 */
class trajet extends ActiveRecord{ // Classe Trajet.
  /**
   * @return string
   */
  public static function tableName(){ // Nom de table.
    return 'fredouil.trajet'; // Table SQL cible.
  }

  /**
   * Voyages associés à ce trajet.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getVoyages(){ // Relation 1->N vers voyages.
    return $this->hasMany(voyage::class,['trajet'=>'id']); // FK voyage.trajet => trajet.id
  }


  /**
   * Trouver un trajet par départ/arrivée.
   *
   * @param string $villedep
   * @param string $villearr
   * @return static|null
   */
  public static function getTrajet($villedep,$villearr){ // Recherche par villes.
    return self::findOne(['depart'=>$villedep,'arrivee'=>$villearr]); // Trajet exact.
  }

  /**
   * Trouver un trajet par id.
   *
   * @param int $idTrajet
   * @return static|null
   */
  public static function getTrajetById($idTrajet){ // Recherche par id.
    return self::findOne(['id'=>$idTrajet]); // Trajet par clé primaire.
  }


  /**
   * Liste distincte des villes de départ pour la barre de recherche.
   *
   * @return array
   */
  public static function getDepart(){//pour la search bar
    return self::find()->select('depart') // Sélectionne la colonne depart.
        ->distinct() // Évite les doublons.
        ->column();// pour retourner un tableau et pas un objet 
  }

  /**
   * Liste distincte des villes d’arrivée pour la barre de recherche.
   *
   * @return array
   */
  public static function getArrivee(){// pour la search bar
    return self::find()->select('arrivee') // Sélectionne la colonne arrivee.
    ->distinct() // Évite les doublons.
    ->column(); // Retourne un tableau simple.
  }






  /**
   * Trouver tous les trajets pour un couple départ/arrivée.
   *
   * @param string $depart
   * @param string $arrivee
   * @return static[]
   */
  public static function chercherTrajets($depart, $arrivee) // Recherche de trajets.
  {
      return self::find() // Démarre la requête.
          ->where(['depart' => $depart, 'arrivee' => $arrivee]) // Conditions.
          ->all(); // Tous les résultats.
  }





  /**
   * Trouver tous les trajets au départ d’une ville.
   *
   * @param string $villedep
   * @return static[]
   */
  public static function getTrajetsDepuis($villedep) // Trajets au départ d'une ville.
  {
      return self::find() // Démarre la requête.
          ->where(['depart' => $villedep]) // Condition départ.
          ->all(); // Tous les résultats.
  }










}






?>






