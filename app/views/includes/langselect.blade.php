<div ng-controller="langDropdown">
    <div class="dropup pull-right">
        <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="langDrop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="flag-icon flag-icon-@{{lang.Active}}"></i>
            @{{lang.All[lang.Active]}}
        </button>
        <ul class="dropdown-menu" aria-labelledby="langDrop">
            <li ng-class="{'active': lang.Active == key}" ng-repeat="(key, name) in lang.All"><a href="<?=URL::to('/lang/');?>/@{{ key }}?returnTo=@{{ loc.url() }}"><i class="flag-icon flag-icon-@{{key}}"></i>  @{{ name }}</a></li>
        </ul>
    </div>
</div>