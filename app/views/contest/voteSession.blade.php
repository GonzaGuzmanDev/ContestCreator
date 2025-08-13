@include('includes.header')
@include('contest.header', array('class' => 'small', 'banner' => ContestAsset::SMALL_BANNER_HTML))

<div class="container with-footer">
    <div class="row vote-sessions">
        <div class="col-md-offset-3 col-md-6 col-sm-6">
            <h2>
                <span>@lang('contest.voting-session')</span>
            </h2>
            <div ng-if="hasVoteSessions == false">
                <h4 class="alert alert-warning text-center">
                    @lang('voting.noVotingSessions')
                </h4>
            </div>
            <div ng-repeat="voteSession in votingSessions">
                <div class="vote">
                    <div ng-if="voteSession.start_at < Date && (voteSession.finish_at > Date || voteSession.finish_at2 > Date)">
                        <div ng-if="voteSession.public == 1 && userVerified == 0" class="alert alert-border">
                            <label >
                                Debe verificar su email para poder ingresar.
                                <br>
                                <span uib-tooltip="Arriba a la derecha (donde figura su nombre) Opcion 'Mis datos' >> 'Enviar e-mail de verificación.' "> Como verificar mi cuenta?</span>
                            </label>
                        </div>
                        <span ng-if="voteSession.public == 1 && userVerified == 1 || voteSession.public == 0">
                            <a ng-if="showAbstains(voteSession) == false" class="" href="#/voting/@{{ voteSession.code }}">
                                <div class="alert alert-border">
                                    <div class="inline">
                                        <h4>@{{ voteSession.name }}</h4>
                                        <span ng-if="voteSession.config.oxoMeeting && voteSession.config.oxoMeeting == 1"
                                            class="badge-text label label-warning label-as-badge">
                                                Live Meeting
                                        </span>
                                        <button type="button" ng-if="!voteSession.judge" class="btn btn-info btn-sm  pull-right">
                                            @lang('voting.enterSession')
                                        </button>
                                    </div>
                                    <judge-progress ng-if="voteSession.public != 1" judge="voteSession.judge" class="pull-right text-center"></judge-progress>
                                    <br>
                                    <div class="inline">
                                        @lang('voting.sessionendon')
                                        <span am-time-ago="voteSession.finish_at" ng-if="voteSession.finish_at > Date"></span>
                                        <span am-time-ago="voteSession.finish_at2" ng-if="voteSession.finish_at < Date && voteSession.finish_at2 > Date"></span>
                                    </div>
                                </div>
                            </a>
                        </span>
                        <div ng-if="showAbstains(voteSession) == true || selectAutoAbstains == true" class="" ng-click="autoAbstainsModal(voteSession)">
                            <div class="alert alert-warning">
                                <button type="button" ng-if="!voteSession.judge" class="btn btn-info btn-sm  pull-right">
                                    @lang('voting.enterSession')
                                </button>
                                <h4>@{{ voteSession.name }}</h4>
                                <judge-progress judge="voteSession.judge" class="pull-right"></judge-progress>
                                <div class="">
                                    @lang('voting.sessionendon')
                                    <span am-time-ago="voteSession.finish_at" ng-if="voteSession.finish_at > Date"></span>
                                    <span am-time-ago="voteSession.finish_at2" ng-if="voteSession.finish_at < Date && voteSession.finish_at2 > Date"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="voteSession.start_at > Date">
                        <div class="alert alert-warning">
                            <h4>@{{ voteSession.name }}</h4>
                            <div class="clearfix"></div>
                            <div class="">
                                @lang('voting.sessionstartson')
                                <span am-time-ago="voteSession.start_at"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6 col-sm-6">
            {{ $contest->getAsset(ContestAsset::VOTING_BOTTOM_HTML)->content }}
        </div>
    </div>
    @include('includes.footer')
</div>