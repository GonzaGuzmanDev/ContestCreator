var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'AdminControllers',
    'ContestAdminControllers',
    'AccountControllers',
    'cfp.hotkeys',
    'ui.bootstrap.datetimepicker',
    'ui.bootstrap.tabs',
    'angularMoment',
    'ui.bootstrap',
    'angular-jwplayer',
    'adminServices',
    'angular-abortable-requests',
    'flow',
    'ui.sortable',
    'checklist-model',
    'textAngular',
    'ui.calendar'
    //'ui.bootstrap'
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});

var ContestAdminControllers = angular.module('ContestAdminControllers', ['ngRoute']);
var AdminControllers = angular.module('AdminControllers', ['ngRoute']);