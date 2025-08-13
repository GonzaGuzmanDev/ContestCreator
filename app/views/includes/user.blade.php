<div class="user-card" ng-if="userCardModel.id || userCardModel.user_id">
    <img ng-src="@{{ userCardModel.profileThumb }}" alt=""/>
    @{{ userCardModel.first_name }}
    @{{ userCardModel.last_name }}
    <em ng-if="userShowEmail">@{{ userCardModel.email }}</em>
</div>