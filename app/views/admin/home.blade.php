@include('admin.header')
<div class="main-block contents">
    <h3><i class="fa fa-sliders"></i> <?=Lang::get('home.admin')?></h3>
    <div class="row">
    @include('admin.menu', array('section' => 'home'))
        <div class="col-sm-9 col-lg-10">
        @if($superadmin)
            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">GC Instances by Zone</h3>
                        </div>
                        <div class="panel-body">
                            <div ng-repeat="(currentZone, currentInstances) in hc.gcInstances">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12">
                                        <h2>
                                            <span class="label label-warning"> @{{currentZone}} <span class="badge">@{{ currentInstances.count }}</span></span>
                                        </h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12">
                                        <div class="panel panel-default">
                                            <!-- List group -->
                                            <ul class="list-group">
                                                <li class="list-group-item list-group-item-info" ng-repeat="currentInstance in currentInstances.instances">
                                                    <div class="user-connected">
                                                        <span>Name: <strong>@{{ currentInstance.name }} </strong></span>
                                                        <span>Status: <strong>@{{ currentInstance.status }} </strong></span>
                                                        <span>Created_at: <strong>@{{ currentInstance.created_at }} </strong></span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success" ng-click="executeManager()"><i class="fa fa-cog"></i> Ejecutar encoder</button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Files</h3>
                        </div>
                        <div class="panel-body">
                            <h2>
                                <span class="label label-primary">Total <span class="badge">@{{ hc.files.encoding+hc.files.queued+hc.files.uploading }}</span></span>
                                <span class="label label-success">Encoding <span class="badge">@{{ hc.files.encoding }}</span></span>
                                <span class="label label-warning">Queued <span class="badge">@{{ hc.files.queued }}</span></span>
                                <span class="label label-info">Uploading <span class="badge">@{{ hc.files.uploading }}</span></span>
                            </h2>
                            <table class="table table-hover encoding-list">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>File</th>
                                    <th>Format</th>
                                    <th>Contest</th>
                                    <th>User</th>
                                    <th>Actualizado</th>
                                    <th>ID</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="file in hc.files.list" ng-class="{'success': file.status == {{ContestFileVersion::ENCODING}}, 'warning': file.status == {{ContestFileVersion::QUEUED}}, 'info': file.status == {{ContestFileVersion::UPLOADING}} }">
                                    <td>@{{ $index + 1 }}</td>
                                    <td>
                                        <uib-progressbar class="active" value="file.percentage" type="info" ng-if="file.status == {{ContestFile::ENCODING}}">
                                            <div class="progress-bar-content">
                                                @{{file.percentage | number : 1}}%
                                            </div>
                                        </uib-progressbar></td>
                                    <td>
                                        <i class="fa @{{ hc.getTypeIcon(file.contest_file.type) }} @{{ hc.getTypeTextStyle(file.contest_file.type) }}"></i>
                                        @{{ file.contest_file.name }}</td>
                                    <td>@{{ file.format.label }} (@{{ file.format.extension }})</td>
                                    <td>@{{ file.contest_file.contest.name }}</td>
                                    <td>
                                        <span user-card user-card-model="file.contest_file.user" user-show-email="false"></span>
                                    </td>
                                    <td>@{{ file.updated_at }}</td>
                                    <td>@{{ file.id }}</td>
                                </tr>
                                <tr ng-repeat="file in hc.files.uploads" ng-class="{'success': file.status == {{ContestFileVersion::ENCODING}}, 'warning': file.status == {{ContestFileVersion::QUEUED}}, 'info': file.status == {{ContestFileVersion::UPLOADING}} }">
                                    <td>@{{ hc.files.list.length + $index + 1 }}</td>
                                    <td>
                                        <uib-progressbar class="active" value="file.percentage" type="info" ng-if="file.status == {{ContestFile::UPLOADING}}">
                                            <div class="progress-bar-content">
                                                @{{file.percentage | number : 1}}%
                                            </div>
                                        </uib-progressbar></td>
                                    <td>
                                        <i class="fa @{{ hc.getTypeIcon(file.contest_file.type) }} @{{ hc.getTypeTextStyle(file.contest_file.type) }}"></i>
                                        @{{ file.contest_file.name }}</td>
                                    <td>@{{ file.format.label }} (@{{ file.format.extension }})</td>
                                    <td>@{{ file.contest_file.contest.name }}</td>
                                    <td>
                                        <span user-card user-card-model="file.contest_file.user" user-show-email="false"></span>
                                    </td>
                                    <td>@{{ file.updated_at }}</td>
                                    <td>@{{ file.id }}</td>
                                </tr>
                                <tr ng-if="hc.files.encoding + hc.files.queued + hc.files.uploading - hc.files.list.length - hc.files.uploads.length > 0">
                                    <td colspan="7" class="text-center default">
                                        @{{ hc.files.encoding + hc.files.queued + hc.files.uploading - hc.files.list.length - hc.files.uploads.length }} more
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-default" ng-click="assignQueuedToManualEncoder()"><i class="fa fa-grav"></i> Asignar archivos en cola a encoder manual</button>
                            <i class="fa fa-check" ng-if="assigned"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Calendar</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <div ui-calendar="uiConfig.calendar" ng-model="contestsEvents"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Users</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <h2>
                                        <span class="label label-primary">Total <span class="badge">@{{ hc.users.total }}</span></span>
                                        <span class="label label-success">Active <span class="badge">@{{ hc.users.active }}</span></span>
                                        <span class="label label-warning">Verified <span class="badge">@{{ hc.users.verified }}</span></span>
                                    </h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <p class="text-muted">
                                        Connected: <span class="badge">@{{ hc.users.countConnected }}</span>
                                    </p>
                                    <div ng-hide="hc.users.showConnected">
                                        <span class="label label-primary">No users connected</span>
                                    </div>
                                    <div ng-if="hc.users.showConnected">
                                        <div class="panel panel-default">
                                            <!-- List group -->
                                            <ul class="list-group">
                                                <li class="list-group-item list-group-item-info" ng-repeat="user in hc.users.connected">
                                                    <div class="user-connected">
                                                        <span class="text-warning"><strong>#@{{ user.id }} </strong></span>
                                                        <span user-card user-card-model="user" ng-if="user" user-show-email="true"></span>
                                                        <a class="btn btn-success btn-xs pull-right" href="<?=url('/admin/loginAs')?>/@{{ user.id }}"><i class="fa fa-user"></i> <?=Lang::get('user.loginAs')?></a>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Network</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <p class="text-muted">
                                        Usage:
                                    </p>
                                    <div google-chart g-chart="hc.network.networkChart"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-lg-6">
                                    <table class="table">
                                        <caption>Averange Usage</caption>
                                        <thead>
                                        <tr>
                                            <th>IN</th>
                                            <th>OUT</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>@{{ hc.network.avgIn }} @{{ hc.network.unit }}</td>
                                            <td>@{{ hc.network.avgOut }} @{{ hc.network.unit }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Cpu & Memory</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 col-lg-6">
                                    <p class="text-muted">
                                        System UP time:
                                    <span class="text-info">
                                        @{{ hc.cpuMemory.upDays }} days
                                    </span>
                                    </p>
                                    <p class="text-muted">
                                        Avg Load:
                                    <span class="text-info">
                                        @{{ hc.cpuMemory.load[0] }} |  @{{ hc.cpuMemory.load[1] }} | @{{ hc.cpuMemory.load[2] }}
                                    </span>
                                    </p>
                                    <div google-chart g-chart="hc.cpuMemory.cpuGauges"></div>
                                </div>
                                <div class="col-sm-12 col-lg-6">
                                    <div google-chart g-chart="hc.cpuMemory.memoryChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">HDD</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6 col-lg-6">
                                    <table class="table">
                                        <caption>Disk Space</caption>
                                        <thead>
                                        <tr>
                                            <th>Total</th>
                                            <th>Used</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>@{{ hc.diskUsage.totalSpace | number:2 }} TB</td>
                                            <td>@{{ hc.diskUsage.usedSpace | number:2 }} TB</td>
                                            <td>@{{ (hc.diskUsage.usedSpace/hc.diskUsage.totalSpace)*100 | number:2 }}%</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6 col-lg-6">
                                    <div google-chart g-chart="hc.diskUsage.diskUsagePie"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-lg-12">
                                    <table class="table">
                                        <caption>Disk IO</caption>
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Read</th>
                                            <th>Write</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>ACTUAL</td>
                                            <td>@{{ hc.diskUsage.actualDiskRead }}</td>
                                            <td>@{{ hc.diskUsage.actualDiskWrite }}</td>
                                        </tr>
                                        <tr>
                                            <td>TOTAL</td>
                                            <td>@{{ hc.diskUsage.totalDiskRead }}</td>
                                            <td>@{{ hc.diskUsage.totalDiskWrite }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        @endif
        </div>
    </div>
</div>
