<?php
use yii\helpers\Html;
//$this->registerJsFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js');

$this->title = 'Registration';
$this->params['breadcrumbs'][] = $this->title;
?>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular.min.js"></script>
    <div class='row text-center' ng-controller="FormController as fcontrol">
        <div class="col-xs-4 col-xs-push-4">
            <h1><?= Html::encode($this->title) ?></h1>
            <form name='registration' action="/registration" ng-submit="fcontrol.submit($event)" method="POST">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>">
                <!-- Name -->
                <div class='form-group'
                        ng-class="{
                            'has-success': registration.username.$valid && !registration.username.$pristine,
                            'has-error':   registration.username.$invalid
                        }"
                >
                    <label>Name</label>
                    <input name='username' type='text'
                            class="form-control"
                            ng-model="fcontrol.username"
                            ng-blur="fcontrol.validateField($event)"
                    >
                    <span class="ng-hide" ng-show="registration.username.$error.custom">Name contains unallowed symbols</span>
                </div>

                <!-- Email -->
                <div class='form-group'
                        ng-class="{
                            'has-success': registration.email.$valid && !registration.email.$pristine,
                            'has-error':   registration.email.$invalid
                        }"
                >
                    <label>Email</label>
                    <input name='email' type='text'
                        class="form-control"
                        ng-model="fcontrol.email"
                        ng-blur="[fcontrol.checkEmail($event), fcontrol.validateField($event)]"
                    >
                    <span class="ng-hide" ng-show="registration.email.$error.custom">Wrong email</span>
                    <span class="ng-hide" ng-show="registration.email.$error.email_check">
                       Specified email is already registered to another account
                    </span>
                </div>

                <!-- Phone -->
                <div class='form-group'
                        ng-class="{
                            'has-success': registration.phone.$valid && !registration.phone.$pristine,
                            'has-error':   registration.phone.$invalid
                        }"
                >
                    <label>Phone</label>
                    <input name='phone' type='text'
                        class="form-control"
                        ng-model="fcontrol.phone"
                        ng-blur="fcontrol.validateField($event)"
                    >
                    <span class="ng-hide" ng-show="registration.phone.$error.custom">Wrong telephone number</span>
                </div>

                <!-- Password -->
                <div class='form-group'
                        ng-class="{
                            'has-success': registration.password.$valid && !registration.password.$pristine,
                            'has-error':   registration.password.$invalid
                        }"
                >
                    <label>Password</label>
                    <input name='password' type='password'
                        class="form-control"
                        ng-model="fcontrol.password"
                        ng-blur="[fcontrol.validateConfirmation($event), fcontrol.validateField($event)]"
                    >
                    <span class="ng-hide" ng-show="registration.password.$error.custom">Password must be at least 6 characters long<span>
                </div>

                <!-- Confirmarion -->
                <div class='form-group'
                        ng-class="{
                            'has-success': registration.confirm.$valid && !registration.confirm.$pristine,
                            'has-error':   registration.confirm.$invalid
                        }"
                >
                    <label>Confirm</label>
                    <input name='confirmation' type='password'
                        class="form-control"
                        ng-model="fcontrol.confirmation"
                        ng-blur="fcontrol.validateConfirmation($event)"
                     >
                    <span class="ng-hide" ng-show="registration.confirm.$error.pass_confirm">Password does not match confirmation</span>
                </div>

                <input type="submit" class='btn btn-primary' value="Submit">
            </form>
        </div>
    </div>

<script>
    angular
        .module('app', [])
        .service('CustomValidator', [CustomValidator])
        .controller('FormController', ['$scope', 'CustomValidator', '$http', FormController]);

    function FormController($scope, CustomValidator, $http){
        var self = this;

        self.submit = function(e){
            if($scope.registration.$invalid || $scope.registration.$pristine){
                e.preventDefault();
            }
        }

        self.validateField = function(e){
            var element = e.target.name;
            var value = self[element];

            if($scope.registration[element].$pristine){return;}

            var is_valid = CustomValidator.validate(element, value);
            $scope.registration[element].$setValidity('custom', is_valid);
        };

        self.validateConfirmation = function(e){
            var element = e.target.name;
            var value = self[element];
            if(element == 'password' && $scope.registration['confirmation'].$pristine){
                return;
            }
            var is_match = (self['password'] == self['confirmation'])
            $scope.registration['password'].$setValidity('pass_confirm', is_match);
            $scope.registration['confirmation'].$setValidity('pass_confirm', is_match);
        }

        self.checkEmail = function(e){
            if($scope.registration['email'].$pristine){ return; }

            var email = self['email'];
            var path_parts = window.location;
            var path = path_parts['protocol'] + '//' + path_parts['hostname'] + '/ajax/check-email';
            var csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');

            var data = {'email': email};
            $http.post(path, data, {
                dataType: 'json',
                headers:{
                    'X-CSRF-TOKEN': csrfToken
                    //"Content-type": "application/json"
                }
            }).then(successCallback, errorCallback);

            function successCallback(response){
                $scope.registration['email'].$setValidity('email_check',  response.data.is_free);
            }
            function errorCallback(response){ }
        }
    }

    function CustomValidator() {
        var self = this
        self.validate = function(element, value) {
            switch(element){
                case 'username': return /^[a-zA-Z ]+$/.test(value); break;
                case 'email': 	 return /^[a-z0-9\-\._]+@[a-z0-9\-_]+\.[\w]{2,6}$/.test(value); break;
                case 'phone': 	 return /^\+380[\d]{9}$/.test(value); break;
                case 'password': return value.length>=6; break;
            }
        };
        //self.checkEmail = function($scope, email){}
    }
</script>