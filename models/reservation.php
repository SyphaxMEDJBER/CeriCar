<?php


namespace app\models;

use yii\db\ActiveRecord;
use app\models\voyage;
use app\models\internaute;



class reservation extends ActiveRecord{
  public static function tableName(){
    return 'fredouil.reservation';
  }

  public function getVoyageObj(){
    return $this->hasOne(voyage::class,['id'=>'voyage']);
  }

  public function getReserveur(){
    return $this-> hasOne(internaute::class,['id'=>'voyageur']);
  }


  public static function getReservationsByVoyageId($idVoyage){
    return self::findAll(['voyage'=>$idVoyage]);
  }



}




?>