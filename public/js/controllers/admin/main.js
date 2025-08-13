OxoAwards.config(['$routeProvider', 'rootUrl', 'currentBaseUrl', function($routeProvider, rootUrl, currentBaseUrl){
        $routeProvider
            .when('/', {
                templateUrl: rootUrl + 'view/blank',
                controller: 'initController',
                resolve:{
                    auth: function($http){
                        return $http.get(rootUrl+'service/check/super').then(function(data) {
                            return data.data;
                        });
                    }
                }
            })
            .otherwise({redirectTo :'/'})
        }]);

AdminControllers.controller('initController',function($location){
        $location.path('/home');
    });
var adminServices = angular.module('adminServices', ['ngResource']);