<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;


class AjaxController extends Controller{

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    function actionCheckEmail(){
        $email = Yii::$app->request->post('email');
        $request = json_decode(file_get_contents('php://input'), true);
        if(!$request || !$request['email']){
            die(json_encode(['is_free'=>false, 'is_error'=>true]));
        }
        $answer = User::findByEmail($request['email']);
        die(json_encode(['is_free'=>!(bool)$answer, 'is_error'=>false, 'a'=>$answer]));
    }
}