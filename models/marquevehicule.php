<?php
namespace app\models;

use yii\db\ActiveRecord;

use app\models\voyage;

class marquevehicule extends ActiveRecord{
  public static function tableName(){
    return 'fredouil.marquevehicule';
  }

  public function getVoyages(){
    return $this->hasMany(voyage::class,['idmarquev'=>'id']);
  }


}














?>