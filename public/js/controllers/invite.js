var InviteControllers = angular.module('InviteControllers', ['ngRoute'])
    .controller('initController',function($scope,$location){
        $location.path('/invite');
    })
    .controller('inviteController',function($scope,Flash,$http,rootUrl,$window,contest,$uibModal,AuthService,currentBaseUrl,inviteRegister,captchaUrl){
        $scope.allowRegister = inviteRegister;
        $scope.login = function(){
            $http.post(currentBaseUrl+'login/', $scope.loginForm).then(function(data){
                AuthService.login(data.user);
                $window.location.href = rootUrl+contest.code+"/#voting";
            }, function(response){
                Flash.show(response.data.flash);
            });
        };
        $scope.registration = {};
        $scope.register = function(){
            $http.post(currentBaseUrl+'register/', $scope.registration).then(function(data){
                AuthService.login(data.user);
                $window.location.href = rootUrl+contest.code+"/#voting";
            }, function(response){
                if(angular.isDefined(response.data.captchaUrl)) $scope.captchaUrl = response.data.captchaUrl;
                $scope.errors = response.data.errors;
                //Flash.show(response.data.flash);
            });
        };
        $scope.captchaUrl = captchaUrl.data;
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
    .controller('rejectController',function($scope,$http,currentBaseUrl){
        $scope.rejected = false;
        $scope.reject = function(){
            $http.post(currentBaseUrl+'reject/', {}).then(function(data){
                $scope.rejected = true;
                //Flash.show(data.flash, 'success');
            }, function(response){
            });
        };
    })
    .controller('keyInviteController',function($scope,Flash,$http,rootUrl,$window,contest,$uibModal,AuthService,currentBaseUrl){
        $scope.login = function(){
            $scope.sending = true;
            $http.post(currentBaseUrl+'login/', {key:$scope.key}).then(function(data){
                $scope.sending = false;
                AuthService.login(data.user);
                $window.location.href = rootUrl+contest.code+"/#voting";
            }, function(response){
                $scope.sending = false;
                Flash.show(response.data.flash);
            });
        };

        $scope.terms = function(){
            Flash.clear();
            $uibModal.open({
                templateUrl: 'terms.html',
                controller: 'TermsCtrl',
                size: 'lg'
            });
        };
    });