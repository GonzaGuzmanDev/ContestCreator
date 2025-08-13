<script type="text/ng-template" id="categoryDropdown.html">
    <a href="" ng-click="addCategory(category)" ng-if="category.final == 1"><transcat ng-model='category' trans-prop="'name'"></transcat></a>
    <a href="" ng-if="category.final == 0"><transcat ng-model='category' trans-prop="'name'"></transcat></a>
    <ul class="dropdown-menu" ng-if="category.children_categories.length > 0">
        <li ng-repeat="category in category.children_categories" ng-include="'categoryDropdown.html'" ng-class="{'dropdown-submenu':category.children_categories.length>0,'disabled':entry.categories_id.indexOf(category.id)!=-1}"></li>
    </ul>
</script>
<script type="text/ng-template" id="categoryDropdownVoting.html">
    <a href="" ng-click="addCategory(category)" ng-if="category.final == 1"><transcat ng-model='category' trans-prop="'name'"></transcat></a>
    <a href="" ng-if="category.final == 0"><transcat ng-model='category' trans-prop="'name'"></transcat></a>
    <ul class="dropdown-menu" ng-if="category.children_categories.length > 0">
        <li ng-repeat="category in category.children_categories" ng-include="'categoryDropDownDuplicate.html'" ng-class="{'dropdown-submenu':category.children_categories.length>0,'disabled':voteCategories.indexOf(category.id)!=-1}"></li>
    </ul>
</script>

<script type="text/ng-template" id="categoryDropDownDuplicate.html">
    <a href="" ng-click="duplicateEntry(category, $event)" ng-if="category.final == 1">
        <transcat ng-model='category' trans-prop="'name'"></transcat>
    </a>
    <a href="" ng-if="category.final == 0">
        <transcat ng-model='category' trans-prop="'name'"></transcat>
    </a>
    <ul class="dropdown-menu" ng-if="category.children_categories.length > 0" style="position: absolute;top: auto;bottom:0%;">
        <li ng-repeat="category in category.children_categories" ng-include="'categoryDropDownDuplicate.html'" ng-class="{'dropdown-submenu':category.children_categories.length>0,'disabled':entry.categories_id.indexOf(category.id)!=-1}"></li>
    </ul>
</script>


