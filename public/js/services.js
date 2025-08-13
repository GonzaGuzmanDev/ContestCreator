OxoAwards.factory('Authenticate', function($resource,rootUrl){
        return $resource(rootUrl+"service/authenticate")
    })
    .factory('AuthService',function($rootScope, $timeout, rootUrl, $location, $http, Authenticate, Flash){
        var me = {
            login: function(user){
                //this.setCurrentUser(user);
                localStorage.authenticated = true;
                Flash.clear();
            },
            logout:function (msg) {
                Authenticate.get({},function(response){
                    localStorage.authenticated = false;
                    $rootScope.currentUser = null;
                    $rootScope.userInscriptions = null;
                    delete $rootScope.currentUser;
                    delete localStorage.authenticated;
                    $location.path('/');
                    /*if(msg != null) Flash.show(msg);
                    else Flash.show(response.flash);*/
                });
            },
            isLoggedIn:function () {
                return eval(localStorage.authenticated) && !!$rootScope.currentUser;
            },
            currentUser:function () {
                return $rootScope.currentUser;
            },
            setCurrentUser: function(user){
                $rootScope.currentUser = angular.extend(user, {picDate: new Date()});
            },
            checkAuth:function(successCallback, errorCallback){
                $http.get(rootUrl+'service/check').success(function(data, status, headers, config) {
                    localStorage.authenticated = !!data.auth;
                    if(me.isLoggedIn()) me.setCurrentUser(data.user);
                    if(angular.isDefined(successCallback)) successCallback();
                }).error(function(data){
                    localStorage.authenticated = false;
                    if(angular.isDefined(errorCallback)) errorCallback();
                });
            }
        };
        return me;
    })
    .factory('Flash', function($rootScope){
        return {
            show: function(message, status, scope){
                if(!angular.isDefined(scope)){
                    scope = $rootScope;
                }
                scope.flash = message;
                scope.flashStatus = angular.isDefined(status) ? status : 'danger';
            },
            clear: function(scope){
                if(!angular.isDefined(scope)){
                    scope = $rootScope;
                }
                scope.flash = "";
                scope.flashStatus = "";
            }
        }
    })
    .factory('Alert', function($uibModal){
        var modal;
        return {
            active: function(){ return _active; },
            show: function(title, icon, message, status){
                modal = $uibModal.open({
                    templateUrl: 'alert-modal.html',
                    controller: function($scope, $sce){
                        $scope.title = title;
                        $scope.icon = icon;
                        $scope.message = message;
                        $scope.status = angular.isDefined(status) ? status : 'default';
                        $scope.close = function(){
                            modal.dismiss();
                        };
                        $scope.trustSrc = function(src) {
                            return $sce.trustAsResourceUrl(src);
                        }
                    }
                });
                modal.result.then(function () {})
            },
            clear: function(){
                if(modal) modal.dismiss();
            }
        }
    })
    .factory('UsersData', function($http, $q, rootUrl){
        return {
            getData: function($term, $contest) {
                var deferred = $q.defer();
                $http.post(rootUrl + 'api/contest/usersData',{term: $term, contest: $contest}).success(function(response){
                    deferred.resolve(response.data);
                }).error(deferred.reject);
                return deferred.promise;
            }
        }
    })
    .factory('UserMedia',function($rootScope, $http, currentBaseUrl){
        var library = [];
        var me = {
            neverLoaded: true,
            lastUpdate: null,
            files: function () {
                return library;
            },
            update: function (successCallback, errorCallback) {
                $http.get(currentBaseUrl + 'files').success(function (response) {
                    library = response;
                    me.lastUpdate = new Date().getTime();
                    me.neverLoaded = false;
                    if (angular.isDefined(successCallback)) successCallback();
                }).error(function (response) {
                    if (angular.isDefined(errorCallback)) errorCallback();
                });
            }
        };
        return me;
    })
    .factory('SelectedCategories', function(){
        var data = [];
        var getData = function () {
            return data;
        };
        var setData = function (id) {
            var exist = data.indexOf(id);
            if(exist > -1) data.splice(exist, 1);
            else data = data.concat(id);
        };
        return {
            getData: getData,
            setData: setData
        }
    })
    .factory('CategoryManager', function($filter, contest){
        var catsList = {};
        var catsTree = null;
        var me = {};
        var addCategoriesList = function(cats){
            //catsList = catsList.concat(cats);
            for(var i =0;i<cats.length;i++){
                var c = cats[i];
                c.selected = false;
                catsList[c.id] = c;
                if(angular.isDefined(c.children_categories) && c.children_categories.length){
                    addCategoriesList(c.children_categories);
                }
            }
        };
        var updateParents = function(cats, parent){
            for(var i =0;i<cats.length;i++){
                var c = cats[i];
                if(parent){
                    c.parent = parent;
                }
                if(angular.isDefined(c.children_categories) && c.children_categories.length){
                    updateParents(c.children_categories, c);
                }
            }
        };
        me.GetCategoriesList = function(){
            return catsList;
        };
        me.SetCategories = function(cats){
            catsTree = cats;
            addCategoriesList(cats);
            updateParents(cats);
        };
        me.GetCategory = function(category_id) {
            //var res = $filter('filter')(catsList, {id: category_id});
            //if (angular.isDefined(res[0])) return res[0];
            if (angular.isDefined(catsList[category_id])) return catsList[category_id];
            return null;
        };
        me.GetPrice = function(category){
            if(!category) return 0;
            if((angular.isDefined(category.price) && category.price!= null && category.price!= '') || category.price == 0) return category.price;
            if(category.parent != null) return me.GetPrice(category.parent);
            if(contest.billing) {
                return contest.billing.mainPrice;
            }
        };
        me.GetTotalPrice = function(entry){
            var priceTotal = 0;
            for(var a = 0; a < entry.categories_id.length; a++){
                if(!me.mustPayCategory(entry, entry.categories_id[a])) continue;
                priceTotal += me.GetPrice(me.GetCategory(entry.categories_id[a]));
            }
            return priceTotal;
        };
        me.mustPayCategory = function(entry, cid){
            var cfound = false;
            angular.forEach(entry.billings, function(v,k){
                if(cfound) return;
                angular.forEach(v.billing_entry_categories, function(bv,bk){
                    if(cfound) return;
                    if(bv.category_id == cid && bv.entry_id == entry.id) cfound = true;
                })
            });
            return !cfound;
        };
        me.getBillingsStatus = function(entry){
            var s = 1;
            angular.forEach(entry.billings, function(bill,key){
                if(bill.status == 2) s = 2; //return 2
                if(bill.status == 3 && s != 2) s = 3;
                if(bill.status == 0 && s != 2 && s != 3) s = 0;
                if(bill.status == 5 && s != 2 && s != 3) s = 5;
            });
            return s;
        };
        me.getBillingsAlertType = function(entry){
                return me.getBillingAlertType(me.getBillingsStatus(entry));
            };
        me.getBillingAlertType = function(status){
                switch(status){
                    case 0: return "success";
                    case 1: return "info";
                    case 2: return "danger";
                    case 3: return "success";
                    case 5: return "warning";
                }
                return "default";
            };
        me.getBillingsPrice = function(bills, entryId){
            var p = 0;
            var aux = null;
            angular.forEach(bills, function(bill,key){
                if(aux == null || aux != bill.id){
                    if(bill.billing_entry_categories){
                        angular.forEach(bill.billing_entry_categories, function(category_bill,key){
                          if(category_bill.entry_id == entryId) p += parseFloat(category_bill.price);
                        })
                    }
                    else p += parseFloat(bill.price);
                    aux = bill.id;
                }
            });
            return p;
        };
        me.mustPayEntry = function(entry){
                return me.getDuePaymentCategories(entry).length > 0;
            };
        me.getDuePaymentCategories = function(entry){
            var cats = [];
            if(!entry.categories_id) return 0;
            for(var i=0; i<entry.categories_id.length;i++){
                var cid = entry.categories_id[i];
                var cfound = false;
                angular.forEach(entry.billings, function(v,k){
                    if(v.status == 2 || cfound) return;
                    angular.forEach(v.billing_entry_categories, function(bv,bk){
                        if(cfound) return;
                        if(bv.category_id == cid && bv.entry_id == entry.id) cfound = true;
                    })
                });
                if(!cfound){
                    cats.push(cid);
                }
            }
            return cats;
        };
        me.getDuePayment = function(entry){
            var price = 0;
            var cats = me.getDuePaymentCategories(entry);
            angular.forEach(cats, function(cid){
                price += me.GetPrice(me.GetCategory(cid));
            });
            return price;
        };
        return me;
    })
    .factory('socket', function($rootScope){
        //Creating connection with server
        var socket = io.connect('https://websocket.oxobox.net:8443/package?token=asdfkadjsflkadsf&package_id=123123');

        socket.on('connect', function(){
            console.log("connected");
            socket.emit('package_joined');
        });

        return {
            on: function (eventName, callback) {
                socket.on(eventName, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        callback.apply(socket, args);
                    });
                });
            },
            emit: function (eventName, data, callback) {
                socket.emit(eventName, data, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        if (callback) {
                            callback.apply(socket, args);
                        }
                    });
                });
            }
        };
        //return socket;

    })
    .factory('cookiesConsent', function($cookies){
        return $cookies['consent'] === undefined ? 0 : $cookies['consent'];
    });