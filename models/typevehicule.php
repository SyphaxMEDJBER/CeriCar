<?php
namespace app\models; // Espace de noms du modèle.
use yii\db\ActiveRecord; // ActiveRecord pour l'accès BDD.
use app\models\voyage; // Modèle Voyage (relation).


/**
 * Modèle TypeVehicule (référence de type de véhicule).
 */
class typevehicule extends ActiveRecord{ // Classe TypeVehicule.
    /**
     * @return string
     */
    public static function tableName(){ // Nom de table.
      return 'fredouil.typevehicule'; // Table SQL cible.
    }


    /**
     * Voyages utilisant ce type de véhicule.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVoyages(){ // Relation vers voyages.
      return $this->hasMany(voyage::class,['idtypev'=>'id']); // FK voyage.idtypev => typevehicule.id


  }










}











?>
