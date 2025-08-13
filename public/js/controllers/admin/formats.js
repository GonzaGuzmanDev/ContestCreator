OxoAwards.config(['$routeProvider', 'rootUrl', 'currentBaseUrl', function($routeProvider,rootUrl,currentBaseUrl){
    $routeProvider.
        when('/formats', {
            templateUrl: currentBaseUrl + 'view/formats/list',
            controller: 'AdminFormatsListCtrl'
        }).
        when('/formats/edit/:id', {
            templateUrl: currentBaseUrl + 'view/formats/form-general',
            controller: 'AdminFormatsEditCtrl',
            resolve:{
                formatData: function(Format, $route){
                    return Format.get({'id': $route.current.params.id}).promise.then(function(response){
                       return response;
                    });
                }
            }
        }).
        when('/formats/new', {
            templateUrl: currentBaseUrl + 'view/formats/form-general',
            controller: 'AdminFormatsCreateCtrl'
        })
}]);

AdminControllers.controller('AdminFormatsListCtrl', function($scope, currentBaseUrl, $sanitize, $location, Format, $uibModal){
    $scope.Math = Math;
    $scope.activeMenu = 'formats';
    $scope.dataLoaded = false;
    $scope.pagination = {};
    $scope.updateFormatList = function(){
        if(angular.isDefined($scope.formatData)){
            $scope.formatData.abort();
        }
        $scope.formatData = Format.query({page:$scope.pagination.page,orderBy:$scope.pagination.orderBy,orderDir:$scope.pagination.orderDir,query:$scope.query}, function(response){
            //$scope.pagination = response.pagination;
            //$scope.formats = response.data;
        });
        $scope.formatData.promise.then(function(response){
            $scope.dataLoaded = true;
            $scope.pagination = response.pagination;
            $scope.formats = response.data;
        });
    };
    $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFormatList(); });
    $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFormatList(); });
    $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFormatList(); });
    $scope.$watch(function(){ return $scope.query; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateFormatList(); });
    $scope.updateFormatList();
    $scope.changeOrder = function(newOrder){
        if($scope.pagination.orderBy == newOrder){
            $scope.pagination.orderDir = !$scope.pagination.orderDir;
        }else{
            $scope.pagination.orderBy = newOrder;
        }
    };
    //$scope.orderProp = 'email';
    $scope.delete = function(format) {
        $uibModal.open({
            templateUrl: currentBaseUrl + 'view/formats/delete',
            controller: 'AdminFormatsDeleteCtrl',
            resolve: {
                format: function () {
                    return format;
                }
            }
        });
    };
})
.controller('AdminFormatsCreateCtrl',function($scope, $sanitize, $location, $routeParams, $timeout, Flash, Format){
    $scope.activeMenu = 'formats';
    $scope.parentId = $routeParams.parentId;
    $scope.save = function() {
        Format.save($scope.format, function(data) {
            if(angular.isDefined(data.errors)){
                $scope.errors = data.errors;
            }else{
                Flash.show(data.flash, 'success', $scope);
            }
        });
    };
})
.controller('AdminFormatsEditCtrl',function($scope, currentBaseUrl, $sanitize, $location, $routeParams, $timeout, $uibModal, Flash, Format, formatData){
    $scope.activeMenu = 'formats';
    $scope.format = formatData;
    $scope.delete = function() {
        $uibModal.open({
            templateUrl: currentBaseUrl + 'view/formats/delete',
            controller: 'AdminFormatsDeleteCtrl',
            resolve: {
                format: function () {
                    return $scope.format;
                }
            }
        });
    };
    $scope.save = function() {
        $scope.saving = true;
        Format.update({id: $scope.format.id}, $scope.format, function(data) {
            $scope.saving = false;
            if(angular.isDefined(data.errors)){
                $scope.errors = data.errors;
            }else{
                Flash.show(data.flash, 'success', $scope);
            }
        });
    };
})
.controller('AdminFormatsDeleteCtrl',function($scope, $sanitize, $location, $route, $timeout, $uibModalInstance, format, Format){
    $scope.format = format;
    $scope.close = function(){
        $uibModalInstance.close();
    };
    $scope.destroy = function() {
        Format.remove({id: $scope.format.id}, $scope.format, function() {
            $scope.close();
            $timeout(function() {
                $location.path('/formats');
                $route.reload();
            });
        });
    };
});