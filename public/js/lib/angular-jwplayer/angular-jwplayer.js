'use strict';
angular.module('angular-jwplayer', []).directive('jwplayer', [ function () {

	var defaultProps = {
		id : 'angular-jwplayer-' + Math.floor((Math.random()*999999999)+1)
	};

	return {
		restrict: 'EC',
		scope: {
			id: '@id',
			setupVars: '=setup'

		},

		template: function(scope, element, attrs) {
            if(scope.id === null || typeof(scope.id) === undefined)
				scope.id = 'angular-jwplayer-' + Math.floor((Math.random()*999999999)+1);
            return "<div id='player' class='player-ph'></div>";
		},
		
		link: function(scope, element, attrs) {
            if(scope.id === null || typeof(scope.id) === undefined)
                scope.id = 'angular-jwplayer-' + Math.floor((Math.random()*999999999)+1);
            var id = 'angular-jwplayer-' + Math.floor((Math.random()*999999999)+1);
            element.find('.player-ph').attr('id', id);
            jwplayer(id).setup(scope.setupVars);
		}
	};
}]);