<!DOCTYPE html>
<html lang="en" ng-app="OxoAwards">
<head>
    <title>OxoAwards</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="img/favicon.png"/>
    @include('includes.css', array('contest' => isset($contest) ? $contest : null))
    @yield('appcss')
</head>
<body>
@if(Config::get('app.maintenanceScheduled'))
    <div class="alert alert-danger navbar-alert text-center">
    @lang('index.maintenanceScheduled', ['from'=>Config::get('app.maintenanceDateFrom'),'to'=>Config::get('app.maintenanceDateTo')])
    </div>
@endif

@include('includes.ng-templates')

@yield('content')
@include('includes.js-libraries')

@yield('appjs')
<script>
OxoAwards
    .constant('rootUrl', "<?php echo URL::to('/'); ?>/")
    .constant('currentBaseUrl', "<?php echo Request::url(); ?>/");
</script>
@yield('app-config')
<script>
OxoAwards.factory('userInscriptions', function($filter, moment, contest){
    var _allInscriptions =
        @if(isset($allInscriptions) && count($allInscriptions))
        <?=$allInscriptions->toJson(JSON_NUMERIC_CHECK)?>;
    @else
        [];
    @endif
    var _allContests =
        @if(isset($allContests) && count($allContests))
        <?=$allContests->toJson(JSON_NUMERIC_CHECK)?>;
    @else
        [];
    @endif
    var _inscriptions =
    @if(isset($inscriptions) && count($inscriptions))
        <?=$inscriptions->toJson(JSON_NUMERIC_CHECK)?>;
    @else
        [];
    @endif
    var _superAdmin =<?=isset($superAdmin) && $superAdmin ? 1 : 0?>;

    var selected = null;
    var billingSelected = null;
    var entriesIds = null;

    var serverDate = "<?=date('Y-m-d H:i:s');?>";
    var serverOffset = moment(serverDate).diff(new Date()) / 1000;
    return {
        Inscriptor: <?=Inscription::INSCRIPTOR?>,
        Judge: <?=Inscription::JUDGE?>,
        Colaborator: <?=Inscription::COLABORATOR?>,
        Owner: <?=Inscription::OWNER?>,
        Updated: new Date(),
        ServerDate: function(){
            return serverDate;
        },
        ServerOffset: function(){
            return serverOffset;
        },
        HasRole: function(role){
            for(var i in _inscriptions){
                if(_inscriptions[i].role == role) return true;
            }
            return false;
        },
        HasRoleInAllInscriptions: function(role){
            for(var i in _allInscriptions){
                if(_allInscriptions[i].role == role) return true;
            }
            return false;
        },
        CountInscriptions: function(){
            return _inscriptions.length;
        },
        CountAllInscriptions: function(){
            return _allInscriptions.length;
        },
        GetRoleInscription: function(role){
            for(var i in _inscriptions){
                if(_inscriptions[i].role == role) return _inscriptions[i];
            }
            return null;
        },
        SetRoleInscription: function(role, data){
            var found = false;
            for(var i in _inscriptions){
                if(_inscriptions[i].role == role){
                    _inscriptions[i] = data;
                    found = true;
                    break;
                }
            }
            if(!found) _inscriptions.push(data);
            this.Updated = new Date();
        },
        allInscriptions: function(){ return _allInscriptions; },
        RemoveInscription: function(role){
            for(var i in _inscriptions){
                if(_inscriptions[i].role == role){
                    _inscriptions.splice(i, 1);
                    break;
                }
            }
            this.Updated = new Date();
        },
        GetAllInscriptions: function(){
            return _allInscriptions;
        },
        GetAllContests: function(){
            return _allContests;
        },
        GetColaboratorStatus: function(status){
            for(var i in _inscriptions){
                if(_inscriptions[i].permits != null){
                    if(_inscriptions[i].permits[status] == true){
                        return true;
                    }
                }
            }
            return false;
        },
        GetInscriptionDeadlines: function(contestId, local){
            var now = moment();
            var sDate = local ? $filter('amAdd')(now, serverOffset, 'seconds').format('YYYY-MM-DD HH:mm:ss') : serverDate;
            sDate = sDate.replace(/\D/g,'');
            if(_inscriptions.length > 0){
                for(var i in _inscriptions){
                    if(_inscriptions[i].contest.id == contestId){
                        if(_inscriptions[i].deadline2_at){
                            var parsed = _inscriptions[i].deadline2_at.replace(/\D/g,'');
                            if( parsed > sDate ) return _inscriptions[i].deadline2_at;
                        }
                        if(_inscriptions[i].deadline1_at){
                            parsed = _inscriptions[i].deadline1_at.replace(/\D/g,'');
                            if( parsed > sDate ) return _inscriptions[i].deadline1_at;
                        }
                        if(_inscriptions[i].contest.inscription_deadline2_at){
                            parsed = _inscriptions[i].contest.inscription_deadline2_at.replace(/\D/g,'');
                            if( parsed > sDate ) return _inscriptions[i].contest.inscription_deadline2_at;
                        }
                        if(_inscriptions[i].contest.inscription_deadline1_at){
                            parsed = _inscriptions[i].contest.inscription_deadline1_at.replace(/\D/g,'');
                            if( parsed > sDate ) return _inscriptions[i].contest.inscription_deadline1_at;
                        }
                        return false;
                    }
                }
            }
            if(contest.inscription_deadline2_at){
                parsed = contest.inscription_deadline2_at.replace(/\D/g,'');
                if( parsed > sDate ) return contest.inscription_deadline2_at;
            }
            if(contest.inscription_deadline1_at){
                parsed = contest.inscription_deadline1_at.replace(/\D/g,'');
                if( parsed > sDate ) return contest.inscription_deadline1_at;
            }
            return false;
        },
        superAdmin: function(){
            return _superAdmin;
        }
    };
});
OxoAwards.factory('metadataFieldsConfig', function(){
    return {
        Type: <?=json_encode(MetadataField::getJSTypes())?>,
        Editables: <?=json_encode(MetadataField::getEditablesTypes())?>,
        NotEditables: <?=json_encode(MetadataField::getNotEditablesTypes())?>,
        Dates: <?=json_encode(MetadataField::getDateTypes())?>,
    };
});
OxoAwards.factory('EntryStatus', function(){
    return <?=json_encode(Entry::getJSStatus())?>;
});
OxoAwards.factory('Languages', function(){
    return {
        Active: '<?=Config::get('app.locale')?>',
        Keys: <?=json_encode(Config::get('app.languages'))?>,
        All: <?
        $langs = Config::get('app.languages');
        $langData = [];
        foreach($langs as $langKey){
            $langData[$langKey] = Lang::get("locale.".$langKey);
        }
        echo json_encode($langData);
        ?>,
        Editables: <? unset($langData[Config::get('app.default_locale')]); echo json_encode($langData);?>,
        Default: '<?=Config::get('app.default_locale')?>'
    };
});
OxoAwards.factory('wizardStatus', function(contest){
    return{
        WIZARD_CREATE_CONTEST: <?=Contest::WIZARD_CREATE_CONTEST?>,
        WIZARD_DATES: <?=Contest::WIZARD_DATES?>,
        WIZARD_CATEGORIES: <?=Contest::WIZARD_CATEGORIES?>,
        WIZARD_REGISTER_FORM: <?=Contest::WIZARD_REGISTER_FORM?>,
        WIZARD_ENTRY_FORM: <?=Contest::WIZARD_ENTRY_FORM?>,
        WIZARD_PAYMENT_FORM: <?=Contest::WIZARD_PAYMENT_FORM?>,
        WIZARD_STYLE: <?=Contest::WIZARD_STYLE?>,
        WIZARD_FINISHED: <?=Contest::WIZARD_FINISHED?>,
        wizard_contest : function(){
            return contest.wizard_config;
        },
        update_wizard_contest : function(contest_updated){
            return contest = contest_updated;
        }
    }
});
OxoAwards.factory('contestStatus', function(contest){
    return{
        STATUS_WIZARD: <?=Contest::STATUS_WIZARD?>,
        STATUS_COMPLETE: <?=Contest::STATUS_COMPLETE?>,
        STATUS_READY: <?=Contest::STATUS_READY?>,
        STATUS_PUBLIC: <?=Contest::STATUS_PUBLIC?>,
        STATUS_CLOSED: <?=Contest::STATUS_CLOSED?>,
        STATUS_BANNED: <?=Contest::STATUS_BANNED?>,
    }
});
OxoAwards.run(function($rootScope, amMoment, userInscriptions) {
    @if(Auth::check())
    $rootScope.currentUser = angular.extend({picDate: new Date().getTime()}, <?=Auth::user()->getJsonData();?>);
    @endif

    @yield('app-run')

    @if(Session::has('message'))
    $rootScope.flashMessage = '{{Session::get('message')}}';
    $rootScope.flashMessageType = '{{Session::get('messageType', 'primary')}}';
    <?Session::forget('message');?>
    <?Session::forget('messageType');?>
    @endif
    amMoment.changeLocale('<?=Config::get('app.momentLocales')[Config::get('app.locale')] ?>');
    amMoment.changeTimezone('<?=Config::get('app.timezone')?>');
    jwplayer.key="yoYd1cn7G7ksZxTgjPZbSRtHAsg1ruWZYNTaFFwQpqY=";
});
</script>
@include('includes.appjs-common')

@yield('app-controllersjs')
</body>
</html>