@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))
<div class="container-fluid with-footer">

    <div class="row">
        @include('contest.tabs', array('active' => 'files'))
        <div class="col-sm-9 col-lg-10">
            <h4 class="well well-sm"><i class="fa fa-files-o"></i> @lang('contest.files.myfiles')</h4>
            <files-panel show-selection="false" toggleable="false" files="true"></files-panel>
        </div>
    </div>
</div>