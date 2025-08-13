OxoAwards.config(['$routeProvider', 'rootUrl', 'currentBaseUrl', function($routeProvider,rootUrl,currentBaseUrl){
        $routeProvider.
            when('/contests', {
                templateUrl: currentBaseUrl + 'view/contests/list',
                controller: 'AdminContestsListCtrl'
            }).
            when('/contests/edit/:code?', {
                templateUrl: currentBaseUrl + 'view/contests/form-general',
                controller: 'AdminContestsEditCtrl',
                resolve:{
                    contestData: function($http, $route){
                        if(angular.isDefined($route.current.params.code)) {
                            return $http.get(rootUrl + 'api/contest/' + $route.current.params.code).then(function (response) {
                                return response.data;
                            });
                        }
                        return {};
                    }
                }
            })
        }]);