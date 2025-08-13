var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'SiteControllers',
    'AccountControllers',
    'bootstrapLightbox',
    'angularMoment',
    'ui.bootstrap',
    'angular-abortable-requests',
    'flow'
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});
var ContestAdminControllers = angular.module('ContestAdminControllers', ['ngRoute']);