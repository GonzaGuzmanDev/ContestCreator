OxoAwards.config(['$routeProvider','rootUrl','currentBaseUrl',
        function($routeProvider,rootUrl,currentBaseUrl){
            $routeProvider.
                when('/', {
                    templateUrl: rootUrl + 'view/blank',
                    controller: 'initController'
                }).
                when('/home',{
                    templateUrl: function(){ return currentBaseUrl + 'view/home?a='+Math.random(); },
                    controller: 'homeController',
                    resolve: {
                        checkRoutes: checkRoutes,
                        adminInfo: function($http, $location, AuthService){
                            if(AuthService.isLoggedIn()){
                                return $http.get(currentBaseUrl+'adminInfo').then(function(response){
                                    return response;
                                });
                            }
                        }
                    }
                }).
                when('/signup/:role',{
                    templateUrl: currentBaseUrl + 'view/signup',
                    controller: 'signupController',
                    resolve: {
                        captchaUrl: function($http){
                            return $http.get(rootUrl+'captcha/url').success(function(data){
                                return data.data;
                            });
                        },
                        inscriptionId: function($http, $route) {
                            return $http.post(currentBaseUrl + 'inscriptionId', {role: $route.current.params.role}).success(function (data) {
                                if(!data) return data.id;
                                else return null;
                            })
                        },
                        newRecord: function() {
                            return true;
                        },
                        //checkRoutes: checkRoutes
                    }
                }).
                when('/updateInscription/:role',{
                    templateUrl: currentBaseUrl + 'view/updateInscription',
                    controller: 'signupController',
                    resolve: {
                        captchaUrl: function($http){
                            return $http.get(rootUrl+'captcha/url').success(function(data){
                                return data.data;
                            });
                        },
                        inscriptionId: function($http, $route) {
                            return $http.post(currentBaseUrl + 'inscriptionId', {role: $route.current.params.role}).success(function (data) {
                                return data.id;
                            })
                        },
                        newRecord: function() {
                            return false;
                        },
                        //checkRoutes: checkRoutes
                    }
                }).
                when('/entries/:id?',{
                    templateUrl: currentBaseUrl + 'view/entries',
                    controller: 'newEntriesController',
                    resolve: {
                        categoriesData: function($http, $route, contest){
                            return $http.get(currentBaseUrl+'categories').success(function(data){
                                if(angular.isDefined($route.current.params.id)){
                                    data.view_user_entries = $route.current.params.id;
                                }
                                return data;
                            });
                        },
                        checkRoutes: checkRoutes
                    }
                }).
            when('/tickets/:id?',{
                    templateUrl: currentBaseUrl + 'view/entries',
                    controller: 'entriesController',
                    resolve: {
                        categoriesData: function($http, $route, contest){
                            return $http.get(currentBaseUrl+'categories').success(function(data){
                                if(angular.isDefined($route.current.params.id)){
                                    data.view_user_entries = $route.current.params.id;
                                }
                                return data;
                            });
                        },
                        checkRoutes: checkRoutes
                    }
                }).
            when('/buyTickets/:id?',{
                templateUrl: currentBaseUrl + 'view/entry',
                controller: 'entryController',
                resolve: {
                    entry: function($http, $route){
                        if(angular.isDefined($route.current.params.id)){
                            return $http.get(currentBaseUrl+'entryInscriptor/'+$route.current.params.id).success(function(data){
                                return data;
                            });
                        }else{
                            return null;
                        }
                    },
                    reachedMaxEntries: function($http){
                        return $http.get(currentBaseUrl+'checkMaxEntries/').success(function(data){
                            return data !== 0;
                        });
                    },
                    metadataFields: function($http){
                        return $http.get(currentBaseUrl+'metadataFields').success(function(data){
                            return data;
                        })
                    },
                    checkRoutes: checkRoutes
                }
            }).
                when('/payments', {
                    templateUrl: currentBaseUrl + 'view/payments',
                    controller: 'paymentsController',
                    resolve: {
                        checkRoutes: checkRoutes
                    }
                }).
                when('/payment/:bill?', {
                    templateUrl: currentBaseUrl + 'view/payment',
                    controller: 'paymentController',
                    resolve:{
                        billData: function($http, $route, contest) {
                            return $http.get(currentBaseUrl + 'payment/' + $route.current.params.bill).then(function(response){
                                return response.data;
                            });
                        },
                        categoriesData: function($http, $route, contest){
                            return $http.get(rootUrl+'api/contest/'+contest.code+'/categoriesData').then(function(response){
                                return response;
                            });
                        },
                        checkRoutes: checkRoutes
                    }
                }).
                when('/voting',{
                    templateUrl: currentBaseUrl + 'view/voting',
                    controller: 'voteSessionController',
                    resolve: {
                        voteSession: function($http, contest){
                            return $http.get(currentBaseUrl+'voting').success(function(data){
                                return data;
                            });
                        },
                        checkRoutes: checkRoutes
                    }
                }).
                when('/voting/:code',{
                    templateUrl: currentBaseUrl + 'view/entries',
                    controller: 'entriesController',
                    resolve: {
                        categoriesData: function($http, contest, $route){
                            return $http.post(currentBaseUrl+'vote/'+$route.current.params.code, {}).success(function(data){
                                return data;
                            });
                        },
                        checkRoutes: checkRoutes
                    }
                }).
                when('/files',{
                    templateUrl: currentBaseUrl + 'view/files',
                    resolve: {
                        checkRoutes: checkRoutes
                    }
                }).
                when('/tech',{
                    templateUrl: currentBaseUrl + 'view/tech',
                    controller: 'techController',
                    resolve: {
                        checkRoutes: checkRoutes
                    }
                }).
                when('/entry/cat/:id',{
                    templateUrl: currentBaseUrl + 'view/entry',
                    controller: 'entryController',
                    resolve: {
                        entry: function($http, $route){
                            return $http.get(currentBaseUrl+'entryCategory/'+$route.current.params.id).success(function(data){
                                return data;
                            });
                        },
                        metadataFields: function($http){
                            return $http.get(currentBaseUrl+'metadataFields').success(function(data){
                                return data;
                            })
                        },
                        reachedMaxEntries: function($http){
                            return $http.get(currentBaseUrl+'checkMaxEntries/').success(function(data){
                                return data !== 0;
                            });
                        },
                        checkRoutes: checkRoutes
                    }
                }).
                when('/entry/:id?',{
                    templateUrl: currentBaseUrl + 'view/entry',
                    controller: 'entryController',
                    resolve: {
                        entry: function($http, $route){
                            if(angular.isDefined($route.current.params.id)){
                                return $http.get(currentBaseUrl+'entryInscriptor/'+$route.current.params.id).success(function(data){
                                    return data;
                                });
                                /*return $http.get(currentBaseUrl + 'entryInscriptor/' + $route.current.params.id).then(function (data) {
                                    return {success: true, data: data};
                                }, function () {
                                    return {success: false}
                                });*/
                            }else{
                                return null;
                            }
                        },
                        reachedMaxEntries: function($http){
                            return $http.get(currentBaseUrl+'checkMaxEntries/').success(function(data){
                                return data !== 0;
                            });
                        },
                        metadataFields: function($http){
                            return $http.get(currentBaseUrl+'metadataFields').success(function(data){
                                return data;
                            })
                        },
                        checkRoutes: checkRoutes
                    }
                }).
                when('/entry/vote/:code/:id',{
                    templateUrl: currentBaseUrl + 'view/entry',
                    controller: 'entryController',
                    resolve: {
                        entry: function($http, $route){
                            if(angular.isDefined($route.current.params.id)){
                                return $http.get(currentBaseUrl+'entryJudge/'+$route.current.params.id+'/'+$route.current.params.code).success(function(data){
                                    data.code = $route.current.params.code;
                                    return data;
                                });
                            }else{
                                return null;
                            }
                        },
                        reachedMaxEntries: function(){
                            return true;
                        },
                        metadataFields: function($http, $route){
                            return $http.get(currentBaseUrl+'judgeMetadataFields').success(function(data){
                                return data;
                            })
                        },
                        checkRoutes: checkRoutes
                    }
                }).
                when('/page/:code',{
                    templateUrl: currentBaseUrl + 'view/pages',
                    controller: 'pageController',
                    resolve: {
                        content: function($http, contest, $route, $location){
                            return $http.get(currentBaseUrl+'page/'+$route.current.params.code).success(function(data){
                                return data;
                            }).error(function(){
                                $location.path('/home');
                            });
                        },
                        code: function($route){
                            return $route.current.params.code;
                        }
                        //checkRoutes: checkRoutes
                    }
                }).
                when('/vote/anonymous/:code?',{
                    templateUrl: currentBaseUrl+'view/anonymous',
                    controller: "voteAnonymousController",
                    resolve: {
                        data: function($http, contest, $route, $location){
                            return $http.get(currentBaseUrl+'voteAnonymousData/'+$route.current.params.code).success(function(data){
                                return data;
                            }).error(function(){

                            });
                        }
                    }
                }).
                when('/collection/:code',{
                    templateUrl: currentBaseUrl + 'view/collections',
                    controller: 'collectionController',
                    resolve: {
                        collection: function($http, contest, $route, $location){
                            return $http.get(currentBaseUrl+'collection/'+$route.current.params.code).success(function(data){
                                if(data.private === true){
                                    $location.path('/collection-key/'+$route.current.params.code);
                                }
                                else return data;
                            }).error(function(){
                                $location.path('/home');
                            });
                        },
                        //checkRoutes: checkRoutes
                    }
                }).
                when('/collection/:code/:id',{
                    templateUrl: currentBaseUrl + 'view/collection',
                    controller: 'collectionEntryController',
                    resolve: {
                        entry: function($http, $route){
                            if(angular.isDefined($route.current.params.id)){
                                return $http.get(currentBaseUrl+'collectionEntry/'+ $route.current.params.code + '/' + $route.current.params.id).success(function(data){
                                    return data;
                                });
                            }else{
                                return null;
                            }
                        },
                        metadataFields: function($http, $route){
                            if(angular.isDefined($route.current.params.id)) {
                                return $http.get(currentBaseUrl + 'collectionMetadataFields/' + $route.current.params.code).success(function (data) {
                                    return data;
                                })
                            }
                        },
                    }
                }).
            when('/collection-key/:code',{
                templateUrl: currentBaseUrl + 'view/collection-key',
                controller: 'collectionKeyController',
                resolve: {
                    code: function($route){
                        return $route.current.params.code;
                    }
                }
            }).
            otherwise({redirectTo :'/home'})
        }]
);

var checkRoutes = function($location, $q, rootUrl, $window, contest, AuthService, wizardStatus, userInscriptions, contestStatus){
    /*** Loged in ***/
    var deferred = $q.defer();
    if(AuthService.isLoggedIn()){
        /** is in wizard status **/
            if(contest.wizard_status !== null && contest.wizard_status < wizardStatus.WIZARD_FINISHED){
                if(userInscriptions.GetRoleInscription(userInscriptions.Owner) || userInscriptions.superAdmin()){
                    switch(contest.wizard_status){
                        case wizardStatus.WIZARD_REGISTER_FORM: $location.path('/admin/inscriptions'); break;
                        case wizardStatus.WIZARD_ENTRY_FORM: $location.path('/admin/entries'); break;
                        case wizardStatus.WIZARD_CATEGORIES: $location.path('/admin/categories'); break;
                        case wizardStatus.WIZARD_PAYMENT_FORM: $location.path('/admin/billingsetup'); break;
                        case wizardStatus.WIZARD_STYLE: $location.path('/admin/style'); break;
                        case wizardStatus.WIZARD_DATES: $location.path('/admin/deadlines'); break;
                        case wizardStatus.WIZARD_FINISHED: $location.path('/home'); break;
                    }
                }
            else{
                $window.location.href = rootUrl;
                deferred.reject();
                return deferred.promise;
            }
        }
        /** Not in wizard status, if user is not admin, colaborator or superadmin, and the contest is not public, it cant see it **/
        if(!userInscriptions.GetRoleInscription(userInscriptions.Owner)
            && !userInscriptions.GetRoleInscription(userInscriptions.Colaborator)
            && !userInscriptions.superAdmin() && contest.status !== contestStatus.STATUS_PUBLIC){
            $window.location.href = rootUrl;
            deferred.reject();
            return deferred.promise;
        }
    }
    /*** Not logged ***/
    else{
        /** is in wizard status **/
        if(contest.wizard_status !== null && contest.wizard_status < wizardStatus.WIZARD_FINISHED){
            $window.location.href = rootUrl;
            deferred.reject();
            return deferred.promise;
        }
        /** not in wizard status **/
        else{
            if($location.path() !== "/home"){
                $location.path('/');
            }else{
                if(contest.status !== contestStatus.STATUS_PUBLIC) {
                    $window.location.href = rootUrl;
                    deferred.reject();
                    return deferred.promise;
                }
            }
        }
    }
};
var ContestControllers = angular.module('ContestControllers', ['ngRoute'])
    .controller('initController',function($location){
        $location.path('/home');
    })
    .controller('voteAnonymousController',function($scope, $http, $window, contest, currentBaseUrl, data, $timeout, Lightbox, CategoryManager){
        //$scope.entries = data.data.entries;
        $scope.voting_user = data.data.votingUser;
        $scope.voteSession = data.data.votingSession;
        $scope.childrenCategories = data.data.childrenCategories;
        $scope.entries = data.data.entries;
        $scope.totalEntries = data.data.totalEntries;
        $scope.listView = "thumbs";
        $scope.entriesRows = [];
        var loadingEntries = false;
        var lastEntryLoaded = 0;
        var entriesPerRow = 24;
        $scope.lastEntryShown = false;
        var newEntries = [];
        $scope.catMan = CategoryManager;

        /*$scope.inViewLoadMoreEntries = function(delay){
            console.log(loadingEntries);
            if(!!loadingEntries) return;
            loadingEntries = true;
            $timeout(function(){
                $scope.loadMoreEntries();
                loadingEntries = false;
            }, delay !== undefined ? delay : 1000);
        };*/

        //$scope.inViewLoadMoreEntries(10);

        $scope.setEntries = function(newEntries){
            $scope.entriesRows.push(newEntries);
            //$scope.entriesRows.push($scope.entries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.totalEntries;
            $scope.firstTime = 0;
            CategoryManager.SetCategories($scope.childrenCategories);
        }

        $scope.setEntries($scope.entries.entries);

        $scope.loadMoreEntries = function(){

            //$scope.setEntries($scope.entries);
            /*$scope.entriesRows.push($scope.entries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.entries.length;
            $scope.firstTime = 0;*/
            /*$http.get(currentBaseUrl+'page/'+code+'/'+lastEntryLoaded).success(function(data){
                $scope.setEntries(data.entries);
            });*/
        };

        $scope.openGallery = function(entry, images, index){
            if(entry.name){
                $scope.getName = function(){
                    return entry.name;
                };
            }else{
                $scope.getNoName = function(){
                    return "<span class='notitle'>Sin título</span>";
                };
            }

            $scope.getCategory = function(category_id){
                return $scope.catMan.GetCategory(category_id);
            };

            var modal = Lightbox.openModal(images, index, {size:'lg', scope: $scope, resolve:{
                field: function(){
                    return images;
                }
            }});
            modal.result.then(function () {
            }, function () {
            });
        };

        $scope.BeforeVoteUpdate = function(entry, results){
            voteResult = results;
            voteResult.error = false;
            entryBeforeVote = angular.copy(entry.votes);
        };
        $scope.VoteUpdated = function(entry, myVote){
            if(!$scope.voteSession) return;
            $http.post(currentBaseUrl+'entryCategoryVotePublic',{id: $scope.voteSession.code, vote: myVote, entryId: entry.id, votingUserPublic: $scope.voting_user.inscription_id}).success(function(data){
                voteResult.error = false;
                //TODO Chequear esto, no debería ser entry.votes??? pero data.votes devuelve otra cosa?
                entry.vote = data.votes;
                $scope.votingUser = data.votingUser;
                /*$scope.countVoteEntries(0);
                $scope.countVoteEntries(1);
                $scope.countVoteEntries(2);
                if($scope.contest.id == 159) filterYesNoVotes(data.entries, data.catId, data.vote);*/
            }).error(function(){
                voteResult.error = true;
                $scope.firstTime = 0;
                entry.votes = entryBeforeVote;
                /*$scope.countVoteEntries(0);
                $scope.countVoteEntries(1);
                $scope.countVoteEntries(2);*/
            });
        };
    })
    .controller('homeController',function($scope, $http, $window, userInscriptions, adminInfo, contest, currentBaseUrl){
        if(adminInfo){
            $scope.role = userInscriptions.Inscriptor;
            $scope.data = adminInfo.data;
            $scope.allRoles = $scope.data.allRoles;
            $scope.inscriptions = $scope.data.inscriptions;
            $scope.entries = $scope.data.entries;
            $scope.incomplete = $scope.complete = $scope.finalize = $scope.approved = $scope.error = 0;
            $scope.billingIncomplete = $scope.billingComplete = $scope.billingError = $scope.billingProcessing = 0;
            $scope.contest = contest;
            $scope.tickets = $scope.data.tickets;
            $scope.files = $scope.data.files;
            $scope.billing = $scope.data.billing;
            angular.forEach($scope.entries, function(item){
                if(item.status == 0)$scope.incomplete = item.total;
                if(item.status == 1)$scope.complete = item.total;
                if(item.status == 2)$scope.finalize = item.total;
                if(item.status == 3)$scope.approved = item.total;
                if(item.status == 4)$scope.error = item.total;
            });

            angular.forEach($scope.billing, function(item){
                if(item.status == 0)$scope.billingIncomplete = item.total;
                if(item.status == 1)$scope.billingComplete = item.total;
                if(item.status == 2)$scope.billingError = item.total;
                if(item.status == 5)$scope.billingProcessing = item.total;
            });

            $scope.selected = function(value){
                userInscriptions.selected = value;
            };

            $scope.filterBilling = function(value){
                userInscriptions.billingSelected = value;
            };

            $scope.countAllEntries = function(){
                var total = parseInt($scope.incomplete, 10) + parseInt($scope.complete, 10) + parseInt($scope.finalize, 10) + parseInt($scope.approved, 10) + parseInt($scope.error, 10);
                return total;
            };

            $scope.countAllBillings = function(){
                var total = parseInt($scope.billingIncomplete, 10) + parseInt($scope.billingComplete, 10) + parseInt($scope.billingError, 10) + parseInt($scope.billingProcessing, 10);
                return total;
            };

            $scope.contestRequest = function(admin){
                $http.post(currentBaseUrl + 'contestStatusRequest').success(function (data) {
                    $window.location.reload();
                });
            }
        }
    })
    .controller('signupController',function($scope,currentBaseUrl,$rootScope,$location,$filter,$route,$sce,$window,$routeParams,$http,$uibModal,$timeout,AuthService, Flash, contest, userInscriptions, metadataFieldsConfig, inscriptionId, captchaUrl){
        $scope.captchaUrl = captchaUrl.data;
        $scope.myinscription = userInscriptions.GetRoleInscription($routeParams.role);
        $scope.userInscriptions = userInscriptions;
        $scope.contest = contest;
        $scope.inscriptionType = null;
        $scope.formData = {};
        $scope.metadata = [];
        $scope.inscriptionTypesRole = [];
        $scope.role = $routeParams.role;
        $scope.newRecord = true;
        $scope.Date = new Date;
        $scope.Date = $scope.Date.toISOString().slice(0,19).replace(/T/g," ");
        $scope.showStatic = false;

        $scope.isobject = function(value){
            return angular.isObject(value);
        };

        if($scope.role != 1 && $scope.role != 3){
            $location.path('/home');
            return;
        }

        if(inscriptionId.data){
            $scope.newRecord = false;
        }

        $scope.contest.inscription_metadata_fields.forEach(function(metadataValue){
            if(metadataValue.role == $scope.role){
               $scope.metadata.push(metadataValue);
           }
        });

        var allData = $scope.metadata;

        $scope.contest.inscription_types.forEach(function(types){
           if(types.role == $scope.role){
               $scope.inscriptionTypesRole.push(types);
           }
        });

        $scope.firstTabIndex = -1;
        $scope.getFirstTabIndex = function(){
            var f = $filter('filter')($scope.metadata, {role:$scope.role});
            for(var i=0; i< f.length; i++){
                if(f[i].type == metadataFieldsConfig.Type.TAB){
                    $scope.firstTabIndex = i;
                    return;
                }
            }
            $scope.firstTabIndex = -1;
        };
        $scope.isTab = function(field){
            return field.type == metadataFieldsConfig.Type.TAB;
        };
        $scope.getTabs = function(){
            return $filter('filter')($scope.metadata, {role: $scope.role, type: metadataFieldsConfig.Type.TAB});
        };
        $scope.getTabIndex = function(tab){
            var f = $filter('filter')($scope.metadata, {role:$scope.role});
            for(var i=0; i< f.length; i++){
                if(f[i] == tab){
                    return i;
                }
            }
            return 0;
        };
        $scope.getNextTabIndex = function(tab){
            var f = $filter('filter')($scope.metadata, {role:$scope.role});
            var from = $scope.getTabIndex(tab) + 1;
            for(var i = from; i < f.length; i++){
                if(f[i].type == metadataFieldsConfig.Type.TAB){
                    return i;
                }
            }
            return i;
        };
        $scope.getTabMetadata = function(tab){
            var f = $filter('filter')($scope.metadata, {role:$scope.role});
            var from = $scope.getTabIndex(tab) + 1;
            var to = $scope.getNextTabIndex(tab) - 1;
            var l = [];
            for(var i = from; i <= to; i++){
                l.push(f[i]);
            }
            return l;
        };
        $scope.getPreTabMetadata = function(){
            var f = $filter('filter')($scope.metadata, {role:$scope.role});
            var from = 0;
            var l = [];
            for(var i = from; i < $scope.firstTabIndex; i++){
                l.push(f[i]);
            }
            return l;
        };
        $scope.getFirstTabIndex();

        $scope.setInscriptionType = function(type){
            $scope.inscriptionType = type;
            $scope.chooseType = $scope.inscriptionType == null;
            $scope.filteredMetadata = [];
            if (!type){
                $scope.metadata = allData;
                return;
            }
            for (var i = 0, l = $scope.metadata.length; i < l; i++) {
                if($scope.metadata[i].inscription_metadata_config_types[type.id] != null){
                    if($scope.metadata[i].inscription_metadata_config_types[type.id].visible == 1) {
                        if($scope.metadata[i].inscription_metadata_config_types[type.id].required == 1){
                            $scope.metadata[i].required = 1;
                        }
                        $scope.filteredMetadata.push($scope.metadata[i]);
                    }
                }
            }
            if($scope.filteredMetadata.length != 0) $scope.metadata = $scope.filteredMetadata;
            $scope.getFirstTabIndex();
        };

        if($scope.inscriptionTypesRole.length > 1 && $scope.newRecord == true){
            $scope.chooseType = true;
        }else if($scope.contest.inscription_types.length == 1){
            $scope.setInscriptionType($scope.contest.inscription_types[0]);
        }

        $scope.isFieldRequired = function(field){
            return !!field.required;
        };

        if($scope.newRecord === false) {
            $filterByInscription = false;
            $scope.contest.inscription_types.forEach(function(val){
                if(val.id == $scope.myinscription.inscription_type_id) $scope.inscriptionType = val;
            });
            $scope.filteredMetadata = [];
            $id = inscriptionId.data['inscription_type'];
            for (var i = 0, l = $scope.metadata.length; i < l; i++) {
                if($scope.myinscription.inscription_type_id != null){
                    if(!$scope.metadata[i].inscription_metadata_config_types[$scope.myinscription.inscription_type_id]){
                        continue;
                    }
                    else {
                        if($scope.metadata[i].inscription_metadata_config_types[$scope.myinscription.inscription_type_id].visible != 1){
                            continue;
                        }
                    }
                }
                $values = [];
                $scope.myinscription.inscription_metadatas.forEach(function(value){
                        if(value.inscription_metadata_field_id == $scope.metadata[i].id){
                            if(isFinite(value.value)) $values.push(parseInt(value.value));
                            else $values.push(value.value);
                        }
                    }
                );
                $scope.metadata[i].value = $values;
                if($scope.metadata[i].inscription_metadata_config_types[$id] != null){
                    if($scope.metadata[i].inscription_metadata_config_types[$id].visible == 1){
                        $filterByInscription = true;
                        if($scope.metadata[i].inscription_metadata_config_types[$id].required == 1){
                            $scope.metadata[i].required = 1;
                        }
                        $scope.formData[$scope.metadata[i].id] = $scope.metadata[i].value;
                        $scope.filteredMetadata.push($scope.metadata[i]);
                    }
                }
                else{
                    $scope.formData[$scope.metadata[i].id] = $scope.metadata[i].value;
                    $scope.filteredMetadata.push($scope.metadata[i]);
                }
            }
            if($scope.filteredMetadata.length != 0) $scope.metadata = $scope.filteredMetadata;
        }

        $scope.inscription = {};
        $scope.emptyObject = function(object){
            for (var key in object) {
                if (hasOwnProperty.call(object, key)) return false;
            }
            return true;
        };

        $scope.send = function(update){
            $scope.sending = true;
            $scope.errors = null;

            if ($scope.inscriptionType != null) {
                $scope.formData.inscriptionType = $scope.inscriptionType.id;
            }
            $scope.formData.role = $scope.role;
            $scope.formData.newRecord = $scope.newRecord;

            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http({
                method: 'POST',
                url: currentBaseUrl+'signup',
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                data: $scope.formData,
                transformRequest: function (data, headersGetter) {
                    var formData = new FormData();
                    angular.forEach(data, function (value, key) {
                        formData.append(key, value);
                    });

                    var headers = headersGetter();
                    delete headers['Content-Type'];

                    return formData;
                }
            }).success(function(data){
                $scope.sending = false;
                if(data.errors){
                    $scope.errors = data.errors;
                    /*angular.forEach($scope.errors, function(errorArr){
                        angular.forEach(errorArr, function(error, errIdx){
                            errorArr[errIdx] = $sce.trustAsHtml(error);
                        });
                    });*/
                    if(angular.isDefined(data.captchaUrl)){
                        $scope.captchaUrl = data.captchaUrl;
                    }
                    Flash.clear($scope);
                }else{
                    userInscriptions.SetRoleInscription($scope.role, data.inscription);
                    if($scope.newRecord === true){
                        AuthService.login(data.user);
                        $window.location.href = "#/entries";
                        $window.location.reload();
                        $scope.sent = true;
                    }
                    $scope.newRecord = false;
                    Flash.show(data.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };
        $scope.delete = function() {
            $uibModal.open({
                templateUrl: currentBaseUrl+'view/deleteInscription',
                controller: 'inscriptionDeleteCtrl',
                resolve: {
                    role: function(){
                        return $scope.role;
                    }
                }
            });
        };

        $scope.viewTerms = function(){
            Flash.clear();
            $uibModal.open({
                templateUrl: 'terms.html',
                controller: 'TermsCtrl',
                size: 'lg'
            });
        };

        $scope.terms = function() {
            $uibModal.open({
                templateUrl: currentBaseUrl + 'view/terms',
                controller: 'TermsCtrl'
            });
        };
    })
    .controller('inscriptionDeleteCtrl',function($scope,$timeout,$window,$http,$uibModalInstance,Flash,currentBaseUrl,userInscriptions, role){
        $scope.inscription = userInscriptions.GetRoleInscription(role);
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            $http.post(currentBaseUrl + 'deleteInscription', {role:$scope.inscription.role}).success(function (data) {
                if(data.errors){
                    $scope.errors = data.errors;
                }else {
                    $uibModalInstance.close();
                    userInscriptions.RemoveInscription(role);
                    $window.location.href = '#/home';
                }
            }).error(function(data){

            });
        };
    })
    .controller('voteSessionController',function($scope,$rootScope,currentBaseUrl,$location,$http,$uibModal,$timeout,$window,$filter,AuthService, Flash, userInscriptions, contest, voteSession){
        $scope.votingSessions = voteSession.data.votingSessions;
        $scope.Date = new Date;
        $scope.Date = $scope.Date.toISOString().slice(0,19).replace(/T/g," ");
        $scope.hasVoteSessions = false;

        $scope.userVerified = $rootScope.currentUser.verified;

        if($scope.votingSessions.length > 0){
            angular.forEach($scope.votingSessions, function(voteSession){
                if((voteSession.start_at < $scope.Date && (voteSession.finish_at > $scope.Date || voteSession.finish_at2 > $scope.Date)) || voteSession.start_at > $scope.Date){
                    $scope.hasVoteSessions = true;
                }
            });
        }

        $scope.showAbstains = function(voteSession){
            if(voteSession.userAutoAbstains){
                if(Object.keys(voteSession.autoAbstains).length && voteSession.userAutoAbstains.length === 0){
                    return true;
                }
            }
            return false;
        };

        $scope.autoAbstainsModal = function(voting_session){
            var autoAbstainsModal = $uibModal.open({
                templateUrl: 'autoAbstainsModal.html',
                controller: 'autoAbstainsModal',
                resolve: {
                    voting_session: function () {
                        return voting_session;
                    }
                }
            });
            autoAbstainsModal.result.then(function (result) {
                if(result == true){
                    $scope.selectAutoAbstains = true;
                    $window.location.href = '#/voting/' + voting_session.code;
                }
            });

            $scope.close = function(){
                $uibModalInstance.close();
            };
        };
    })
    .controller('autoAbstainsModal', function($scope, rootUrl, $filter, $rootScope, currentBaseUrl, $timeout, $anchorScroll, $sanitize, $location, $http, Flash, $uibModalInstance, voting_session){
        $scope.fields = voting_session.autoAbstains;
        var selectedData = {judge: voting_session.judge.id, votingSessionId: voting_session.id};

        $scope.selected = [];

        $scope.assignSelected = function(data){
            /*$scope.selected = data;
            console.log($scope.selected);*/
        };

        $http.post(currentBaseUrl + 'selectedAutoAbstain', { data: selectedData}).success(function (data) {
            $scope.assignSelected(data);
        });

        $scope.close = function(){
            $uibModalInstance.close();
        };

        var data = {
            judge : voting_session.judge.id,
            inscription_id: voting_session.judge.inscription_id,
            voteSessionCode: voting_session.code,
            voting_session_id : voting_session.id,
            entry_metadata_fields : $scope.selected,
            config : voting_session.config
        };

        $scope.accept = function(){
            $http.post(currentBaseUrl + 'userAutoAbstain', {data:data}).success(function (data) {
                $uibModalInstance.close(true);
            })
        }
    })
    .controller('newEntriesController',function($scope,currentBaseUrl,$location,$rootScope,$http,$uibModal,$timeout,$sce,
                                             $filter,$route,AuthService,EntryStatus,Flash,Lightbox,
                                             CategoryManager,userInscriptions,contest,categoriesData,SelectedCategories) {
        $scope.catMan = CategoryManager;
        var view_user_entries_id = null;
        contest.categories = categoriesData.data.categories;
        contest.children_categories = categoriesData.data.children_categories;
        $scope.inscriptionData = categoriesData.data.inscription;
        $scope.entriesPerUser = categoriesData.data.entryPerUser;
        $scope.voteSession = categoriesData.data.votingSession;
        $scope.totalUnread = categoriesData.data.totalMsgs;
        $scope.totalCheck = categoriesData.data.totalCheck;
        $scope.inscription = userInscriptions.GetRoleInscription(categoriesData.data.role);
        view_user_entries_id = categoriesData.data.view_user_entries ? categoriesData.data.view_user_entries : null;
        $scope.loggedUser = null;
        $scope.entriesLoading = false;

        if(view_user_entries_id != null && $scope.entriesPerUser){
            angular.forEach($scope.entriesPerUser, function(item){
                if(view_user_entries_id == item.id){
                    $scope.loggedUser = item;
                }
            });
        }

        $scope.totalEntryCategory = categoriesData.data.finalTotal;
        $scope.totalEntries = categoriesData.data.finalTotal;
        $scope.statusEntryCategory = categoriesData.data.totalEntriesCategory;

        $scope.statusEntryBillings = categoriesData.data.totalBillings;

        $scope.showFilters = false;
        $scope.clearView = false;
        $scope.winners = false;

        $scope.selCats = SelectedCategories;
        $scope.eStatus = EntryStatus;

        $scope.contest = contest;

        $scope.discountValue = 0;

        $scope.inscriptionType = $scope.inscriptionData ? $scope.inscriptionData.inscription_type : null;

        $scope.categoriesList = $scope.contest.categories;
        $scope.listView = "list";
        $scope.showGrouped = false;
        $scope.entryPerUser = false;

        $scope.showStatic = true;

        // number of entries in each post //
        var entriesPerRow = 15;
        var catEntriesPerRow = 15;
        // Number of entries loaded //
        var lastEntryLoaded = 0;
        // entries object, prepared for scroll pagination //
        $scope.entriesRows = [];

        $scope.lastEntryShown = false;
        $scope.TotalMsgsIds = [];
        $scope.unreadIds = false;

        $scope.pagination = {
            query:'',
            queryAdmin:'',
            filterMetadata:[],
            sortBy: 'name',
            sortInverted:false,
            statusFilters: userInscriptions.selected ? [userInscriptions.selected] : [],
            billingFilters: [],
            messageFilters: false,
            checkFilters: false
        };

        //*****************************************************************************************
        //************************ Entries loader ************************************************
        //*****************************************************************************************
        $scope.inViewLoadMoreEntries = function(delay){
            $scope.loadMoreEntries();
        };

        $scope.loadMoreEntries = function(cat){
            var thisLastEntryLoaded = lastEntryLoaded;
            if(lastEntryLoaded > $scope.totalEntries) return;
            if(cat){
                if(cat.lastEntryLoaded > cat.totalEntries) return;
                thisLastEntryLoaded = cat.lastEntryLoaded;
                cat.loading = true;
            }
            var postData = {
                user_id: view_user_entries_id,
                inscription: $scope.inscription,
                voteSession: $scope.voteSession,
                lastEntryLoaded: thisLastEntryLoaded,
                entriesPerRow: entriesPerRow,
                filters: $scope.pagination,
                loggedUserByAdmin: $scope.loggedUser ? $scope.loggedUser.id : null,
                category: cat ? cat.id : null
            };

            if($scope.showAllEntries){
                postData.showAllEntries = true;
            };
            $scope.entriesLoading = true;

            $http.post(currentBaseUrl + 'entries', postData).success(function (data) {
                if(cat){
                    setCatEntries(data, cat);
                }
                else{
                    setEntries(data);
                }
            });
        };

        $scope.loadMoreCatEntries = function(cat){
            if(cat.lastEntryLoaded > cat.totalEntries) return;
            var postData = {
                user_id: view_user_entries_id,
                inscription: $scope.inscription,
                voteSession: $scope.voteSession,
                lastEntryLoaded: cat.lastEntryLoaded,
                entriesPerRow: catEntriesPerRow,
                filters: $scope.pagination,
                category: cat.id
            };

            if($scope.showAllEntries){
                postData.showAllEntries = true;
            }
            $http.post(currentBaseUrl + 'entries', postData).success(function (data) {
                setCatEntries(data, cat);
            });
        };

        var setEntries = function(data){
            $scope.entries = data.entries;
            $scope.totalEntries = data.totalEntries;
            data.entriesIds != null ? $scope.entriesIds = data.entriesIds : $scope.entriesIds;
            data.metadataFields != null ? $scope.metadataFields = data.metadataFields : $scope.metadataFields;
            $scope.totalMsgs = data.totalMsgs;
            if((data.totalEntryCategory || data.statusEntryCategory || data.billingEntryCategory) && lastEntryLoaded == 0){
                if($scope.pagination.billingFilters.length > 0 || $scope.pagination.filterMetadata.length > 0 || (data.totalEntryCategory && $scope.pagination.query.length > 0)) $scope.statusEntryCategory = data.statusEntryCategory;
                else $scope.statusEntryCategory = categoriesData.data.totalEntriesCategory;
                if($scope.pagination.statusFilters.length > 0 || $scope.pagination.filterMetadata.length > 0 || (data.totalEntryCategory && $scope.pagination.query.length > 0)) $scope.statusEntryBillings = data.billingEntryCategory;
                else $scope.statusEntryBillings = categoriesData.data.totalBillings;
                $scope.totalEntryCategory = data.totalEntryCategory;
            }
            else if(lastEntryLoaded == 0){
                $scope.totalEntryCategory = categoriesData.data.finalTotal;
                $scope.statusEntryCategory = categoriesData.data.totalEntriesCategory;
                $scope.statusEntryBillings = categoriesData.data.totalBillings;
            }

            if(!$scope.entries) return;

            $scope.categories = data.children_categories;
            CategoryManager.SetCategories($scope.categories);
            $scope.entriesRows.push($scope.entries);
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.totalEntries;
            $scope.entriesLoading = false;
        };

        // renew entries by post if the search bar is used
        $scope.$watch('pagination.query', function(){
            if($scope.entriesRows.length == 0) return;
            $scope.pagination.queryAdmin = '';
            //$scope.pagination.filterMetadata = [];
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
        });

        $scope.$watch('pagination.queryAdmin', function(){
            if($scope.entriesRows.length == 0 || $scope.pagination.queryAdmin.length > 0) return;
            $scope.pagination.queryAdmin = '';
            $scope.pagination.query = '';
            //$scope.pagination.filterMetadata = [];
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
        });

        $scope.searchInput = function(){
            /*console.log($scope.entriesRows.length);
            if($scope.entriesRows.length == 0) return;*/
            var cut = $scope.pagination.queryAdmin.indexOf(":");
            if(cut !== -1) {
                var shownVal = document.getElementById("adminSearch").value;
                var index = shownVal.split(':');
                var metadataFieldId = document.querySelector("#metadataFieldsAutoComplete option[value='" + index[0] + ":']").dataset.id;
                var value = shownVal.substring(cut+1, shownVal.length);
                var searchMetadata = {'id': metadataFieldId, 'value' : value, 'label': shownVal};
                $scope.pagination.filterMetadata.push(searchMetadata);
                $scope.pagination.queryAdmin = '';
            }
            else{
                $scope.pagination.query = $scope.pagination.queryAdmin;
            }
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
        };

        $scope.unselectFilterMetadata = function(index){
            $scope.pagination.filterMetadata.splice(index, 1);
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
        };


        $scope.toggleEntryPerUser = function(){
            $scope.entryPerUser = !$scope.entryPerUser;
        };

        $scope.toggleListView = function(){
            $scope.listView = $scope.listView == "list" ? "thumbs" : "list";
        };

        $scope.countEntries = function(status){
            if($scope.statusEntryCategory)
                return $scope.statusEntryCategory[status] ? $scope.statusEntryCategory[status] : 0;
            else return 0;
        };

        //*****************************************************************************************
        //************************ Categories view ************************************************
        //*****************************************************************************************

        $scope.toggleListGrouped = function(){
            $scope.entryPerUser = false;
            $scope.showGrouped = !$scope.showGrouped;
            lastEntryLoaded = 0;
            $scope.entriesLoading = false;
            $scope.entriesRows = [];
            $scope.lastEntryShown = false;
            toggleAll($scope.categories, false);
        };

        $scope.toggleCat = function(cat, v, childs){
            cat.loading = false;
            cat.lastEntryShown = false;
            cat.open = v == null ? !cat.open : !!v;
            /* Guardo las categorias abiertas */
            $scope.selCats.setData(cat.id);
            if(!!childs && angular.isDefined(cat.children_categories)){
                toggleAll(cat.children_categories, cat.open);
            }
        };

        function toggleAll(cats, open){
            for(var c in cats){
                $scope.toggleCat(cats[c], !!open, true);
            }
        }

        $scope.expandAll = function(){
            toggleAll($scope.categories, true);
        };

        $scope.collapseAll = function(){
            toggleAll($scope.categories, false);
        };

        $scope.inViewLoadMoreCatEntries = function(cat){
            if(!!cat.loading) return;
            $scope.loadMoreEntries(cat);
        };

        var setCatEntries = function(data, cat){
            cat.entries = data.entries;
            cat.totalEntries = data.totalEntries;
            $scope.entriesIds = data.entriesIds;
            if(!cat.entries) return;

            if((data.totalEntryCategory || data.statusEntryCategory || data.billingEntryCategory) && cat.lastEntryLoaded == 0){
                $scope.totalEntryCategory = data.totalEntryCategory;
                $scope.statusEntryCategory = data.statusEntryCategory;
                $scope.statusEntryBillings = data.billingEntryCategory;
            }
            else if(!data.totalEntryCategory && !data.statusEntryCategory && !data.billingEntryCategory){
                $scope.totalEntryCategory = categoriesData.data.finalTotal;
                $scope.statusEntryCategory = categoriesData.data.totalEntriesCategory;
                $scope.statusEntryBillings = categoriesData.data.totalBillings;
            }

            lastEntryLoaded = 1;
            
            cat.entries.forEach(function(entry){
                if(!entry.entry_log) return;
                if(entry.entry_log.length > 0){
                    entry.entry_log.forEach(function(msg){
                        if(msg.read_by.search('"'+$rootScope.currentUser.id.toString()+'"') === -1){
                            $scope.TotalMsgsIds.push(entry.id);
                            $scope.totalUnread ++;
                        }else{
                            $scope.TotalMsgsIds.push(entry.id);
                        }
                    })
                }
            });
            cat.entriesRows.push(cat.entries);
            cat.lastEntryLoaded += catEntriesPerRow;
            cat.lastEntryShown = cat.lastEntryLoaded > cat.totalEntries;
            cat.loading = false;
        };

        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.catEntries = function(category){
            return $scope.filteredEntries.filter(function (entry) {
                return entry.categories_id.indexOf(category.id) !== -1;
            });
        };

        //****************************************************************************************
        //****************** Entries bulk actions ***********************************************
        //****************************************************************************************
        function findEntry(bulkEntries, entry_id) {
            for (var i = 0; i < bulkEntries.length; i++) {
                if (bulkEntries[i]['id'] === entry_id) {
                    return i;
                }
            }
            return null;
        };

        $scope.bulkEntries = [];

        $scope.addEntriesToBulk = function (entry) {
            var selected = {'id' : entry.id,
                'name' : entry.name,
                'status' : entry.status,
                'entry_categories': entry.entry_categories
            };

            if(contest.type == 1) return;
            if($filter('filter')($scope.bulkEntries, {'id':entry.id}).length > 0) {
                $scope.bulkEntries.splice(findEntry($scope.bulkEntries, entry.id), 1);
            }
            else $scope.bulkEntries.push(selected);
        };

        $scope.isSelected = function(entry){
            return $filter('filter')($scope.bulkEntries, {'id':entry.id}).length > 0;
        };

        $scope.selectAll = function(){
            $scope.bulkEntries = [];
            angular.forEach($scope.entriesIds, function(entry){
                $scope.bulkEntries.push(entry);
            });
        };

        $scope.deselectAll = function(){
            $scope.bulkEntries = [];
        };

        $scope.entriesBulkActions = function(entries, status){
            if(entries == 0) return;
            if(!status) $scope.changeStatus(entries);
            else $scope.changeStatus(entries, status);
        };


        //*****************************************************************************************

        $scope.userInscriptions = userInscriptions;
        $scope.inTimeForInscriptions = function(){
            return !!userInscriptions.GetInscriptionDeadlines($scope.contest.id, true);
        };

        $scope.reachedMaxEntries = function(){
            if(!$scope.entries) return;
            return $scope.contest.max_entries > 0 ? $scope.entries.length >= $scope.contest.max_entries : false;
        };

        $scope.getHeight = function (divider){
            return 100/divider;
        };

        $scope.setClearView = function(){
            $scope.clearView = !$scope.clearView;
        };

        if(!navigator.userAgent.match(/Android/i) && !navigator.userAgent.match(/webOS/i)
            && !navigator.userAgent.match(/iPhone/i) && !navigator.userAgent.match(/iPad/i)
            && !navigator.userAgent.match(/iPod/i) && !navigator.userAgent.match(/BlackBerry/i)
            && !navigator.userAgent.match(/Windows Phone/i)){
            var $window = $(window);
            var $stickyEl = $('#the-sticky-div');
            if($stickyEl.length == 0) return;
            var elTop = $stickyEl.offset().top;
            $window.scroll(function() {
                $stickyEl.toggleClass('sticky', $window.scrollTop() > elTop);
            });
        }

        $scope.signupEndTimeAgo = $filter('amSubtract')(userInscriptions.GetInscriptionDeadlines($scope.contest.id), userInscriptions.ServerOffset(), 'seconds');


        $scope.statusFilters = userInscriptions.selected ? [userInscriptions.selected] : [];
        $scope.toggleFilterBy = function(status){
            if($scope.entriesLoading) return;
            $scope.bulkEntries = [];
            if(status != null) {
                var index = $scope.statusFilters.indexOf(status);
                if (index != -1) {
                    $scope.statusFilters.splice(index, 1);
                }
                else $scope.statusFilters.push(status);
            }else{
                $scope.statusFilters = [];
            }
            $scope.pagination.statusFilters = $scope.statusFilters;
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
            $timeout(function(){
            }, 100);
        };

        $scope.openEntryInList = function(entry){
            /*userInscriptions.selectedCat = catid;
            if(catid == null){
                userInscriptions.entriesIds = $scope.entriesIds;
            }*/

            userInscriptions.entriesIds = $scope.entriesIds;
            var path = currentBaseUrl + "#/entry/" + entry.id;
            window.open(path, '_blank');
            window.focus();
            /*if(angular.element(event.currentTarget).attr('href')){
                var href_path = angular.element(event.currentTarget).attr('href').substring(1);
                $location.path(href_path);
            }
            else{
                var path = currentBaseUrl + "#/entry/" + entry.id;
                window.open(path, '_blank');
            }*/
        };

        $scope.toggleUser = function(user, v){
            user.open = v == null ? !user.open : !!v;
            if(!user.loaded && !user.loading){
                user.loading = true;
                $http.post(currentBaseUrl+'userEntries', {user: user.id}).success(function(data){
                    user.entries = data.entries;
                    user.loaded = true;
                    user.loading = false;
                });
            }
        };

        /*$scope.expandAllUserEntries = function(){
            toggleAllUsers($scope.entriesPerUser, true);
        };
        $scope.collapseAllUserEntries = function(){
            toggleAllUsers($scope.entriesPerUser, false);
        };

        function toggleAllUsers(users, open){
            for(var c in users){
                $scope.toggleUser(users[c], !!open, true);
            }
        }*/

        //*************************************************************************
        //************************* Messages Section *****************************
        //*************************************************************************

        $scope.filterByUnread = function(){
            $scope.pagination.messageFilters = !$scope.pagination.messageFilters;
            $scope.unreadIds = !$scope.unreadIds;
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
            $timeout(function(){
            }, 100);
        };

        $scope.unreadMessages = function(entryLog){
            $scope.result = 0;
            if(!entryLog) return;
            if(entryLog.length > 0){
                angular.forEach(entryLog, function(log){
                    if(log.read_by != null){
                        if(log.read_by.search('"'+$rootScope.currentUser.id.toString()+'"') == -1){
                            $scope.result++;
                        }
                    }
                    else{
                        $scope.result++;
                    }
                })
            }else{
                $scope.result = -1;
            }
            return $scope.result;
        };


        //*************************************************************************
        //************************* Finalized check Section ***********************
        //*************************************************************************
        $scope.filterByCheck = function(){
            $scope.pagination.checkFilters = !$scope.pagination.checkFilters;
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            //post to filter by check
            $scope.loadMoreEntries();
        };


        //*************************************************************************
        //****************** Play media *******************************************
        //*************************************************************************

        $scope.openVideo = function(fileVersion, thumb) {
            $scope.mediaMobile = "";
            /*if(fileVersion[0].extension == "jpg" || fileVersion[0].extension == "png"){
                window.location.href = fileVersion[0].url;
            }else{*/
            var videoModal = $uibModal.open({
                templateUrl: 'playVideo.html',
                controller: 'playVideoMobileController',
                resolve: {
                    fileVersion: function () {
                        return fileVersion[0].url;
                    },
                    fileType: function () {
                        return fileVersion[0].extension;
                    },
                    thumb: function() {
                        return thumb;
                    }
                }
            });
            videoModal.result.then(function (result) {
            });
            //}
        };

        $scope.openVideoMobile = function(fileVersion){
            /*var video = document.getElementById('video');
            video.play();*/
            window.location.href = fileVersion[0].url;
        };

        $scope.openGallery = function(entry, images, index){
            if(entry.name){
                $scope.getName = function(){
                    return entry.name;
                };
            }else{
                $scope.getNoName = function(){
                    return "<span class='notitle'>Sin título</span>";
                };
            }
            var modal = Lightbox.openModal(images, index, {size:'lg', scope: $scope, resolve:{
                field: function(){
                    return images;
                }
            }});
            modal.result.then(function () {
            }, function () {
            });
        };
        $scope.showLog = function(entry){
            var logModal = $uibModal.open({
                templateUrl: 'entryLog.html',
                controller: 'entryLogController',
                resolve: {
                    entry: function() {
                        return entry;
                    },
                    fields: function () {
                        return $scope.entry_metadata_fields;
                    },
                    entryLog: function(currentBaseUrl){
                        return $http.post(currentBaseUrl+'entryLog',{id: entry.id}).success(function(data){
                            return data.entryLog;
                        });
                    }
                }
            });
            logModal.result.then(function(){
                angular.forEach(entry.entry_log, function(log){
                    log.read_by = '"'+$rootScope.currentUser.id.toString()+'"';
                });
            });
        };

        $scope.showForm = function(user) {
            var modalInstance = $uibModal.open({
                keyboard: true,
                templateUrl: currentBaseUrl + 'view/inscription-form',
                controller: 'showInscriptionFormCtrl',
                resolve: {
                    fields: function () {
                        return contest.inscription_metadata_fields;
                    },
                    inscription: function ($http) {
                        return $http.post(currentBaseUrl + 'inscriptionForm', {user_id: user.id}).then(function (response) {
                            return response.data;
                        });
                    },
                    user: function(){
                        return user;
                    }
                },
                scope: $scope
            });
        };


        serialize = function(obj) {
            var str = [];
            for(var p in obj)
                if (obj.hasOwnProperty(p)) {
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                }
            return str.join("&");
        };

        angular.element(document).on('click', '.searchBox', function (e) {
            e.stopPropagation();
        });

        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.catEntries = function(category){
            return $scope.entries.filter(function (entry) {
                return entry.categories_id.indexOf(category.id) !== -1;
            });
        };

        /**************************************************************************/
        $scope.getIncompleteFields = function(fields){
            var returnFields = "<ul style='margin:0px; padding: 15px; text-align: left;'>";
            angular.forEach(fields, function(field){
                returnFields += "<li style='margin:0px;'>"+field+"</li>";
            })
            return $sce.trustAsHtml(returnFields+"</ul>");
        }

        /****************Change Entry Status and Payments ***********************/
        //************** if status is null, is used to only pay ******************
        //************** if status = finalize, checks if it needs to be pay ******
        $scope.changeStatus = function(entry, status){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: status != null ? 'entrySifter.html' : currentBaseUrl+'view/payEntry',
                controller: 'changeEntryStatusCtrl',
                resolve: {
                    data: function ($http) {
                        if(!entry.length){
                            $scope.aux = [];
                            $scope.aux.push(entry);
                            entry = $scope.aux;
                        }
                        var data = {
                            entries: entry,
                            status: status,
                            onlyPay: status == null ? true : false
                        };
                        return $http.post(currentBaseUrl + 'filterChangeEntryStatus', data).then(function (response) {
                            return response.data;
                        });
                    },
                    status: function () {
                        status === null ? status = entry.status : status;
                        return status;
                    },
                    categories: function(){
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                if($scope.pagination.billingFilters.length == 0){
                    result.totalEntriesCategory ? categoriesData.data.totalEntriesCategory = result.totalEntriesCategory : categoriesData.data.totalEntriesCategory;
                    $scope.statusEntryCategory = categoriesData.data.totalEntriesCategory;
                }
                if($scope.pagination.statusFilters.length == 0){
                    result.totalBillings ? categoriesData.data.totalBillings = result.totalBillings : categoriesData.data.totalBillings;
                    $scope.statusEntryBillings = categoriesData.data.totalBillings;
                }

                $scope.prefilteredEntries = $filter('entriesSearch')(result.returnEntries, $scope.pagination.query);

                angular.forEach(result.returnEntries, function(value){
                    angular.forEach($scope.entriesRows, function(entries){
                        angular.forEach(entries, function(ent){
                            if(value.id == ent.id){
                                ent.status = value.status;
                                ent.billings = value.billings;
                            }
                        })
                    })
                });
            });
        };

        $scope.checkEntry = function(id, entry){
            $http.post(currentBaseUrl + 'checkFinalizedEntry', {'id': id}).then(function (response) {
                setCheckEntry(entry, response.data.entry);
            });
        };

        var setCheckEntry = function(entry, newValue){
            //TODO do it with filters
            angular.forEach($scope.entriesRows, function(entries){
                angular.forEach(entries, function(ent){
                    if(entry.id === ent.id){
                        ent.check = newValue.check;
                    }
                })
            });
            !newValue.check ? $scope.totalCheck--  : $scope.totalCheck++;
        };

        /**********************************************************************************/
        //************************* BILLING ************************************************/
        $scope.billingFilters = [];
        $scope.toggleBillingFilterBy = function(status){
            if($scope.entriesLoading) return;
            $scope.bulkEntries = [];
            if(status != null) {
                var index = $scope.billingFilters.indexOf(status);
                if (index != -1) {
                    $scope.billingFilters.splice(index, 1);
                }
                else $scope.billingFilters.push(status);
            }else{
                $scope.billingFilters = [];
            }
            $scope.pagination.billingFilters = $scope.billingFilters;
            $scope.entriesRows = [];
            lastEntryLoaded = 0;
            $scope.loadMoreEntries();
            $timeout(function(){
            }, 100);
        };

        $scope.countBillingEntries = function(status) {
            return $scope.statusEntryBillings[status] ? $scope.statusEntryBillings[status] : 0;
        };

        $scope.isPayable = function(entry){
            if($scope.voteSession) return;
            if(entry.categories_id.length > 1 || entry.billings.length > 0){
                return 1;
            }
            else if($scope.catMan.getDuePayment(entry) == 0){
                return 0;
            }else return 1;
        };


        $scope.showPayments = function(entry){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: currentBaseUrl+'view/entryPayments',
                controller: 'viewPaymentsCtrl',
                resolve: {
                    entry: function () {
                        return entry;
                    },
                    fields: function () {
                        return {};
                    },
                    categories: function(){
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                entry = result;
            });
        };

    })
    .controller('entriesController',function($scope,currentBaseUrl,$location,$window,$rootScope,$http,$uibModal,$timeout,$sce,
                                         $filter,$route,AuthService,EntryStatus,Flash,Lightbox,
                                         CategoryManager,userInscriptions,contest,categoriesData,SelectedCategories,socket) {
        $scope.catMan = CategoryManager;
        var view_user_entries_id = null;
        contest.categories = categoriesData.data.categories;
        contest.children_categories = categoriesData.data.children_categories;
        CategoryManager.SetCategories(categoriesData.data.children_categories);
        $scope.inscriptionData = categoriesData.data.inscription;
        $scope.filteredCategories = categoriesData.data.filtered_categories;
        $scope.entriesPerUser = categoriesData.data.entryPerUser;
        $scope.voteSession = categoriesData.data.votingSession;
        $scope.inscription = userInscriptions.GetRoleInscription(categoriesData.data.role);
        view_user_entries_id = categoriesData.data.view_user_entries ? categoriesData.data.view_user_entries : null;
        var processProgressData = function(cData){
            if ($scope.voteSession) {
                $scope.votingUser = cData.votingUser;
                $scope.voting = {shortlist: cData.parentShortlist};
            }
        };
        $scope.showFilters = false;
        processProgressData(categoriesData.data);
        $scope.clearView = false;
        $scope.winners = false;
        if($scope.votingUser)
            $scope.totalEntryCategory = $scope.votingUser.progress.total;
        $scope.eStatus = EntryStatus;
        $scope.contest = contest;
        $scope.discountValue = 0;
        $scope.inscriptionType = $scope.inscriptionData ? $scope.inscriptionData.inscription_type : null;
        $scope.categories = $scope.contest.children_categories;
        $scope.categoriesList = $scope.contest.categories;
        $scope.listView = "list";
        $scope.showGrouped = false;
        $scope.entryPerUser = false;
        $scope.showStatic = true;
        $scope.entriesLoading = true;
        $scope.lastEntryShown = false;

        $scope.userInscriptions = userInscriptions;
        $scope.inTimeForInscriptions = function(){
            return !!userInscriptions.GetInscriptionDeadlines($scope.contest.id, true);
        };
        $scope.reachedMaxEntries = function(){
            return $scope.contest.max_entries > 0 ? $scope.entries.length >= $scope.contest.max_entries : false;
        };

        $scope.getHeight = function (divider){
            return 100/divider;
        };

        $scope.setClearView = function(){
            $scope.clearView = !$scope.clearView;
        };

        if(!navigator.userAgent.match(/Android/i) && !navigator.userAgent.match(/webOS/i)
            && !navigator.userAgent.match(/iPhone/i) && !navigator.userAgent.match(/iPad/i)
            && !navigator.userAgent.match(/iPod/i) && !navigator.userAgent.match(/BlackBerry/i)
            && !navigator.userAgent.match(/Windows Phone/i)){
                var $window = $(window);
                var $stickyEl = $('#the-sticky-div');
                if($stickyEl.length == 0) return;
                var elTop = $stickyEl.offset().top;
                $window.scroll(function() {
                    $stickyEl.toggleClass('sticky', $window.scrollTop() > elTop);
                });
        }

        $scope.signupEndTimeAgo = $filter('amSubtract')(userInscriptions.GetInscriptionDeadlines($scope.contest.id), userInscriptions.ServerOffset(), 'seconds');
        $scope.entries = [];
        $scope.filteredEntries = [];
        $scope.prefilteredEntries = [];
        $scope.selCats = SelectedCategories;
        if ($scope.voteSession) {
            if($scope.voteSession.config.showDefaultEntries == 1) $scope.showGrouped = false;
            else $scope.showGrouped = true;
            $scope.voteSessionId = $scope.voteSession.id;
            $scope.voteSessionCode = $scope.voteSession.code;
            $scope.voteSessionName = $scope.voteSession.name;
            $scope.showAllEntries = false;
        }
        $scope.sifter = userInscriptions.GetColaboratorStatus('sifter');
        $scope.editPermit = userInscriptions.GetColaboratorStatus('edit');
        $scope.viewer = userInscriptions.GetColaboratorStatus('viewer');

        $scope.bulkEntries = [];

        $scope.toggleShortlist = function () {
            $scope.showAllEntries = !$scope.showAllEntries;
        };
        $scope.reloadEntries = function () {
            loadEntries(true);
        };
        $scope.toggleFromShortlist = function(entCatId){
            var index = $scope.voting.shortlist.indexOf(entCatId);
            var shortlist = index == -1;
            if (shortlist) {
                $scope.voting.shortlist.push(entCatId);
            }else $scope.voting.shortlist.splice(index, 1);

            var data = {
                'entryCategoryId' : entCatId,
                'shortList' : shortlist
            };
            $http.post(currentBaseUrl + 'shortList/'+ $scope.voteSession.code+ '/', data).success(function(response){
                //loadEntries(true);
                loadProgressData();
            })
        };

        $scope.$watch(function () {
            return $scope.showAllEntries;
        }, function (a, b) {
            if (a != b) {
                loadEntries(true);
            }
        });
        $scope.addEntriesToBulk = function (entry) {
            if(contest.type == 1) return;
            var index = $scope.bulkEntries.indexOf(entry);
            if (index != -1) {
                $scope.bulkEntries.splice(index, 1);
            }
            else $scope.bulkEntries.push(entry);
        };

        $scope.isSelected = function(entry){
            return $scope.bulkEntries.indexOf(entry) != -1;
        };
        $scope.selectAll = function(){
            $scope.bulkEntries = $scope.filteredEntries;
        };

        $scope.deselectAll = function(){
            $scope.bulkEntries = [];
        };

        $scope.getIncompleteFields = function(fields){
            var returnFields = "<ul style='margin:0px; padding: 15px; text-align: left;'>";
            angular.forEach(fields, function(field){
                returnFields += "<li style='margin:0px;'>"+field+"</li>";
            })
            return $sce.trustAsHtml(returnFields+"</ul>");
        }


        $scope.entriesBulkActions = function(entries, status){
            if(entries == 0) return;
            if(!status) $scope.payEntry(entries);
            else $scope.changeStatus(entries, status);
        };


        if(view_user_entries_id != null && $scope.entriesPerUser){
            angular.forEach($scope.entriesPerUser, function(item){
                if(view_user_entries_id == item.id){
                    $scope.loggedUser = item;
                }
            });
        }

        $scope.unreadMsgsEntries = true;

        $scope.unreadMessages = function(entryLog){
            $scope.result = 0;
            if(!entryLog) return;
            if(entryLog.length > 0){
                angular.forEach(entryLog, function(log){
                    if(log.read_by != null){
                        if(log.read_by.search('"'+$rootScope.currentUser.id.toString()+'"') == -1){
                            $scope.result++;
                        }
                    }
                    else{
                        $scope.result++;
                    }
                })
            }else{
                $scope.result = -1;
            }
            return $scope.result;
        };

        $scope.isPayable = function(entry){
            if($scope.voteSession) return;
            if(entry.categories_id.length > 1 || entry.billings.length > 0){
                return 1;
            }
            else if($scope.catMan.getDuePayment(entry) == 0){
                return 0;
            }else return 1;
        };

        $scope.openVideo = function(fileVersion, thumb) {
            $scope.mediaMobile = "";
            /*if(fileVersion[0].extension == "jpg" || fileVersion[0].extension == "png"){
                window.location.href = fileVersion[0].url;
            }else{*/
                var videoModal = $uibModal.open({
                    templateUrl: 'playVideo.html',
                    controller: 'playVideoMobileController',
                    resolve: {
                        fileVersion: function () {
                            return fileVersion[0].url;
                        },
                        fileType: function () {
                            return fileVersion[0].extension;
                        },
                        thumb: function() {
                            return thumb;
                        }
                    }
                });
                videoModal.result.then(function (result) {
                });
            //}
        };

        $scope.openVideoMobile = function(fileVersion){
            /*var video = document.getElementById('video');
            video.play();*/
            window.location.href = fileVersion[0].url;
        }

        $scope.openGallery = function(entry, images, index){
            if(entry.name){
                $scope.getName = function(){
                    return entry.name;
                };
            }else{
                $scope.getNoName = function(){
                    return "<span class='notitle'>Sin título</span>";
                };
            }
            var modal = Lightbox.openModal(images, index, {size:'lg', scope: $scope, resolve:{
                field: function(){
                    return images;
                }
            }});
            modal.result.then(function () {
            }, function () {
            });
        };
        $scope.showLog = function(entry){
            var LogModal = $uibModal.open({
                templateUrl: 'entryLog.html',
                controller: 'entryLogController',
                resolve: {
                    entry: function() {
                        return entry;
                    },
                    fields: function () {
                        return $scope.entry_metadata_fields;
                    },
                    entryLog: function(currentBaseUrl){
                        return $http.post(currentBaseUrl+'entryLog',{id: entry.id}).success(function(data){
                            return data.entryLog;
                        });
                    }
                }
            });
            LogModal.result.then(function (result) {
            });
        };

        $scope.showForm = function(user) {
            var modalInstance = $uibModal.open({
                keyboard: true,
                templateUrl: currentBaseUrl + 'view/inscription-form',
                controller: 'showInscriptionFormCtrl',
                resolve: {
                    fields: function () {
                        return contest.inscription_metadata_fields;
                    },
                    inscription: function ($http) {
                        return $http.post(currentBaseUrl + 'inscriptionForm', {user_id: user.id}).then(function (response) {
                                return response.data;
                        });
                    },
                    user: function(){
                        return user;
                    }
                },
                scope: $scope
            });
        };


        serialize = function(obj) {
            var str = [];
            for(var p in obj)
                if (obj.hasOwnProperty(p)) {
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                }
            return str.join("&");
        };

        angular.element(document).on('click', '.searchBox', function (e) {
            e.stopPropagation();
        });

        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.catEntries = function(category){
            return $scope.filteredEntries.filter(function (entry) {
                return entry.categories_id.indexOf(category.id) !== -1;
            });
        };

        $scope.pagination = {query:'', sortBy: 'name', sortInverted:false};
        $scope.setSortBy = function(by){
            if($scope.pagination.sortBy == by){
                $scope.pagination.sortInverted = !$scope.pagination.sortInverted;
            }else{
                $scope.pagination.sortBy = by;
            }
            $scope.setFilteredEntries();
        };
        $scope.toggleListGrouped = function(){
            $scope.entryPerUser = false;
            $scope.showGrouped = !$scope.showGrouped;
            $scope.filteredEntries = $filter('orderBy')($scope.filteredEntries, 'groupBy',true);
        };
        $scope.toggleEntryPerUser = function(){
            $scope.entryPerUser = !$scope.entryPerUser;
        };
        $scope.toggleListView = function(){
            $scope.listView = $scope.listView == "list" ? "thumbs" : "list";
        };
        $scope.entriesRows = [];
        var lastEntryLoaded = 0;
        $scope.lastEntryShown = false;
        $scope.totalUnread = 0;
        $scope.TotalMsgsIds = [];
        $scope.unreadIds = [];

        var setEntries = function(data){
            $scope.entries = data.entries;
            $scope.entriesLoading = false;
            $scope.entries.forEach(function(entry){
                if(!entry.entry_log) return;
               if(entry.entry_log.length > 0){
                   entry.entry_log.forEach(function(msg){
                       if(msg.read_by.search('"'+$rootScope.currentUser.id.toString()+'"') == -1){
                           $scope.TotalMsgsIds.push(entry.id);
                           $scope.totalUnread ++;
                       }else{
                           $scope.TotalMsgsIds.push(entry.id);
                       }
                   })
               }
            });
            $scope.setFilteredEntries();
        };

        $scope.openEntryInList = function(entry, event, catid){
            userInscriptions.selectedCat = catid;
            if(catid == null){
                var allEntriesIds = [];
                angular.forEach($scope.prefilteredEntries, function(item){
                    allEntriesIds.push({'id' : item.id});
                });
                userInscriptions.entriesIds = allEntriesIds;
            }else{
                var varEntries = CategoryManager.GetCategory(catid).filteredEntries;
                userInscriptions.entriesIds = varEntries;//.map(function(a) {return a.id;});
            }
            //var path = angular.element(event.currentTarget).attr('href').substring(1);
            //$location.path(path);
            //userInscriptions.entriesIds = $scope.entriesIds;
            var path = currentBaseUrl + angular.element(event.currentTarget).attr('href');
            window.open(path, '_blank');
            window.focus();
        };

        $scope.setFilteredEntries = function(){
            $scope.entriesRows = [];
            $scope.lastEntryShown = true;
            lastEntryLoaded = 0;
            $scope.prefilteredEntries = $filter('entriesSearch')($scope.entries, $scope.pagination.query);
            $scope.prefilteredEntries = $filter('entriesCommaSearch')($scope.entries, $scope.pagination.query);
            $scope.prefilteredEntries = $filter('orderBy')($scope.prefilteredEntries, $scope.pagination.sortBy, $scope.pagination.sortInverted);
            $scope.prefilteredEntries = $filter('unreadEntries')($scope.prefilteredEntries, $scope.unreadIds);
            $scope.filteredEntries = $filter('entriesStatus')($scope.prefilteredEntries, $scope.statusFilters);
            $scope.filteredEntries = $filter('entriesBilling')($scope.filteredEntries, $scope.billingFilters);
            //$scope.filteredEntries = $filter('entriesVote')($scope.filteredEntries, $scope.votingStatusFilters);
            //$scope.filteredEntries = $filter('dinamicEntriesFilter')($scope.filteredEntries, $scope.dinamicEntriesFilters);
            $scope.filteredEntries = $filter('showWinners')($scope.filteredEntries, $scope.winners);
            $scope.filteredEntries = $filter('yesNoFilters')($scope.filteredEntries, $scope.yesNoEntriesFilters);
            //$scope.filteredEntries = $filter('filterYesNoEntries')($scope.filteredEntries, $scope.filterYesNoEntries, $scope.responseCat);
            if($scope.showGrouped) {
                var allCats = CategoryManager.GetCategoriesList();
                for (var i in allCats) {
                    var cat = allCats[i];
                    if(cat.final != 1){
                        cat.entriesCount = 0;
                        continue;
                    }
                    cat.entriesRows = [];
                    cat.lastEntryShown = false;
                    cat.lastEntryLoaded = 0;
                    cat.filteredEntries = $filter('entriesCategory')($scope.filteredEntries, cat.id);
                    cat.filteredEntries = $filter('entriesVoteCategory')(cat.filteredEntries, $scope.votingStatusFilters, cat.id);
                    cat.filteredEntries = $filter('dinamicEntriesFilterCategory')(cat.filteredEntries, $scope.dinamicEntriesFilters, cat.id);
                    //$scope.filteredEntries = $filter('dinamicEntriesFilter')($scope.filteredEntries, $scope.dinamicEntriesFilters);
                    cat.entriesCount = cat.filteredEntries.length;
                }
                for (var i in allCats) {
                    var cat = allCats[i];
                    if(cat.final != 1) continue;
                    var mCat = cat;
                    while(mCat.parent != null){
                        mCat.parent.entriesCount += cat.entriesCount;
                        mCat = mCat.parent;
                    }
                }
            }else{
                $scope.filteredEntries = $filter('entriesVote')($scope.filteredEntries, $scope.votingStatusFilters);
                $scope.filteredEntries = $filter('dinamicEntriesFilter')($scope.filteredEntries, $scope.dinamicEntriesFilters);
            }
            $scope.inViewLoadMoreEntries(10);
        };
        var entriesPerRow = 24;
        $scope.loadMoreEntries = function(){
            if(lastEntryLoaded > $scope.filteredEntries.length) return;
            $scope.entriesRows.push($scope.filteredEntries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.filteredEntries.length;
            $scope.firstTime = 0;
        };
        var loadingEntries = false;
        $scope.inViewLoadMoreEntries = function(delay){
            if(!!loadingEntries) return;
            loadingEntries = true;
            $timeout(function(){
                $scope.loadMoreEntries();
                loadingEntries = false;
            }, delay !== undefined ? delay : 1000);
        };
        $scope.$watch('pagination.query', function(){
            $scope.bulkEntries = [];
            $scope.setFilteredEntries();
        });

        $scope.$watch(function(){ return $scope.showGrouped; }, function(a){
            //if(a) return;
            /*$scope.entriesLoading = true;
            $http.post(currentBaseUrl+'entries', {user_id: view_user_entries_id, inscription: $scope.inscription, filteredCategories: $scope.filteredCategories, voteSession: $scope.voteSession}).success(function(data){
                setEntries(data.entries);
                $scope.entriesLoading = false;
            });*/
            $scope.setFilteredEntries();
        });
        var loadEntries = function (reloadProgress) {
            var postData = {
                user_id: view_user_entries_id,
                inscription: $scope.inscription,
                filteredCategories: $scope.filteredCategories,
                voteSession: $scope.voteSession
            };
            if($scope.showAllEntries){
                postData.showAllEntries = true;
            }
            $http.post(currentBaseUrl + 'entries', postData).success(function (data) {
                setEntries(data);
                if(reloadProgress){
                    loadProgressData();
                }
            });
        };
        loadEntries(false);

        var loadProgressData = function(){
            var postData = {};
            if($scope.showAllEntries){
                postData.showAllEntries = true;
            }
            return $http.post(currentBaseUrl+'vote/'+$scope.voteSession.code, postData).success(function(data){
                processProgressData(data);
            });
        };

        $scope.unreadIds = [];
        $scope.filterByUnread = function(){
            if($scope.unreadIds.length == 0) $scope.unreadIds = $scope.TotalMsgsIds;
            else $scope.unreadIds = [];
            $scope.setFilteredEntries();
        };

        $scope.statusFilters = [];
        $scope.toggleFilterBy = function(status){
            $scope.bulkEntries = [];
            if(status != null) {
                var index = $scope.statusFilters.indexOf(status);
                if (index != -1) {
                    $scope.statusFilters.splice(index, 1);
                }
                else $scope.statusFilters.push(status);
            }else{
                $scope.statusFilters = [];
            }
            $scope.setFilteredEntries();
        };

        $scope.showWinners = function(){
            $scope.winners = !$scope.winners;
            if($scope.winners == false) $scope.dinamicEntriesFilters = [];
            $scope.setFilteredEntries();
            $scope.filteredEntries = $filter('orderBy')($scope.filteredEntries, 'groupBy',true);
        };

        $scope.votingStatusFilters = [];
        $scope.toggleVotingFilterBy = function(status){
            if(status != null) {
                var index = $scope.votingStatusFilters.indexOf(status);
                if (index != -1) {
                    $scope.votingStatusFilters.splice(index, 1);
                }
                else $scope.votingStatusFilters.push(status);
            }else{
                $scope.votingStatusFilters = [];
            }
            $scope.setFilteredEntries();
        };

        var entryBeforeVote = null;
        var voteResult = null;
        $scope.BeforeVoteUpdate = function(entry, results){
            voteResult = results;
            voteResult.error = false;
            entryBeforeVote = angular.copy(entry.votes);
        };
        $scope.VoteUpdated = function(entry, myVote){
            if(!$scope.voteSession) return;
            $http.post(currentBaseUrl+'entryCategoryVote',{id: $scope.voteSession.code, vote: myVote, entryId: entry.id}).success(function(data){
                voteResult.error = false;
                //TODO Chequear esto, no debería ser entry.votes??? pero data.votes devuelve otra cosa?
                entry.vote = data.votes;
                $scope.votingUser = data.votingUser;
                $scope.countVoteEntries(0);
                $scope.countVoteEntries(1);
                $scope.countVoteEntries(2);
                //if($scope.contest.id == 159 || $scope.contest.id == 158) filterYesNoVotes(data.entries, data.catId, data.vote);
                filterYesNoVotes(data.entries, data.catId, data.vote);
            }).error(function(){
                voteResult.error = true;
                $scope.firstTime = 0;
                entry.votes = entryBeforeVote;
                $scope.countVoteEntries(0);
                $scope.countVoteEntries(1);
                $scope.countVoteEntries(2);
            });
        };

        $scope.dinamicEntriesFilters = [];
        $scope.dinamicEntriesFilter = function(status){
            if(status != null) {
                var index = $scope.dinamicEntriesFilters.indexOf(status);
                if (index != -1) {
                    $scope.dinamicEntriesFilters.splice(index, 1);
                }
                else $scope.dinamicEntriesFilters.push(status);
            }else{
                $scope.dinamicEntriesFilters = [];
            }
            $scope.setFilteredEntries();
            $scope.filteredEntries = $filter('orderBy')($scope.filteredEntries, 'groupBy',true);
        };

        $scope.yesNoEntriesFilters = [];
        $scope.yesNoFilters = function(status){
            if(status != null) {
                var index = $scope.yesNoEntriesFilters.indexOf(status);
                if (index != -1) {
                    $scope.yesNoEntriesFilters.splice(index, 1);
                }
                else $scope.yesNoEntriesFilters.push(status);
            }else{
                $scope.yesNoEntriesFilters = [];
            }
            $scope.setFilteredEntries();
        };

        $scope.billingFilters = [];
        $scope.toggleBillingFilterBy = function(status){
            $scope.bulkEntries = [];
            var index = $scope.billingFilters.indexOf(status);
            if (index != -1) {$scope.billingFilters.splice(index, 1);}
            else $scope.billingFilters.push(status);
            $scope.setFilteredEntries();
        };

        if(userInscriptions.selected != null){
            $scope.toggleFilterBy(userInscriptions.selected);
        }

        $scope.showMetals = false;
        $scope.countEntries = function(status) {
            var ents = $filter('entriesBilling')($scope.prefilteredEntries, $scope.billingFilters);
            var count = 0;
            angular.forEach(ents, function(item){
                if(item.votes){
                    $scope.showMetals = true;
                    $scope.voteSessionMetals = item.voteSession;
                }
                if(status == null || item.status == status){
                    count += item.categories_id.length;
                }
            });
            if($scope.showMetals == true && $scope.winners == true){
                count = $scope.filteredEntries.length;
            }
            return count;
        };

        $scope.countYesNoEntries = function(status){
            var count = 0;
            angular.forEach($scope.prefilteredEntries, function(item){
                angular.forEach(item.votes, function(votes) {
                    if(votes.vote == status) count ++;
                })
            })
            return count;
        };

        $scope.countVoteEntries = function(status, dinamic){
            var count = 0;
            var categVotes = 0;
            angular.forEach($scope.prefilteredEntries, function(item){
                angular.forEach(item.categories_id, function(category, i) {
                    categVotes++;
                    var votes = item.votes[category];
                    if(dinamic == true){
                        if(votes){
                            if(votes['vote']){
                                if(status == votes['vote'].name){
                                    count++;
                                }
                            }
                        }
                    }else{
                        if(!item.votes[category]){
                            if(status == 2){
                                count++;
                            }
                        }else{
                            switch (status) {
                                case 0:
                                    if (votes.abstain == true) count++;
                                    break;
                                case 1:
                                    /*** YES NO ****/
                                    if($scope.voteSession.vote_type == 2){
                                        if(votes.vote === 0 || votes.vote === 1) count++;
                                        break;
                                    }
                                    if(votes.vote || votes.vote === 0){
                                        /**** METALERO ****/
                                        if($scope.voteSession.vote_type == 4){
                                            if(votes['vote']) count++;
                                        }
                                        else {
                                            if ($scope.voteSession.config.criteria && $scope.voteSession.config.criteria.length > 0) {
                                                if (Object.keys(votes.vote).length == $scope.voteSession.config.criteria.length) {
                                                    count++;
                                                }
                                            } else {
                                                count++;
                                            }
                                        }
                                    }
                                    break;
                                case 2:
                                    /*** YES NO ****/
                                    if($scope.voteSession.vote_type == 2){
                                        if(votes.vote === null && votes.vote !== 0) count++;
                                        break;
                                    }
                                    if ((!votes || !votes.vote) && votes.abstain != true && votes.vote != 0){
                                        count++;
                                    }
                                    break;
                            }
                        }
                    }
                });
                //if($categVotes == count) count = 1;
            });
            return count;
        };

        $scope.countBillingEntries = function(status) {
            var ents = $filter('entriesStatus')($scope.prefilteredEntries, $scope.statusFilters);
            var count = 0;
            angular.forEach(ents, function(item){
                if(status == null){
                    if(item.billings.length == 0){
                        count++;
                    }
                }else {
                    if (item.billings.length > 0) {
                        angular.forEach(item.billings, function (billing) {
                            if (billing.status == status) count++;
                        });
                    }
                }
            });
            return count;
        };

        $scope.toggleCat = function(cat, v, childs){
            cat.open = v == null ? !cat.open : !!v;
            /* Guardo las categorias abiertas */
            $scope.selCats.setData(cat.id);
            cat.entriesRows = [];
            if(cat.final == 1 && cat.open){
                cat.entriesRows = [];
                cat.lastEntryShown = false;
                cat.lastEntryLoaded = 0;
                //cat.filteredEntries = $filter('entriesCategory')($scope.filteredEntries, cat.id);
                if(!childs) $scope.inViewLoadMoreCatEntries(cat, 100);
            }
            if(!!childs && angular.isDefined(cat.children_categories)){
                toggleAll(cat.children_categories, cat.open);
            }
        };

        var catEntriesPerRow = 12;
        $scope.inViewLoadMoreCatEntries = function(cat, delay){
            if(!!cat.loading) return;
            cat.loading = true;
            $timeout(function(){
                $scope.loadMoreCatEntries(cat);
                cat.loading = false;
            }, delay !== undefined ? delay : 1000);
        };
        $scope.loadMoreCatEntries = function(cat){
            if(!cat.filteredEntries) cat.filteredEntries = [];
            if(!cat.entriesRows) cat.entriesRows = [];
            if(cat.lastEntryLoaded > cat.filteredEntries.length) return;
            cat.entriesRows.push(cat.filteredEntries.slice(cat.lastEntryLoaded, cat.lastEntryLoaded + catEntriesPerRow));
            cat.lastEntryLoaded += catEntriesPerRow;
            cat.lastEntryShown = cat.lastEntryLoaded > cat.filteredEntries.length;
        };

        $scope.toggleUser = function(user, v){
            user.open = v == null ? !user.open : !!v;
            if(!user.loaded && !user.loading){
                user.loading = true;
                $http.post(currentBaseUrl+'userEntries', {user: user.id}).success(function(data){
                    user.entries = data.entries;
                    user.loaded = true;
                    user.loading = false;
                });
            }
        };

        function toggleAll(cats, open){
            for(var c in cats){
                $scope.toggleCat(cats[c], !!open, true);
            }
        }
        function toggleAllUsers(users, open){
            for(var c in users){
                $scope.toggleUser(users[c], !!open, true);
            }
        }
        $scope.expandAll = function(){
            toggleAll($scope.categories, true);
        };

        $scope.collapseAll = function(){
            toggleAll($scope.categories, false);
        };
        $scope.expandAllUserEntries = function(){
            toggleAllUsers($scope.entriesPerUser, true);
        };
        $scope.collapseAllUserEntries = function(){
            toggleAllUsers($scope.entriesPerUser, false);
        };
        $scope.terms = function() {
            $uibModal.open({
                templateUrl: currentBaseUrl + 'view/terms',
                controller: 'TermsCtrl'
            });
        };
        $scope.removeCategory = function(category, entry_id){
            $http.post(currentBaseUrl+'removeEntryFromCategory', {category_id: category.id, entry_id: entry_id}).success(function(data){
            });
            $location.path('/entries');
            $route.reload();
        };

        $scope.newInscriptionCategory = function(category_id){
            $http.get(currentBaseUrl+'/entry', {category_id: category_id}).success(function(data){
            });
        };


        $scope.changeStatus = function(entry, status){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: 'entrySifter.html',
                controller: 'changeEntryStatusCtrl',
                resolve: {
                    entry: function () {
                        if(!entry.length){
                            $scope.aux = [];
                            $scope.aux.push(entry);
                            entry = $scope.aux;
                        }
                        return entry;
                    },
                    fields: function () {
                        return {};
                    },
                    status: function () {
                        return status;
                    },
                    onlyPay: function () {
                        return false;
                    },
                    categories: function(){
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                angular.forEach(result, function(value){
                    angular.forEach(entry, function(ent){
                        if(value.id == ent.id){
                            ent.status = value.status;
                            ent.billings = value.billings;
                            return;
                        }
                    })
                })
            });
        };
        $scope.payEntry = function(entry){
            var s = entry.status;
            $scope.discountValue = 0;
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: currentBaseUrl+'view/payEntry',
                controller: 'changeEntryStatusCtrl',
                resolve: {
                    entry: function () {
                        if(!entry.length){
                            $scope.aux = [];
                            $scope.aux.push(entry);
                            entry = $scope.aux;
                        }
                        return entry;
                    },
                    fields: function () {
                        return {};
                    },
                    onlyPay: function () {
                        return true;
                    },
                    categories: function(){
                        return $scope.categories;
                    },
                    status: function(){
                        return s;
                    },
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                angular.forEach(result, function(value){
                    angular.forEach(entry, function(ent){
                        if(value.id == ent.id){
                            ent.status = value.status;
                            ent.billings = value.billings;
                            return;
                        }
                    })
                })
            });
        };
        $scope.showPayments = function(entry){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: currentBaseUrl+'view/entryPayments',
                controller: 'viewPaymentsCtrl',
                resolve: {
                    entry: function () {
                        return entry;
                    },
                    fields: function () {
                        return {};
                    },
                    categories: function(){
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                entry = result;
            });
        };
        $scope.firstTime = 0;
        var filterYesNoVotes = function(entries, responseCat, responseVote){
            if(responseVote == 0 || responseVote == null){
                $scope.firstTime = 0;
                return;
            }
            $scope.responseCat = responseCat;
            //if(entries && $scope.voteSession.config.yesPerCategory){
            if(entries){
                $scope.entries = entries;
                $scope.setFilteredEntries();
            }
            $scope.responseCat = null;
            /*$scope.filterYesNoEntries = entries;
            $scope.responseCat = responseCat;
            $scope.setFilteredEntries();
            $scope.responseCat = null;
            $scope.filterYesNoEntries = [];*/
        };

        /*if($scope.voteSession) {
            if($scope.voteSession.vote_type != 2 || $scope.firstTime == 1) return;
            $scope.$watch(function () {
                return $scope.filteredEntries;
            }, function (newval, oldval) {
                if($scope.firstTime == 1) return;
                angular.forEach(newval, function(val1){
                    angular.forEach(oldval, function(val2){
                        if(val1.id == val2.id){
                            angular.forEach(val1.votes, function(val, cat){
                                if(val1.votes[cat].vote != val2.votes[cat].vote && $scope.firstTime == 0){
                                    $scope.firstTime = 1;
                                    $http.post(currentBaseUrl+'entryCategoryVote',{id: $scope.voteSession.code, vote: val1.votes, entryId: val1.id}).success(function(data){
                                        filterYesNoVotes(data.entries, data.catId, data.vote);
                                    }).error(function(){
                                        $scope.firstTime = 0;
                                    });
                                }
                            })
                        }
                    });
                });
            }, true);
        }*/

        //Set the default toolbar for users
        let oxoMeetToolbar = [
            'microphone', 'camera', 'hangup', 'profile', 'chat','settings', 'raisehand',
            'videoquality', 'filmstrip', 'stats', 'shortcuts', 'tileview', 'videobackgroundblur'
        ];

        $scope.meetModerator = false;
        if($scope.voteSession && $scope.voteSession.config.oxoMeetModerators){
            let moderators = $scope.voteSession.config.oxoMeetModerators.split(',');
            angular.forEach(moderators, function(moderator){
                if(moderator === $scope.currentUser.email){
                    $scope.meetModerator = true;
                    //Set the toolbar for moderators
                    oxoMeetToolbar = [
                        'microphone', 'camera', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'profile', 'info', 'chat', 'recording',
                        'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                        'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                        'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                        'e2ee'
                    ];
                }
            });
        }
        $scope.inLobby = false;
        let connectToMeet = function(){
            let meetServer = 'meet.oxoawards.com';
            $scope.voteSession.config.oxoMeetServer ? meetServer = $scope.voteSession.config.oxoMeetServer : "";
            $timeout(function() {
                const domain = meetServer;
                const options = {
                    roomName: $scope.voteSession.config.oxoMeetingLink,
                    /*width: 900,
                    height: 600,*/
                    parentNode: document.querySelector('#meeting'),
                    interfaceConfigOverwrite: {
                        TOOLBAR_BUTTONS: oxoMeetToolbar,
                        DEFAULT_BACKGROUND: 'white',
                    },
                    userInfo: {
                        displayName: $scope.currentUser.first_name+" "+$scope.currentUser.last_name,
                    }
                };
                if($scope.inLobby === false){
                    const api = new JitsiMeetExternalAPI(domain, options);

                    api.executeCommand('toggleTileView');
                    api.executeCommand('toggleLobby', true);
                    api.executeCommand('setVideoQuality', 720);

                    $scope.currentProjectUrl = $sce.trustAsResourceUrl($scope.voteSession.config.oxoMeetingLink);

                    api.executeCommand('password', $scope.voteSession.config.oxoMeetingPassword);
                    // join a protected channel
                    api.on('passwordRequired', function ()
                    {
                        api.executeCommand('password', $scope.voteSession.config.oxoMeetingPassword);
                    });
                }
            },1000);
        }

        if($scope.voteSession && $scope.voteSession.config.oxoMeeting &&  $scope.votingUser.status !== 6){
            connectToMeet();
        }

        /*let checkUserInLobby = function(){
            console.log("ESPERANDO AUTORIZACION PARA SALIR DEL LOBBY");
            $http.get(currentBaseUrl+'getMeetUserInLobby/'+$scope.votingUser.id).success(function(data){
                $scope.inLobby = data.inLobby;
            });
            if($scope.inLobby === true){
                autoCheckUserInLobby = $timeout(checkUserInLobby, 3000);
            }else{
                $timeout.cancel( autoCheckUserInLobby );
                connectToMeet();
            }
        }*/

        $scope.goToLobby = function(judge){
            //$scope.inLobby = !$scope.inLobby;
            $http.post(currentBaseUrl+'MeetUserInLobby', {votingUserId: judge.id}).success(function(data){
                socket.emit('lobby',{id: data.judge.id, status: data.judge.status});
            });
            /*if($scope.inLobby === true){
                checkUserInLobby();
            }*/
        }

        socket.on('user_lobby', function(rsp){
            if(parseInt(rsp.data.id) === $scope.votingUser.id){
                if(parseInt(rsp.data.status) !== 6){
                    $scope.inLobby = false;
                }else{
                    $scope.inLobby = true;
                }
                connectToMeet();
            }
        });

        socket.on('show_entry', function(rsp){
            if(rsp.data.votingEntries)
                $scope.pagination.query = rsp.data.votingEntries.toString();
            if($scope.pagination.query.length > 0){
                $scope.expandAll();
            }else{
                $scope.collapseAll();
            }
        })

        socket.on('connected', function(rsp){
            console.log("connected");
        });

        $scope.judgesInLobby = function(id) {
            $http.post(currentBaseUrl + 'getUsersInLobby', {
                votingUserId: id,
                voteSessionId: $scope.voteSession.id
            }).success(function (data) {
                $scope.usersInLobby = data.usersInLobby;
            });
        }
        let votingEntries = [];
        $scope.votingEntry = function(entry) {
            if(entry.voteSelected === true){
                entry.voteSelected = false;
                let index = votingEntries.indexOf(entry.id);
                if (index !== -1) votingEntries.splice(index, 1);
                socket.emit('voting_entry', {votingEntries: votingEntries});
                return;
            }
            if(entry.length === 0){
                angular.forEach($scope.filteredEntries, function(entry){
                    votingEntries = [];
                    entry.voteSelected = false;
                });
            }
            entry.voteSelected = true;
            votingEntries.push(entry.id);
            socket.emit('voting_entry', {votingEntries: votingEntries});
        }
    })
    .controller('playVideoMobileController', function($scope, $sce, fileVersion, fileType, thumb) {
        $scope.mediaMobile = $sce.trustAsResourceUrl(fileVersion);
        $scope.fileType = fileType;
        $scope.thumb = thumb;
    })
    .controller('viewPaymentsCtrl', function($scope, rootUrl, currentBaseUrl, $sanitize, $location, $http, $timeout, Flash, Alert,Languages, $uibModalInstance, CategoryManager, contest, entry, fields, categories){
        $scope.entry = entry;
        $scope.categories = categories;
        $scope.fields = fields || {};

        $scope.close = function(entry){
            $uibModalInstance.close(entry);
        };
        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories(categories);


        $scope.cancelPayment = function(billId){
            $http.post(currentBaseUrl+'cancelPayment', {billId: billId}).success(function(data){
                angular.forEach($scope.entry.billings, function(bill, key){
                    if(bill.id === billId){
                        $scope.entry.billings[key] = [];
                    }
                });
                $scope.close($scope.entry);
            });
        }
    })
    .controller('showInscriptionFormCtrl',function($scope, rootUrl, currentBaseUrl, $sanitize, $location, $http, $timeout, Flash, Alert, $uibModalInstance, fields, inscription, user){
        var metaFields = fields;
        angular.forEach(metaFields, function(field, key){
            metaFields[key]['value'] = '';
        });
        if(inscription.inscription_metadatas.length > 0){
            angular.forEach(metaFields, function(field, key){
                angular.forEach(inscription.inscription_metadatas, function(val){
                    if(val.inscription_metadata_field_id == field.id){
                        if(field.type == 5){
                            if(!metaFields[key]['value']) metaFields[key]['value'] = [];
                            metaFields[key]['value'].push(parseInt(val.value));
                        }else{
                            metaFields[key]['value'] = val.value;
                        }
                    }
                });
            });
        }

        $scope.leaveNote = function(note, inscriptionId, action, id){
            $http.post(currentBaseUrl+'adminNote',{note: note, inscriptionId: inscriptionId, action: action, id: id}).success(function(data){
                Flash.show(data.flash, 'success', $scope);
                $scope.note = null;
                $scope.inscription.notes = data.note;
            });
        };

        $scope.inscription = inscription;
        $scope.metadata = metaFields;
        $scope.user = user;
        $scope.close = function(){
            $uibModalInstance.close();
        };
    })
    .controller('buyTicketCtrl', function($scope, rootUrl, currentBaseUrl, $sanitize, Languages, $window, $route, $location, $http, $timeout, Flash, Alert, $uibModalInstance, /*TCOSandbox,*/ contest, tickets, total, totalTickets){
        $scope.total = total;
        $scope.tickets = tickets;
        $scope.totalTickets = totalTickets;
        $scope.payment = {};
        $scope.transaction = false;

        var stripe = Stripe('pk_test_51HKoshEQ1j1oqB8WdEDmG3VWqhjZfn8DdDh2KpEY6vY1FBBNFCgfBGYziKEosS61L7Se3ZNXEBWpjPC8LeE671D900aJEH8cvH');

        $scope.cancel = function(){
            $uibModalInstance.close();
        };
        /*$scope.close = function(){
            $uibModalInstance.dismiss();
        };*/

        $scope.accept = function () {
            if(!$scope.modalForm.$invalid) {
                $scope.sending = true;
                $scope.flash = null;
                Flash.clear($scope);
                /*if($scope.payment.method == 'TCO') {
                    var args = {
                        sellerId: contest.billing.methods.TCO.data.sellerId,
                        publishableKey: contest.billing.methods.TCO.data.publicKey,
                        ccNo: $scope.payment.TCO.ccNo,
                        cvv: $scope.payment.TCO.cvv,
                        expMonth: $scope.payment.TCO.expMonth,
                        expYear: $scope.payment.TCO.expYear
                    };
                    // Make the token request
                    TCO.requestToken(TCOSuccessCallback, TCOErrorCallback, args);
                }else{*/
                /* TODO pasarle un array y que lo haga todo el php */
                send({
                    method: $scope.payment.method,
                    tickets: $scope.tickets,
                });
                //}
            }
        };
        /*var TCOSuccessCallback = function(data) {
            send({
                method: $scope.payment.method,
                tickets: $scope.tickets,
                token: data.response.token.token,
                payment: data.response.paymentMethod
            });
        };

        // Called when token creation fails.
        var TCOErrorCallback = function(data) {
            if (data.errorCode === 200) {
                // This error code indicates that the ajax call failed. We recommend that you retry the token request.
            } else {
                Flash.show(data.errorMsg, 'danger',$scope);
            }
            $scope.sending = false;
        };*/

        $scope.sending = false;
        var send = function(data){
            if ($scope.payment.method !== "stripe") {
                $http.post(currentBaseUrl + 'payTickets', data).success(function (rsp) {
                    if (angular.isDefined(rsp.msg)) {
                        Alert.show('Error', 'ban', rsp.msg, 'danger');
                    } else {
                        if (angular.isDefined(rsp.success)) {
                            Alert.show(rsp.title, 'check', rsp.success, 'info');
                        }
                        $uibModalInstance.close(rsp);
                    }
                    $scope.sending = false;
                });
            }

            if ($scope.payment.method === "stripe") {
                $http.post(currentBaseUrl + 'payTickets', data)
                    .then(function (session) {
                        var stripe = Stripe('pk_test_51HKoshEQ1j1oqB8WdEDmG3VWqhjZfn8DdDh2KpEY6vY1FBBNFCgfBGYziKEosS61L7Se3ZNXEBWpjPC8LeE671D900aJEH8cvH');
                        return stripe.redirectToCheckout({sessionId: session.data.session.id});
                    })
                    .then(function (result) {
                        // If `redirectToCheckout` fails due to a browser or network
                        // error, you should display the localized error message to your
                        // customer using `error.message`.
                        if (result.error) {
                            alert(result.error.message);
                        }
                    });
                $scope.sending = false;
            };
        };

        $scope.reloadRoute = function() {
            $route.reload();
        }
    })
    .controller('changeEntryStatusCtrl', function($scope, rootUrl, currentBaseUrl, $sanitize, Languages, $window, $location, $http, $timeout, Flash, Alert, $uibModalInstance, /*TCOSandbox,*/ EntryStatus, CategoryManager, contest, data, status, categories){
        /*TCO.loadPubKey(TCOSandbox ? 'sandbox' : 'production');*/
        Flash.clear($scope);
        $scope.entry = data.entries;
        $scope.bulkBillings = data.bulkBillings;
        $scope.notPayed = data.countNoPayed;
        $scope.countPayed = data.countPayed;
        $scope.onlyPay = data.onlyPay;
        $scope.status = status;
        $scope.categories = categories;
        $scope.fields = {};
        $scope.msg = "";
        $scope.payment = {};
        $scope.language = Languages.Active;
        $scope.langDefault = Languages.Default;
        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories(categories);

        $scope.cancel = function(){
            $uibModalInstance.close();
        };
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };

        $scope.priceTotal = 0;
        angular.forEach($scope.entry,function(ent){
            if(ent.mustPay && ent.categories_id){
                $scope.priceTotal = $scope.priceTotal + CategoryManager.GetTotalPrice(ent);
            }
            if($scope.catMan.getDuePayment(ent) == 0){
                $scope.payable = 0;
                return;
            }else $scope.payable = 1;
        })

        $scope.mustPay = false;

        $scope.accept = function () {
            if(!$scope.modalForm.$invalid) {
                $scope.sending = true;
                $scope.flash = null;
                Flash.clear($scope);

                /*if(contest.billing && $scope.mustPay && ($scope.onlyPay || $scope.status == EntryStatus.FINALIZE)  && $scope.payment.method == 'TCO') {
                    var args = {
                        sellerId: contest.billing.methods.TCO.data.sellerId,
                        publishableKey: contest.billing.methods.TCO.data.publicKey,
                        ccNo: $scope.payment.TCO.ccNo,
                        cvv: $scope.payment.TCO.cvv,
                        expMonth: $scope.payment.TCO.expMonth,
                        expYear: $scope.payment.TCO.expYear
                    };
                    // Make the token request
                    TCO.requestToken(TCOSuccessCallback, TCOErrorCallback, args);
                }else{*/
                /* TODO pasarle un array y que lo haga todo el php */
                if(!$scope.entry.length){
                    $scope.aux = [];
                    $scope.aux.push($scope.entry);
                    $scope.entry = $scope.aux;
                }
                send({
                    method: $scope.payment.method,
                    entry: $scope.entry,
                    status: status,
                    onlyPay: $scope.onlyPay,
                    message: $scope.msg,
                    discountValue: $scope.discountValue,
                    notPayed: $scope.notPayed
                });
                //}
            }
        };
        $scope.sending = false;

        var send = function(data) {
            if ($scope.payment.method !== "stripe") {
                $http.post(currentBaseUrl + 'changeEntryStatus', data).success(function (rsp) {
                    if (angular.isDefined(rsp.msg)) {
                        Alert.show('Error', 'ban', rsp.msg, 'danger');
                    } else {
                        if (angular.isDefined(rsp.success)) {
                            Alert.show(rsp.title, 'check', rsp.success, 'info');
                        }
                        $uibModalInstance.close(rsp);
                    }
                    $scope.sending = false;
                });
            }

            if ($scope.payment.method === "stripe") {
                $http.post(currentBaseUrl + 'changeEntryStatus', data)
                    .then(function (session) {
                        var stripe = Stripe('pk_test_51HKoshEQ1j1oqB8WdEDmG3VWqhjZfn8DdDh2KpEY6vY1FBBNFCgfBGYziKEosS61L7Se3ZNXEBWpjPC8LeE671D900aJEH8cvH');
                        return stripe.redirectToCheckout({sessionId: session.data.session.id});
                    })
                    .then(function (result) {
                        // If `redirectToCheckout` fails due to a browser or network
                        // error, you should display the localized error message to your
                        // customer using `error.message`.
                        if (result.error) {
                            alert(result.error.message);
                        }
                    });
                $scope.sending = false;
            };
        };


        /*var TCOSuccessCallback = function(data) {
            send({
                method: $scope.payment.method,
                entry: $scope.entry,
                status: status,
                onlyPay: $scope.onlyPay,
                message: $scope.msg,
                token: data.response.token.token,
                payment: data.response.paymentMethod
            });
        };

        // Called when token creation fails.
        var TCOErrorCallback = function(data) {
            if (data.errorCode === 200) {
                // This error code indicates that the ajax call failed. We recommend that you retry the token request.
            } else {
                Flash.show(data.errorMsg, 'danger',$scope);
            }
            $scope.sending = false;
        };*/

        $scope.hasDiscount = function(discounts){
            if(!discounts){
                return false;
            }
            angular.forEach(discounts, function(discount){
                if(parseInt(discount.min_entries) <= parseInt($scope.notPayed)){
                    if(parseInt(discount.max_entries) >= parseInt($scope.notPayed) || discount.max_entries == null){
                        $scope.discountValue = discount.value;
                    }
                }
            });

            if($scope.discountValue > 0) return true;
            return false;
        };
    }).
    controller('changeStatusFile', function($scope, rootUrl, currentBaseUrl, $sanitize, $location, $http, $timeout, Flash, $uibModalInstance, file, status){
        Flash.clear($scope);
        $scope.file = file;
        $scope.tech_status = status;
        $scope.msg = "";
        $scope.cancel = function(){
            $uibModalInstance.close();
        };
        $scope.accept = function(){
            $http.post(currentBaseUrl+'changeFileStatus', {fileId: $scope.file.id, status: $scope.tech_status, message: $scope.msg}).success(function(rsp){

                $uibModalInstance.close(rsp);
            });
        }
    })
    .controller('entryLogController', function($scope, rootUrl, $filter, $rootScope, currentBaseUrl, $timeout, $anchorScroll, $sanitize, $location, $http, Flash, $uibModalInstance, entry, fields, entryLog){
        $scope.entryLog = entryLog.data.entryLog;
        $scope.close = function() {
            $uibModalInstance.dismiss();
        };
        $scope.entry = entry;
        $scope.fields = fields;
        $scope.cancel = function(){
            $uibModalInstance.close();
        };
        $scope.message = function(message, entryId){
            $http.post(currentBaseUrl+'entryMessage',{message: message, entryId: entryId}).success(function(data){
                Flash.show(data.flash, 'success', $scope);
                $scope.entryMessage = null;
                $scope.entryLog = data.entryLog;
            });
            $timeout(function() {
                $location.hash('bottom');
                $anchorScroll();
            }, 0, false);
        };

        $scope.isResponse = function(entryLog){
            if(entryLog.user.id != $rootScope.currentUser.id){
                return true;
            }else{
                return false;
            }
        };

        $scope.wasRead = function(readBy){
            if(readBy != null){
                if(readBy.search('"'+$rootScope.currentUser.id.toString()+'"') == -1){
                    return true;
                }
            }
            return false;
        };

        $scope.onlyMsgs = function(){
            var res = $filter('filter')($scope.entryLog, {status:"5"}, true);
        };

        $scope.onlyStatus = function(){
            $scope.filterStates = [
                {status:"1"},
                {status:"2"},
                {status:"3"},
                {status:"4"}
            ];

            $scope.filterFunction = function(stat){
                return $scope.filterStates;
            };

            var onlyStatus = $filter('filter')($scope.entryLog, $scope.filterFunction);
        };
    })
    .controller('entryIncompleteController', function($scope, rootUrl, currentBaseUrl, $sanitize, $location, $http, Flash, $uibModalInstance, entry, fields){
        $scope.close = function() {
            $uibModalInstance.dismiss();
        };
        $scope.entry = entry;
        $scope.fields = fields;
        $scope.cancel = function(){
            $uibModalInstance.close();
        };
    })
    .controller('techController', function($scope){
        $scope.test = '';
        $scope.fileIsOk = function(f){
        }
    })
    .controller('pageController', function($scope, $timeout, $http, content, contest, code, Lightbox, currentBaseUrl){
        $scope.content = content.data.content;
        $scope.entries = content.data.entries;
        $scope.totalEntries = content.data.totalEntries;
        $scope.listView = "thumbs";
        $scope.entriesRows = [];
        var loadingEntries = false;
        var lastEntryLoaded = 0;
        var entriesPerRow = 24;
        $scope.lastEntryShown = false;
        var newEntries = [];

        $scope.inViewLoadMoreEntries = function(delay){
            if(!!loadingEntries) return;
            loadingEntries = true;
            $timeout(function(){
                $scope.loadMoreEntries();
                loadingEntries = false;
            }, delay !== undefined ? delay : 1000);
        };

        $scope.inViewLoadMoreEntries(10);

        $scope.setEntries = function(newEntries){
            $scope.entriesRows.push(newEntries);
            //$scope.entriesRows.push($scope.entries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.totalEntries;
            $scope.firstTime = 0;
        }

        $scope.loadMoreEntries = function(){
            if(!$scope.entries) return;
            if(lastEntryLoaded > $scope.totalEntries) return;
            /*$scope.entriesRows.push($scope.entries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.entries.length;
            $scope.firstTime = 0;*/
            $http.get(currentBaseUrl+'page/'+code+'/'+lastEntryLoaded).success(function(data){
                $scope.setEntries(data.entries);
            });
        };

        $scope.openGallery = function(entry, images, index){
            if(entry.name){
                $scope.getName = function(){
                    return entry.name;
                };
            }else{
                $scope.getNoName = function(){
                    return "<span class='notitle'>Sin título</span>";
                };
            }

            $scope.getCategory = function(category_id){
                return $scope.catMan.GetCategory(category_id);
            };

            var modal = Lightbox.openModal(images, index, {size:'lg', scope: $scope, resolve:{
                field: function(){
                    return images;
                }
            }});
            modal.result.then(function () {
            }, function () {
            });
        };
    })
    .controller('entryController',function($document,$scope,$rootScope,rootUrl,currentBaseUrl,$window, $location,$http,$uibModal,$timeout,$filter,$route,AuthService,CategoryManager, UsersData, userInscriptions, metadataFieldsConfig, Flash, hotkeys, contest, entry, metadataFields, reachedMaxEntries){
        /*if(entry.success == false){
            $location.path('/home');
        }*/
        $scope.selectedTickets = [];
        $scope.uData = UsersData;
        $scope.contest = contest;
        $scope.categories = $scope.contest.categories;
        $scope.categoriesList = $scope.contest.children_categories;
        $scope.userInscriptions = userInscriptions;
        $scope.entry_metadata_fields = metadataFields.data;
        $scope.metadata_values = [];
        $scope.columnsAndLabels = [];
        $scope.signupEndTimeAgo = $filter('amSubtract')(userInscriptions.GetInscriptionDeadlines($scope.contest.id), userInscriptions.ServerOffset(), 'seconds');

        $scope.inTimeForInscriptions = function(){
            return !!userInscriptions.GetInscriptionDeadlines($scope.contest.id, true);
        };
        var maxReached = parseInt(reachedMaxEntries.data) !== 0;
        $scope.reachedMaxEntries = function(){
            return maxReached;
        };

        $scope.viewOptions = {hideDescriptions: false};

        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories($scope.contest.children_categories);

        $scope.entry = entry != null ? entry.data : {categories_id:[], entry_metadata_values:[]};
        $scope.errors = $scope.entry.errors;
        $scope.newEntry = !$scope.entry.id;

        if($scope.userInscriptions.duplicate_category_id != null){
            $scope.entry.categories_id.push($scope.userInscriptions.duplicate_category_id);
            $scope.userInscriptions.duplicate_category_id = null;
        }
        if($scope.userInscriptions.duplicate_metadata_fields != null){
            $scope.entry.entry_metadata_values = $scope.userInscriptions.duplicate_metadata_fields;
            $scope.userInscriptions.duplicate_metadata_fields = null;
        }

        $scope.inscriptionType = $scope.entry.inscription_type ? $scope.entry.inscription_type : null;

        var firstVoteSent = false;

        $scope.$watch(function(){ return; }, function(newval, oldval) {
            // when you bind it to the controller's scope, it will automatically unbind
            // the hotkey when the scope is destroyed (due to ng-if or something that changes the DOM)
            hotkeys.bindTo($scope)
                .add({
                    combo: 'left',
                    description: 'previous',
                    callback: function() {
                        $scope.previous();
                    }
                }).add({
                    combo: 'right',
                    description: 'next',
                    callback: function() {
                        $scope.next();
                    }
                });
        }, true);

        $scope.voteResult = {};
        $scope.BeforeVoteUpdate = function(entry, voteResult){
            $scope.voteResult = voteResult;
        };
        var havingErrors = false;
        var entryBeforeVote = null;
        $scope.$watch(function(){ return $scope.entry.votes; }, function(newval, oldval) {
            if(havingErrors) {
                havingErrors = false;
                return;
            }
            if(!firstVoteSent){
                firstVoteSent = true;
                return;
            }
            entryBeforeVote = angular.copy(oldval);
            $scope.voteResult.error = false;
            $http.post(currentBaseUrl+'entryCategoryVote',{id: $scope.voteSession.code, vote: $scope.entry.votes, entryId: $scope.entry.id}).success(function(data){
            }).error(function(){
                $scope.voteResult.error = true;
                havingErrors = true;
                $scope.entry.votes = entryBeforeVote;
            });
        }, true);
        
        $scope.hideDescriptions = function(){
            if($scope.voteSession) return;
            $scope.viewOptions.hideDescriptions = !$scope.viewOptions.hideDescriptions;
        };

        $scope.next = function () {
            if($scope.nextEntry){
                if($scope.voteSession) {
                    window.location.href = '#/entry/vote/' + $scope.voteSessionCode + '/' + $scope.nextEntry;
                }
                if(!$scope.voteSession){
                    window.location.href = '#/entry/' + $scope.nextEntry;
                }
            }
        };

        $scope.previous = function () {
            if($scope.previousEntry){
                if($scope.voteSession) window.location.href = '#/entry/vote/' + $scope.voteSessionCode + '/' + $scope.previousEntry;
                if(!$scope.voteSession) window.location.href = '#/entry/' + $scope.previousEntry;
            }
        };

        $scope.entriesIds = userInscriptions.entriesIds;
        $scope.showOnlyCat = entry && !!entry.data.votingSession ? userInscriptions.selectedCat : null;

        if($scope.entriesIds && !$scope.newEntry){
            var entryId = $filter('filter')($scope.entriesIds, {id: $scope.entry.id})[0];
            $scope.currentPosition = $scope.entriesIds.indexOf(entryId);
            if($scope.currentPosition + 1 === $scope.entriesIds.length){
                $scope.nextEntry = $scope.entriesIds[0].id;
            }
            else{
                $scope.nextEntry = $scope.entriesIds[$scope.currentPosition + 1].id;
            }
            if($scope.currentPosition  > 0) {
                $scope.previousEntry = $scope.entriesIds[$scope.currentPosition - 1].id;
            }
            else if($scope.currentPosition  === 0){
                $scope.previousEntry = $scope.entriesIds[$scope.entriesIds.length - 1].id;
            }
        }

        $scope.sifter = userInscriptions.GetColaboratorStatus('sifter');
        $scope.editPermit = userInscriptions.GetColaboratorStatus('edit');
        $scope.viewer = userInscriptions.GetColaboratorStatus('viewer');

        $scope.getEntryTotalErrors = function(){
            var c = 0;
            angular.forEach($scope.errors, function(v, i){
                c += v.length;
            });
            return c;
        };
        $scope.entryPermitsEdit = function(){
            if($scope.sifter == true && $scope.editPermit == true) return true;
            if($scope.sifter == false && $scope.editPermit == true) return true;
            if($scope.sifter == true && $scope.editPermit == false) return false;
            return false;
        };

        $scope.showPayments = function(entry){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: currentBaseUrl+'view/entryPayments',
                controller: 'viewPaymentsCtrl',
                resolve: {
                    entry: function () {
                        return entry;
                    },
                    fields: function () {
                        return {};
                    },
                    categories: function(){
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                entry.status = result.status;
                entry.billings = result.billings;
            });
        };
        $scope.unreadMessages = function(entryLog){
            $scope.result = 0;
            if(entryLog){
                if(entryLog.length > 0){
                    angular.forEach(entryLog, function(log){
                        if(log.read_by != null){
                            if(log.read_by.search('"'+$rootScope.currentUser.id.toString()+'"') == -1){
                                $scope.result++;
                            }
                        }
                        else{
                            $scope.result++;
                        }
                    })
                }
            }
            return $scope.result;
        };

        $scope.showLog = function(){
            if($scope.voteSession) return;
            var errorLogModal = $uibModal.open({
              templateUrl: 'entryLog.html',
              controller: 'entryLogController',
              resolve: {
                  entry: function() {
                      return $scope.entry;
                  },
                  fields: function () {
                      return $scope.entry_metadata_fields;
                  },
                  entryLog: function(){
                      return $http.post(currentBaseUrl+'entryLog',{id: $scope.entry.id}).success(function(data){
                          return data.entryLog;
                      });
                  }

              }
          });
            errorLogModal.result.then(function () {})
        };
        $scope.showIncomplete = function(entry){
          var errorLogModal = $uibModal.open({
              templateUrl: 'entryIncomplete.html',
              controller: 'entryIncompleteController',
              resolve: {
                  entry: function() {
                      return entry;
                  },
                  fields: function () {
                      return $scope.entry_metadata_fields;
                  }
              }
          });
            errorLogModal.result.then(function () {})
        };

        /*$scope.printModal = function(entry){
            console.log(currentBaseUrl);
            $http.get(currentBaseUrl+'export-pdf/'+entry.id).success(function(data){
            });

            /*var errorLogModal = $uibModal.open({
                templateUrl: 'entryPrint.html',
                controller: 'entryPrintController',
                size: 'print',
                resolve: {
                    entry: function() {
                        return entry;
                    },
                    entry_metadata_fields: function () {
                        return $scope.entry_metadata_fields;
                    },
                    selectedTemplates: function() {
                        return $scope.selectedTemplates;
                    },
                    categories: function(){
                        return $scope.categories;
                    },
                    columnsAndLabels: function(){
                        return $scope.columnsAndLabels;
                    }

                }
            });
            errorLogModal.result.then(function () {})
        };*/

        /****************Change Entry Status and Payments ***********************/
        //************** if status is null, is used to only pay ******************
        //************** if status = finalize, checks if it needs to be pay ******

        $scope.changeStatus = function(entry, status){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: status != null ? 'entrySifter.html' : currentBaseUrl+'view/payEntry',
                controller: 'changeEntryStatusCtrl',
                resolve: {
                    data: function ($http) {
                        if(!entry.length){
                            $scope.aux = [];
                            $scope.aux.push(entry);
                            entry = $scope.aux;
                        }
                        var data = {
                            entries: entry,
                            status: status,
                            onlyPay: status == null ? true : false
                        };
                        return $http.post(currentBaseUrl + 'filterChangeEntryStatus', data).then(function (response) {
                            return response.data;
                        });
                    },
                    status: function () {
                        status === null ? status = entry.status : status;
                        return status;
                    },
                    categories: function(){
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
                angular.forEach(result.returnEntries, function(value){
                    $scope.entry.status = value.status;
                    $scope.entry.billings = value.billings;
                });
            });
        };

        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.selectedTemplates = [];
        $scope.rebuildSelectedTemplates = function(){
            $scope.selectedTemplates = [];
            angular.forEach($scope.entry.categories_id, function(val, ind){
                var cat = $scope.getCategory(val);
                if(!cat) return;
                if ($scope.selectedTemplates.indexOf(cat.template_id) == -1) $scope.selectedTemplates.push(cat.template_id);
            });
        };

        $scope.checkEntryPerCategory = function(){
            if($scope.contest.single_category == 1){
                if($scope.entry.categories_id.length > 0) return false;
            }
            return true;
        };
        $scope.checkSingleCategory = function(){
            return $scope.contest.single_category == 1;
        };

        $scope.addCategory = function (category) {
            if($scope.checkSingleCategory()){
                $scope.entry.categories_id = [];
                $scope.entry.categories_id.push(category.id);
            }else if($scope.entry.categories_id) {
                if ($scope.entry.categories_id.indexOf(category.id) == -1) $scope.entry.categories_id.push(category.id);
            }else if($scope.entry.selected_cat){
                $scope.entry.categories_id = [];
                $scope.entry.categories_id.push($scope.entry.selected_cat);
            }
            $scope.rebuildSelectedTemplates();
        };

        $scope.duplicateEntry = function(category){
            angular.forEach($scope.entry.entry_metadata_values, function(metadata){
                metadata.entry_id = null;
                metadata.id = null;
            });
            userInscriptions.duplicate_category_id = category.id;
            userInscriptions.duplicate_metadata_fields = $scope.entry.entry_metadata_values;
            $location.path('/entry');
        };

        if(entry != null) {
            $scope.inscription = [];
            $scope.inscription.role = entry.data.role;
            if(entry.data.votingSession != null) {
                $scope.voteSession = entry.data.votingSession;
                $scope.voteSessionCode = $scope.voteSession.code;
                if(entry.data.votes) {
                    if(entry.data.votes.abstain == false){
                        //TODO voto simple, entries en varias categorias
                    }
                    //$scope.vote = entry.data.votes;
                }
            }
            if(entry.data.cat != null){
                $scope.addCategory(entry.data.cat);
                $scope.entry.entry_metadata_values = [];
            }
        }else{
            $scope.inscription = userInscriptions.GetRoleInscription(userInscriptions.Inscriptor);
        }

        $scope.removeCategory = function(category){
            if ($scope.entry.categories_id.indexOf(category.id) != -1) $scope.entry.categories_id.splice($scope.entry.categories_id.indexOf(category.id), 1);
            $scope.rebuildSelectedTemplates();
        };

        $scope.initial = angular.copy($scope.entry);
        $scope.showStatic = angular.isDefined($scope.entry.id);
        if(entry == null) {
            $scope.entry.user = $rootScope.currentUser;
        }
        
        $scope.firstTabIndex = -1;
        $scope.getFirstTabIndex = function(){
            for(var i=0; i< $scope.entry_metadata_fields.length; i++){
                if($scope.entry_metadata_fields[i].type == metadataFieldsConfig.Type.TAB){
                    $scope.firstTabIndex = i;
                    return;
                }
            }
            $scope.firstTabIndex = -1;
        };
        $scope.isTab = function(field){
            return field.type == metadataFieldsConfig.Type.TAB;
        };
        $scope.getTabs = function(){
            return $filter('filter')($scope.entry_metadata_fields, {type: metadataFieldsConfig.Type.TAB});
        };
        $scope.getTabIndex = function(tab){
            for(var i=0; i< $scope.entry_metadata_fields.length; i++){
                if($scope.entry_metadata_fields[i] == tab){
                    return i;
                }
            }
            return 0;
        };
        $scope.getNextTabIndex = function(tab){
            var from = $scope.getTabIndex(tab) + 1;
            for(var i = from; i < $scope.entry_metadata_fields.length; i++){
                if($scope.entry_metadata_fields[i].type == metadataFieldsConfig.Type.TAB){
                    return i;
                }
            }
            return i;
        };
        $scope.getTabMetadata = function(tab){
            var from = $scope.getTabIndex(tab) + 1;
            var to = $scope.getNextTabIndex(tab) - 1;
            var l = [];
            for(var i = from; i <= to; i++){
                l.push($scope.entry_metadata_fields[i]);
            }
            return l;
        };
        $scope.getPreTabMetadata = function(){
            var from = 0;
            var l = [];
            for(var i = from; i < $scope.firstTabIndex; i++){
                l.push($scope.entry_metadata_fields[i]);
            }
            return l;
        };
        $scope.getFirstTabIndex();

        //var ua = navigator.userAgent.toLowerCase();
        //var isSafari = ua.indexOf('safari') != -1 && ua.indexOf('chrome') == -1;

        $scope.getMetadata = function(entry_metadata_field_id, allvalues){
            var fFiles = [];
            var fValue = [];

            var savedMetadataId = null;
            var res = $filter('filter')($scope.entry.entry_metadata_values, {entry_metadata_field_id:entry_metadata_field_id}, true);
            res.forEach(function(value){
                if(value['id'] != null)
                    savedMetadataId = value['id'];
                if(value['files'] != 0){
                    fFiles = value['files'];
                    fValue = [];
                }
                else{
                    if(value['value']){
                        if(value['value']['label']) {
                            //var filter = $filter('filter')($scope.entry_metadata_fields, {id: entry_metadata_field_id});
                            if ($scope.columnsAndLabels[value['entry_metadata_field_id']]) {
                                $scope.columnsAndLabels[value['entry_metadata_field_id']][value['value']['label']] = value['value']['value'];
                            }
                            else{
                                var item = {};
                                item[value['value']['label']] = value['value']['value'];
                                $scope.columnsAndLabels[value['entry_metadata_field_id']] = item;
                            }
                        }
                        fValue.push(value['value']);
                        /*if(isSafari){
                            var regex = /[0-9]{1,4}-[0-9]{1,2}-[0-9]{1,2} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2}/g;
                            console.log(value['value']);
                            if (regex.exec(value['value']) !== null) {
                                console.log(value['value'].split("-").join("/"));
                                fValue.push(value['value'].split("-").join("/"));
                            }else{
                                fValue.push(value['value']);
                            }
                        }else{
                            fValue.push(value['value']);
                        }*/
                    }
                }
            });

            if(angular.isDefined(res[0])){
                return allvalues ? res : res[0];
            }

            var o = {entry_metadata_field_id: entry_metadata_field_id, value: fValue, files:fFiles, id:savedMetadataId};
            $scope.entry.entry_metadata_values.push(o);
            return o;
        };
        //var firstEditableField = null;
        $scope.updateEntryFields = function() {
            $scope.entry_metadata_fields.every(function (element, index, array) {
                //if(firstEditableField == null && metadataFieldsConfig.Editables.indexOf(element.type) != -1) firstEditableField = element;
                element.model = $scope.getMetadata(element.id, false);
                element.allmodels = $scope.getMetadata(element.id, true);
                return true;
            });
        };
        $scope.updateEntryFields();

        $scope.isFieldHidden = function(field){
            if($scope.selectedTemplates.length == 0) return false;
            for(var i =0; i<$scope.selectedTemplates.length; i++){
                if($scope.selectedTemplates[i] == null) return false;
                var conf = $filter('filter')(field.entry_metadata_config_template, {template_id:$scope.selectedTemplates[i]});
                if(angular.isDefined(conf[0]) && conf[0].visible) return false;
            }
            return true;
        };
        $scope.isFieldRequired = function(field){
            //if($scope.isTitleField(field)) return true;
            if($scope.selectedTemplates.length == 0) return !!field.required == 1;
            for(var i =0; i<$scope.selectedTemplates.length; i++){
                if($scope.selectedTemplates[i] == null && !!field.required == 1) return true;
                var conf = $filter('filter')(field.entry_metadata_config_template, {template_id:$scope.selectedTemplates[i]});
                if(angular.isDefined(conf)) if(angular.isDefined(conf[0]) && conf[0].required) return true;
            }
            return false;
        };
        $scope.isTitleField = function(field){
            return field == firstEditableField;
        };

        $scope.reset = function(){
            $scope.showStatic = true;
            $scope.entry = angular.copy($scope.initial);
            if ($scope.entryForm) $scope.entryForm.$setPristine();
        };
        $scope.getName = function(){
            return $scope.entry.name;
            /*var val = "";
            console.log($scope.entry_metadata_fields);
            console.log(metadataFieldsConfig);
            $scope.entry_metadata_fields.every(function(element, index, array){
                if(metadataFieldsConfig.Editables.indexOf(parseInt(element.type))!= -1){
                    console.log("VALUEEE");
                    console.log(element);
                    val = $scope.getMetadata(element.id).value;
                    console.log(val);
                    if($scope.voteSession && $scope.voteSessionCode) $scope.entry_metadata_fields[index].entry_metadata_config_template = [];
                    return false;
                }
                return true;
            });
            return val == "" ? false : val;*/
        };

        $scope.getNoName = function(){
            return "Sin título";
        };

        $scope.edit = function() {
            $scope.showStatic = false;
            $scope.flash = null;
        };

        $scope.save = function(){
            //$scope.errors = [];
            var data = {
                id: $scope.entry.id,
                user: $scope.entry.user,
                metadata: $scope.entry.entry_metadata_values,
                categories: $scope.entry.categories_id,
                columnsAndLabels: $scope.columnsAndLabels
            };

            $scope.sending = true;
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(currentBaseUrl+'entry', data).success(function(data){
                $scope.sending = false;
                if(data.errors){
                    $scope.errors = data.errors;
                    $scope.entry.errors = data.errors;
                    $scope.showIncomplete($scope.entry);
                    Flash.clear($scope);
                }else if(data.error){
                    Flash.show(data.error, 'danger', $scope);
                }else{
                    //$window.location.reload();
                    $scope.sent = true;
                    $scope.entry = data.entry;
                    $scope.errors = data.entry.errors;
                    $scope.updateEntryFields();
                    $scope.initial = angular.copy($scope.entry);
                    $scope.showStatic = true;
                    $scope.metadata_values = [];
                    if ($scope.entryForm) $scope.entryForm.$setPristine();
                    Flash.show(data.flash, 'success', $scope);
                    $location.path('/entry/'+$scope.entry.id);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };
        $scope.onunloads = [];
        $window.onbeforeunload = function(){
            var ret = '';
            $scope.onunloads.forEach(function (fn) { if(fn && fn()) ret += fn(); });
            if(ret != '') return ret;
        };

        $scope.delete = function(entry) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl+'view/deleteEntry',
                controller: 'entryDeleteCtrl',
                resolve: {
                    captchaUrl: function($http){
                        return $http.get(rootUrl+'captcha/url').success(function(data){
                            return data.data;
                        });
                    },
                    entry: function(){
                        return entry;
                    },
                    fields: function () {
                        return $scope.entry_metadata_fields;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $location.path('/entries');
            }, function () {});
        };

        /*$scope.judgePrivate = function(field){
            if(judge && $scope.voteSessionCode){
               if(field.private == 1) return false;
            }
            return true;
        };*/

        $scope.rebuildSelectedTemplates();
        $scope.showForm = function(user) {
            var modalInstance = $uibModal.open({
                keyboard : true,
                templateUrl: currentBaseUrl+'view/inscription-form',
                controller: 'showInscriptionFormCtrl',
                resolve: {
                    fields: function () {
                        return contest.inscription_metadata_fields;
                    },
                    inscription: function ($http) {
                        return $http.post(currentBaseUrl + 'inscriptionForm', {user_id: user.id}).then(function (response) {
                            return response.data;
                        });
                    },
                    user: function(){
                        return user;
                    }
                },
                scope: $scope
            });
        };

        $scope.categoriesTotal = 0;

        $scope.AddTicketToCart = function(category){
            category.selected = !category.selected;
            category.selected ? category.totalTickets = 1 : category.totalTickets = 0;
            var index = $scope.selectedTickets.indexOf(category.id);
            if (index != -1) {$scope.selectedTickets.splice(index, 1);}
            else $scope.selectedTickets.push(category.id);
            if(category.selected == true){
                category.totalPriceTickets = category.price;
                $scope.categoriesTotal = $scope.categoriesTotal + category.price;
            }
            if(category.selected == false){
                $scope.categoriesTotal = $scope.categoriesTotal - category.totalPriceTickets;
            }
        };


        if($scope.entry.selectedTickets){
            angular.forEach($scope.entry.selectedTickets, function(cat){
                $scope.AddTicketToCart(cat);
            })
        }else $scope.selectedTickets =  [];


        $scope.moreOrLessTickets = function(category, more){
            if(more == true){
                category.totalTickets ++;
                category.totalPriceTickets = category.totalPriceTickets + category.price;
                $scope.categoriesTotal = $scope.categoriesTotal + category.price;
            }
            if(more == false && category.totalTickets > 1){
                category.totalTickets --;
                category.totalPriceTickets = category.totalPriceTickets - category.price;
                $scope.categoriesTotal = $scope.categoriesTotal - category.price;
            }
        }

        $scope.buyTickets = function(){
            if($scope.selectedTickets.length == 0) return;
            var tickets = [];
            var totalTickets = 0;
            angular.forEach($scope.contest.children_categories, function(ticket){
                if(ticket.selected && ticket.selected == true){
                    tickets.push(ticket);
                    totalTickets = totalTickets + ticket.totalTickets;
                }
            });
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: currentBaseUrl+'view/payTicket',
                controller: 'buyTicketCtrl',
                resolve: {
                    tickets: function () {
                        return tickets;
                    },
                    total: function(){
                        return $scope.categoriesTotal;
                    },
                    totalTickets: function(){
                        return totalTickets;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(!result) return;
            });
        }

    })
    .controller('paymentsController', function($scope, $rootScope, $timeout, rootUrl, currentBaseUrl, $http, $uibModal, CategoryManager, Flash, contest, userInscriptions){
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};

        $scope.billingIncomplete = $scope.billingComplete = $scope.billingError = $scope.billingProcessing =
            $scope.incompleteMoney = $scope.completeMoney = $scope.errorMoney = $scope.processingMoney = 0;
        angular.forEach($scope.billing, function(item){
            $scope.currency = item.currency;
            if(item.status == 0){
                $scope.billingIncomplete = item.total;
                $scope.incompleteMoney = item.totalBilling;
            }
            if(item.status == 1){
                $scope.billingComplete = item.total;
                $scope.completeMoney = item.totalBilling;
            }
            if(item.status == 2){
                $scope.billingError = item.total;
                $scope.errorMoney = item.totalBilling;
            }
            if(item.status == 5){
                $scope.billingProcessing = item.total;
                $scope.processingMoney = item.totalBilling;
            }
        });

        $scope.totalBilling = parseFloat($scope.incompleteMoney) + parseFloat($scope.completeMoney) + parseFloat($scope.processingMoney);

        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories(contest.categories);
        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.updateAllPagesList = function(){
            $http.post(rootUrl + $scope.contest.code + '/payments', $scope.pagination).then(function(response){
                $scope.billings = [];
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.billings = response.data.data;
                $scope.pagination.filters = response.data.filters;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; }$scope.updateAllPagesList();});
        $scope.updateAllPagesList();

        $scope.statusFilters = [];
        $scope.toggleFilterBy = function(status){
            var index = $scope.statusFilters.indexOf(status);
            if (index != -1) {$scope.statusFilters.splice(index, 1);}
            else $scope.statusFilters.push(status);
            $scope.pagination.filters = $scope.statusFilters;
            $scope.updateAllPagesList();
        };

        if(userInscriptions.billingSelected != null){
            $scope.toggleFilterBy(userInscriptions.billingSelected);
        }

        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
            $scope.updateAllPagesList();
        };
    })
    .controller('paymentController', function($scope, Flash, $http, rootUrl, $uibModal, CategoryManager, contest, billData, categoriesData,currentBaseUrl){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.contest = contest;
        $scope.bill = billData.bill;
        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories(categoriesData.data.categories);
        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };
    })
    .controller('entryPrintController', function($scope, rootUrl, currentBaseUrl, $sanitize, $location, $http, Flash, $uibModalInstance, entry, entry_metadata_fields, $filter, columnsAndLabels, categories, selectedTemplates, metadataFieldsConfig){
        $scope.close = function() {
            $uibModalInstance.dismiss();
        };

        $scope.showStatic = true;

        $scope.checkIfValues = function(values, label){
            var hasValue = false;
            if(label == null){
                angular.forEach(values, function(val){
                    if(hasValue) return;
                    if(val.value.value){
                        if(val.value.value.length > 0 || Object.keys(val.value.value).length > 0)
                            hasValue = true;
                    }
                });
            }
            if(label != null){
                angular.forEach(values, function(val){
                    if(hasValue) return;
                    if(val.value.label == label){
                        var arrayValues = [];
                        for (var key in val.value.value) {
                            var tempObj = {};
                            tempObj[key] = val.value.value[key];
                            arrayValues.push(tempObj);
                        }
                        if(arrayValues.length > 0){
                            hasValue = true;
                        }
                    }
                });
            }
            return hasValue;

        };

        $scope.print = function () {
            window.print();
        };

        $scope.getPages = function(){
            return Math.ceil($(".printEntry").height()/1100);
        };

        $scope.entry = entry;
        $scope.entry_metadata_fields = entry_metadata_fields;
        $scope.columnsAndLabels = columnsAndLabels;
        $scope.cancel = function(){
            $uibModalInstance.close();
        };
        $scope.selectedTemplates = selectedTemplates;
        $scope.categories = categories;

        $scope.entry = entry != null ? entry : {categories_id:[], entry_metadata_values:[]};

        $scope.getCategory = function(category_id){
            var res = $filter('filter')($scope.categories, {id:category_id}, true);
            if(angular.isDefined(res[0])) return res[0];
            return null;
        };

        $scope.isTab = function(field){
            return field.type == metadataFieldsConfig.Type.TAB;
        };

        $scope.isFieldHidden = function(field){
            if($scope.selectedTemplates.length == 0) return false;
            for(var i =0; i<$scope.selectedTemplates.length; i++){
                if($scope.selectedTemplates[i] == null) return false;
                var conf = $filter('filter')(field.entry_metadata_config_template, {template_id:$scope.selectedTemplates[i]});
                if(angular.isDefined(conf[0]) && conf[0].visible) return false;
            }
            return true;
        };
    })
    .controller('entryDeleteCtrl',function($scope,$timeout,$window,$http,$uibModalInstance,Flash,currentBaseUrl,userInscriptions, captchaUrl, entry, fields){
        $scope.captchaUrl = captchaUrl.data;
        $scope.entry = entry;
        $scope.fields = fields || {};
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };

        $scope.destroy = function() {
            $http.post(currentBaseUrl + 'deleteEntry', {id:entry.id, captcha: $scope.captcha}).success(function (response) {
                if(response.errors){
                    $scope.errors = response.errors;
                    $scope.captcha = '';
                    $scope.captchaUrl = response.captchaUrl;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data){

            });
        };
    })
    .controller('DeleteFileController',function($scope,currentBaseUrl,$http,$uibModalInstance,file,captchaUrl,tech){
        $scope.file = file;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.captcha = '';
        $scope.destroy = function(){
            $scope.deleting = true;
            $http.post(currentBaseUrl + 'deleteFile', {id:$scope.file.id, captcha:$scope.captcha, tech: tech}).success(function (data) {
                $scope.deleting = false;
                if(data.errors){
                    $scope.errors = data.errors;
                    if(angular.isDefined(data.captchaUrl)){
                        $scope.captcha = '';
                        $scope.captchaUrl = data.captchaUrl;
                    }
                }else {
                    $uibModalInstance.close();
                }
            }).error(function(data){
                $scope.deleting = false;
                if(angular.isDefined(data.captchaUrl)){
                    $scope.captchaUrl = data.captchaUrl;
                }
            });
        };
        $scope.captchaUrl = captchaUrl.data;
    })
    .controller('DownloadFilesController',function($scope,currentBaseUrl,$http,$uibModalInstance, files, contest){
        $scope.filesRows = files.data.files;
        $scope.originals = 0;
        $scope.encodes = 0;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.fileName = '';
        $scope.param1 = $scope.param2 = $scope.param3 = $scope.param4 = $scope.param5 = 0;
        $scope.addToName = function(param){
            if(param == 'fileVersion.extension')
                $scope.fileName += '.'+param;
            else  $scope.fileName += ' - '+param;
            switch(param){
                case 'entry.id': $scope.param1 = !$scope.param1; break;
                case 'fileVersion.id': $scope.param2 = !$scope.param2; break;
                case 'entry.label': $scope.param3 = !$scope.param3; break;
                case 'file.name': $scope.param4 = !$scope.param4; break;
                case 'fileVersion.extension': $scope.param5 = !$scope.param5; break;
            }
        }

        $scope.getName = function(param1,param2,param3,param4,param5){
            var aux = '';
            if($scope.param1) aux += param1;
            if($scope.param2) aux += ' '+param2;
            if($scope.param3) aux += ' '+param3;
            if($scope.param4) aux += ' '+param4;
            if($scope.param5) aux += '.'+param5;
            return aux;
        }

        $scope.deleteValues = function(){
            $scope.fileName = '';
            $scope.param1=$scope.param2=$scope.param3=$scope.param4=$scope.param5=0;
        }

        let downloadData = [];
        angular.forEach($scope.filesRows, function(files){
            if(files.contest_file_versions[1] && files.entry_metadata_values[0]){
                downloadData.push({"id": "contests/"+contest.id+"/"+files.contest_file_versions[1].id+"."+files.contest_file_versions[1].extension,
                "name": files.entry_metadata_values[0].categ_name+"/"+files.entry_metadata_values[0].id+" - "+files.name+"."+files.contest_file_versions[1].extension})
            }else if(files.contest_file_versions[0] && files.entry_metadata_values[0]){
                downloadData.push({"id": "contests/"+contest.id+"/"+files.contest_file_versions[0].id+"."+files.contest_file_versions[0].extension,
                    "name": files.entry_metadata_values[0].categ_name+"/"+files.entry_metadata_values[0].id+" - "+files.name+"."+files.contest_file_versions[0].extension})
            }
        });

        console.log(downloadData);

        $scope.descargarJSON = function() {
            var json = JSON.stringify(downloadData);
            var blob = new Blob([json], {type: "application/json"});
            var url = URL.createObjectURL(blob);

            var a = document.createElement("a");
            a.href = url;
            a.download = "archivo.json";
            a.click();
        };

    })
    .controller('TermsCtrl',function($scope,$uibModalInstance){
        $scope.close = function(){
            $uibModalInstance.close();
        };
    })
    .controller('LightboxCtrl',function($scope, Lightbox){
        $scope.Lightbox = Lightbox;
    })
    .controller('collectionController',function($scope, $location, $http, collection, currentBaseUrl){
        if(collection.closed === true) collection = [];
        $scope.loading = false;
        $scope.collection = collection.data.collection;
        $scope.entries = collection.data.entries;
        //$scope.entries = $scope.allEntries.slice(0,20);
        $scope.categories = collection.data.categories;
        $scope.prizes = collection.data.prizes;
        $scope.showMenuBar = true;
        $scope.showCategories = false;
        $scope.showSearch = false;
        $scope.showPrizes = false;
        $scope.selectedCategory = null;
        $scope.selectedPrizes = [];
        let selectedCat = null;
        $scope.config = $scope.collection.config;
        $scope.pagination = {
            query: null,
            prizes: []
        };

        let postCollection = function(){
            console.log($scope.pagination);
            $http.post(currentBaseUrl+'collectionEntries',{categoryId: selectedCat, pagination: $scope.pagination, votingSessionId: $scope.collection.voting_session_id, code: $scope.collection.code}).success(function(data){
            }).success(function(data){
                $scope.entries = data;
                $scope.loading = false;
            }).error(function(){
                $scope.loading = false;
            });
        }

        $scope.$watch('pagination.query', function(){
            $scope.showCategories = false;
            if($scope.pagination.query === null) return;
            $scope.loading = true;
            cat = {id: null, parent_id: null};
            $scope.toggleCat(cat);
            postCollection();
        });

        function closeChildren(cat, childCats, final){
            for(var cc in childCats){
                if(cat.parent_id !== childCats[cc].id && cat.id !== childCats[cc].id){
                    childCats[cc].open = false;
                }
                if(childCats[cc].children_categories.length > 0){
                    closeChildren(cat, childCats[cc].children_categories, childCats[cc].final);
                }
            }
        }

        $scope.selectPrize = function(prize){
            let index = $scope.pagination.prizes.indexOf(prize.id);
            if(index != -1){
                $scope.pagination.prizes.splice(index, 1);
                $scope.selectedPrizes.splice(index, 1);
            }else{
                $scope.pagination.prizes.push(prize.id);
                $scope.selectedPrizes.push(prize);
            }
            console.log($scope.pagination.prizes);
            postCollection();

        }

        $scope.toggleCat = function(cat, v){
            $scope.loading = true;
            $scope.category = cat;
            if(cat.id !== null){
                $scope.showSearch = false;
                $scope.pagination.query = null;
            }
            if(cat.open === true && cat.final === 1) {
                $scope.loading = false;
                return;
            }
            for(var c in $scope.categories){
                if(cat.id !== $scope.categories[c].id && cat.parent_id === null) {
                    $scope.categories[c].open = false;
                }
                if($scope.categories[c].children_categories.length > 0){
                    closeChildren(cat, $scope.categories[c].children_categories, cat.final);
                }
            }
            cat.open = v == null ? !cat.open : !!v;
            selectedCat = cat.id;
            $scope.selectedCategory = cat.name;
            if(cat.id === null){
                $scope.loading = false;
                return;
            }
            postCollection();
        };

        $scope.openEntryInList = function(entry) {
            var path = currentBaseUrl + "#/collection/" + $scope.collection.code + "/" + entry.id;
            window.open(path, '_blank');
            window.focus();
        }
    })
    .controller('collectionEntryController',function($document,$scope,$rootScope,rootUrl,currentBaseUrl,$window, $location,$http,$uibModal,$timeout,$filter,$route,AuthService,CategoryManager, UsersData, userInscriptions, metadataFieldsConfig, Flash, hotkeys, contest, entry, metadataFields){
        $scope.entry = entry.data;
        //$scope.entry_metadata_fields = $scope.entry.entry_metadata_values;
        $scope.entry_metadata_fields = metadataFields.data;
        $scope.showStatic = true;
        $scope.viewOptions = {hideDescriptions: true};

        $scope.getMetadata = function(entry_metadata_field_id, allvalues) {
            var fFiles = [];
            var fValue = [];

            var savedMetadataId = null;
            var res = $filter('filter')($scope.entry.entry_metadata_values, {entry_metadata_field_id: entry_metadata_field_id}, true);

            if(angular.isDefined(res[0])){
                return allvalues ? res : res[0];
            }

            var o = {entry_metadata_field_id: entry_metadata_field_id, value: fValue, files:fFiles, id:savedMetadataId};
            $scope.entry.entry_metadata_values.push(o);
            return o;
        }

        $scope.updateEntryFields = function() {
            $scope.entry_metadata_fields.every(function (element) {
                element.model = $scope.getMetadata(element.id, false);
                element.allmodels = $scope.getMetadata(element.id, true);
                return true;
            });
        };
        $scope.updateEntryFields();

        $scope.firstTabIndex = -1;
        $scope.getFirstTabIndex = function(){
            for(var i=0; i< $scope.entry_metadata_fields.length; i++){
                if($scope.entry_metadata_fields[i].type == metadataFieldsConfig.Type.TAB){
                    $scope.firstTabIndex = i;
                    return;
                }
            }
            $scope.firstTabIndex = -1;
        };
        $scope.isTab = function(field){
            return field.type == metadataFieldsConfig.Type.TAB;
        };
        $scope.getTabs = function(){
            return $filter('filter')($scope.entry_metadata_fields, {type: metadataFieldsConfig.Type.TAB});
        };
        $scope.getTabIndex = function(tab){
            for(var i=0; i< $scope.entry_metadata_fields.length; i++){
                if($scope.entry_metadata_fields[i] == tab){
                    return i;
                }
            }
            return 0;
        };
        $scope.getNextTabIndex = function(tab){
            var from = $scope.getTabIndex(tab) + 1;
            for(var i = from; i < $scope.entry_metadata_fields.length; i++){
                if($scope.entry_metadata_fields[i].type == metadataFieldsConfig.Type.TAB){
                    return i;
                }
            }
            return i;
        };
        $scope.getTabMetadata = function(tab){
            var from = $scope.getTabIndex(tab) + 1;
            var to = $scope.getNextTabIndex(tab) - 1;
            var l = [];
            for(var i = from; i <= to; i++){
                l.push($scope.entry_metadata_fields[i]);
            }
            return l;
        };
        $scope.getPreTabMetadata = function(){
            var from = 0;
            var l = [];
            for(var i = from; i < $scope.firstTabIndex; i++){
                l.push($scope.entry_metadata_fields[i]);
            }
            return l;
        };
        $scope.getFirstTabIndex();
    })
    .controller('collectionKeyController',function($scope, $http, $location, $route, $window, rootUrl, contest, Flash, code, currentBaseUrl){
        $scope.sending = false;
        $scope.key = "";
        $scope.login = function(){
            $scope.sending = true;
            $http.post(currentBaseUrl+'collectionInvitation',{code: code, key: $scope.key }).success(function(data){
            }).success(function(data){
                if(data.success === false){
                    Flash.show(data.msg);
                }
                if(data.success === true){
                    /*$location.path('/collection/'+code);
                    $route.reload();*/
                    $window.location.href = rootUrl+contest.code+"/#collection/"+code;
                }
            });
            $scope.sending = false;
        };
    });



OxoAwards.config(function (rootUrl, LightboxProvider, isLogged) {
    LightboxProvider.fullScreenMode = true;

    LightboxProvider.getThumbUrl = function (image) {
        if(image) {
            return image.thumb;
        }
    };
    LightboxProvider.getImageUrl = function (image) {
        if(image && angular.isDefined(image.contest_file_versions)) {
            for(var i = 0; i < image.contest_file_versions.length; i++) {
                var version = image.contest_file_versions[i];
                if(parseInt(version.status) !== 2 || parseInt(version.source) === 1) continue;
                return image.contest_file_versions[i].url;
            }
            return image.thumb;
        }
    };
    LightboxProvider.getImageVersion = function (image) {
        if(image && angular.isDefined(image.contest_file_versions)) {
            for(var i = 0; i < image.contest_file_versions.length; i++) {
                var version = image.contest_file_versions[i];
                if(parseInt(version.status) !== 2 || parseInt(version.source) === 1) continue;
                return image.contest_file_versions[i];
            }
        }
    };
    LightboxProvider.getSources = function (image) {
        var o = [];
        if(image && angular.isDefined(image.contest_file_versions)) {
            for(var i = 0; i < image.contest_file_versions.length; i++) {
                var version = image.contest_file_versions[i];
                if(parseInt(version.status) !== 2 || (parseInt(version.source) === 1 && !LightboxProvider.isDocument(image) && version.extension !== "pdf")) continue;
                o.push({
                    file: version.url,
                    extension: version.extension,
                    label: version.label
                });
            }
        }
        return o;
    };
    LightboxProvider.isPlayable = function(image){
        return parseInt(image.status) === 2 && LightboxProvider.getSources(image).length > 0;
    };
    LightboxProvider.getImageThumb = function (image) {
        if(image) {
            return image.thumb;
        }
    };

    LightboxProvider.getImageCaption = function (image) {
        return image.name;
    };

    LightboxProvider.isVideo = function (image) {
        return image && parseInt(image.type) === 0;
    };

    LightboxProvider.isImage = function (image) {
        return image && parseInt(image.type) === 1;
    };

    LightboxProvider.isAudio = function (image) {
        return image && parseInt(image.type) === 2;
    };

    LightboxProvider.isDocument = function (image) {
        return image && parseInt(image.type) === 3;
    };

    LightboxProvider.calculateImageDimensionLimits = function (dimensions) {
        if (dimensions.windowWidth >= 768) {
            return {
                // 92px = 2 * (30px margin of .modal-dialog
                //             + 1px border of .modal-content
                //             + 15px padding of .modal-body)
                // with the goal of 30px side margins; however, the actual side margins
                // will be slightly less (at 22.5px) due to the vertical scrollbar
                'maxWidth': dimensions.windowWidth - 20,
                // 126px = 92px as above
                //         + 34px outer height of .lightbox-nav
                'maxHeight': dimensions.windowHeight - 77
            };
        } else {
            return {
                // 52px = 2 * (10px margin of .modal-dialog
                //             + 1px border of .modal-content
                //             + 15px padding of .modal-body)
                'maxWidth': dimensions.windowWidth - 20,
                // 86px = 52px as above
                //        + 34px outer height of .lightbox-nav
                'maxHeight': dimensions.windowHeight - 86
            };
        }
    };
    if(isLogged == true) LightboxProvider.templateUrl = rootUrl+'view/gallery-modal';
    if(isLogged == false) LightboxProvider.templateUrl = rootUrl+'view/gallery-modal-public';
});
