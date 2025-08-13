var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'InviteControllers',
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
                    templateUrl: rootUrl + contest.code + '/view/invite/key',
                    controller: 'keyInviteController',
                    resolve: {
                        /*captchaUrl: function($http){
                            return $http.get(rootUrl+'captcha/url').success(function(data){
                                return data.data;
                            });
                        }*/
                    }
                }).
                otherwise({redirectTo :'/'})
        }]
);