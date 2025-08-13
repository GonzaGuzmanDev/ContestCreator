var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'BillingRedirectControllers',
    'AccountControllers',
    'angularMoment',
    'ui.bootstrap',
    'angular-abortable-requests',
    'flow'
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});

OxoAwards.config(['$routeProvider','rootUrl','contest',
        function($routeProvider,rootUrl, contest){
            $routeProvider.
                when('/',{
                    templateUrl: rootUrl + contest.code + '/view/billing/redirect',
                    controller: 'initController',
                    resolve:{
                        categoriesData: function($http, contest, $route){
                            return $http.get(rootUrl+contest.code+'/categories').success(function(data){
                                return data;
                            });
                        }
                    }
                }).
                otherwise({redirectTo :'/'})
        }]
);