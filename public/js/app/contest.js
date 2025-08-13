var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'ContestAdminControllers',
    'AccountControllers',
    'ContestControllers',
    'ui.bootstrap',
    'cfp.hotkeys',
    'ui.bootstrap.datetimepicker',
    'ui.bootstrap.tabs',
    'angularMoment',
    'angular-abortable-requests',
    'ui.sortable',
    'bootstrapLightbox',
    'checklist-model',
    'angular-jwplayer',
    'angular-inview',
    'flow',
    'textAngular',
    'ngOnload',
    'ui.calendar',
    'ngCookies'
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});
var ContestAdminControllers = angular.module('ContestAdminControllers', ['ngRoute']);