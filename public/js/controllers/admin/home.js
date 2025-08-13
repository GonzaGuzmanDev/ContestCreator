OxoAwards.config(['$routeProvider', 'rootUrl', 'currentBaseUrl', function($routeProvider, rootUrl, currentBaseUrl){
        $routeProvider
            .when('/home', {
                templateUrl: currentBaseUrl + 'view/home',
                controller: 'homeController as hc',
                resolve:{
                    currentMetrics: function(Metrics){
                        return Metrics
                            .get()
                            .success(function (response) {
                                return response;
                            });
                    }
                }
            })
        }]);
google.charts.load('current', {'packages':['gauge', 'corechart', 'bar']});
AdminControllers.controller('homeController', function($scope, $timeout, rootUrl, $http, Metrics, currentMetrics){
    var homeAdmin = this;
    homeAdmin.error = false;
    $scope.calendarClick = function(a){
        //console.log(a);
    };
    $scope.uiConfig = {
        calendar:{
            //height: 200,
            //editable: true,
            defaultView: 'basicWeek',
            header:{
                center: 'month basicWeek basicDay agendaWeek agendaDay',
                left: 'title',
                right: 'today prev,next'
            },
            eventClick: $scope.calendarClick,
            //eventDrop: $scope.alertOnDrop,
            //eventResize: $scope.alertOnResize
        }
    };
    $scope.contestsEvents = currentMetrics.data.metrics.events;
    setMetrics(currentMetrics.data.metrics);

    $scope.executeManager = function(){
        $http.get(rootUrl+'api/gcmanager').success(function(data){
            return data;
        });
    };
    $scope.assignQueuedToManualEncoder = function(){
        $scope.assigned = false;
        $http.get(rootUrl+'api/assignQueuedToManualEncoder').success(function(data){
            if(data.status === 200){
                $scope.assigned = true;
            }
            return data;
        });
    };
    var updateData = function(){
        getMetrics();
        homeAdmin.updateTimer = $timeout(function(){
            updateData();
        }, 5000);
    };

    $timeout(function(){
        updateData();
    }, 5000);

    $scope.$on(
        "$destroy",
        function( event ) {
            $timeout.cancel( homeAdmin.updateTimer );
        }
    );

    function getMetrics(){
        Metrics
            .get()
            .success(function (response) {
                setMetrics(response.metrics);
            });
    }

    function setMetrics(metrics){
        homeAdmin.metrics = metrics;
        if(homeAdmin.metrics.hasOwnProperty('cpuMemory')){
            setCpuMemory(homeAdmin.metrics.cpuMemory);
        }
        if(homeAdmin.metrics.hasOwnProperty('diskUsage')){
            setDiskUsage(homeAdmin.metrics.diskUsage);
        }
        if(homeAdmin.metrics.hasOwnProperty('network')){
            setNetwork(homeAdmin.metrics.network);
        }
        if(homeAdmin.metrics.hasOwnProperty('files')){
            setFiles(homeAdmin.metrics.files);
        }
        if(homeAdmin.metrics.hasOwnProperty('users')){
            setUsers(homeAdmin.metrics.users);
        }
        if(homeAdmin.metrics.hasOwnProperty('gcInstances')){
            setGCInstances(homeAdmin.metrics.gcInstances);
        }
    }

    function setCpuMemory(cpuMemoryData){
        homeAdmin.cpuMemory = cpuMemoryData;
        homeAdmin.cpuMemory.cpuGauges = {};
        homeAdmin.cpuMemory.cpuGauges.type = "gauge";
        homeAdmin.cpuMemory.cpuGauges.options = {
            width: 250, height: 120,
            redFrom: 90, redTo: 100,
            yellowFrom: 75, yellowTo: 90,
            minorTicks: 5
        };
        homeAdmin.cpuMemory.cpuGauges.data = [
            ['Label', 'Value'],
            ['User', Number(homeAdmin.cpuMemory.cpuUser)],
            ['System', Number(homeAdmin.cpuMemory.cpuSystem)]
        ];
        homeAdmin.cpuMemory.memoryChart = {};
        homeAdmin.cpuMemory.memoryChart.type = "bars";
        homeAdmin.cpuMemory.memoryChart.data = [
            ['Memory', 'Total', 'Used', { role: 'annotation' } ],
            ['RAM', Number(homeAdmin.cpuMemory.memoryTotal)/Math.pow(1024, 2), Number(homeAdmin.cpuMemory.memoryTotal)/Math.pow(1024, 2) - Number(homeAdmin.cpuMemory.memoryAvailable)/Math.pow(1024, 2) - Number(homeAdmin.cpuMemory.memoryFree)/Math.pow(1024, 2), ''],
            ['SWAP', Number(homeAdmin.cpuMemory.memorySwapTotal)/Math.pow(1024, 2), Number(homeAdmin.cpuMemory.memorySwapTotal)/Math.pow(1024, 2) - Number(homeAdmin.cpuMemory.memorySwapFree)/Math.pow(1024, 2), '']
        ];
        homeAdmin.cpuMemory.memoryChart.options = {
            title: 'System Memory',
            chartArea: {width: '75%'},
            hAxis: {
                title: 'Mem [GB]',
                minValue: 0
            },
            legend: {position: 'top', alignment: 'start'}
        };

        homeAdmin.getTypeIcon = function (type) {
            switch (parseInt(type)) {
                case 0:
                    return "fa-video-camera";
                case 1:
                    return "fa-picture-o";
                case 2:
                    return "fa-volume-up";
                case 3:
                    return "fa-file-text";
                case 4:
                    return "fa-file";
                default:
                    return "fa-file";
            }
        };
        homeAdmin.getTypeTextStyle = function (type) {
            return "";
            switch (parseInt(type)) {
                case 0:
                    return "text-danger";
                case 1:
                    return "text-warning";
                case 2:
                    return "text-success";
                case 3:
                    return "text-info";
                case 4:
                    return "text-primary";
                default:
                    return "text-primary";
            }
        };
    }

    function setDiskUsage(distUsageData){
        homeAdmin.diskUsage = distUsageData;
        homeAdmin.diskUsage.diskUsagePie = {};
        homeAdmin.diskUsage.diskUsagePie.type = "pie";
        homeAdmin.diskUsage.diskUsagePie.options = {
            legend: 'none',
            pieSliceText: 'label',
            backgroundColor: '#2e3338'
        };
        homeAdmin.diskUsage.diskUsagePie.data = [
            ['Usage', 'Space [TB]'],
            ['Free', Number(homeAdmin.diskUsage.totalSpace) - Number(homeAdmin.diskUsage.usedSpace)],
            ['Used', Number(homeAdmin.diskUsage.usedSpace)]
        ];
    }

    function setNetwork(networkData){
        homeAdmin.network = networkData;
        homeAdmin.network.networkChart = {};
        homeAdmin.network.networkChart.type = "area";
        homeAdmin.network.networkChart.options = {
            legend: {position: 'top'},
            vAxis: {title: '[KB/s]', minValue: 0},
            hAxis: {title: 'Time', textPosition: 'none'},
            width: '100%',
            chartArea: {width: '75%'}
        };
        homeAdmin.network.networkChart.data = [
            ['Time', 'IN', 'OUT']
        ];
        for(var i = 0; i < homeAdmin.network.samples; i++){
            var data = homeAdmin.network.network[i];
            homeAdmin.network.networkChart.data.push([data[0], Number(data[1]), Number(data[2])]);
        }
    }

    function setFiles(filesData){
        homeAdmin.files = filesData;
    }

    function setUsers(usersData){
        homeAdmin.users = usersData;
        homeAdmin.users.countConnected = homeAdmin.users.connected.length;
        homeAdmin.users.showConnected = homeAdmin.users.countConnected > 0;
    }

    function setGCInstances(gcInstancesData) {
        homeAdmin.gcInstances = gcInstancesData;
    }
});