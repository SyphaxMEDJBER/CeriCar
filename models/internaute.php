<?php
namespace app\models;

use yii\db\ActiveRecord;
use app\models\reservation;
use app\models\voyage;

class internaute extends ActiveRecord{

  //fonction obligatoire => le nom de la table
    public static function tableName(){
      return 'fredouil.internaute';
    }


    //['clé_étrangère_dans_reservation' => 'clé_locale_dans_internaute']
    //un internaute peut avoir plusieurs réservations
    public function getReservations(){
      return $this->hasMany(reservation::class ,['voyageur'=>'id'])
    }


    //un conducteur peut proposer plusieurs voyages
    public function getVoyages(){
      return $this->hasMany(voyage::class,['conducteur'=>'id']);
    }










}












?>