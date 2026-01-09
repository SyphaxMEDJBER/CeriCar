<?php
namespace app\models; // Espace de noms du modèle.

use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.

use app\models\voyage; // Modèle Voyage (relation).

/**
 * Modèle MarqueVehicule (référence de marque de véhicule).
 */
class marquevehicule extends ActiveRecord{ // Classe MarqueVehicule.
  /**
   * @return string
   */
  public static function tableName(){ // Nom de table.
    return 'fredouil.marquevehicule'; // Table SQL cible.
  }

  /**
   * Voyages utilisant cette marque.
   *
   * @return \yii\db\ActiveQuery
   */
  public function getVoyages(){ // Relation vers voyages.
    return $this->hasMany(voyage::class,['idmarquev'=>'id']); // FK voyage.idmarquev => marquevehicule.id
  }


}














?>
