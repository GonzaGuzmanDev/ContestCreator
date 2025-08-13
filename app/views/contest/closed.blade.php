@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div class="alert alert-danger alert-xl alert-box text-center">
    @lang('contest.closedContest')
</div>