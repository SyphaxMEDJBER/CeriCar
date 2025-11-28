<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\voyage;


class trajet extends ActiveRecord{
  public static function tableName(){
    return 'fredouil.trajet';
  }

  public function getVoyages(){
    return $this->hasMany(voyage::class,['trajet'=>'id']);
  }


  public static function getTrajet($villedep,$villearr){
    return self::findOne(['depart'=>$villedep,'arrivee'=>$villearr]);
  }

}






?>