@extends($superadmin ? 'admin.contests.form-layout-superadmin' : (!$contest->wizard_status || $contest->wizard_status == Contest::WIZARD_FINISHED) ? 'admin.contests.form-layout' : 'admin.contests.wizard-layout', array('section' => 'contests'))
@section('tabs')
    @include($superadmin ? 'admin.contests.tabs-superadmin' : 'contest.tabs', array('active' => $active))
@endsection