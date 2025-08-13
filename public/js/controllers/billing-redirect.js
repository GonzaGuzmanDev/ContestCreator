var BillingRedirectControllers = angular.module('BillingRedirectControllers', ['ngRoute'])
    .controller('initController',function($scope,rootUrl,CategoryManager,contest,entry,bill,billingStatus,categoriesData){
        $scope.entry = entry;
        $scope.billings = [bill];
        $scope.bill = bill;
        $scope.billingStatus = billingStatus;
        contest.categories = categoriesData.data.categories;
        contest.children_categories = categoriesData.data.children_categories;
        CategoryManager.SetCategories(categoriesData.data.children_categories);
        $scope.catMan = CategoryManager;
        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };
    });