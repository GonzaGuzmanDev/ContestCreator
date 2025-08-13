OxoAwards.config(['$routeProvider', 'currentBaseUrl', function($routeProvider,currentBaseUrl){
    $routeProvider.
        when('/contest-files', {
            templateUrl: currentBaseUrl + 'view/contest-files/list',
            controller: 'AdminContestFilesListCtrl'
        }).
        when('/contest-files/edit/:id', {
            templateUrl: currentBaseUrl + 'view/contest-files/form',
            controller: 'AdminContestFilesEditCtrl',
            resolve:{
                contestFileData: function(Format, $route){
                    return Format.get({'id': $route.current.params.id}).promise.then(function(response){
                        return response;
                    });
                }
            }
        }).
        when('/contest-files/new', {
            templateUrl: currentBaseUrl + 'view/contest-files/form',
            controller: 'AdminContestFilesCreateCtrl'
        })
}]);

AdminControllers.controller('AdminContestFilesListCtrl', function($scope, currentBaseUrl, $sanitize, $location, ContestFile, $uibModal){
    $scope.Math = Math;
    $scope.activeMenu = 'contest-files';
    $scope.dataLoaded = false;
    $scope.pagination = {};
    $scope.updateFileList = function(){
        if(angular.isDefined($scope.contestFilesData)){
            $scope.contestFilesData.abort();
        }
        $scope.contestFilesData = ContestFile.query({page:$scope.pagination.page,orderBy:$scope.pagination.orderBy,orderDir:$scope.pagination.orderDir,query:$scope.query}, function(response){
            //$scope.pagination = response.pagination;
            //$scope.contestFiles = response.data;
        });
        $scope.contestFilesData.promise.then(function(response){
            $scope.dataLoaded = true;
            $scope.pagination = response.pagination;
            $scope.contestFiles = response.data;
        });
    };
    $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFileList(); });
    $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFileList(); });
    $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFileList(); });
    $scope.$watch(function(){ return $scope.query; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFileList(); });
    $scope.updateFileList();
    $scope.changeOrder = function(newOrder){
        if($scope.pagination.orderBy == newOrder){
            $scope.pagination.orderDir = !$scope.pagination.orderDir;
        }else{
            $scope.pagination.orderBy = newOrder;
        }
    };
    //$scope.orderProp = 'email';
    $scope.delete = function(contestFile) {
        $uibModal.open({
            templateUrl: currentBaseUrl + 'view/contest-files/delete',
            controller: 'AdminContestFilesDeleteCtrl',
            resolve: {
                contestFile: function () {
                    return contestFile;
                }
            }
        });
    };
})
.controller('AdminContestFilesCreateCtrl',function($scope, $sanitize, $location, $routeParams, $timeout, ContestFile){
    $scope.activeMenu = 'contest-files';
    $scope.parentId = $routeParams.parentId;
    $scope.save = function() {
        ContestFile.save($scope.contestFile, function(data) {
            if(angular.isDefined(data.errors)){
                $scope.errors = data.errors;
            }else{
                $timeout(function() {
                    $location.path('/contest-files');
                });
            }
        });
    };
})
.controller('AdminContestFilesEditCtrl',function($scope, currentBaseUrl, $sanitize, $location, $routeParams, $timeout, $uibModal, ContestFile, contestFileData){
    $scope.activeMenu = 'contest-files';
    $scope.contestFile = contestFileData;
    $scope.delete = function() {
        $uibModal.open({
            templateUrl: currentBaseUrl + 'view/contest-files/delete',
            controller: 'AdminContestFilesDeleteCtrl',
            resolve: {
                contestFile: function () {
                    return $scope.contestFile;
                }
            }
        });
    };
    $scope.save = function() {
        ContestFile.update({id: $scope.contestFile.id}, $scope.contestFile, function(data) {
            if(angular.isDefined(data.errors)){
                $scope.errors = data.errors;
            }else{
                $timeout(function() {
                    $location.path('/contest-files');
                });
            }
        });
    };
})
.controller('AdminContestFilesDeleteCtrl',function($scope, $sanitize, $location, $route, $timeout, $uibModalInstance, contestFile, ContestFile){
    $scope.contestFile = contestFile;
    $scope.close = function(){
        $uibModalInstance.close();
    };
    $scope.destroy = function() {
        ContestFile.remove({id: $scope.contestFile.id}, $scope.contestFile, function() {
            $scope.close();
            $timeout(function() {
                $location.path('/contest-files');
                $route.reload();
            });
        });
    };
});