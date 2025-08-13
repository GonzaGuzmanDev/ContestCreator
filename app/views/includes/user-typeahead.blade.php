<a>
    <div class="user-selector">
        <img ng-src="@{{match.model.profileThumb}}" alt="" class="user-selector-thumb">
        <span ng-bind-html="match.model.first_name | uibTypeaheadHighlight:query"></span>
        <span ng-bind-html="match.model.last_name | uibTypeaheadHighlight:query"></span>
        <span class="text-muted" ng-bind-html="match.model.email | uibTypeaheadHighlight:query"></span>
    </div>
</a>