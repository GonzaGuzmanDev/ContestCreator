var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'SiteControllers',
    'AccountControllers',
    'ui.bootstrap',
    'angularMoment',
    'angular-abortable-requests',
    'flow',
    'ui.bootstrap.datetimepicker',
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});