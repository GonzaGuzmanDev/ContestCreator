OxoAwards.config(['$routeProvider','rootUrl','currentBaseUrl','contest',
        function($routeProvider,rootUrl,currentBaseUrl, contest){
            $routeProvider
                .when('/admin', {
                    templateUrl: currentBaseUrl + 'view/admin/home',
                    controller: 'AdminContestsHome',
                    resolve:{
                        adminInfo: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/adminInfo').then(function(response){
                                return response;
                            });
                        }
                    }
                })
                .when('/admin/inscriptions', {
                    templateUrl: currentBaseUrl + 'view/admin/inscriptions',
                    controller: 'AdminContestInscriptionsEditCtrl',
                    resolve:{
                        categoriesData: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/categoriesData').then(function(response){
                                return response;
                            });
                        },
                        inscriptionsData: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/inscriptionData').then(function(response){
                                return response;
                            });
                        }
                    }
                })
                .when('/admin/categories', {
                    templateUrl: currentBaseUrl + 'view/admin/categories',
                    controller: 'AdminContestCategoriesEditCtrl',
                    resolve:
                    {
                        categoriesData: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/categoriesData').then(function(response){
                                return response;
                            });
                        },
                        inscriptionsData: function($http) {
                            return $http.get(rootUrl + 'api/contest/' + contest.code + '/inscriptionData/').then(function(response){
                                return response.data;
                            });
                        }
                    }
                })
                .when('/admin/import-contest', {
                    templateUrl: currentBaseUrl + 'view/admin/import-contest',
                    controller: 'AdminContestImportContestEditCtrl',
                    resolve:
                    {
                        contestIds: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/contestsIds').then(function(response){
                                return response;
                            });
                        },
                    }
                })
                .when('/admin/entries', {
                    templateUrl: currentBaseUrl + 'view/admin/entries',
                    controller: 'AdminContestEntriesEditCtrl',
                    resolve:{
                        categoriesData: function($http, $route){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/categoriesData').then(function(response){
                                return response;
                            });
                        },
                        entriesData: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/entriesData').then(function(response){
                                return response;
                            });
                        }
                    }
                })
                .when('/admin/billingsetup', {
                    templateUrl: currentBaseUrl + 'view/admin/billingsetup',
                    controller: 'AdminContestBillingSetupEditCtrl',
                    resolve:{
                        contestPaymentsMethods: function($http) {
                            return $http.get(rootUrl + 'api/contest/' + contest.code + '/paymentsData').then(function(response){
                                return response.data;
                            });
                        }
                    }
                })
                .when('/admin/style', {
                    templateUrl: currentBaseUrl + 'view/admin/style',
                    controller: 'AdminContestStyleEditCtrl',
                    resolve:{
                        contestStyle: function($http) {
                            return $http.get(rootUrl + 'api/contest/' + contest.code + '/styleData').then(function(response){
                                return response.data;
                            });
                        }
                    }
                })
                .when('/admin/inscriptions-list', {
                    templateUrl: currentBaseUrl + 'view/admin/inscriptions-list',
                    controller: 'AdminContestAllInscriptionsEditCtrl',
                })
                .when('/admin/inscription/:inscription?', {
                    templateUrl: currentBaseUrl + 'view/admin/inscription',
                    controller: 'AdminContestInscriptionCtrl',
                    resolve:{
                        inscriptionData: function($http, $route) {
                            if(angular.isDefined($route.current.params.inscription)) {
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/inscription/' + $route.current.params.inscription).then(function (response) {
                                    return response.data;
                                });
                            }else{
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/inscription/').then(function (response) {
                                    return response.data;
                                });
                            }
                        }
                    }
                })
                .when('/admin/deadlines', {
                    templateUrl: currentBaseUrl + 'view/admin/deadlines',
                    controller: 'AdminContestDeadlinesCtrl',
                })
                .when('/admin/pages', {
                    templateUrl: currentBaseUrl + 'view/admin/pages',
                    controller: 'AdminContestAllPagesCtrl',
                })
                .when('/admin/pages/page/:page?', {
                    templateUrl: currentBaseUrl + 'view/admin/page',
                    controller: 'AdminContestPageCtrl',
                    resolve:{
                        categoriesData: function($http, $route, contest){
                            return $http.get(currentBaseUrl+'categories').success(function(data){
                                if(angular.isDefined($route.current.params.id)){
                                    data.view_user_entries = $route.current.params.id;
                                }
                                return data;
                            });
                        },
                        pageData: function($http, $route) {
                            if(angular.isDefined($route.current.params.page)){
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/page/' + $route.current.params.page).then(function(response){
                                    return response.data;
                                });
                            }
                            return {};
                        }
                    }
                })
                .when('/admin/assets', {
                    templateUrl: currentBaseUrl + 'view/admin/assets',
                    controller: 'AdminContestAssetsCtrl'
                })
                .when('/admin/voting-sessions', {
                    templateUrl: currentBaseUrl + 'view/admin/voting-sessions',
                    controller: 'AdminContestVotingSessionsCtrl',
                    resolve:{
                    }
                })
                .when('/admin/voting-session/:voting?', {
                    templateUrl: currentBaseUrl + 'view/admin/voting-session',
                    controller: 'AdminContestVotingSessionEditCtrl',
                    resolve:{
                        voting: function($http, $route) {
                            if(angular.isDefined($route.current.params.voting)) {
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/voting/' + $route.current.params.voting).then(function (response) {
                                    return response.data;
                                });
                            }else{
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/voting/').then(function (response) {
                                    return response.data;
                                });
                            }
                        },
                    }
                }).
                when('/admin/billing', {
                    templateUrl: currentBaseUrl + 'view/admin/billing',
                    controller: 'AdminContestBillingCtrl',
                    resolve: {
                        categoriesData: function($http, $route){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/categoriesData').then(function(response){
                                return response;
                            });
                        },
                        adminInfo: function($http){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/adminInfo').then(function(response){
                                return response;
                            });
                        }
                    }
                }).
                when('/admin/billing/bill/:bill?', {
                    templateUrl: currentBaseUrl + 'view/admin/bill',
                    controller: 'AdminContestBillCtrl',
                    resolve:{
                        billData: function($http, $route) {
                            return $http.get(rootUrl + 'api/contest/' + contest.code + '/bill/' + $route.current.params.bill).then(function(response){
                                return response.data;
                            });
                        },
                        categoriesData: function($http, $route){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/categoriesData').then(function(response){
                                return response;
                            });
                        }
                    }
                })
                .when('/admin/mail', {
                    templateUrl: currentBaseUrl + 'view/admin/mail',
                    controller: 'AdminContestMailEditCtrl',
                    resolve:{
                        contestStyle: function($http) {
                            return $http.get(rootUrl + 'api/contest/' + contest.code + '/styleData').then(function(response){
                                return response.data;
                            });
                        }
                    }
                })
                .when('/admin/newsletters', {
                    templateUrl: currentBaseUrl + 'view/admin/newsletters',
                    controller: 'AdminContestAllNewslettersCtrl',
                })
                .when('/admin/newsletters/newsletter/:newsletter?', {
                    templateUrl: currentBaseUrl + 'view/admin/newsletter',
                    controller: 'AdminContestNewsletterCtrl',
                    resolve:{
                        newsletterData: function($http, $route) {
                            if(angular.isDefined($route.current.params.newsletter)){
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/newsletter/' + $route.current.params.newsletter).then(function(response){
                                    return response.data;
                                });
                            }
                            return {};
                        },
                        categoriesData: function($http, $route, contest){
                            return $http.get(currentBaseUrl+'categories').success(function(data){
                                if(angular.isDefined($route.current.params.id)){
                                    data.view_user_entries = $route.current.params.id;
                                }
                                return data;
                            });
                        }
                    }
                })
                .when('/admin/collections', {
                    templateUrl: currentBaseUrl + 'view/admin/collections',
                    controller: 'AdminContestAllCollectionsCtrl',
                })
                .when('/admin/collection/:code?', {
                    templateUrl: currentBaseUrl + 'view/admin/collection',
                    controller: 'AdminContestCollectionCtrl',
                    resolve:{
                        collectionData: function($http, $route) {
                            if(angular.isDefined($route.current.params.code)){
                                return $http.get(rootUrl + 'api/contest/' + contest.code + '/collection/' + $route.current.params.code).then(function(response){
                                    return response.data;
                                });
                            }
                            return {};
                        },
                        metadataFields: function($http, $route){
                            return $http.get(currentBaseUrl+'metadataFields').success(function(data){
                                return data;
                            })
                        },
                        votingSessions: function($http){
                            return $http.get(rootUrl + 'api/contest/' + contest.code + '/votingSessions').then(function(response){
                                return response.data;
                            });
                        }
                    }
                })
                .when('/admin/meta-analysis', {
                    templateUrl: currentBaseUrl + 'view/admin/meta-analysis',
                    controller: 'AdminContestMetaAnalysis',
                    resolve:
                        {
                            metadataAnalytics: function($http, $route){
                                return $http.get(currentBaseUrl+'metadataAnalytics').success(function(data){
                                    return data;
                                })
                            },
                        }
                })
            }
        ]
);