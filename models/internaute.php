<?php
namespace app\models;

use yii\db\ActiveRecord;
use app\models\reservation;
use app\models\voyage;
use yii\web\IdentityInterface;

class internaute extends ActiveRecord implements IdentityInterface{

  //fonction obligatoire => le nom de la table
    public static function tableName(){
      return 'fredouil.internaute';
    }


    //['clé_étrangère_dans_reservation' => 'clé_locale_dans_internaute']
    //un internaute peut avoir plusieurs réservations
    public function getReservations(){
      return $this->hasMany(reservation::class ,['voyageur'=>'id']);
    }


    //un conducteur peut proposer plusieurs voyages
    public function getVoyages(){
      return $this->hasMany(voyage::class,['conducteur'=>'id']);
    }



  public static function getUserByIdentifiant($pseudo){
    return self::findOne(['pseudo'=>$pseudo]);
  }


  public static function getUserById($id)
    {
        return self::findOne($id);

    }


    public static function getProfilById($id)
    {
         return self::findOne($id);

    }

    public static function findIdentity($id){
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null){
        return null;
    }

    public function getId(){
        return $this->id;
    }

    public function getAuthKey(){
        return null;
    }

    public function validateAuthKey($authKey){
        return true;
    }

    public static function findByPseudo($pseudo){
        return self::findOne(['pseudo' => $pseudo]);
    }

    public function validatePassword($password){
        return md5($password) === $this->pass;
    }
    
    








}























?>