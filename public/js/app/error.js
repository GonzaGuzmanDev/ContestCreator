var OxoAwards = angular.module("OxoAwards",[
    'ngRoute',
    'ngResource',
    'ngSanitize',
    'LoginControllers',
    'AccountControllers',
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
    'ui.calendar'
]).run(function($http, CSRF_TOKEN){
    $http.defaults.headers.common['csrf_token'] = CSRF_TOKEN;
});