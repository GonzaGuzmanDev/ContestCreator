OxoAwards.config(['$routeProvider', 'rootUrl', 'currentBaseUrl', function ($routeProvider, rootUrl, currentBaseUrl) {
        $routeProvider
            .when('/users', {
                templateUrl: currentBaseUrl + 'view/users/list',
                controller: 'AdminUsersListCtrl'
            })
            .when('/users/edit/:id', {
                templateUrl: currentBaseUrl + 'view/users/form-general',
                controller: 'AdminUsersEditCtrl',
                resolve:{
                    userData: function(User, $route){
                        return User.get({'id': $route.current.params.id}).promise.then(function(response){
                            return response;
                        });
                    }
                }
            })
            .when('/users/edit/:id/inscriptions', {
                templateUrl: currentBaseUrl + 'view/users/form-inscriptions',
                controller: 'AdminUsersInscriptionsCtrl',
                resolve:{
                    userData: function(User, $route){
                        return User.get({'id': $route.current.params.id}).promise.then(function(response){
                            return response;
                        });
                    },
                    userInscriptionsData: function($http, $route){
                        return $http.get(rootUrl + 'api/user/'+$route.current.params.id+'/inscriptionsData').then(function(response){
                            return response.data;
                        });
                    }
                }
            })
            .when('/users/new', {
                templateUrl: currentBaseUrl + 'view/users/form-general',
                controller: 'AdminUsersCreateCtrl'
            })
        }]);

AdminControllers.controller('AdminUsersListCtrl', function($scope, rootUrl, currentBaseUrl, Flash, $sanitize, $location, User, $uibModal){
    $scope.Math = Math;
    $scope.activeMenu = 'users';
    $scope.dataLoaded = false;
    $scope.pagination = {};
    $scope.updateUsersList = function(){
        if(angular.isDefined($scope.usersData)){
            $scope.usersData.abort();
        }
        $scope.usersData = User.query({page:$scope.pagination.page,orderBy:$scope.pagination.orderBy,orderDir:$scope.pagination.orderDir,query:$scope.query}, function(response){
            //$scope.pagination = response.pagination;
            //$scope.users = response.data;
        });
        $scope.usersData.promise.then(function(response){
            $scope.dataLoaded = true;
            $scope.pagination = response.pagination;
            $scope.users = response.data;
        });
    };
    $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateUsersList(); });
    $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateUsersList(); });
    $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateUsersList(); });
    $scope.$watch(function(){ return $scope.query; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateUsersList(); });
    $scope.updateUsersList();
    $scope.changeOrder = function(newOrder){
        if($scope.pagination.orderBy == newOrder){
            $scope.pagination.orderDir = !$scope.pagination.orderDir;
        }else{
            $scope.pagination.orderBy = newOrder;
        }
    };
    //$scope.orderProp = 'email';
    $scope.delete = function(user) {
        var modalInstance = $uibModal.open({
            templateUrl: currentBaseUrl+'view/users/delete',
            controller: 'AdminUsersDeleteCtrl',
            resolve: {
                captchaUrl: function($http){
                    return $http.get(rootUrl+'captcha/url').success(function(data){
                        return data.data;
                    });
                },
                user: function () {
                    return user;
                }
            }
        });
        modalInstance.result.then(function (data) {
            Flash.show(data.flash, 'success', $scope);
            $scope.updateUsersList();
        }, function () {});
    };
    $scope.loginAs = function(user) {
        // TODO LOGIN AS
        console.log({"LOGIN AS": user});
    };
}).controller('AdminUsersCreateCtrl',function($scope,$sanitize,$location,$routeParams,$timeout,User){
    $scope.activeMenu = 'users';
    $scope.parentId = $routeParams.parentId;
    $scope.user = {super: 0};
    $scope.userSuper = {super: 0};
    $scope.save = function() {
        $scope.user.super = $scope.userSuper.super;
        User.save($scope.user, function(data) {
            if(angular.isDefined(data.errors)){
                $scope.errors = data.errors;
            }else{
                $timeout(function() {
                    $location.path('/users');
                });
            }
        });
    };
})
.controller('AdminUsersEditCtrl',function($scope,rootUrl,currentBaseUrl,Flash,$sanitize,$location,$routeParams,$timeout,$uibModal,User,userData){
    $scope.activeMenu = 'users';
    $scope.user = userData;
    $scope.user['super'] = parseInt($scope.user['super']);

    $scope.delete = function() {
        var modalInstance = $uibModal.open({
            templateUrl: currentBaseUrl + 'view/users/delete',
            controller: 'AdminUsersDeleteCtrl',
            resolve: {
                captchaUrl: function($http){
                    return $http.get(rootUrl+'captcha/url').success(function(data){
                        return data.data;
                    });
                },
                user: function () {
                    return $scope.user;
                }
            }
        });
        modalInstance.result.then(function (data) {
            Flash.show(data.flash, 'success', $scope);
            $location.path('/users');
        }, function () {});
    };
    $scope.save = function() {
        console.log($scope.user['active']);
        User.update({id: $scope.user.id}, $scope.user, function(data) {
            if(angular.isDefined(data.errors)){
                $scope.errors = data.errors;
            }else{
                $timeout(function() {
                    $location.path('/users');
                });
            }
        });
    };
})
.controller('AdminUsersInscriptionsCtrl',function($scope, $timeout, $uibModal, User, userData, userInscriptionsData){
    // Oculta los botones de cancelar y guardar
    $scope.hideSaveFooter = true;
    $scope.activeMenu = 'users';
    $scope.user = userData;
    $scope.user.super = parseInt($scope.user.super);
    $scope.userInscriptions = userInscriptionsData.inscriptions;
})
.controller('AdminUsersDeleteCtrl',function($scope,$http,Flash,rootUrl,$sanitize,$location,$route,$timeout,$uibModalInstance,user,captchaUrl,User){
    $scope.captchaUrl = captchaUrl.data;
    $scope.user = user;
    $scope.close = function(){
        $uibModalInstance.close();
    };
    $scope.destroy = function() {
        $http.post(rootUrl + 'api/user/' + $scope.user.id + '/delete', {captcha: $scope.captcha}).success(function(response, status, headers, config){
            $scope.sending = false;
            if(response.errors){
                $scope.errors = response.errors;
                $scope.captcha = '';
                $scope.captchaUrl = response.captchaUrl;
                Flash.clear($scope);
            }else if(response.error){
                Flash.show(response.error, 'danger', $scope);
            }else{
                $uibModalInstance.close(response);
                Flash.show(response.flash, 'success', $scope);
            }
        }).error(function(data, status, headers, config){
            $scope.sending = false;
            Flash.show(data.error.message, 'danger', $scope);
        });
    };
});