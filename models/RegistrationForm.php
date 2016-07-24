<?php

namespace app\models;

use Yii;
use yii\base\Model;

class RegistrationForm extends Model{
    public $username;
    public $email;
    public $phone;
    public $password;
    public $confirmation;

    public function rules()
    {
        return [
            [['username', 'email', 'phone', 'password', 'confirmation'], 'required'],
            ['username', 'validateName'],
            ['email', 'validateEmail'],
            ['password', 'validatePassword'],
            ['phone', 'validatePhone'],
        ];
    }

    public function validateName($attribute, $params){
        if(!preg_match('/^[a-zA-Zà-ÿÀ-ß ]+$/', $this->username)){
            $this->addError($attribute, 'Name contains unallowed symbols.');
        }
    }

    public function validateEmail($attribute, $params){
        if(!preg_match('/^[a-z0-9\-\._]+@[a-z0-9\-_]+\.[\w]{2,6}$/', $this->email)){
            $this->addError($attribute, 'Incorrect e-mail.');
        }
        if(User::findByEmail($this->email)){
            $this->addError($attribute, 'Another user has already registered with specified email address.');
        }
    }

    public function validatePhone($attribute, $params){
        if(!preg_match('/^\+380[\d]{9}$/', $this->phone)){
            $this->addError($attribute, 'Incorrect telephone number.');
        }
    }

    public function validatePassword($attribute, $params){
        if(strlen($this->password)<6){
            $this->addError($attribute, 'Password must be at least 6 characters.');
        }
        if($this->password !== $this->confirmation){
            $this->addError($attribute, 'Password does not match confirmation.');
        }
    }
}