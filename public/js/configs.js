OxoAwards.factory('myHttpInterceptor', function ($q, $location, Flash) {
    var canceller = $q.defer();
    return {
        request: function(config) {
            // promise that should abort the request when resolved.
            config.timeout = canceller.promise;
            return config;
        },
        response: function (response) {
            return response;
        },
        responseError: function (response) {
            if (response.status == 401) {
                delete localStorage.authenticated;
                $location.path('/login');
                Flash.show(response.data.flash);
            }
            return $q.reject(response);
        }
    };
});
OxoAwards.config(function($httpProvider, $sceDelegateProvider) {
    $httpProvider.interceptors.push('myHttpInterceptor');
    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    $sceDelegateProvider.resourceUrlWhitelist([
        // Allow same origin resource loads.
        'self',
        // Allow loading from our assets domain.  Notice the difference between * and **.
        'https://plataforma*.clicpago.com/**',
        'https://reinvention.la/Order/**'
    ]);
}).run(function($rootScope){
    $rootScope.$on('$locationChangeStart',function(evt, absNewUrl, absOldUrl) {
        $rootScope.previousUrl = absOldUrl;
        if($rootScope.reloading) evt.preventDefault();
    });
});
