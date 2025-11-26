<?php


namspace app\models;

use yii\db\ActiveRecord;
use app\models\voyage;
use app\models\internaute;



class reservation extends ActiveRecord{
  public static function tableName(){
    return 'fredouil.reservation';
  }

  public function getVoyage(){
    return $this->hasOne(voyage::class,['voyage'=>'id']);
  }

  public function getReserveur(){
    return $this-> hasOne(internaute::class,['voyageur'=>'id']);
  }







}





















?>