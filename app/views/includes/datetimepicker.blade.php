<div class="dropdown {{isset($inline) && $inline ? "dropdown-inline" : ""}}">
    <a class="dropdown-toggle" id="dropdown{{ $field }}" role="button" data-toggle="dropdown">
        <div class="input-group">
            <input type="text" class="form-control" id="inputStartAt" name="inscription_start_at" ng-model="{{ $field }}" placeholder="{{ $placeholder }}" ng-disabled="{{isset($disabled) ? $disabled : false}}" date-field>
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
    </a>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
        <datetimepicker ng-model="{{ $field }}" data-datetimepicker-config="{ startView: 'month', minView: 'minute', minuteStep:10 {{ isset($limitLeft) ? ', limitLeft:'.$limitLeft:''}}{{ isset($limitRight) ? ', limitRight:'.$limitRight:''}}}"></datetimepicker>
    </ul>
</div>