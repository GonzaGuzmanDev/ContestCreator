OxoAwards.config(['$routeProvider','rootUrl',
    function($routeProvider, rootUrl) {
        $routeProvider.
            when('/login', {
                templateUrl: rootUrl + 'view/login',
                controller: 'loginController'
            }).
            when('/logout', {
                templateUrl: rootUrl + 'view/blank',
                controller: 'logoutController'
            }).
            when('/register', {
                templateUrl: rootUrl + 'view/register',
                controller: 'registerController',
                resolve: {
                    captchaUrl: function($http){
                        return $http.get(rootUrl+'captcha/url').success(function(data){
                            return data.data;
                        });
                    }
                }
            });
    }]);

var LoginControllers = angular.module('LoginControllers', ['ngRoute'])
    .controller('initController',function($location, AuthService, Flash, auth){
        if(angular.isDefined(auth)){
            if(auth.auth){
                AuthService.login(auth.user);
                AuthService.setCurrentUser(auth.user);
                $location.path('/home');
            }else{
                AuthService.logout();
            }
        }else{
            $location.path('/login');
        }
    })

    .controller('loginController',function($scope,$rootScope,$location,$injector,$timeout,rootUrl,currentBaseUrl,$uibModal,$http,$window,Authenticate,AuthService,Flash){
        if (AuthService.isLoggedIn()){
            $location.path('/home');
            $scope.hide = true;
        }
        /* Si estoy en un contest, tomo el provider contest, sino, le asigno null*/
        try {
            var contest = $injector.get('contest');
        }catch(e){
            var contest = null;
        }
        $scope.loginForm = {};
        $scope.login = function(){
            Flash.clear();
            if($scope.loginForm.remember !== true) delete $scope.loginForm.remember;
            Authenticate.save($scope.loginForm,
            function(data) {
                $scope.userLogin = data.user;
                $scope.id = $scope.userLogin['id'];
                if($scope.userLogin['active'] == 0){
                    AuthService.logout("Usuario Bloqueado");
                }
                else{
                    AuthService.login($scope.userLogin.user);
                    if(contest != null){
                        /*Si no tiene categorias es un encuestador,y va directamente al formulario de inscripcion*/
                        if(contest.categories != null){
                            if(contest.categories.length == 0 && data.user.super != 1){
                                $http.post(currentBaseUrl+'inscriptionExists/',{ 'con':contest.id, 'id':$scope.id}).success(function(data){
                                    $scope.exist = data.data;
                                    if($scope.exist.length == 0){
                                        //$rootScope.reloading = true;
                                        $window.location.href = '#/signup/'+data.returnTo;
                                        //$scope.$apply();
                                        $window.location.reload(true);
                                    } else{
                                        //$rootScope.reloading = true;
                                        var insc = $scope.exist[0];
                                        $window.location.href = '#/updateInscription/'+insc.role;
                                        //$scope.$apply();
                                        $window.location.reload(true);
                                    }
                                });
                            }
                            else {
                                $rootScope.reloading = true;
                                $window.location.href = $rootScope.previousUrl;
                                $window.location.reload(true);
                            }
                        }
                    }
                    else {
                        $rootScope.reloading = true;
                        $window.location.href = $rootScope.previousUrl;
                        $window.location.reload(true);
                    }
                }
            },function(response){
                Flash.show(response.data.flash);
            })
        };
        $scope.rememberPass = function(){
            Flash.clear();
            var modalInstance = $uibModal.open({
                templateUrl: 'rememberPass.html',
                controller: 'RememberPassCtrl'
            });
            modalInstance.result.then(function (data) {
                //Flash.show(data.flash, 'success');
            }, function () {
                //$log.info('Modal dismissed at: ' + new Date());
            });
        };
    })
    .controller('logoutController',function($scope, $rootScope, AuthService, $http, rootUrl){
        $rootScope.userInscriptions = null;
        delete $rootScope.currentUser;
        $http.get(rootUrl+'service/logout').success(function(){
            AuthService.logout();
        });
    })
    .controller('RememberPassCtrl',function($scope,rootUrl,$http,$uibModalInstance,Flash){
        Flash.clear($scope);
        $scope.submit = function () {
            Flash.clear($scope);
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(rootUrl+'service/rememberPassword', $scope.rememberForm).success(function(data){
                $uibModalInstance.close(data);
                //Flash.show(data.flash, 'success', $scope);
                Flash.show(data.flash, 'info');
            }).error(function(data){
                Flash.show(data.flash, 'danger', $scope);
            });
        };
        $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    })
    .controller('registerController',function($scope,$rootScope,rootUrl,$sanitize,$location,$http,AuthService,Flash,$uibModal, $window, captchaUrl){
        if (AuthService.isLoggedIn()){
            $location.path('/home');
            $scope.hide = true;
        }
        Flash.clear();
        $scope.register = function(){
            if($scope.registration.accept === false) delete $scope.registration.accept;
            delete $scope.errors;
            $http.post(rootUrl+'service/register/', $scope.registration).success(function(data, status, headers, config){
                if(data.errors){
                    $scope.errors = data.errors;
                    if(angular.isDefined(data.captchaUrl)){
                        $scope.captchaUrl = data.captchaUrl;
                    }
                }else{
                    if(angular.isDefined(data.user)) {
                        AuthService.login(data.user);
                        //$rootScope.reloading = true;
                        $window.location.href = $rootScope.previousUrl;
                        $window.location.reload(true);
                        $location.path('/home');
                    }else{
                        $location.path('/login');
                    }
                    Flash.show(data.flash, 'success');
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.');
            });
        };
        $scope.viewTerms = function(){
            Flash.clear();
            $uibModal.open({
                templateUrl: 'terms.html',
                controller: 'TermsCtrl',
                size: 'lg'
            });
        };
        $scope.captchaUrl = captchaUrl.data;
    })
    .controller('TermsCtrl',function($scope,$uibModalInstance){
        $scope.close = function () {
            $uibModalInstance.dismiss('cancel');
        };
    });