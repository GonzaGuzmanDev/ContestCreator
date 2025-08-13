OxoAwards.config(['$routeProvider','rootUrl',
        function($routeProvider,rootUrl){
            $routeProvider.
                when('/home',{
                    templateUrl: rootUrl + 'view/blank',
                    controller: 'homeController'
                }).
                otherwise({redirectTo :'/'})
        }]
);

var VerifyControllers = angular.module('VerifyControllers', ['ngRoute'])
    .controller('homeController',function($location){
        $location.path('/');
    })
    .controller('initController',function($scope,rootUrl,$timeout,$window){
        if(!$scope.verified){
            $timeout(function(){
                $window.location.href = rootUrl+'#/account/data';
            },3000);
        }
    });