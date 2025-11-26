<?php
namespace app\models;

use yii\db\ActiveRecord;

class voyage extends ActiveRecord{

  public static function tableName(){
    return 'fredouil.voyage';
  }

  public function getConducteur(){
    return $this->hasOne(internaute::class,['id'=>'conducteur']);
  }

  public function getReservations(){
    return $this->hasMany(reservation::class,['voyage'=>'id']);
  }

  // public function getVoyageurs(){
  //   return $this->hasMany(internaute::class,[''])
  // }

  public function getTrajet(){
    return $this->hasOne(trajet::class,['id'=>'trajet']);
  }

  public function getTypeVehicule(){
    return $this->hasOne(typevehicule::class,['id'=>'idtypev']);
  }

    public function getMarqueVehicule(){
    return $this->hasOne(marquevehicule::class,['id'=>'imarquev']);
  }




}














?>