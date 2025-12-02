<?php
namespace app\models;

use yii\db\ActiveRecord;
use app\models\internaute;
use app\models\reservation;
use app\models\trajet;
use app\models\typevehicule;
use app\models\marquevehicule;

class voyage extends ActiveRecord{

  public static function tableName(){
    return 'fredouil.voyage';
  }

  public function getConducteurObj(){
    return $this->hasOne(internaute::class,['id'=>'conducteur']);
  }

  public function getReservations(){
    return $this->hasMany(reservation::class,['voyage'=>'id']);
  }

  // public function getVoyageurs(){
  //   return $this->hasMany(internaute::class,[''])
  // }

  public function getTrajetObj(){
    return $this->hasOne(trajet::class,['id'=>'trajet']);
  }

  public function getTypeVehicule(){
    return $this->hasOne(typevehicule::class,['id'=>'idtypev']);
  }

    public function getMarqueVehicule(){
    return $this->hasOne(marquevehicule::class,['id'=>'idmarquev']);
  }

  public static function getVoyagesByTrajetId($idTrajet){
    return self::findAll(['trajet'=>$idTrajet]);

  }




}














?>