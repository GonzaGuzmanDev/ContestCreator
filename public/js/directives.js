OxoAwards.directive('focusMe', function($timeout) {
    return {
        link: function(scope, element, attrs) {
            scope.$watch(attrs.focusMe, function(value) {
                if(value === true) {
                    $timeout(function() {
                        element[0].focus();
                        scope[attrs.focusMe] = false;
                    });
                }
            });
        }
    };
}).directive('includeReplace', function () {
    return {
        require: 'ngInclude',
        restrict: 'A', /* optional */
        link: function (scope, el, attrs) {
            el.replaceWith(el.children());
        }
    };
}).directive('userCard', [function() {
    return {
        restrict: 'AE',
        scope: {
            userCardModel: '=',
            userShowEmail: '='
        },
        templateUrl: 'userCard.html'
    };
}]).directive('entryCard', [function() {
    return {
        restrict: 'AE',
        scope: {
            entry: '=',
            fields: '=?'
        },
        templateUrl: 'entryCard.html',
        controller: function($scope, $filter, metadataFieldsConfig){
            $scope.getName = function(){
                var val = "";
                if(angular.isDefined($scope.entry.name) && $scope.entry.name != '') return $scope.entry.name;
                if($scope.fields){
                    $scope.fields.every(function(element){
                        if(metadataFieldsConfig.Editables.indexOf(parseInt(element.type))!= -1){
                            val = $scope.getMetadata(element.id).value;
                            return false;
                        }
                        return true;
                    });
                }
                return val == "" ? false : val;
            };
            $scope.getNoName = function(){
                return "<span class='notitle'>Sin título</span>";
            };
            $scope.getMetadata = function(entry_metadata_field_id){
                var res = $filter('filter')($scope.entry.entry_metadata_values, {entry_metadata_field_id:entry_metadata_field_id}, true);
                if(angular.isDefined(res[0])) return res[0];
                $scope.files = [];

                $scope.entry.entry_metadata_values.forEach(function(value){
                    if(value['entry_metadata_field_id'] == entry_metadata_field_id) {
                        if(value['id'] != null)
                            $scope.metadata_value_id = value['id'];
                        if(value['files'] != 0){
                            $scope.files = value['files'];
                            $scope.value = '';
                        }
                        else{
                            $scope.value = value['value'];
                        }
                    }
                });

                var o = {entry_metadata_field_id: entry_metadata_field_id, value: $scope.value, files:$scope.files, id:$scope.metadata_value_id};
                $scope.entry.entry_metadata_values.push(o);
                $scope.metadata_values.push(o);
                return o;
            };
        }
    };
}]).directive('filesPanel', ['currentBaseUrl', 'inAdmin', function(currentBaseUrl, inAdmin) {
    return {
        restrict: 'E',              // only matches element name
        transclude: true,           // it gives the contents access to the outside scope
        scope: {                    // Esto le pasa lo que querramos al templado, dentro del html podemos usar files.BLABLABLA
            field: '=?',              // toma esto <my-files-panel files="CONTENT" >
            entry: '=?',              // toma esto <my-files-panel entry="CONTENT" >
            user: '=?',              // toma esto <my-files-panel user="CONTENT" >
            toggleable: '=?',              // toma esto <my-files-panel toggleable="CONTENT" >
            //viewList: '=',              // toma esto <my-files-panel user="CONTENT" >
            showSelection: '=?',              // toma esto <my-files-panel show-selection="CONTENT" >
            tech: '=?',
            msg: '=?',
            files: '=?',
            config: '=?'
        },
        //templateUrl:  currentBaseUrl + (inAdmin ? 'file/panel' : 'view/files/panel'),
        template:'<ng-include src="template"/>',
        link: function postLink(scope) {
            scope.template = currentBaseUrl + (inAdmin ? 'file/panel' : (scope.tech ? 'view/files/panelTech' : 'view/files/panel'));
        },
        controller: function ($rootScope, $scope, rootUrl, contest, currentBaseUrl, $http, $filter, $uibModal, $q, $timeout, $interval, $window) {
            $scope.fileUploadUrl = currentBaseUrl + 'uploadFile';
            $scope.flowObj = {
                target: function(file,flow,isTest){
                    if($scope.flowObj.uploadUris[file.name]){
                        return $scope.flowObj.uploadUris[file.name];
                    }
                    return $scope.fileUploadUrl;
                },
                successStatuses: [200, 201, 202, 308],
                singleFile:false,
                chunkSize: 5*1024*1024,
                testChunks: false,
                simultaneousUploads: 1,
                uploadedFiles: [],
                uploadedVersions: [],
                uploadUris: {},
                headers: function(file,flow,isTest){
                    if(isTest) return;
                    return {
                        'content-range': "bytes " + flow.startByte + "-" + (flow.endByte - 1) + "/" + file.size
                    };
                },
                OnFilesSubmitted: function (files, event, flow) {
                    if(files.length == 0) return;
                    var data = {files: []};
                    for(var i = 0; i < files.length; i++){
                        var f = files[i];
                        data.files.push({name: f.name, size: f.size});
                    }
                    $http.post(currentBaseUrl + 'files/new', data)
                        .success(function (response) {
                            $scope.flowObj.uploadedFiles = angular.extend($scope.flowObj.uploadedFiles, response.uploadedFiles);
                            $scope.flowObj.uploadedVersions = angular.extend($scope.flowObj.uploadedVersions, response.uploadedVersions);
                            $scope.flowObj.uploadUris = angular.extend($scope.flowObj.uploadUris, response.uploadUris);
                            if(response.success){
                                for(var i = 0; i < files.length; i++){
                                    $scope.updateFiles(files[i], $scope.flowObj.uploadedFiles[files[i].name], flow);
                                }
                                flow.upload();
                            }
                        });
                },
                OnFileSuccess: function (file, event, flow, message) {
                    if($scope.flowObj.uploadedVersions[file.name]){
                        var data = {id: $scope.flowObj.uploadedVersions[file.name]};
                        $http.post(currentBaseUrl + 'files/uploaded', data)
                            .success(function (response) {
                                $scope.updateFile(response, $scope.field);
                            });
                    }
                },
                OnFileProgress: function(file, chunk){
                    var data = {id:$scope.flowObj.uploadedVersions[file.name], progress:file.progress()};
                    $scope.updateFile({
                        code:$scope.flowObj.uploadedFiles[file.name].code,
                        status:$scope.flowObj.uploadedFiles[file.name].status,
                        currentSpeed:file.currentSpeed ,
                        averageSpeed:file.averageSpeed ,
                        progress:file.progress()*100}, $scope.field);
                    $http.post(currentBaseUrl + 'files/uploadProgress', data)
                        .success(function (response) {
                        }).error(function(){
                            file.cancel();
                        });
                },
                OnFileError: function(file, message, chunk){
                    var data = {id:$scope.flowObj.uploadedVersions[file.name], progress:file.progress()};
                    $scope.setSelected($scope.flowObj.uploadedFiles[file.name], $scope.field, false);
                    $http.post(currentBaseUrl + 'files/uploadError', data)
                        .success(function (response) {
                            $timeout(function(){
                                $scope.filterFiles();
                            });
                        });
                },
                query: function(file){
                    return {};
                },
                uploadMethod: 'PUT'
            };
            $scope.retryUpload = function(file){
                file.retry();
            };
            $scope.cancelUpload = function(file){
                file.cancel();
                var data = {id:$scope.flowObj.uploadedVersions[file.name], progress:file.progress()};
                $scope.setSelected($scope.flowObj.uploadedFiles[file.name], $scope.field, false);
                $http.post(currentBaseUrl + 'files/uploadCanceled', data)
                    .success(function (response) {
                        $timeout(function(){
                            $scope.filterFiles();
                        });
                    });
            };
            $scope.updateFiles = function (newFile, $message, $flow) {
                if(!$scope.tech) $scope.filterFiles();
                if($scope.toggleable && !!$message){
                    var data = angular.fromJson($message);
                    $scope.toggleFile(data, $scope.field);
                }
            };
            $scope.onFilesSubmitted = function ($files, $flow) {
                /* Chequear que lo que suban coincida con los filtros */
                $flow.upload();
            };
            $scope.fileTypes = [];
            if($scope.config){
                if($scope.config.types){
                    angular.forEach($scope.config.types, function(type){
                      switch(type){
                          /*video*/
                          case 0: $scope.fileTypes.push('video/*, .mxf, .mkv'); break;
                          /* image */
                          case 1: $scope.fileTypes.push('image/*'); break;
                          /* audio */
                          case 2: $scope.fileTypes.push('audio/*'); break;
                          /* document */
                          case 3: $scope.fileTypes.push('text/*, .csv, .doc, .docx, .pdf, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel'); break;
                          /* other */
                          case 4: $scope.fileTypes.push('.zip, .rar'); break;
                      }

                    })
                }
            }

            $scope.filterType = function(fileType){
                val = 0;
                angular.forEach($scope.config.types, function(type){
                    if(parseInt(type) == parseInt(fileType)){
                        val = 1;
                    }
                });
                return val;
            };

            $scope.init = function (record) {
                $scope.record = record;
            };
            $scope.showMyFiles = !$scope.toggleable;
            $scope.toggle = function(){
                $scope.showMyFiles = !$scope.showMyFiles;
            };
            $scope.pagination = {
                total: 0,
                perPage: 10,
                page: 1,
                query: '',
                sortBy: 'name',
                sortInverted: false,
                selectedTypes: [],
                inEntries: false,
                metadataFields: [],
                statusFilters: [],
                encodeErrorFiles: false
            };
            $scope.contest = contest;
            if (angular.isDefined($scope.entry)) {
                $scope.pagination.user = $scope.entry.user;
                $scope.$watch('entry.user', function (prevval, newval) {
                    if (angular.isDefined(prevval) && prevval.id == newval.id) return;
                    if (angular.isDefined($scope.entry.user.id) || angular.isDefined($scope.entry.user.user_id)) {
                        $scope.pagination.user = $scope.entry.user;
                        $scope.record.files = [];
                        $scope.filterFiles();
                    }
                }, true);
            }

            $scope.encodeValues = function(params, type){
                var reEncodeConfig = {
                    start: 'HH:MM:SS',
                    end: 'HH:MM:SS'
                };
                var values = angular.fromJson(params);
                switch(type){
                    case 'start': if(values) reEncodeConfig.start = values['start'];
                    case 'end': if(values) reEncodeConfig.end = values['end'];
                }
                return reEncodeConfig;

            };

            var filters = function(){
                $scope.filesRows = [];
                $scope.lastFileShown = false;
                lastFileLoaded = 0;
                $scope.filteredFiles = $filter('filesStatus')($scope.files, $scope.statusFilters);
                //$scope.filteredFiles = $filter('filesSearch')($scope.filteredFiles, $scope.pagination.query);
                //$scope.filteredFiles = $filter('filesTypes')($scope.filteredFiles, $scope.pagination.selectedTypes);
                $scope.filteredFiles = $filter('filesInEntry')($scope.filteredFiles, $scope.filesInEntry);
                //$scope.filteredFiles = $filter('filesErrorEncode')($scope.filteredFiles, $scope.encodeErrorFiles);
                //$scope.filteredFiles = $filter('filesMetadataValueId')($scope.filteredFiles, $scope.fileMetadataId);
                //$scope.pagination.total = $scope.filteredFiles.length;
                $scope.loadMoreFiles();
            };

            $scope.toggleType = function (type) {
                var index = $scope.pagination.selectedTypes.indexOf(type);
                if (index == -1) $scope.pagination.selectedTypes.push(type);
                else $scope.pagination.selectedTypes.splice(index, 1);
                $scope.filterFiles();
                //if($scope.tech) filters();
                //else $scope.filterFiles();
            };

            $scope.filesInEntry = false;
            $scope.toggleFilesInEntry = function(){
                $scope.pagination.inEntries = !$scope.pagination.inEntries;
                $scope.filterFiles();
                /*if($scope.filesInEntry == false) $scope.filesInEntry = true;
                else $scope.filesInEntry = false;*/
                //filters();
                //$scope.loadMoreFiles();
            };

            $scope.encodeErrorFiles = false;
            $scope.toggleEncodeErrorFiles = function(){
                if($scope.pagination.encodeErrorFiles == false) $scope.pagination.encodeErrorFiles = true;
                else $scope.pagination.encodeErrorFiles = false;
                $scope.filterFiles();
                /*filters();
                $scope.loadMoreFiles();*/
            };


            $scope.OpenDownloadFiles = function(files){
                $scope.filesRows = [];
                var modal = $uibModal.open({
                    templateUrl: currentBaseUrl + 'view/downloadFiles',
                    controller: 'DownloadFilesController',
                    resolve: {
                        files: function () {
                            return $http.post(currentBaseUrl + 'files', angular.extend($scope.pagination,{downloadAll: true},{user:$scope.user},{tech: $scope.tech}), {timeout: $scope.filterRequestCanceler.promise}).success(function (response) {
                                $scope.filteredFiles = response.files;
                            });
                        }
                    }
                });
                modal.result.then(function () {
                }, function () {
                    filters();
                });
            };

            $scope.delFiles = false;
            $scope.toggleDeletedFiles = function(){
                if($scope.delFiles == false) {
                    $scope.delFiles = true;
                    $scope.pagination.total = $scope.deletedFiles.length;
                }
                else{
                    $scope.delFiles = false;
                    filters();
                }
            };

            $scope.viewList = true;
            $scope.toggleView = function (type) {
                $scope.viewList = !$scope.viewList;
            };
            $scope.typeSelected = function (type) {
                return $scope.pagination.selectedTypes.indexOf(type) != -1;
            };
            $scope.filterRequestCanceler = $q.defer();

            $scope.filesRows = [];
            var lastFileLoaded = 0;
            $scope.lastFileShown = false;

            var setFiles = function(files){
                $scope.files = files;
                $scope.allFiles = files;
                filters();
            };

            /* La cantidad de files q va a devolver el post hasta llegar al final */
            //var countFilesValue = 500;

            $scope.filterFiles = function (download, update) {
                if(update){
                    $scope.filteredFiles = [];
                }
                //if(countFiles == null){ countFiles = countFilesValue}
                if(!$scope.tech && !$scope.files && (!$scope.user || typeof $scope.user != 'object')) return;
                if (angular.isDefined($scope.filterRequestCanceler)) $scope.filterRequestCanceler.resolve();
                $scope.filesLoading = true;
                $http.post(currentBaseUrl + 'files', angular.extend($scope.pagination,{downloadAll: download},{user:$scope.user},{tech: $scope.tech}), {timeout: $scope.filterRequestCanceler.promise}).success(function (response) {
                        $scope.filteredFiles = response.files;
                        $scope.fileMetadataIdIndex = [];
                        angular.forEach(response.filesMetadataValueIndex, function(filesMetadataValueIndex){
                            $scope.fileMetadataIdIndex.push(filesMetadataValueIndex['label']);
                        });
                        $scope.pagination.total = response.total;
                        $scope.updateList($scope.filteredFiles, $scope.field);
                        $scope.pagination.page = response.page;
                        $scope.filesLoading = false;
                        $scope.superAdmin = response.superAdmin;
                        if ($scope.tech || update) {
                            setFiles($scope.filteredFiles);
                            $scope.deletedFiles = response.deleted;
                        }
                }).error(function (response) {
                });
            };
            var filesPerRow = 10;
            $scope.loadMoreFiles = function(){
                if(!$scope.filteredFiles) return;
                if(lastFileLoaded > $scope.filteredFiles.length) return;
                $scope.filesRows.push($scope.filteredFiles.slice(lastFileLoaded, lastFileLoaded + filesPerRow));
                lastFileLoaded += filesPerRow;
                $scope.lastFileShown = lastFileLoaded > $scope.filteredFiles.length;
            };
            var lastH;
            var w = angular.element($window);
            w.bind("scroll", function(e) {
                var dh = angular.element(document).height();
                if(lastH != dh){
                    if(w.scrollTop() + w.height() > dh - 100) {
                        lastH = dh;
                        $scope.$apply(function(){
                            $scope.loadMoreFiles();
                        });
                    }
                }
            });

            $scope.updateList = function (newFiles, list) {
                angular.forEach(newFiles, function(newFile, key) {
                    $scope.updateFile(newFile, list);
                });
            };
            $scope.updateFile = function(newFile, list) {
                var listFile = $filter('filter')(list, {code: newFile.code}, true);
                angular.forEach(listFile, function(lFile, key) {
                    lFile.status = newFile.status;
                    lFile.progress = newFile.progress;
                    lFile.currentSpeed = newFile.currentSpeed;
                    lFile.averageSpeed = newFile.averageSpeed;
                });
            };

            $scope.changeStatus = function(file, status) {
                if(status == 2) {
                    var modalInstance = $uibModal.open({
                        backdrop: 'static',
                        keyboard: false,
                        templateUrl: 'editError.html',
                        controller: 'changeStatusFile',
                        resolve: {
                            file: function () {
                                return file;
                            },
                            status: function () {
                                return status;
                            }
                        },
                        scope: $scope
                    });
                    modalInstance.result.then(function (result) {
                        if (result) {
                            file.tech_status = status.toString();
                            file.description = result.description;
                            filters();
                        }
                    });
                }else{
                    $http.post(currentBaseUrl+'changeFileStatus', {fileId: file.id, status: status}).success(function(rsp){
                        file.tech_status = status.toString();
                        console.log(rsp);
                        file.description = rsp.description;
                        filters();
                    });
                }
            };

            $scope.statusFilters = [];
            $scope.toggleFilterBy = function(status){
                var index = $scope.pagination.statusFilters.indexOf(status);
                if (index != -1) {$scope.pagination.statusFilters.splice(index, 1);}
                else $scope.pagination.statusFilters.push(status);
                $scope.filterFiles();
            };

            /*$scope.entryFilters = [];
            $scope.toggleFilterByEntry = function(status){
                var index = $scope.entryFilters.indexOf(status);
                if (index != -1) {$scope.entryFilters.splice(index, 1);}
                else $scope.entryFilters.push(status);
                $scope.pagination.entryFilters = $scope.entryFilters;
                $scope.filterFiles();
            };*/

            $scope.toggleMetadataValue = function(status){
                var index = $scope.pagination.metadataFields.indexOf(status);
                if (index != -1) {$scope.pagination.metadataFields.splice(index, 1);}
                else $scope.pagination.metadataFields.push(status);
                $scope.filterFiles();
            }

            $scope.setSortBy = function (by) {
                if ($scope.pagination.sortBy == by) {
                    $scope.pagination.sortInverted = !$scope.pagination.sortInverted;
                } else {
                    $scope.pagination.sortBy = by;
                }
                $scope.filteredFiles = $filter('orderBy')($scope.filteredFiles, $scope.pagination.sortBy, $scope.pagination.sortInverted);
                //$scope.filterFiles();
            };
            $scope.$watch('pagination.query', function () {
                $scope.filterFiles();
                /*if(!$scope.tech) $scope.filterFiles();
                else{
                    filters();
                    $scope.loadMoreFiles();
                }*/
            });
            $scope.$watch('pagination.page', function () {
                $scope.filterFiles();
            });

            //Upload prevent close
            $scope.uploader = {};
            if ($scope.$parent.onunloads) {
                $scope.$parent.onunloads.push(function () {
                    if (!$scope.uploader.flow) return;
                    if ($scope.uploader.flow.isUploading()) {
                        return "Está subiendo archivos, está seguro que desea salir? Las transferencias se cancelarán";
                    }
                });
            }
            $rootScope.$on('$locationChangeStart', function (event) {
                if (!$scope.uploader.flow) return;
                if ($scope.uploader.flow.isUploading()) {
                    var answer = confirm("Está subiendo archivos, está seguro que desea salir? Las transferencias se cancelarán");
                    if (!answer) {
                        event.preventDefault();
                    }
                }
            });
            $scope.saveFilename = function (file) {
                $http.post(currentBaseUrl + 'saveFile', {id: file.id, name: file.name, tech: $scope.tech}).success(function (data) {
                    if (data.errors) {
                        $scope.errors = data.errors;
                    } else {
                        file.editable = false;
                        $scope.updateFiles();
                    }
                });
            };

            $scope.secondsToHms = function (d) {
                if(d <= 0) return;
                d = Number(d);
                var h = Math.floor(d / 3600);
                var m = Math.floor(d % 3600 / 60);
                var s = Math.floor(d % 3600 % 60);
                return ((h > 0 ? h + ":" + (m < 10 ? "0" : "") : "") + m + ":" + (s < 10 ? "0" : "") + s);
            }

            $scope.reEncode = [];
            $scope.showReencode = [];
            $scope.ReEncodeFile = function(file, params){
                if(params){
                    if(params.start || params.end){
                        var param = {'start': params.start, 'end': params.end}
                    }
                    else{
                        var param = {'rotate': params};
                    }
                }
                $http.post(currentBaseUrl + 'reEncode', {id: file.id, param: param}).success(function (data) {
                    $scope.showReencode[file.id] = data;
                });
                $scope.reEncode = [];
            };
            $scope.CreateNewVersion = function(file, format){
                $http.post(currentBaseUrl + 'createVersion', {id: file.id, format: format.id}).success(function (data) {
                    file.contest_file_versions = data.file.contest_file_versions;
                });
            };
            $scope.RemakeThumbs = function(file){
                $http.post(currentBaseUrl + 'makeThumbs', {id: file.id}).success(function (data) {
                    file.thumb = data.file.thumb+'?a='+Math.random();
                });
            };

            $scope.formatBytes = function (bytes) {
                //if(bytes == 0) return '0 Byte';
                var k = 1000; // or 1024 for binary
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
                var i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(1024, i)).toPrecision(3)) + ' ' + sizes[i];
            };

            $scope.reEncodeQueue = function(file){
                var encodeStatus = false;
                encodeStatus = file.status;
                angular.forEach(file.contest_file_versions, function(item){
                    if(file.status == 2 && item.config){
                        encodeStatus = 'reEncoded';
                    }
                    if(file.status == 0 && item.config){
                        encodeStatus ='reEncodeQueue';
                    }
                    if(file.status == 1 && item.config){
                        encodeStatus = 'reEncoding';
                    }
                });
                return encodeStatus;
            };

            angular.element(document).on('click', '.searchBox', function (e) {
                e.stopPropagation();
            });
            $scope.deleteFile = function (file, list) {
                var modal = $uibModal.open({
                    templateUrl: currentBaseUrl + 'view/deleteFile',
                    controller: 'DeleteFileController',
                    resolve: {
                        file: function () {
                            return file;
                        },
                        captchaUrl: function ($http) {
                            return $http.get(rootUrl + 'captcha/url').success(function (data) {
                                return data.data;
                            });
                        },
                        tech: function() {
                            return $scope.tech;
                        }
                    }
                });
                modal.result.then(function () {
                    file.deleted = 1;
                    filters();
                    $scope.setSelected(file, $scope.field, false);
                    $scope.loadMoreFiles();
                    for(var i in file.contest_file_versions){
                        if(file.contest_file_versions[i].source == "1"){
                            var fullName = file.name+"."+file.contest_file_versions[i].extension;
                            var uploadedFile = $scope.flowObj.uploadedFiles[fullName];
                            uploadedFile.cancel();
                            break;
                        }
                    }
                }, function () {
                });
            };

            $scope.getTypeIcon = function (type) {
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
            $scope.getTypeString = function (type) {
                switch (parseInt(type)) {
                    case 0:
                        return "Video";
                    case 1:
                        return "Imagen";
                    case 2:
                        return "Audio";
                    case 3:
                        return "Documento";
                    case 4:
                        return "Otro";
                    default:
                        return "Otro";
                }
            };
            $scope.getTypeTextStyle = function (type) {
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

            // Timer para chequear el status de los archivos.
            var checkStatus = $interval(
                function(){
                    $scope.updateFiles();
                    console.log("Check status");
                },
                60000
            );
            // When the DOM element is removed from the page,
            // AngularJS will trigger the $destroy event on
            // the scope. This gives us a chance to cancel any
            // pending timer that we may have.
            $scope.$on(
                "$destroy",
                function( event ) {
                    $interval.cancel( checkStatus );
                }
            );

            if(!$scope.showSelection) return;

            $scope.addFile = function (file, list) {
                if (!$scope.isSelected(file, list)) list.push(file);
            };
            $scope.removeFile = function (file, list) {
                var fileIndex = -1;
                $filter('filter')(list, function (item, index) {
                    if (file.id == item.id) {
                        fileIndex = index;
                        return true;
                    }
                    return false;
                });
                if (fileIndex != -1) $scope.removeItem(fileIndex, list);
            };
            $scope.removeItem = function (index, list) {
                list.splice(index, 1);
            };
            $scope.isSelected = function (file, list) {
                var l = $filter('filter')(list, {code: file.code}, true);
                return l.length > 0;
            };
            $scope.toggleFile = function (file, list) {
                var v = !$scope.isSelected(file, list);
                $scope.setSelected(file, list, v);
            };
            $scope.setSelected = function (file, list, v) {
                if (!v) $scope.removeFile(file, list);
                else $scope.addFile(file, list);
            };
        }
    };
}]).directive('dateField', function($filter, moment){
    return {
        require: 'ngModel',
        scope: {
            ngModel : '=',
            minDate: "=",
            maxDate: "="
        },
        link: function(scope, element, attrs, modelCtrl) {
            var encodeDate = function(inputValue){
                if(!inputValue){
                    modelCtrl.$setViewValue(null);
                    modelCtrl.$render();
                    return null;
                }
                var dateValue = new Date(inputValue);
                //var mDate = moment(new Date(inputValue));
                //var transformedInput = mDate.format('YYYY-MM-DD');
                var transformedInput = $filter('date')(dateValue, 'yyyy-MM-dd HH:mm:ss');
                modelCtrl.$setViewValue(transformedInput);
                modelCtrl.$render();
                return transformedInput;
            };
            scope.$watch(function(){return modelCtrl.$viewValue;}, encodeDate);
            modelCtrl.$parsers.push(function (inputValue) {
                return encodeDate(inputValue);
            });
        }
    };
}).directive('dateLimiter', function($filter, moment){
    return {
        scope: {
            minDate: "=",
            maxDate: "="
        },
        link: function(scope, element, attrs) {
            scope.minDate = scope.minDate ? moment.utc(scope.minDate, 'YYYY-MM-DD HH:mm:ss') : null;
            scope.maxDate = scope.maxDate ? moment.utc(scope.maxDate, 'YYYY-MM-DD HH:mm:ss') : null;
            scope.$parent.dateBeforeRender = function($view, $dates) {
                if (scope.maxDate) {
                    $dates.filter(function (date) {
                        return date.localDateValue() >= scope.maxDate.valueOf();
                    }).forEach(function (date) {
                        date.selectable = false;
                    })
                }
                if (scope.minDate) {
                    var activeDate = scope.minDate.clone().subtract(1, $view).add(1, 'minute');
                    $dates.filter(function (date) {
                        return date.localDateValue() <= activeDate.valueOf();
                    }).forEach(function (date) {
                        date.selectable = false;
                    })
                }
            };
        }
    };
}).directive('trans', function($filter, Languages){
    return {
        require: 'ngModel',
        template: '<span ng-bind-html="getTrans(ngModel) | nl2br:true "></span>',
        replace: true,
        scope: {
            ngModel : '=',
            transProp: "=transProp"
        },
        link: function(scope, element, attrs, modelCtrl) {
            var l = function(){
                return angular.isDefined(scope.$parent.selectedLang)
                    ? scope.$parent.selectedLang : Languages.Active;
            };
            scope.getTrans = function(c){
                var lang = l();
                if(!c) return;
                if(lang == Languages.Default){
                    return c[scope.transProp] != null ? c[scope.transProp] : '';
                }else{
                    if(scope.transProp == 'description'){
                        if(c.trans)
                            if(!c.trans[lang][scope.transProp]) return '';
                    }
                    return c.trans != null &&
                    c.trans[lang] != null &&
                    c.trans[lang][scope.transProp] != null && c.trans[lang][scope.transProp] != "" ? c.trans[lang][scope.transProp] : c[scope.transProp];
                }
            };
        }
    };
}).directive('transcat', function($filter, Languages){
    return {
        require: 'ngModel',
        template: '<span ng-bind-html="getTrans(ngModel) | nl2br:true " tooltip-placement="top" uib-tooltip-html="getFullTrans(ngModel) | nl2br:true"></span>',
        replace: true,
        scope: {
            ngModel : '=',
            transProp: "=transProp"
        },
        link: function(scope, element, attrs, modelCtrl) {
            var l = function(){
                return angular.isDefined(scope.$parent.selectedLang)
                    ? scope.$parent.selectedLang : Languages.Active;
            };
            scope.getTrans = function(c){
                var lang = l();
                if(!c) return;
                if(lang == Languages.Default){
                    return c[scope.transProp] != null ? (c[scope.transProp].length > 15 ? c[scope.transProp].substring(0,20)+"..." : c[scope.transProp]) : '';
                }else{
                    if(scope.transProp == 'description'){
                        if(!c.trans[lang][scope.transProp]) return '';
                    }
                    return c.trans != null &&
                    c.trans[lang] != null &&
                    c.trans[lang][scope.transProp] != null && c.trans[lang][scope.transProp] != "" ?
                        (c.trans[lang][scope.transProp].length > 15 ? c.trans[lang][scope.transProp].substring(0,20)+"..." : c.trans[lang][scope.transProp]) :
                        (c[scope.transProp].length > 15 ? c[scope.transProp].substring(0,20)+"..." : c[scope.transProp]);
                }
            };
            scope.getFullTrans = function(c){
                var lang = l();
                if(!c) return;
                if(lang == Languages.Default){
                    return c[scope.transProp] != null ? c[scope.transProp] : '';
                }else{
                    if(scope.transProp == 'description'){
                        if(!c.trans[lang][scope.transProp]) return '';
                    }
                    return c.trans != null &&
                    c.trans[lang] != null &&
                    c.trans[lang][scope.transProp] != null && c.trans[lang][scope.transProp] != "" ? c.trans[lang][scope.transProp] : c[scope.transProp];
                }
            };
        }
    };
}).directive('transOptions', function($filter, Languages){
    return {
        require: 'ngModel',
        template: '<span ng-bind-html="getLabel(ngModel) | nl2br:true "></span>',
        replace: true,
        scope: {
            ngModel : '=',
            transProp: "=transProp",
            transIndex: "=transIndex"
        },
        //field.config.columns[$index]
        //field.trans.columns[key][$index]
        link: function(scope, element, attrs, modelCtrl) {
            var l = function(){
                return angular.isDefined(scope.$parent.selectedLang)
                    ? scope.$parent.selectedLang : Languages.Active;
            };
            scope.getLabel = function(c){
                var lang = l();
                if(l() == Languages.Default){
                    return c.config[scope.transProp][scope.transIndex];
                }else{
                    return c.trans != null &&
                    c.trans[scope.transProp] != null &&
                    c.trans[scope.transProp][lang] != null &&
                    c.trans[scope.transProp][lang][scope.transIndex] != null &&
                    c.trans[scope.transProp][lang][scope.transIndex] != ""
                        ?
                        c.trans[scope.transProp][lang][scope.transIndex]
                        :
                        c.config[scope.transProp][scope.transIndex];
                }
            };
        }
    };
}).directive('filesGallery', function($filter, Lightbox){
    return {
        transclude: true,
        template: '<div ng-transclude></div>',
        scope: {
            ngFilesList: '=',
            ngFilesField: '='
        },
        link: function(scope, element, attrs, modelCtrl) {
            scope.getName = scope.$parent.getName;
            scope.getNoName = scope.$parent.getNoName;
            scope.$parent.openGallery = function(images, index, parentIndex, tech){
                if(parentIndex) index = (parentIndex * 10) + index;
                if(tech) images.tech = true;
                var modal = Lightbox.openModal(images, index, {size:'lg', scope: scope, resolve:{
                    field: function(){
                        return scope.ngFilesField;
                    }
                }});
                modal.result.then(function () {
                }, function () {
                });
            }
        }
    };
}).directive('headerBanner', function($filter, Languages, Flash, $timeout, contest){
    return {
        scope: {
            banner: '=',
            status: '=',
            controls: '=',
        },
        link: function($scope, element, attrs) {
            $scope.status = {editing: false, saving: false, saved: false, advanced: false};
            if(contest) $scope.status.bannerObj = contest.banners[$scope.banner];
            $scope.$watch(function(){return $scope.status.editing;}, function(v){
                if(v){
                    $scope.status.saved = false;
                    $scope.status.prevContent = $scope.status.bannerObj.html;
                }
            })
        },
        controller: function ($scope, $http, rootUrl) {
            $scope.controls = {
                save: function () {
                    $scope.status.saving = true;
                    var data = {type: $scope.banner, content: $scope.status.bannerObj.html};
                    $http.post(rootUrl + 'api/contest/'+ contest.code+ '/asset', data).success(function(response, status, headers, config){
                        $scope.status.saving = false;
                        if(response.errors){
                            $scope.errors = response.errors;
                            Flash.clear($scope);
                        }else if(response.error){
                            Flash.show(response.error, 'danger', $scope);
                        }else{
                            $scope.status.editing = false;
                            $scope.status.saved = true;
                            $timeout(function(){
                                $scope.status.saved = false;
                            }, 3000);
                            //Flash.show(response.flash, 'success', $scope);
                        }
                    }).error(function(data, status, headers, config){
                        $scope.status.saving = false;
                        Flash.show(data.error.message, 'danger', $scope);
                    });
                },
                toggle: function () {
                    $scope.status.advanced = !$scope.status.advanced;
                },
                cancel: function () {
                    $scope.status.bannerObj.html = $scope.status.prevContent;
                    $scope.status.editing = false;
                }
            };
        }
    };
}).directive('showDuringResolve', function($rootScope) {

    return {
        link: function(scope, element) {

            element.addClass('ng-hide');

            var unregister = $rootScope.$on('$routeChangeStart', function() {
                element.removeClass('ng-hide');
            });

            scope.$on('$destroy', unregister);
        }
    };
}).directive('videoFullscreen', function() {
    return {
        restrict: 'AE',
        scope:false,
        link: function(scope, element) {
            var vid = element[0];

            vid.onclick = function (e) {
                console.log('click');
                console.log(vid);
                if (vid.requestFullscreen) {
                    vid.requestFullscreen();
                } else if (vid.msRequestFullscreen) {
                    vid.msRequestFullscreen();
                } else if (vid.webkitRequestFullscreen) {
                    vid.webkitRequestFullscreen();
                }
            };
            vid.onStartPlaying = function (e) {
                console.log('play');
                console.log(vid);
                if (vid.requestFullscreen) {
                    vid.requestFullscreen();
                } else if (vid.msRequestFullscreen) {
                    vid.msRequestFullscreen();
                } else if (vid.webkitRequestFullscreen) {
                    vid.webkitRequestFullscreen();
                }
            };
            vid.onTouch = function (e) {
                console.log('play');
                console.log(vid);
                if (vid.requestFullscreen) {
                    vid.requestFullscreen();
                } else if (vid.msRequestFullscreen) {
                    vid.msRequestFullscreen();
                } else if (vid.webkitRequestFullscreen) {
                    vid.webkitRequestFullscreen();
                }
            };
            vid.touch = function (e) {
                console.log('play');
                console.log(vid);
                if (vid.requestFullscreen) {
                    vid.requestFullscreen();
                } else if (vid.msRequestFullscreen) {
                    vid.msRequestFullscreen();
                } else if (vid.webkitRequestFullscreen) {
                    vid.webkitRequestFullscreen();
                }
            };
        }
    };
}).directive('resolveLoader', function($rootScope, $timeout) {

    return {
        restrict: 'E',
        replace: true,
        template: '<div class="alert alert-success ng-hide"><strong>Welcome!</strong> Content is loading, please hold.</div>',
        link: function(scope, element) {

            $rootScope.$on('$routeChangeStart', function(event, currentRoute, previousRoute) {
                if (previousRoute) return;

                $timeout(function() {
                    element.removeClass('ng-hide');
                });
            });

            $rootScope.$on('$routeChangeSuccess', function() {
                element.addClass('ng-hide');
            });
        }
    };
}).directive('voteTool', function() {
    return {
        restrict: 'E',
        templateUrl: 'voteTool.html',
        scope:{
            'voteSession': '=',
            'myVote': '=',
            'myEntry': '=',
            'readOnly': '=',
            'hideResult': '=',
            'results': '=',
            'cat': '=',
            'votesPerCat': '=',
        },
        link: function(scope,element, attr){
            scope.getCatId = function(){
                return scope.cat;
            };

            if(scope.myVote == null){
                scope.myVote = [];
                scope.myVote[scope.cat] = {};
            }
            if(!scope.voteSession) return;
            if(!scope.voteSession.config && scope.voteSession.vote_type == 1){
                scope.voteSession.config = {max:10,min:1,step:1,usecriteria:false};
            }
            scope.isEmpty = function(item){
                if(Object.keys(item).length == 0) return true;
                return false;
            };

            scope.orderByScore = function(){
                scope.voteItems.sort(function(a, b) {
                    return parseFloat(b.score) - parseFloat(a.score);
                });
            };

            if(scope.voteSession.vote_type == 4){ // Metalero
                scope.voteItems = scope.voteSession.config.extra;
                var perCategory = {};
                var perCategorySelected = {};
                angular.forEach(scope.voteSession.config.extra, function(extra){
                    perCategory[extra.name] = extra.countPerCategory;
                    perCategorySelected[extra.name] = 0;
                })
            }
            scope.controlPerCategory = function(item, cat){
                var validator = true;
                angular.forEach(scope.votesPerCat, function(entries){
                    angular.forEach(entries, function(entry){
                        if(entry.votes[cat].vote){
                            if(entry.votes[cat].vote['name'] === item.name){
                                if(perCategory[item.name] !== 0){// && perCategorySelected[item.name] <= perCategory[item.name]){
                                    validator = false;
                                }
                                //perCategorySelected[item.name]++;
                            }
                        }
                    });
                });
                return validator;
            }
            scope.voteResult = {};
            scope.BeforeVoteUpdate = function(){
                if(scope.$parent.BeforeVoteUpdate != null) scope.$parent.BeforeVoteUpdate(scope.myEntry, scope.voteResult);
            };
            scope.VoteUpdated = function(myVote){
                if(scope.$parent.VoteUpdated != null) scope.$parent.VoteUpdated(scope.myEntry, myVote);
            };
            scope.getTotalVotes = function(){
                if(scope.voteSession.config.max && scope.voteSession.config.min !== null && scope.voteSession.config.step)
                    return new Array((scope.voteSession.config.max - (scope.voteSession.config.min - 1))/scope.voteSession.config.step);
            };
            scope.getCriteriaResult = function(){
                if(scope.myVote == null || scope.myVote[scope.cat] == null || scope.myVote[scope.cat].abstain) return false;
                if(scope.myVote[scope.cat].vote == null) return false;
                if(!scope.voteSession.config.usecriteria) return scope.myVote[scope.cat].vote;
                if(angular.isDefined(scope.myVote[scope.cat].final)) return scope.myVote[scope.cat].final;
                var result = 0;
                for(var i = 0; i < scope.voteSession.config.criteria.length; i++){
                    if(scope.myVote[scope.cat].vote[i] == null) return false;
                    var weight = scope.voteSession.config.criteria[i].weight;
                    weight = weight != null && weight != '' ? parseFloat(weight)/100 : 1 / scope.voteSession.config.criteria.length;
                    result += weight * scope.myVote[scope.cat].vote[i];
                }
                return scope.roundNumber(result);
            };
            scope.roundNumber = function(i) {
                return Math.round(i * 100)/100;
            };
        }
    };
}).directive('voteWeightedTool', function() {
    return {
        restrict: 'EA',
        templateUrl: 'voteWeightedTool.html',
        scope:{
            'voteSession': '=',
            'myVote': '=',
            'cat': '=',
            'keys': '=',
            'inscription': '=',
        },
        link: function(scope,element, attr){
            if(!scope.voteSession) return;
            if(!scope.voteSession.config){
                scope.voteSession.config = {max:10,min:1,step:1};
            }
            scope.getTotalVotes = function(){
                return new Array((scope.voteSession.config.max - (scope.voteSession.config.min - 1))/scope.voteSession.config.step);
            };
            scope.ShowVoteResult = function(votes){
                var score = 0;
                if(Object.keys(votes).length === 1){
                    return votes[0].name.substring(0,1)+" - "+votes[0].score;
                };
                angular.forEach(votes, function(vote){
                    score = score + vote.score;
                });
                return score;
            };

            scope.getCriteriaResult = function(){
                if(scope.voteSession.vote_type == 0) // Veritron
                {   if(scope.myVote[scope.cat].vote){
                        if(scope.myVote[scope.cat].vote[0] == 0) return 'No';
                    }
                }
                if(scope.myVote == null || scope.myVote[scope.cat] == null || scope.myVote[scope.cat].abstain) return false;
                if(scope.myVote[scope.cat].vote == null) return false;
                if(!scope.voteSession.config.usecriteria){
                    if(scope.myVote[scope.cat].vote == 0) return '0';
                    return Array.isArray(scope.myVote[scope.cat].vote) || ((typeof scope.myVote[scope.cat].vote === "object") && (scope.myVote[scope.cat].vote !== null))? scope.myVote[scope.cat].vote[0] : scope.myVote[scope.cat].vote;
                }
                var result = 0;
                for(var i = 0; i < scope.voteSession.config.criteria.length; i++){
                    if(scope.myVote[scope.cat].vote[i] == null) return false;
                    var weight = scope.voteSession.config.criteria[i].weight;
                    weight = weight != null && weight != '' ? parseFloat(weight)/100 : 1 / scope.voteSession.config.criteria.length;
                    result += weight * scope.myVote[scope.cat].vote[i];
                }
                return scope.roundNumber(result);
            };
            scope.roundNumber = function(i) {
                return Math.round(i * 100)/100;
            };
            scope.borderRadius = function(data){
                if(!data) return "0";
                if(data.length == 1) return "5px 0 0 5px; !important";
                if(data.length > 1){
                    if(data.key == 0) return "5px 0 0 0px; !important";
                    if(data.key == (data.length - 1)) return "0px 0 0 5px; !important";
                    return "0px 0 0 0px; !important";
                }
            }
        }
    };
}).directive('judgeProgress', function($rootScope, $timeout) {
    return {
        restrict: 'E',
        scope:{
            'judge': '='
        },
        templateUrl: 'judgeProgress.html'
    };
}).directive('googleChart', ['$window', function($window){
    return {
        restrict: 'AE',
        scope: {
            gChart: '='
        },
        link: function (scope, element, attr) {
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var chart = {};
                if (scope.gChart.type == 'gauge') {
                    chart = new google.visualization.Gauge(element[0]);
                     google.visualization.events.addListener(chart, 'ready', function () {
                         $('tbody', element[0]).css('background-color', '#2e3338');
                         $('tbody', element[0]).append('<tr style="border: 0; padding: 0; margin: 0"><td style="width: 250px;" colspan="2" align="center">CPU</td></tr>');
                     });
                }
                if (scope.gChart.type == 'bars') {
                    chart = new google.visualization.BarChart(element[0]);
                }
                if (scope.gChart.type == 'pie') {
                    chart = new google.visualization.PieChart(element[0]);
                }
                if (scope.gChart.type == 'area') {
                    chart = new google.visualization.AreaChart(element[0]);
                }
                if (chart) {
                    var data = google.visualization.arrayToDataTable(scope.gChart.data);
                    chart.draw(data, scope.gChart.options);
                }
            }
            angular.element($window).bind('resize', drawChart);
            scope.$on('$rootChangeStart', function(){
                angular.element($window).unbind('resize', drawChart);
            })
        }
    }
}]).directive('copyToClipboard',  function ($window) {
    //<button  copy-to-clipboard="scope" class="button"></button>
    var body = angular.element($window.document.body);
    var textarea = angular.element('<textarea/>');
    textarea.css({
        position: 'fixed',
        opacity: '0'
    });

    function copy(toCopy) {
        textarea.val(toCopy);
        body.append(textarea);
        textarea[0].select();

        try {
            var successful = document.execCommand('copy');
            if (!successful) throw successful;
        } catch (err) {
            console.log("failed to copy", toCopy);
        }
        textarea.remove();
    }

    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            element.bind('click', function (e) {
                copy(attrs.copyToClipboard);
            });
        }
    }
}).directive('bindUnsafeHtml', ['$compile', function ($compile) {
    return function(scope, element, attrs) {
        scope.$watch(
            function(scope) {
                // watch the 'bindUnsafeHtml' expression for changes
                return scope.$eval(attrs.bindUnsafeHtml);
            },
            function(value) {
                // when the 'bindUnsafeHtml' expression changes
                // assign it into the current DOM
                element.html(value);

                // compile the new DOM and link it to the current
                // scope.
                // NOTE: we only compile .childNodes so that
                // we don't get into infinite loop compiling ourselves
                $compile(element.contents())(scope);
            }
        );
    };
}]).directive('file', function () {
    return {
        scope: {
            file: '='
        },
        link: function (scope, el, attrs) {
            el.bind('change', function (event) {
                var file = event.target.files[0];
                scope.file = file ? file : undefined;
                scope.$apply();
            });
        }
    };
}).directive('html5vfix', ['$timeout', function($timeout) {
    return {
        restrict: 'A',
        link: function(scope, element, attr) {
            var el = angular.element(element);
            var player = el.closest('video,audio');
            player.load();
            attr.$set('src', attr.vsrc);
        }
    }
}]).directive('imageZoomer', function($interval, $timeout) {
    var container = null;
    var minScale = 1, maxScale = 1;
    var maxed = false;
    var lastPos = null;
    var lastDelta = null;
    var tweenInt = null;
    return {
        restrict: 'AE', //attribute or element
        scope: {
            version: '=',
            thumb: '='
        },
        replace: false,
        template: '<div ng-class="{\'dragging\': dragging, \'loading\': loading, \'draggable\': !fitted, \'center-y\': centerY, \'center-x\': centerX, \'fitted\': fitted}" ng-mousedown="onMDown($event)" ng-mouseup="onMUp($event)" ng-mousemove="onMove($event)" ng-mouse-wheel ng-mouse-wheel-down="onWheel($event, false)" ng-mouse-wheel-up="onWheel($event, true)" ng-dblclick="onDblClick($event);" unselectable="on" onselectstart="return false;">' +
        '<img class="preview" ng-src="{{thumb}}" alt="" ng-onload="onImageThumbLoad();" style="width:{{version.width * scale}}px;height:{{version.height * scale}}px; left: {{left}}px; top: {{top}}px;"/>' +
        '<img class="image" ng-src="{{version.url}}" alt="" ng-onload="onImageLoad();"' +
        ' style="width:{{version.width * scale}}px; height:{{version.height * scale}}px; left: {{left}}px; top: {{top}}px;"/>' +
        '<div class="loading" ng-if="loading"> <i class="fa fa-spin fa-spinner"></i> </div>' +
        '</div>',
        link: function($scope, elem, attr, ctrl) {
            $scope.scale = 1;
            $scope.dragging = false;
            $scope.fitted = false;
            $scope.centerX = false;
            $scope.centerY = false;
            $scope.center = {x:0.5,y:0.5};
            container = elem[0];
            var boundingRect;
            $scope.loading = true;
            $scope.onImageLoad = function(){
                $scope.loading = false;
                calcScales();
                fixImage();
                $timeout(function(){
                    fixImage();
                });
            };
            $scope.onImageThumbLoad = function(){
            };
            $scope.$watch('version.url', function(v){
                $scope.loading = true;
            });
            var calcScales = function(){
                boundingRect = container.getBoundingClientRect();
                if(boundingRect.width > $scope.version.width && boundingRect.height > $scope.version.height){
                    minScale = 1;
                    maxScale = Math.min(boundingRect.width / $scope.version.width, boundingRect.height / $scope.version.height);
                }else{
                    maxScale = 1;
                    minScale = Math.min(boundingRect.width / $scope.version.width, boundingRect.height / $scope.version.height);
                }
                if($scope.fitted) $scope.fitScale();
                else if(maxed) $scope.maxScale();
                else fixImage();
            };
            $scope.fitScale = function(){
                $scope.scale = minScale;
                fixImage();
            };
            $scope.maxScale = function(){
                $scope.scale = maxScale;
                fixImage();
            };
            $scope.onMDown = function(e){
                e = angular.element.event.fix(e);
                $scope.dragging = true;
                lastPos = {x: e.pageX, y: e.pageY};
                if(tweenInt != null) $interval.cancel(tweenInt);
                e.preventDefault();
            };
            var intTime = 1;
            var lastMs;
            var speed;
            $scope.onMUp = function(e){
                if(!$scope.dragging || !lastDelta) return;
                e = angular.element.event.fix(e);
                $scope.dragging = false;
                speed = {x: (lastDelta.x*4) / ($scope.version.width * $scope.scale), y: (lastDelta.y * 4)/ ($scope.version.height * $scope.scale)};
                e.preventDefault();
                if(speed.x == 0 && speed.y == 0) return;
                intTime = 0.5;
                tweenInt = $interval(function(){
                    $scope.center.x -= (1 - Math.cos(intTime * (Math.PI / 2))) * speed.x;
                    $scope.center.y -= (1 - Math.cos(intTime * (Math.PI / 2))) * speed.y;
                    fixImage();
                    intTime -= ((new Date()).getTime() - (!!lastMs ? lastMs : 0))/1000;
                    if(intTime <= 0) $interval.cancel(tweenInt);
                    lastMs = (new Date()).getTime();
                }, 10);
            };
            $scope.onMove = function(e){
                if(!$scope.dragging) return;
                e = angular.element.event.fix(e);
                lastDelta = {x: e.pageX - lastPos.x, y: e.pageY - lastPos.y};
                $scope.center.x -= (lastDelta.x) / ($scope.version.width * $scope.scale);
                $scope.center.y -= (lastDelta.y) / ($scope.version.height * $scope.scale);
                fixImage();
                lastPos = {x: e.pageX, y: e.pageY};
                lastMs = (new Date()).getTime();
                e.preventDefault();
            };
            $scope.onWheel = function(e, v){
                if(tweenInt != null) $interval.cancel(tweenInt);
                e = angular.element.event.fix(e);
                var w = $scope.version.width * $scope.scale;
                var h = $scope.version.height * $scope.scale;
                var wx = (w * $scope.center.x - (boundingRect.width / 2) + e.pageX) / w;
                var wy = (h * $scope.center.y - (boundingRect.height / 2) + e.pageY) / h;
                var pScale = $scope.scale;
                $scope.scale += v ? 0.1 : -0.1;
                $scope.scale = $scope.scale.clamp(minScale,maxScale);
                $scope.center.x -= (wx - $scope.center.x) * (1 - ($scope.scale / pScale));
                $scope.center.y -= (wy - $scope.center.y) * (1 - ($scope.scale / pScale));
                fixImage();
            };
            $scope.onDblClick = function(e){
                $scope.onWheel(e, true);
            };
            var fixImage = function(){
                var cont = container.getBoundingClientRect();
                $scope.scale = $scope.scale.clamp(minScale,maxScale);
                $scope.fitted = $scope.scale == minScale;
                maxed = $scope.scale == maxScale;
                $scope.centerX = cont.width > $scope.version.width * $scope.scale;
                if($scope.centerX){
                    $scope.center.x = 0.5;
                }
                $scope.left = (cont.width / 2) - ($scope.version.width * $scope.scale * $scope.center.x);
                if(!$scope.centerX) {
                    $scope.left = Math.max(Math.min($scope.left, 0), cont.width - $scope.version.width * $scope.scale);
                    $scope.center.x = (Math.abs($scope.left) + (cont.width / 2)) / ($scope.version.width * $scope.scale);
                }

                $scope.centerY = cont.height > $scope.version.height * $scope.scale;
                if($scope.centerY){
                    $scope.center.y = 0.5;
                }
                $scope.top = (cont.height / 2) - ($scope.version.height * $scope.scale * $scope.center.y);
                if(!$scope.centerY) {
                    $scope.top = Math.max(Math.min($scope.top, 0), cont.height - $scope.version.height * $scope.scale);
                    $scope.center.y = (Math.abs($scope.top) + (cont.height / 2)) / ($scope.version.height * $scope.scale);
                }
            };
            var getWindowDimensions = function () {
                return {
                    'h': container.getBoundingClientRect().height,
                    'w': container.getBoundingClientRect().width
                };
            };
            $scope.$watch(getWindowDimensions, function (newValue, oldValue) {
                calcScales();
            }, true);
            calcScales();
            $scope.fitScale();
        }
    };
}).directive('ngMouseWheel', function() {
    return function(scope, element, attrs) {
        element.bind("DOMMouseScroll mousewheel onmousewheel", function(event) {

            // cross-browser wheel delta
            scope.$event = window.event || event; // old IE support
            var delta = Math.max(-1, Math.min(1, (scope.$event.wheelDelta || -scope.$event.detail)));

            if(delta < 0) {
                scope.$apply(function(){
                    scope.$eval(attrs.ngMouseWheelDown);
                });
            }else if(delta > 0) {
                scope.$apply(function(){
                    scope.$eval(attrs.ngMouseWheelUp);
                });
            }
            // for IE
            event.returnValue = false;
            // for Chrome and Firefox
            if(event.preventDefault)  {
                event.preventDefault();
            }
        });
    };
}).directive('ngDraggable', ['$document', '$window', function($document, $window) {
    return {
        restrict: 'A',
        link: function(scope, elm, attrs) {
            var startX, startY, initialMouseX, initialMouseY;
            elm.css({ position: 'absolute' });

            elm.bind('mousedown', function($event) {
                startX = elm.prop('offsetLeft');
                startY = elm.prop('offsetTop');
                initialMouseX = $event.clientX;
                initialMouseY = $event.clientY;
                $document.bind('mousemove', mousemove);
                $document.bind('mouseup', mouseup);
                return false;
            });

            var fixPosition = function(){
                var elmPosition = elm.position();
                if(elmPosition.left < 0){
                    elm.css('left', 0);
                }else if(elmPosition.left > $window.innerWidth - elm.width()){
                    elm.css('left', $window.innerWidth - elm.width());
                }
                if(elmPosition.top < 0){
                    elm.css('top', 0);
                }else if(elmPosition.top > $window.innerHeight - elm.height()){
                    elm.css('top', $window.innerHeight - elm.height());
                }
            };
            function mousemove($event) {
                if($event.buttons === 0){
                    mouseup();
                    return;
                }
                var dx = $event.clientX - initialMouseX;
                var dy = $event.clientY - initialMouseY;
                elm.css({
                    top: startY + dy + 'px',
                    left: startX + dx + 'px'
                });
                fixPosition();
                return false;
            }

            function mouseup() {
                $document.unbind('mousemove', mousemove);
                $document.unbind('mouseup', mouseup);
            }
            angular.element($window).bind("resize", fixPosition);
        }
    };
}]).directive('modalResizer', ['$document', function ($document) {
    return {
        restrict: 'AE', //attribute or element
        replace: false,
        template: '<div>' +
            '<div class="rb"></div>' +
            '<div class="s"></div>' +
            '<div class="rb"></div>' +
            '<div class="rb"></div>' +
            '<div class="s"></div>' +
            '<div class="rb"></div>' +
            '<div class="rb"></div>' +
            '<div class="rb"></div>' +
            '</div>',
        link: function ($scope, elem, attrs) {
            let modal = elem.parent();
            let _mouseLastPos = {x:0,y:0};
            $scope.resizing = false;

            let stopDragging = function(){
                angular.element(elem).removeClass("resizing");
                $scope.resizing = false;
                $document.unbind("DOMMouseMove mousemove onmousemove", onDocDrag);
                $document.unbind("DOMMouseUp mouseup onmouseup", onMouseUp);
            }
            elem.bind('DOMMouseDown mousedown onmousedown', function (e) {
                if(e.button !== 0) { return; }
                e = angular.element.event.fix(e);
                angular.element(elem).addClass("resizing");
                $scope.resizing = true;

                //e.stopPropagation();
                $document.bind("DOMMouseMove mousemove onmousemove", onDocDrag);
                $document.one("DOMMouseUp mouseup onmouseup", onMouseUp);
                _mouseLastPos = {x: e.pageX, y: e.pageY};
                handleDragMousePosition(this, window.event || event, true);
                //return false;
            });
            let onMouseUp = function(){
                handleDragMousePosition(this, window.event || event, true);
                stopDragging();
            }
            let onDocDrag = function(event){
                if(event.buttons === 0){
                    stopDragging();
                }
                handleDragMousePosition(this, window.event || event, true);
            };
            let handleDragMousePosition = function(elem, event, resume) {
                if (!$scope.resizing) {
                    return;
                }
                let e = window.event || event; // old IE support
                e = angular.element.event.fix(e);

                let ePos = {x: e.pageX, y: e.pageY};
                let diffpx = {x:ePos.x - _mouseLastPos.x, y:ePos.y - _mouseLastPos.y};

                modal.css('width', '+='+diffpx.x);
                modal.css('height', '+='+diffpx.y);

                if(modal.width() < 180){
                    modal.css('width', 180);
                }
                if(modal.height() < 160){
                    modal.css('height', 160);
                }
                _mouseLastPos = ePos;
                e.returnValue = false;
                // for Chrome and Firefox
                if (e.preventDefault) {
                    e.preventDefault();
                }
                e.stopPropagation();
                e.stopImmediatePropagation();
                return e;
            };
        }
    };
}]).directive('oxoMeet', [function($document) {
    let fullScreen = false;
    let minimized = false;
    let columns = false;
    function activateFullscreen(element) {
        let el = element[0];
        if(el.requestFullscreen) {
            el.requestFullscreen();        // W3C spec
        }
        else if (el.mozRequestFullScreen) {
            el.mozRequestFullScreen();     // Firefox
        }
        else if (el.webkitRequestFullscreen) {
            el.webkitRequestFullscreen();  // Safari
        }
        else if(el.msRequestFullscreen) {
            el.msRequestFullscreen();      // IE/Edge
        }
        fullScreen = true;
        element.addClass('fullscreen');
        deactivateMinimized(element);
        deactivateColumns(element);
    }
    function deactivateFullscreen(element) {
        if(document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
        fullScreen = false;
        element.removeClass('fullscreen');
    }
    function deactivateMinimized(element){
        element.removeClass('minimize');
        minimized = false;
    }
    function deactivateColumns(element){
        element.removeClass('columns');
        angular.element("#main-content").removeClass("oxo-meet-columns");
        columns = false;
    }
    return {
        restrict: 'A',
        link: function(scope, elm, attrs) {
            scope.minimize = function(){
                if(columns){
                    deactivateColumns(elm);
                }
                elm.addClass('minimize');
                minimized = true;
            };
            scope.maximize = function(){
                activateFullscreen(elm);
            };
            scope.restore = function(){
                if(fullScreen){
                    deactivateFullscreen(elm);
                }
                if(minimized){
                    deactivateMinimized(elm);
                }
                if(columns){
                    deactivateColumns(elm);
                }
            };
            scope.columns = function(){
                if(minimized){
                    deactivateMinimized(elm);
                }
                elm.addClass('columns');
                angular.element("#main-content").addClass("oxo-meet-columns");
                columns = true;
            };

            document.addEventListener('fullscreenchange', (event) => {
                let fullscreenElement = document.fullscreenElement
                    || document.mozFullScreenElement
                    || document.webkitFullscreenElement;
                if (fullscreenElement) {
                    //console.log('Entered fullscreen:', document.fullscreenElement);
                } else {
                    //console.log('Exited fullscreen.');
                    fullScreen = false;
                    angular.element(elm[0]).removeClass('fullscreen');
                }
            });
        }
    };
}]).directive('cookieConsent', function ($cookies, cookiesConsent) {
    if(cookiesConsent == 1){
        return {
        };
    }else{
        return {
            restrict: "EA",
            template:
                '<div ng-hide="acceptConsent == 1" class="alert-warning alert-box alert-tight text-center" style="color:white;font-size:15px;"> <b> Este sitio utiliza cookies para mejorar la experiencia de usuario {{acceptConsent}} </b>' +
                '<a class="btn btn-sm btn-success" ng-click="consent(true)"> Aceptar cookies </a>' +
                '</div>',
            controller: function ($scope) {
                $scope.consent = function () {
                    $cookies['consent'] = 1;
                    $scope.acceptConsent = 1;
                };
                $scope.$watch("$cookies['consent']",function(newValue,oldValue) {
                    $scope.acceptConsent = $cookies['consent'];
                });
            }
        };
    }
});
Number.prototype.clamp = function(min, max) {
    return Math.min(Math.max(this, min), max);
};