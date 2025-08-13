OxoAwards.config(['$routeProvider','rootUrl',
    function($routeProvider,rootUrl) {
        $routeProvider.
            when('/account/data', {
                templateUrl: rootUrl + 'view/account/data',
                controller: 'AccountDataController',
                resolve: {
                    user: function(AuthService){
                        return AuthService.currentUser();
                    }
                }
            }).
            when('/account/security', {
                templateUrl: rootUrl + 'view/account/security',
                controller: 'AccountSecurityController'
            }).
            when('/account/config', {
                templateUrl: rootUrl + 'view/account/config',
                controller: 'AccountConfigController',
                resolve: {
                    langs: function($http){
                        return $http.get(rootUrl+'account/language').success(function(data){
                            return data;
                        });
                    },
                    social: function($http){
                        return $http.get(rootUrl+'account/social').success(function(data){
                            return data;
                        });
                    }
                }
            });
    }]);

var AccountControllers = angular.module('AccountControllers', ['ngRoute'])
    .controller('ProfilePictureController',function($scope, rootUrl, AuthService){
        $scope.pictureUploadPath = rootUrl+'account/profilePicture';
        $scope.picDate = new Date().getTime();
        $scope.updatePicture = function(){
            $scope.picDate = new Date().getTime();
            AuthService.currentUser().picDate = $scope.picDate;
        }
    })
    .controller('AccountDataController',function($scope,rootUrl,$sanitize,$location,$http,$route,AuthService,Flash,$uibModal,user){
        $scope.activeMenu = 'data';
        Flash.clear();
        $scope.user = user;
        $scope.save = function(){
            Flash.clear();
            delete $scope.errors;
            $http.post(rootUrl+'account/data/', $scope.user).success(function(data, status, headers, config){
                if(data.errors){
                    $scope.errors = data.errors;
                }else{
                    AuthService.setCurrentUser(data.user);
                    Flash.show(data.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.');
            });
        };
        $scope.sendVerifyEmail = function(){
            Flash.clear($scope);
            $http.get(rootUrl+'account/sendverifyemail/').success(function(data, status, headers, config){
                if(data.error){
                    $scope.verifyError = data.error;
                }else{
                    //AuthService.setCurrentUser(data.user);
                    Flash.show(data.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.');
            });
        };
    })
    .controller('AccountSecurityController',function($scope,rootUrl,$sanitize,$location,$http,AuthService,Flash,$uibModal){
        $scope.activeMenu = 'security';
        Flash.clear();
        $scope.savePass = function(){
            delete $scope.errors;
            $http.post(rootUrl+'account/security/', $scope.passChange).success(function(data, status, headers, config){
                if(data.errors){
                    $scope.errors = data.errors;
                }else{
                    delete $scope.passChange;
                    Flash.show(data.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.');
            });
        };
    })
    .controller('AccountConfigController',function($scope,rootUrl,$rootScope,$timeout,$window,$http,AuthService,Flash,$uibModal,userInscriptions, langs, social){
        $scope.activeMenu = 'config';
        Flash.clear();
        $scope.superAdmin = userInscriptions.superAdmin();
        //Language
        $scope.langForm = {lang: langs.data.current};
        $scope.langOptions = langs.data.list;
        $scope.saveLanguage = function(){
            Flash.clear($scope);
            $http.post(rootUrl+'account/language/', $scope.langForm).success(function(data, status, headers, config){
                if(data.error){
                    Flash.show(data.error, 'danger', $scope);
                }else{
                    Flash.show(data.flash, 'success', $scope);
                    $timeout(function(){
                        $window.location.reload();
                    }, 2000);
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.', 'warning', $scope);
            });
        };
        $scope.notif = {};
        $scope.inscriptions = userInscriptions;
        $scope.saveNotifications = function(){
            Flash.clear($scope.notif);
            $http.post(rootUrl+'account/notifications/', $rootScope.currentUser.notifications).success(function(data, status, headers, config){
                if(data.error){
                    Flash.show(data.error, 'danger', $scope.notif);
                }else{
                    Flash.show(data.flash, 'success', $scope.notif);
                }
            }).error(function(data, status, headers, config){
                Flash.show('Error. Please try again later.', 'warning', $scope.notif);
            });
        };

        //Delete account
        $scope.deleteAccount = function(){
            var modalInstance = $uibModal.open({
                templateUrl: 'delete.html',
                controller: 'DeleteModalCtrl'
            });
            modalInstance.result.then(function (data) {
                //Flash.show(data.flash, 'success');
            }, function () {
                //$log.info('Modal dismissed at: ' + new Date());
            });
        };

        //Social networks
        $scope.social = social.data;
    })
    .controller('DeleteModalCtrl',function($scope,rootUrl,$http,$uibModalInstance,$location,Flash){
        Flash.clear($scope);
        $scope.delete = function () {
            Flash.clear($scope);
            $http.post(rootUrl+'account/deleteAccount', {'sure': true}).success(function(data){
                if(data.flash) {
                    $uibModalInstance.close(data);
                    $location.path('/login');
                    Flash.show(data.flash, 'info');
                }else{
                    Flash.show(data.error, 'danger');
                }
            }).error(function(data){
                Flash.show(data.error, 'danger', $scope);
            });
        };
        $scope.close = function () {
            $uibModalInstance.dismiss('cancel');
        };
    })
    .controller('userDropdown',function($scope, userInscriptions, contestStatus){
        $scope.contests = userInscriptions.GetAllContests();
        $scope.$watch(function(){ return userInscriptions.Updated; }, function(newVal){
            $scope.inscriptions = userInscriptions.GetAllInscriptions();
        });
        var currentDate = new Date();
        $scope.inscriptionsCount = 0;
        angular.forEach(userInscriptions.GetAllInscriptions(), function(item){
            //var closedDate = new Date(item.contest.finish_at);
            //if(closedDate > currentDate){
            if(item.contest.status !== contestStatus.STATUS_CLOSED && item.contest.status !== contestStatus.STATUS_BANNED){
                $scope.inscriptionsCount++;
            }
        });

        angular.forEach($scope.contests, function(item){
            //var closedDate = new Date(item.finish_at);
            if(item.status !== contestStatus.STATUS_CLOSED && item.status !== contestStatus.STATUS_BANNED){
                $scope.inscriptionsCount++;
            }
        });

        $scope.contestClosed = function(status){
            if(status !== contestStatus.STATUS_CLOSED && status !== contestStatus.STATUS_BANNED){
                return true;
            }else return false;
            /*var closedDate = new Date(closeDateVar);
            if(closedDate > currentDate){
                return true;
            }else{
                return false;
            }*/
        };
    })
    .controller('langDropdown',function($scope, rootUrl, Languages, $location){
        $scope.lang = Languages;
        $scope.loc = $location;
    })
    .controller('MetadataFieldController',function($rootScope, $scope, $filter, Languages){
        $scope.selectedLang = Languages.Active;
        $scope.removeFile = function(file, list){
            var fileIndex = -1;
            $filter('filter')(list, function(item, index) {
                if(file.id == item.id){
                    fileIndex = index;
                    return true;
                }
                return false;
            });
            if(fileIndex != -1) $scope.removeItem(fileIndex, list);
        };
        $scope.removeItem = function(index, list){
            list.splice(index,1);
        };

        $scope.getTypeIcon = function (type) {
            switch (parseInt(type)) {
                case 0:
                    return "fa-video-camera";
                case 1:
                    return "fa-picture-o";
                case 2:
                    return "fa-volume-up";
                case 3:
                    return "fa-file-text";
                case 4:
                    return "fa-file";
                default:
                    return "fa-file";
            }
        };
        $scope.getTypeString = function (type) {
            switch (parseInt(type)) {
                case 0:
                    return "Video";
                case 1:
                    return "Imagen";
                case 2:
                    return "Audio";
                case 3:
                    return "Documento";
                case 4:
                    return "Otro";
                default:
                    return "Otro";
            }
        };
        $scope.getTypeTextStyle = function (type) {
            switch (parseInt(type)) {
                case 0:
                    return "text-danger";
                case 1:
                    return "text-warning";
                case 2:
                    return "text-success";
                case 3:
                    return "text-info";
                case 4:
                    return "text-primary";
                default:
                    return "text-primary";
            }
        };
        $scope.showData = function(column, selected){
            $scope.exist = 0;
            if(selected){
                selected.forEach(function(value){
                    if(column == value){
                        $scope.exist = 1;
                    }
                });
                return $scope.exist;
            }
        };
        $scope.isText = function(data, textArray){
            if(textArray){
                for (var key in textArray) {
                    if (!textArray.hasOwnProperty(key)) continue;
                    if(key == data){
                        var obj = textArray[key];
                        return obj;
                    }
                }
            }
            return 0;
        }

        $scope.countChar = function (val) {
            if(val)
                $scope.textAreaLen = val.length;
        };
    });