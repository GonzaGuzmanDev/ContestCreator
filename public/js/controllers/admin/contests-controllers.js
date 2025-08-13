AdminControllers
    .controller('AdminContestsListCtrl', function($scope, rootUrl, currentBaseUrl, $http, $sanitize, $location, Flash, $uibModal){
        $scope.Math = Math;
        $scope.activeMenu = 'contests';
        $scope.dataLoaded = false;
        $scope.pagination = {};
        $scope.updateContestsList = function(){
            $http.post(rootUrl + 'api/contests/', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.contests = response.data.data;
                $scope.pagination.filterContest = response.data.filterContest;
            });
        };

        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateContestsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateContestsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateContestsList(); });
        $scope.$watch(function(){ return $scope.pagination.query; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateContestsList(); });
        $scope.updateContestsList();
        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
        };
        $scope.delete = function(contest) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/contests/delete',
                controller: 'AdminContestsDeleteCtrl',
                resolve: {
                    captchaUrl: function($http){
                        return $http.get(rootUrl+'captcha/url').success(function(data){
                            return data.data;
                        });
                    },
                    contest: function () {
                        return contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateContestsList();
            }, function () {});
        };

        $scope.updateInvoiceStatus = function(contest, invoice_status){
            contest.invoice_status = invoice_status;
        }

        $scope.openInvoiceModal = function(contest){
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/contests/contestInvoice',
                controller: 'AdminContestsInvoice',
                resolve: {
                    contest: function () {
                        return contest;
                    },
                    invoiceData: function() {
                        return $http.get(rootUrl + 'api/contest/'+contest.code+'/invoice').then(function (response) {
                            return response.data;
                        });
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateInvoiceStatus(contest, data.data);
            }, function () {});
        }

        $scope.updateContest = function(contest, status){
            contest.status = status['status'];
        }

        $scope.contestRequest = function(contest, admin, status){
            $http.post(currentBaseUrl + 'contestStatusRequest', {contest: contest, admin: admin, status: status}).success(function (status) {
                $scope.updateContest(contest, status);
            });
        }

        $scope.filterContest = [];
        $scope.filterContestFunction = function(id){
            var index = $scope.filterContest.indexOf(id);
            if(index == -1){
                $scope.filterContest.push(id);
            }else{
                $scope.filterContest.splice(index, 1);
            }
            $scope.pagination.filterContest = $scope.filterContest;
            $scope.updateContestsList();
        }

    })
    .controller('AdminContestsInvoice', function($scope, rootUrl, currentBaseUrl, $filter, $sanitize, $location, $routeParams, $timeout, $uibModalInstance, Flash, $http, contest, invoiceData){
        $scope.contest = contest;
        $scope.activeMenu = 'contests';
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        if(invoiceData[0]){
            $scope.params = invoiceData[0];
        }
        else $scope.params = {'contest_id': $scope.contest.id};

        $scope.accept = function(){
            $http.post(rootUrl + 'api/contest/'+$scope.contest.code+'/invoice', {params: $scope.params}).then(function(response){
                $uibModalInstance.close(response);
                Flash.show(response.flash, 'success', $scope);
            })
        }
    })
    .controller('AdminContestsEditCtrl', function($scope, rootUrl, currentBaseUrl, $filter, $sanitize, $location, default_storage_sources_bucket, $routeParams, $timeout, $uibModal, Flash, $http, contestData){
        $scope.activeMenu = 'contests';
        $scope.showThis = true;
        $scope.contest = angular.extend({public:0, inscription_public: 0, voters_public: 0, max_entries:0, storage_sources_bucket: default_storage_sources_bucket}, contestData);
        $scope.contest.single_category = !!$scope.contest.single_category;
        $scope.contest.block_finished_entry = !!$scope.contest.block_finished_entry;
        $scope.contest.admin_reset_password = !!$scope.contest.admin_reset_password;
        $scope.enableDelete = true;
        if(!$scope.contest.type) $scope.contest.type = 0;
        console.log($scope.contest.type);
        $scope.delete = function() {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/contests/delete',
                controller: 'AdminContestsDeleteCtrl',
                resolve: {
                    captchaUrl: function($http){
                        return $http.get(rootUrl+'captcha/url').success(function(data){
                            return data.data;
                        });
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $location.path('/contests');
            }, function () {});
        };
        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(rootUrl + 'api/contest/save/' + (angular.isDefined($scope.contest.id) ? $scope.contest.id : ''), $scope.contest).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.contest = response.contest;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };
    })
    .controller('AdminContestsDeleteCtrl',function($scope, rootUrl, $sanitize, $location, $route, $timeout, $uibModalInstance, $http, Flash, contest, captchaUrl){
        $scope.captchaUrl = captchaUrl.data;
        $scope.contest = contest;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(rootUrl + 'api/contest/delete/' + $scope.contest.code, {captcha: $scope.captcha}).success(function(response, status, headers, config){
                $scope.sending = false;
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
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
    });