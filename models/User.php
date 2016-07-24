<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName(){
        return 'user';
    }
    //-------------------------------------------------------------------
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    //-------------------------------------------------------------------



    public static function findByEmail($emial){
        return self::findOne(['email' => $emial]);
    }



    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password, $hash)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $hash);
    }

    public function customLoad($data, $formName = ''){
        if(!is_array($data)){
            return false;
        }
        if($formName && !$data[$formName]){
            return false;
        }
        $data = ($formName)?$data[$formName]:$data;

        foreach($data as $property=>$value){
            if($property == 'confirmation'){continue;}
            if($property == '_csrf'){continue;}
            if($property == 'password'){
                $this->password = Yii::$app->getSecurity()->generatePasswordHash($value);
                continue;
            }
            $this->$property = $value;
        }

        return true;
    }
}
