<?php
namespace app\models;
use yii\db\ActiveRecord;
use app\models\voyage;


class typevehicule extends ActiveRecord{
  public static function tableName(){
    return 'fredouil.typevehicule';
  }


  public function getVoyages(){
    return $this->hasMany(voyage::class,['idtypev'=>'id']);


}










}











?>