adminServices
    .factory('User', ['$resource', 'rootUrl', 'RequestFactory',
        function($resource,rootUrl,RequestFactory){
            return RequestFactory.createResource({url: rootUrl + 'api/users/:id', options:{}, actions:{
                'get': {method:'GET', params:{id:'@id'}, isArray:false},
                'save': {method:'POST'},
                'query': {method:'GET', params:{page:'@page', query:'@query'}, isArray:false},
                'update': {method:'PUT'},
                'remove': {method:'DELETE'},
                'delete': {method:'DELETE'}
            }});
        }])/*.factory('Contest', ['$resource', 'rootUrl', 'RequestFactory',
 function($resource,rootUrl,RequestFactory){
 return RequestFactory.createResource({url: rootUrl + 'api/contests/:id', options:{}, actions:{
 'get': {method:'GET', params:{id:'@id'}, isArray: false},
 'save': {method:'POST'},
 'query': {method:'GET', params:{page:'@page', query:'@query'}, isArray:false},
 'update': {method:'PUT'},
 'remove': {method:'DELETE'},
 'delete': {method:'DELETE'},
 'saveFormats': {url: rootUrl + 'api/contest/:id/formats', method:'POST', params:{id:'@id'}},
 'saveInscriptionData': {url: rootUrl + 'api/contest/:id/inscriptionData', method:'POST', params:{id:'@id'}},
 'saveCategories': {url: rootUrl + 'api/contest/:id/categoriesData', method:'POST', params:{id:'@id'}},
 'saveEntriesData': {url: rootUrl + 'api/contest/:id/entriesData', method:'POST', params:{id:'@id'}},
 'saveHomeData': {url: rootUrl + 'api/contest/:id/homeData', method:'POST', params:{id:'@id'}},
 'getAllInscriptionsData': {url: rootUrl + 'api/contest/:id/allInscriptionsData', method:'GET', params:{id:'@id', page:'@page', query:'@query'}},
 'getUsersData': {url: rootUrl + 'api/contest/usersData', method:'POST', params:{term: '@term'}},
 'saveInscription': {url: rootUrl + 'api/contest/:id/inscription', method:'POST', params:{id:'@id'}},
 'destroyInscription': {url: rootUrl + 'api/contest/:id/inscription/:inscription', method:'DELETE', params:{id:'@id', inscription: '@inscription'}},
 'getAllPagesData': {url: rootUrl + 'api/contest/:id/allPagesData', method:'GET', params:{id:'@id', page:'@page', query:'@query'}},
 'savePage': {url: rootUrl + 'api/contest/:id/page', method:'POST', params:{id:'@id'}},
 'destroyPage': {url: rootUrl + 'api/contest/:id/page/:page', method:'DELETE', params:{id:'@id', page: '@page'}}
 }});
 }])*/
    .factory('Format', ['$resource', 'rootUrl', 'RequestFactory',
        function($resource, rootUrl, RequestFactory){
            return RequestFactory.createResource({url: rootUrl + 'api/formats/:id', options:{}, actions:{
                'get': {method:'GET', params:{id:'@id'}, isArray: false},
                'save': {method:'POST'},
                'query': {method:'GET', isArray: false},
                'update': {method:'PUT'},
                'remove': {method:'DELETE'},
                'delete': {method:'DELETE'},
                'saveContests': {url: rootUrl + 'api/format/:id/contests', method:'POST', params:{id:'@id'}}
            }});
        }])
    .factory('ContestFile', ['$resource', 'rootUrl', 'RequestFactory',
        function($resource, rootUrl, RequestFactory){
            return RequestFactory.createResource({url: rootUrl + 'api/contest-files/:id', options:{}, actions:{
                'get': {method:'GET', params:{id:'@id'}, isArray: false},
                'save': {method:'POST'},
                'query': {method:'GET', params:{page:'@page', query:'@query'}, isArray:false},
                'update': {method:'PUT'},
                'remove': {method:'DELETE'},
                'delete': {method:'DELETE'}
            }});
        }])
    .factory('Metrics', ['rootUrl', '$http',
        function (rootUrl, $http) {
            return {
                get: function () {
                    return $http({
                        url: rootUrl + 'api/metrics',
                        method: 'GET'
                    });
                }
            }
        }]);