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

  public static function getTrajetById($idTrajet){
    return self::findOne(['id'=>$idTrajet]);
  }


  public static function getDepart(){//pour la search bar
    return self::find()->select('depart')
        ->distinct()
        ->column();// pour retourner un tableau et pas un objet 
  }

  public static function getArrivee(){// pour la search bar
    return self::find()->select('arrivee')
    ->distinct()
    ->column();
  }






  public static function chercherTrajets($depart, $arrivee)
  {
      return self::find()
          ->where(['depart' => $depart, 'arrivee' => $arrivee])
          ->all();
  }




}






?>









