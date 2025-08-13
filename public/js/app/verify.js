var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'VerifyControllers',
    'AccountControllers',
    'angularMoment',
    'ui.bootstrap',
    'angular-abortable-requests',
    'flow'
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});