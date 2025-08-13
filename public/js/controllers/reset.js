OxoAwards.config(['$routeProvider','rootUrl',
        function($routeProvider,rootUrl){
            $routeProvider.
                when('/',{
                    templateUrl: rootUrl + 'view/password/reset',
                    controller: 'resetController'
                }).
                otherwise({redirectTo :'/'})
        }]
);

var ResetControllers = angular.module('ResetControllers', ['ngRoute'])
    .controller('resetController',function($scope,rootUrl,$http, Flash, resetToken){
        $scope.reminder = { token: resetToken };
        Flash.clear();
        $scope.submit = function(){
            $scope.errors = $scope.error = undefined;
            $http.post(rootUrl+'service/password/reset/', $scope.reminder).success(function(data, status, headers, config){
                if(data.errors){
                    $scope.errors = data.errors;
                }else if(data.error){
                    $scope.error = data.error;
                }else{
                    Flash.show(data.flash, 'success');
                    $scope.passReseted = true;
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.');
            });
        };
    });