OxoAwards.config(['$routeProvider','rootUrl',
        function($routeProvider, rootUrl){
            $routeProvider.
                when('/', {
                    templateUrl: rootUrl + 'view/blank',
                    controller: 'initController'
                }).
                when('/home',{
                    templateUrl: rootUrl + 'view/home',
                    controller: 'homeController'
                }).
                when('/privacypolicy',{
                    templateUrl: rootUrl + 'view/privacypolicy',
                    controller: 'privacyController'
                }).
                when('/termsofuse',{
                    templateUrl: rootUrl + 'view/termsofuse',
                    controller: 'termsController'
                }).
                when('/applyForContest',{
                    templateUrl: rootUrl + 'view/applyForContest',
                    controller: 'applyForContestController',
                    resolve: {
                        captchaUrl: function($http){
                            return $http.get(rootUrl+'captcha/url').success(function(data){
                                return data.data;
                            });
                        },
                    }
                }).
                when('/loginApplyForContest',{
                    templateUrl: rootUrl + 'view/loginApplyForContest',
                    controller: 'loginApplyForContestController',
                    resolve: {
                        captchaUrl: function($http){
                            return $http.get(rootUrl+'captcha/url').success(function(data){
                                return data.data;
                            });
                        },
                    }
                }).
                otherwise({redirectTo :'/'})
        }]
);

var SiteControllers = angular.module('SiteControllers', ['ngRoute'])
    .controller('initController',function($scope, $location){
        $location.path('/home');
    })
    .controller('homeController',function($anchorScroll){
        $anchorScroll();
    })
    .controller('loginApplyForContestController',function($scope,Flash,$http,rootUrl,$window,$uibModal,Authenticate, AuthService,currentBaseUrl,captchaUrl){
        console.log(rootUrl+"/applyForContest");
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
                        $window.location.href = rootUrl+"#/applyForContest";
                        $window.location.reload(true);
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
        $scope.registration = {};
        $scope.register = function(){
            $http.post(currentBaseUrl+'service/register/', $scope.registration).then(function(data){
                AuthService.login(data.user);
                $window.location.href = rootUrl+"#/applyForContest";
                $window.location.reload(true);
            }, function(response){
                if(angular.isDefined(response.data.captchaUrl)) $scope.captchaUrl = response.data.captchaUrl;
                $scope.errors = response.data.errors;
                //Flash.show(response.data.flash);
            });
        };
        $scope.captchaUrl = captchaUrl.data;
    })
    .controller('applyForContestController',function($scope,$anchorScroll, $window, $http, $location, rootUrl, Flash){
        $scope.contest = angular.extend({name: '',public:0, inscription_public: 0, voters_public: 0, max_entries:0});
        $scope.nameAvailable = null;
        $scope.codeAvailable = null;
        $anchorScroll();
        $scope.$watch('contest.name', function(){
            $scope.contest.code = $scope.contest.name.replace(/ /g, '-').toLowerCase();
            $scope.isAvailable($scope.contest.name, $scope.contest.code);
        });

        $scope.$watch('contest.code', function(){
            $scope.contest.code = $scope.contest.code.replace(/ /g, '-').toLowerCase();
            $scope.isAvailable($scope.contest.name, $scope.contest.code);
        });

        $scope.isAvailable = function(name, code){
            if(name){
                $http.post(rootUrl+'available',{name: name, code: code}).success(function(data){
                    $scope.nameAvailable = data.name;
                    $scope.codeAvailable = data.code;
                });
            }
        };
        $scope.contest.wizardAdmin = true;

        $scope.saveAndNext = function(import_contest){
            if($scope.nameAvailable == false){
                Flash.show('Nombre Invalido', 'danger', $scope);
                return;
            }
            if($scope.codeAvailable == false){
                Flash.show('Codigo Invalido', 'danger', $scope);
                return;
            }
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $scope.contest.import = import_contest;
            $http.post(rootUrl + 'api/contest/save/' + (angular.isDefined($scope.contest.id) ? $scope.contest.id : ''), $scope.contest).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.contest = response.contest;
                    Flash.show(response.flash, 'success', $scope);
                    if(import_contest == true) $window.location.href = rootUrl+$scope.contest.code+"#/admin/import-contest";
                    else $window.location.href = rootUrl+$scope.contest.code+"#/admin/inscriptions";
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };
    })
    .controller('privacyController',function($scope, $location, $anchorScroll){
        $scope.scrollTo = function(hash){
            $location.hash(hash);
            $anchorScroll();
        };
        $anchorScroll();
    })
    .controller('termsController',function($anchorScroll){
        $anchorScroll();
    })
    .controller('contactFormController',function($scope, rootUrl, $http, Flash){
        $scope.send = function(){
            $scope.sending = true;
            $http.post(rootUrl+'contact', $scope.data).success(function(data){
                $scope.sending = false;
                if(angular.isDefined(data.errors)){
                    $scope.errors = data.errors;
                }else{
                    delete $scope.errors;
                    Flash.show(data.flash, 'success', $scope);
                }
            }).error(function(data){
                delete $scope.errors;
                $scope.sending = false;
                Flash.show(data.flash, 'danger', $scope);
            });
        };
    });