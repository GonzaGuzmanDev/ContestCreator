ContestAdminControllers
    .controller('AdminContestsHome', function($scope, adminInfo, contest){
        $scope.uiConfig = {
            calendar:{
                height: 650,
                //editable: true,
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
        $scope.data = adminInfo['data'];
        $scope.contestEvents = $scope.data.events;
        $scope.inscriptions = $scope.data['inscriptions'];
        $scope.allRoles = $scope.data['allRoles'];
        $scope.hideSaveFooter = true;
        $scope.entries = $scope.data['entries'];
        $scope.contest = contest;
    })
    .controller('AdminContestInscriptionsEditCtrl', function($scope, rootUrl, currentBaseUrl, $filter, $sanitize, $location, $window,
                                                             $http, $timeout, Flash, Languages, userInscriptions, CategoryManager, $uibModal,
                                                             contest, categoriesData, inscriptionsData, wizardStatus){
        $scope.langs = Languages;
        $scope.selectedLang = Languages.Default;
        $scope.setLang = function(l){
            $scope.selectedLang = l;
        };
        $scope.selectedRole = userInscriptions.Inscriptor;
        $scope.contest = contest;
        //if(wizardStatus.wizard_contest() == 0)
        $scope.activeMenu = '';
        $scope.categories = categoriesData.data.categories;
        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories($scope.categories);
        $scope.roles = inscriptionsData.data.roles;
        $scope.inscriptionTypes = inscriptionsData.data.inscriptionTypes;
        $scope.inscriptionMetadata = inscriptionsData.data.inscriptionMetadata;
        $scope.metadatas = {};
        $scope.contestsIds = categoriesData.data.contestsIds;
        if($scope.contest.wizard_config)
            wizardStatus.wizard_contest().inscriptions == 1 ?  $scope.wizardHasInscriptions = true : $scope.wizardHasInscriptions = false;
        else $scope.wizardHasInscriptions = true;
        //$scope.contest.wizard_status >= wizardStatus.WIZARD_REGISTER_FORM ? $scope.showThis = true : '';
        $scope.contest.wizard_status && $scope.contest.wizard_status != wizardStatus.WIZARD_FINISHED ? $scope.showThis = true : '';

        for(var i in $scope.roles){
            $scope.metadatas[i] = $filter('filter')($scope.inscriptionMetadata, {role:i*1}, true);
        }
        $scope.changeWizardHasInscriptions = function(param){
            $scope.wizardHasInscriptions = param;
        };

        $scope.addInscriptionType = function(role){
            var t = {errMsg:'new'+Math.random(), name:'', role:role, category_config_type:[]};
            $scope.inscriptionTypes.push(t);
            var list = $scope.metadatas[role];
            for(var i in list){
                if(!angular.isDefined(list[i].inscription_metadata_config_types)) list[i].inscription_metadata_config_types = [];
                list[i].inscription_metadata_config_types[t.id] = {required:list[i].required, visible: 1};
            }
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.moveFieldUp = function(list, index){
            var temp = list[index-1];
            list[index - 1] = list[index];
            list[index] = temp;
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.moveFieldDown = function(list, index){
            var temp = list[index+1];
            list[index + 1] = list[index];
            list[index] = temp;
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.removeInscriptionType = function(type){
            var role = type.role;
            for(var r in $scope.metadatas) {
                for(var i in $scope.metadatas[r]) {
                    delete $scope.metadatas[r][i].inscription_metadata_config_types;
                }
            }
            var index = $scope.inscriptionTypes.indexOf(type);
            $scope.inscriptionTypes.splice(index, 1);
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.addMetadataField = function(role, type){
            var o = {role:role, inscription_metadata_config_types:{}, errMsg:'new'+Math.random()};
            if(angular.isDefined(type)) o.type = type;
            $scope.inscriptionMetadata.push(o);
            $scope.metadatas[role].push(o);
            for(var i in $scope.inscriptionTypes) {
                o.inscription_metadata_config_types[$scope.inscriptionTypes[i].id] = {required:0, visible: 1};
            }
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.removeMetadataField = function(field){
            var index = $scope.inscriptionMetadata.indexOf(field);
            var r = field.role*1;
            var index2 = $scope.metadatas[r].indexOf(field);
            $scope.inscriptionMetadata.splice(index, 1);
            $scope.metadatas[r].splice(index2, 1);
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.addFieldOption = function(field){
            if(!angular.isDefined(field.config)) field.config = {};
            if(!angular.isDefined(field.config.options)) field.config.options = [];
            field.config.options.push('');
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.isFieldRequired = function(field){
            return !!field.required;
        };
        $scope.configInscriptionType = function(type){
            //var modalInstance =
            $uibModal.open({
                templateUrl: 'editInscriptionTypeConfig.html',
                controller: 'AdminConfigInscriptionType',
                resolve: {
                    type: function () {
                        return type;
                    }
                },
                scope: $scope
            });
        };
        $('body').on('click','.dropdown-menu.dropdown-checkboxes input,.dropdown-menu.dropdown-checkboxes .dropdown-item', function(e) {
            e.stopPropagation();
        });
        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var insData = [];
            for(var i in $scope.roles){
                insData = insData.concat($scope.metadatas[i]);
            }
            var data = {
                id: $scope.contest.id,
                inscriptionTypes: $scope.inscriptionTypes,
                inscriptionMetadata: insData,
                wizardHasInscriptions: $scope.wizardHasInscriptions
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/inscriptionData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.roles = response.data.roles;
                    $scope.inscriptionTypes = response.data.inscriptionTypes;
                    $scope.inscriptionMetadata = response.data.inscriptionMetadata;
                    Flash.show(response.flash, 'success', $scope);
                    $scope.contest = response.contest;
                    wizardStatus.update_wizard_contest(response.contest);
                    if(response.contest.wizard_status < wizardStatus.WIZARD_FINISHED){
                        $location.path("admin/entries");
                    }
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                $scope.errors = data.errors;
                $scope.errors2 = data.errors2;
                Flash.show(data.flash, 'danger', $scope);
            });
        };


        /*var setImport = function(response){
            $scope.inscriptionTypes = response.inscriptionTypes;
            $scope.inscriptionMetadata = response.inscriptionMetadata;
            $scope.roles = response.roles;
            deleteIds($scope.inscriptionMetadata);
            for(var i in $scope.roles){
                $scope.metadatas[i] = $filter('filter')($scope.inscriptionMetadata, {role:i}, true);
            }
            console.log($scope.metadatas);
        }

        $scope.import = function(contest_id){
            console.log(contest_id);
            var data = { contestIdUsers: contest_id };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/importContestData', data).success(function(response){
                setImport(response);
            }).error(function(data){
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        var deleteIds = function(fields){
            angular.forEach(fields, function(field){
                field.id = null;
            });
        };*/

    })
    .controller('AdminConfigInscriptionType', function($scope, rootUrl, $sanitize, $location, $http, $timeout, $filter, Flash, $uibModalInstance, type){
        Flash.clear($scope);
        $scope.type = type;
        $scope.modelList = [];

        $scope.checkAll = function(category) {
            if(type.category_config_type.indexOf(category.id) == -1) type.category_config_type.push(category.id);
            if(angular.isDefined(category.children_categories)){
                angular.forEach(category.children_categories, function(categ) {
                    $scope.checkAll(categ);
                });
            }
        };

        $scope.toggleThis = function(category){
            if(type.category_config_type.indexOf(category.id) == -1){
                $scope.checkAll(category);
                $scope.checkParent(category);
                category.expanded = true;
            }else{
                $scope.uncheckAll(category);
            }
        };

        $scope.checkParent = function(category){
            if(type.category_config_type.indexOf(category.id) == -1){
                type.category_config_type.push(category.id);
            }
            if(!!category.parent) $scope.checkParent(category.parent);
        };

        $scope.uncheckAll = function(category){
            var spliceIndex = type.category_config_type.indexOf(category.id);
            if(spliceIndex != -1)
                type.category_config_type.splice(spliceIndex, 1);
            for (var i = type.category_config_type.length - 1; i >= 0; i--) {
                angular.forEach(category.children_categories, function(categ) {
                    if (type.category_config_type[i] == categ.id){
                        type.category_config_type.splice(i, 1);
                    }
                    if(categ.final == 0) {
                        angular.forEach(categ.children_categories, function (children_categ) {
                            $scope.uncheckAll(children_categ);
                        });
                    }
                });
            }
        };
        $scope.close = function () {
            $uibModalInstance.close();
        };
    })
    .controller('AdminConfigEntryMetadataTemplate', function($scope, rootUrl, $sanitize, $location, $http, $timeout, $filter, Flash, $uibModalInstance, template){
        Flash.clear($scope);
        $scope.template = template;
        $scope.modelList = [];

        $scope.checkAll = function(category) {
            if(template.categories_ids.indexOf(category.id) == -1){
                template.categories_ids.push(category.id);
                $scope.clearCatFromOtherTemplates(category, template);
            }
            if(angular.isDefined(category.children_categories)){
                angular.forEach(category.children_categories, function(categ) {
                    $scope.checkAll(categ);
                });
            }
        };

        $scope.toggleThis = function(category){
            if(template.categories_ids.indexOf(category.id) == -1){
                $scope.checkAll(category);
                $scope.checkParent(category);
                category.expanded = true;
            }else{
                $scope.uncheckAll(category);
            }
        };

        $scope.checkParent = function(category){
            if(template.categories_ids.indexOf(category.id) == -1){
                template.categories_ids.push(category.id);
                $scope.clearCatFromOtherTemplates(category, template);
            }
            if(!!category.parent) $scope.checkParent(category.parent);
        };

        $scope.uncheckAll = function(category){
            var spliceIndex = template.categories_ids.indexOf(category.id);
            if(spliceIndex != -1)
                template.categories_ids.splice(spliceIndex, 1);
            for (var i = template.categories_ids.length - 1; i >= 0; i--) {
                angular.forEach(category.children_categories, function(categ) {
                    if (template.categories_ids[i] == categ.id){
                        template.categories_ids.splice(i, 1);
                    }
                    if(categ.final == 0) {
                        angular.forEach(categ.children_categories, function (children_categ) {
                            $scope.uncheckAll(children_categ);
                        });
                    }
                });
            }
        };

        $scope.close = function () {
            $uibModalInstance.close();
        };
    })
    .controller('AdminContestImportContestEditCtrl', function($scope, rootUrl, $sanitize, $location, $http, $timeout, $filter, Languages, Flash, $uibModal, contest, contestIds){
        $scope.contest = contest;
        $scope.contestsIds = contestIds.data.contestsIds;
        $scope.metadataFieldsCount = contestIds.data.metadataFieldsCount;
        $scope.selected = null;
        $scope.hideSaveFooter = true;
        $scope.sending = false;

        $scope.selectContest = function(contest){
            $scope.selected = contest;
        }

        $scope.importContest = function(){
            var data = {
                selCat: $scope.selCat,
                selEntryForm: $scope.selEntryForm,
                selUserForm: $scope.selUserForm,
                selEmails: $scope.selEmails,
                selStyle: $scope.selStyle,
                selPayments: $scope.selPayments,
                selVoting: $scope.selVoting,
                contestId: $scope.selected
            };
            $scope.sending = true;
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/importContestData', data).success(function(response){
                $scope.FlashSelCat = $scope.FlashSelEntryForm = $scope.FlashSelUserForm = $scope.FlashSelEmails = $scope.FlashSelStyle = 0;
                $scope.sending = false;
                Flash.show(response.flash, 'success', $scope);
                $scope.FlashSelCat = $scope.selCat;
                $scope.FlashSelEntryForm = $scope.selEntryForm;
                $scope.FlashSelUserForm = $scope.selUserForm;
                $scope.FlashSelEmails = $scope.selEmails;
                $scope.FlashSelStyle = $scope.selStyle;
                $scope.FlashSelPayments = $scope.selPayments;
                $scope.FlashSelVoting = $scope.selVoting;
                $scope.selCat = $scope.selEntryForm = $scope.selUserForm = $scope.selEmails = $scope.selStyle = 0;
            }).error(function(response){
                $scope.selCat = $scope.selEntryForm = $scope.selUserForm = $scope.selEmails = $scope.selStyle = 0;
                Flash.show(response.error.message, 'danger', $scope);
                $scope.sending = false;
            });
        };
    })
    .controller('AdminContestCategoriesEditCtrl', function($scope, rootUrl, $sanitize, $window, $location, $http, $timeout, $filter, Languages, Flash, $uibModal, contest, categoriesData, inscriptionsData, wizardStatus){
        $scope.contest = contest;
        $scope.categories = categoriesData.data.categories;
        $scope.EntryMetadataTemplates = categoriesData.data.EntryMetadataTemplate;
        $scope.roles = inscriptionsData.roles;
        $scope.inscriptionTypes = inscriptionsData.inscriptionTypes;
        $scope.langs = Languages;
        $scope.selectedLang = Languages.Default;
        $scope.setLang = function(l){
            $scope.selectedLang = l;
        };

        $scope.contestsIds = categoriesData.data.contestsIds;
        //$scope.contest.wizard_status >= wizardStatus.WIZARD_CATEGORIES ? $scope.showThis = true : '';
        $scope.contest.wizard_status && $scope.contest.wizard_status != wizardStatus.WIZARD_FINISHED ? $scope.showThis = true : '';

        /*$scope.importCategories = function(contest_id){
            var data = { contestIdCat: contest_id };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/importContestData', data).success(function(response){
                setImportCategories(response);
            }).error(function(data){
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        var setImportCategories = function(response){
            $scope.categories = response.categories;
            $scope.EntryMetadataTemplates = response.EntryMetadataTemplate;
            deleteIdsCategories($scope.categories);
            deleteTemplateIds($scope.EntryMetadataTemplates);
        };

        var deleteIdsCategories = function(category){
            angular.forEach(category, function(categ){
                categ.id = null;
                categ.parent_id = null;
                categ.template_id = null;
                if(categ.children_categories) deleteIdsCategories(categ.children_categories);
            });
        };

        var deleteTemplateIds = function(templates){
            angular.forEach(templates, function(template){
                template.id = null;
                angular.forEach(template.entry_metadata_config_templates, function(field){
                    field.template_id = null;
                })
            });
        };*/

        var parseCats = function(cats, parent){
            for(var i =0;i<cats.length;i++){
                var c = cats[i];
                if(parent) c.parent = parent;
                if(angular.isDefined(c.children_categories) && c.children_categories.length){
                    parseCats(c.children_categories, c);
                }
            }
        };
        parseCats($scope.categories);

        var clearCatsParents = function(list){
            for(var i = 0; i < list.length; i++){
                var c = list[i];
                delete c.parent;
                if(angular.isDefined(c.children_categories) && c.children_categories.length){
                    clearCatsParents(c.children_categories);
                }
            }
        };
        $scope.GetTemplateName = function(tId){
            var res = $filter('filter')($scope.EntryMetadataTemplates, {id:tId}, true);
            if(angular.isDefined(res[0])) return res[0].name;
            return null;
        };
        $scope.addCategory = function(list, parent){
            list.push({name:'', children_categories:[], parent: parent, errMsg:('new'+Math.random()).replace(".","")});
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.getCategory = function(category_id){
            var res = $filter('filter')($scope.categories, {id:category_id}, true);
            if(angular.isDefined(res[0])) return res[0];
            return null;
        };
        $scope.setCategoryMetadata = function(category, template){
            category.template_id = template;
        };
        $scope.removeCategory = function(cat, index){
            if(!cat.parent)
            cat.splice(index,1);
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.moveCatUp = function(cat, index){
            var list = cat.parent.children_categories;
            var temp = list[index-1];
            list[index - 1] = list[index];
            list[index] = temp;
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.moveCatDown = function(cat, index){
            var list = cat.parent.children_categories;
            var temp = list[index+1];
            list[index + 1] = list[index];
            list[index] = temp;
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.moveCatLeft = function(cat, index){
            if(!cat.parent.parent) return;
            var list = cat.parent.children_categories;
            var newlist = cat.parent.parent.children_categories;
            newlist.splice(newlist.indexOf(cat.parent) + 1, 0, cat);
            cat.parent = cat.parent.parent;
            list.splice(index,1);
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.moveCatRight = function(cat, index){
            var list = cat.parent.children_categories;
            var prev = list[index-1];
            prev.children_categories.push(list[index]);
            list.splice(index,1);
            cat.parent = prev;
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.onSortStop = function(){
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.configCategory = function(category){
            //var modalInstance =
            $uibModal.open({
                templateUrl: 'editCategoryConfig.html',
                controller: 'AdminConfigCategory',
                resolve: {
                    category: function () {
                        return category;
                    },
                    categories: function () {
                        return $scope.categories;
                    }
                },
                scope: $scope
            });
            /*modalInstance.result.then(function (data) {
             //Flash.show(data.flash, 'success', $scope);
             //$scope.updateAllInscriptionsList();
             }, function () {
             });*/
        };
        $scope.GetCategoryPrice = function(category){
            if(angular.isDefined(category.price) && category.price!= null && category.price!= '') return category.price;
            if(category.parent != null) return $scope.GetCategoryPrice(category.parent);
            return $scope.contest.billing.mainPrice;
        };

        $scope.setCategs = function(categories){
            $scope.categories = categories;
        };

        $scope.previous = function() {
            $location.path("admin/entries");
        }

        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            $scope.categWithParents = angular.copy($scope.categories);
            clearCatsParents($scope.categWithParents);
            var data = {
                categories: $scope.categWithParents
            };
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/categoriesData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    //$scope.setCategs(response.data.categories);
                    Flash.show(response.flash, 'success', $scope);

                    if(response.contest.wizard_status < wizardStatus.WIZARD_FINISHED){
                        $location.path("admin/billingsetup");
                    }
                }
            }).error(function(data){
                $scope.sending = false;
                $scope.errors = data.errors;
                Flash.show(data.flash, 'danger', $scope);
            });
        };
    })
    .controller('AdminConfigCategory', function($scope, rootUrl, $sanitize, $location, $http, $timeout, Flash, $uibModalInstance, category, categories){
        Flash.clear($scope);
        $scope.category = category;
        $scope.categories = categories;
        $scope.close = function () {
            $uibModalInstance.close();
        };
    })
    .controller('AdminContestEntriesEditCtrl', function($scope, rootUrl, $sanitize, $window, $location, $http, $uibModal, $filter, $timeout, Flash, Languages, contest, categoriesData, entriesData, wizardStatus){
        $scope.contest = contest;
        $scope.categories = categoriesData.data.categories;
        $scope.EntryMetadataTemplates = entriesData.data.EntryMetadataTemplate;
        $scope.EntryMetadataField = entriesData.data.EntryMetadataField;
        $scope.langs = Languages;
        $scope.selectedLang = Languages.Default;
        $scope.setLang = function(l){
            $scope.selectedLang = l;
        };
        $scope.setPreviewTemplate = function(t){
            $scope.previewtemplate = t;
        };

        if($scope.contest.wizard_config)
            wizardStatus.wizard_contest().entries == 1 ? $scope.wizardHasEntries = true : $scope.wizardHasEntries = false;
        else $scope.wizardHasEntries = true;
        //$scope.contest.wizard_status >= wizardStatus.WIZARD_ENTRY_FORM ? $scope.showThis = true : '';
        $scope.contest.wizard_status && $scope.contest.wizard_status != wizardStatus.WIZARD_FINISHED ? $scope.showThis = true : '';
        $scope.previous = {status: false};
        $scope.contestsIds = categoriesData.data.contestsIds;

        $scope.changeWizardHasEntries = function(param){
            $scope.wizardHasEntries = param;
        };

        /*var setImportEntryForm = function(response){
            $scope.EntryMetadataField = response.EntryMetadataField;
            $scope.EntryMetadataTemplates = response.EntryMetadataTemplate;
            $scope.categDiff = response.categDiff;
            deleteIds($scope.EntryMetadataField);
            deleteTemplateIds($scope.EntryMetadataTemplates, $scope.categDiff);
        };

        $scope.importForms = function(contest_id){
            var data = { contestIdEntries: contest_id };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/importContestData', data).success(function(response){
                setImportEntryForm(response);
            }).error(function(data){
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        var deleteIds = function(fields){
            angular.forEach(fields, function(field){
                field.id = null;
                field.contest_id = null;
                angular.forEach(field.entry_metadata_config_template, function(emc_template){
                    emc_template.template_id = null;
                })
            });
        };

        var deleteTemplateIds = function(templates, diff){
            angular.forEach(templates, function(template){
                template.id = null;
                angular.forEach(template.entry_metadata_config_templates, function(field){
                    field.template_id = null;
                });
                angular.forEach(template.categories_ids, function(categ){
                    var newValue = parseInt(categ) + parseInt(diff);
                    var index = template.categories_ids.indexOf(categ);
                    template.categories_ids[index] = String(newValue);
                });
            });
        };*/

        $scope.isFieldHidden = function(field){
            if(!$scope.previewtemplate) return false;
            var conf = $filter('filter')(field.entry_metadata_config_template, {template_id:$scope.previewtemplate.id});
            if(angular.isDefined(conf[0])) return !conf[0].visible;
            return true;
        };
        $scope.isFieldRequired = function(field){
            if(!$scope.previewtemplate) return !!field.required;
            var conf = $filter('filter')(field.entry_metadata_config_template, {template_id:$scope.previewtemplate.id});
            if(angular.isDefined(conf[0])) return !!conf[0].required;
            return !!field.required;
        };
        $scope.addMetadataTemplate = function(){
            var t = {errMsg:('new'+Math.random()).replace(".","")};
            $scope.EntryMetadataTemplates.push(t);
            $scope.EntryMetadataField.forEach(function(field){
                $scope.getFieldTemplateConfig(field, t);
            });
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.removeMetadataTemplate = function(template){
            $scope.EntryMetadataField.forEach(function(field){
                field.entry_metadata_config_template = $filter('filter')(field.entry_metadata_config_template, {template_id:"!"+template.id});
            });
            var index = $scope.EntryMetadataTemplates.indexOf(template);
            $scope.EntryMetadataTemplates.splice(index, 1);
            if(template == $scope.previewtemplate) $scope.previewtemplate = null;
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.getFieldTemplateConfig = function(field, template){
            //console.log(template);return;
            if(!angular.isDefined(field.entry_metadata_config_template)) field.entry_metadata_config_template = [];
            var res = $filter('filter')(field.entry_metadata_config_template, {template_id:template.id}, true);
            if(!res.length){
                var c = {template_id: template.id, required:field.required?1:0, visible: 1};
                field.entry_metadata_config_template.push(c);
                res.push(c);
            }
            return res[0];
        };
        $scope.configMetadataTemplate = function(template){
            //var modalInstance =
            $uibModal.open({
                templateUrl: 'editMetadataTemplateConfig.html',
                controller: 'AdminConfigEntryMetadataTemplate',
                resolve: {
                    template: function () {
                        return template;
                    }
                },
                scope: $scope
            });
        };
        $scope.clearCatFromOtherTemplates = function(category, template){
            var others = $filter('filter')($scope.EntryMetadataTemplates, {id:"!"+template.id});
            angular.forEach(others, function(oTemplate, key) {
                var index = oTemplate.categories_ids.indexOf(category.id);
                if(index != -1) oTemplate.categories_ids.splice(index, 1);
            });
        };
        $scope.addMetadataField = function(roleId,type){
            var o = {};
            if(angular.isDefined(type)){o.type = type;}
            o.errMsg = ('new'+Math.random()).replace(".","");
            $scope.EntryMetadataField.push(o);
            $scope.EntryMetadataTemplates.forEach(function(template){
                $scope.getFieldTemplateConfig(o, template);
            });
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.removeMetadataField = function(field){
            var index = $scope.EntryMetadataField.indexOf(field);
            $scope.EntryMetadataField.splice(index, 1);
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };
        $scope.addFieldOption = function(field){
            if(!angular.isDefined(field.config)) field.config = {};
            if(!angular.isDefined(field.config.options)) field.config.options = [];
            field.config.options.push('');
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };

        $scope.addFieldColumn = function(field){
            if(!angular.isDefined(field.config)) field.config = {};
            if(!angular.isDefined(field.config.columns)) field.config.columns = [];
            field.config.columns.push('');
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };

        $scope.addFieldLabel = function(field){
            if(!angular.isDefined(field.config)) field.config = {};
            if(!angular.isDefined(field.config.labels)) field.config.labels = [];
            field.config.labels.push('');
            if ($scope.contestForm) $scope.contestForm.$setDirty();
        };

        $scope.previous = function() {
            $location.path("admin/inscriptions");
        }

        $scope.save = function() {
            if ($scope.contestForm){
                $scope.contestForm.$setPristine();
            }
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                id: $scope.contest.id,
                EntryMetadataTemplates: $scope.EntryMetadataTemplates,
                EntryMetadataField: $scope.EntryMetadataField,
                wizardHasEntries: $scope.wizardHasEntries
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/entriesData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    Flash.show(response.flash, 'success', $scope);
                    $scope.EntryMetadataField = response.data.EntryMetadataField;
                    $scope.EntryMetadataTemplates = response.data.EntryMetadataTemplate;
                    wizardStatus.update_wizard_contest(response.contest);
                    if($scope.wizardHasEntries === false){
                        $location.path("admin/style");
                    }
                    else if(response.contest.wizard_status < wizardStatus.WIZARD_FINISHED){
                        $location.path("admin/categories");
                    }
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                $scope.errors = data.errors;
                $scope.errors2 = data.errors2;
                Flash.show(data.flash, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestBillingSetupEditCtrl', function($scope, Flash, rootUrl, $location, $window, $http, Languages, contest, contestPaymentsMethods, wizardStatus){
        $scope.langs = Languages;
        $scope.contest = contest;
        $scope.contest.billing = contestPaymentsMethods.billing;
        $scope.contest.discounts = contestPaymentsMethods.discounts;
        $scope.enableDelete = false;
        $scope.selectedLang = Languages.Default;
        $scope.setLang = function(l){
            $scope.selectedLang = l;
        };

        $scope.contest.wizard_status && $scope.contest.wizard_status != wizardStatus.WIZARD_FINISHED ? $scope.showThis = true : '';
        
        if($scope.contest.wizard_config)
            wizardStatus.wizard_contest().billing == 1 ? $scope.wizardHasPayment = true : $scope.wizardHasPayment = false;
        else $scope.wizardHasPayment = true;

        $scope.changeWizardHasPayment = function(param){
            $scope.wizardHasPayment = param;
        };

        $scope.previous = function() {
            $location.path("admin/categories");
        };

        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $scope.contest.wizardHasPayment = $scope.wizardHasPayment;
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code +'/payments', $scope.contest).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.contest = response.contest;
                    wizardStatus.update_wizard_contest($scope.contest);
                    Flash.show(response.flash, 'success', $scope);
                    if($scope.contest.wizard_status && $scope.contest.wizard_status < wizardStatus.WIZARD_FINISHED){
                        $location.path("admin/style");
                    }
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };
        $scope.addDiscount = function(){
            if($scope.contest.discounts == null) $scope.contest.discounts = [];
            $scope.contest.discounts.push({});
        };
        $scope.removeDiscount = function(index){
            if($scope.contest.discounts == null) $scope.contest.discounts = [];
            if($scope.contest.discounts[index] != null)
                $scope.contest.discounts.splice(index, 1);
        };
    })
    .controller('AdminContestStyleEditCtrl', function($scope, $location, Flash, $window, rootUrl, $http, contest, contestStyle, wizardStatus){
        // Oculta los botones de cancelar y guardar
        $scope.contestAssets = contestStyle;
        $scope.contest = contest;
        $scope.homeHtml = {};
        $scope.homeBottomHtml = {};
        $scope.termsHtml = {};
        $scope.contest.wizard_status ? $scope.hideSaveFooter = false : $scope.hideSaveFooter = true;
        for(var i = 0; i < $scope.contestAssets.length; i++){
            var contestAsset = $scope.contestAssets[i];
            if (contestAsset.type == 2) $scope.homeHtml = contestAsset;
            if (contestAsset.type == 3) $scope.homeBottomHtml = contestAsset;
            if (contestAsset.type == 5) $scope.termsHtml = contestAsset;
            if (contestAsset.type == 15) $scope.votingBottomHtml = contestAsset;
            if (contestAsset.type == 16) $scope.newInscriptionMessage = contestAsset;
            if (contestAsset.type == 17) $scope.newJudgeInscriptionMessage = contestAsset;
        }
        $scope.picDate = new Date().getTime();
        $scope.updatePicture = function(){
            $scope.picDate = new Date().getTime();
        };

        $scope.previous = function() {
            if(wizardStatus.wizard_contest().entries == 0)
                $location.path("admin/entries");
            else if(wizardStatus.wizard_contest().entries == 1)
                $location.path("admin/billingsetup");
        };

        $scope.preview = function(){
            $scope.saveStyle();
            $window.location.reload();
        }

        $scope.saveHomeHtml = function() {
            $scope.errors = {};
            var data = {
                id: $scope.contest.id,
                homeData: $scope.homeHtml
            };
            //'saveHomeData': {url: rootUrl + 'api/contest/:id/homeData', method:'POST', params:{id:'@id'}},
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveHomeOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.saveHomeBottomHtml = function() {
            $scope.errors = {};
            var data = {
                id: $scope.contest.id,
                homeData: $scope.homeBottomHtml
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveHomeBottomOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.saveVotingBottomHtml = function() {
            $scope.errors = {};
            $scope.votingBottomHtml.type = 15;
            var data = {
                id: $scope.contest.id,
                homeData: $scope.votingBottomHtml
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveVotingBottomOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.saveTermsHtml = function(){
            $scope.errors = {};
            var data = {
                id: $scope.contest.id,
                homeData: $scope.termsHtml
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveTermsOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.saveNewInscriptionMessage = function() {
            $scope.errors = {};
            $scope.newInscriptionMessage.type = 16;
            var data = {
                id: $scope.contest.id,
                homeData: $scope.newInscriptionMessage
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveNewInscriptionMessageOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.saveNewJudgeInscriptionMessage = function() {
            $scope.errors = {};
            $scope.newJudgeInscriptionMessage.type = 17;
            var data = {
                id: $scope.contest.id,
                homeData: $scope.newJudgeInscriptionMessage
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveNewJudgeInscriptionMessageOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.saveStyle = function() {
            $scope.errors = {};
            $scope.savingStyles = true;
            var data = {
                id: $scope.contest.id,
                style: $scope.contest.style,
                custom_style: $scope.contest.custom_style
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/styles', data).success(function(response, status, headers, config){
                $scope.sending = false;
                $scope.savingStyles = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveStylesOk = true;
                    $scope.savingStyles = false;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                $scope.savingStyles = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        $scope.save = function(){
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/finishWizard',"").success(function(response){
            });
            $location.path("admin/deadlines");
        }
    }).controller('AdminContestAllInscriptionsEditCtrl', function($scope, $timeout, $rootScope, rootUrl, currentBaseUrl, inAdmin, $http, $uibModal, Flash, userInscriptions, contest){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.activeMenu = 'inscriptions-list';
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {inscriptor: 0, judge: 0, owner: 0, colaborator: 0};
        //$scope.pagination.filterType = [];
        $scope.viewer = userInscriptions.GetColaboratorStatus('viewer');

        $scope.updateAllInscriptionsList = function(){
            $scope.pagination.filterType = $scope.filterType;
            $scope.pagination.filterJudgeType = $scope.filterJudgeType;
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/allInscriptionsData', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.allRoles = response.data.allRoles;
                $scope.inscriptions = response.data.data;
                $scope.inscriptionTypes = response.data.inscriptionTypes;
            });
        };

        $scope.filterUsers = function(filter){
            switch(filter){
                case userInscriptions.Inscriptor:
                    $scope.pagination.inscriptor === 1 ? $scope.pagination.inscriptor = 0 : $scope.pagination.inscriptor = 1;
                    if($scope.pagination.inscriptor === 0) $scope.filterType = [];
                    break;
                case userInscriptions.Judge:
                    $scope.pagination.judge === 1 ? $scope.pagination.judge = 0 : $scope.pagination.judge = 1;
                    break;
                case userInscriptions.Owner:
                    $scope.pagination.owner === 1 ? $scope.pagination.owner = 0 : $scope.pagination.owner = 1;
                    break;
                case userInscriptions.Colaborator:
                    $scope.pagination.colaborator === 1 ? $scope.pagination.colaborator = 0 : $scope.pagination.colaborator = 1;
                    break;
            }
            $scope.updateAllInscriptionsList();
        };

        $scope.filterType = [];
        $scope.filterByType = function(type){
            $index = $scope.filterType.indexOf(type);
            if($index != -1)
                $scope.filterType.splice($index, 1);
            else
                $scope.filterType.push(type);
            $scope.updateAllInscriptionsList();
        };

        $scope.filterJudgeType = [];
        $scope.filterByTypeJudge = function(type){
            $index = $scope.filterJudgeType.indexOf(type);
            if($index != -1)
                $scope.filterJudgeType.splice($index, 1);
            else
                $scope.filterJudgeType.push(type);
            $scope.updateAllInscriptionsList();
        };

        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInscriptionsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInscriptionsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInscriptionsList(); });
        $scope.$watch(function(){ return $scope.pagination.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; }$scope.updateAllInscriptionsList();});
        $scope.updateAllInscriptionsList();
        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
        };
        $scope.delete = function(inscription) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + (!inAdmin ? 'view/admin/inscription/delete' : 'view/contests/delete-inscription'),
                controller: 'AdminContestAllInscriptionsDeleteCtrl',
                resolve: {
                    inscription: function () {
                        return {inscription: inscription, allRoles: $scope.allRoles};
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateAllInscriptionsList();
            }, function () {
            });
        };
    })
    .controller('AdminContestAllInscriptionsDeleteCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, Flash, $http, inscription, contest){
        $scope.contest = contest;
        $scope.inscription = inscription.inscription;
        $scope.allRoles = inscription.allRoles;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/inscription/'+$scope.inscription.id).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestInscriptionCtrl', function($scope, $uibModal,$rootScope, currentBaseUrl, rootUrl, Flash, $http, $filter, contest, inscriptionData, UsersData, userInscriptions){
        // Oculta los botones de cancelar y guardar
        $scope.uData = UsersData;
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'inscription';
        $scope.contest = contest;
        $scope.showPermits = inscriptionData.showPermits;
        $scope.allRoles = inscriptionData.allRoles;
        $scope.inscription = {};
        $scope.user = {};
        $scope.msg = null;
        var inscriptorHasTypes = 0;
        var JudgeHasTypes = 0;
        $scope.viewer = userInscriptions.GetColaboratorStatus('viewer');
        $scope.showStatic = true;

        $scope.isobject = function(value){
            return angular.isObject(value);
        };

        $scope.metadataFields = $scope.contest.inscription_metadata_fields;
        if(inscriptionData.inscriptions[0])
            $scope.metadataValues = inscriptionData.inscriptions[0].inscription_metadatas;

        angular.forEach($scope.metadataFields, function(field, key){
            $scope.metadataFields[key]['value'] = '';
        });

        var metadataShowed = [];

        $scope.filterMetadata = function(field, key){
            angular.forEach($scope.metadataValues, function(val){
                if(val.inscription_metadata_field_id == field.id){
                    if(field.type == 5){
                        if(!$scope.metadataFields[key]['value']) metadataShowed[key]['value'] = [];
                        metadataShowed[key]['value'].push(val.value);
                    }else{
                        metadataShowed[key]['value'] = val.value;
                    }
                }
            });
        }

        angular.forEach($scope.metadataFields, function(field, key){
            metadataShowed[key] = $scope.metadataFields[key];
            if(field.inscription_metadata_config_types[inscriptionData.inscription.inscription_type_id]){
                if(field.inscription_metadata_config_types[inscriptionData.inscription.inscription_type_id].visible == 1){
                    $scope.filterMetadata(field, key);
                }else{
                    metadataShowed.splice(key,1);
                }
            }else{
                $scope.filterMetadata(field, key);
            }

        });

        $scope.metadata = metadataShowed;

        angular.forEach($scope.allRoles, function(val){
            if(val.label == "Inscriptor"){
                if(val.types.length > 0) inscriptorHasTypes = 1;
                else inscriptorHasTypes = 0;
            }
            if(val.label == "Judge"){
                if(val.types.length > 0) JudgeHasTypes = 1;
                else JudgeHasTypes = 0;
            }
        });

        $scope.setTypeNull = function(){
            if($scope.inscription.inscription_type)
                $scope.inscription.inscription_type.id = null;
        };

        $scope.exist = function(value){
            if(typeof value === 'object')return false;
            else return true;};

        if (!inscriptionData.new) {
            $scope.inscription = inscriptionData.inscription;
            $scope.selectedUser = {
                user_id: $scope.inscription.user_id,
                first_name: $scope.inscription.first_name,
                last_name: $scope.inscription.last_name,
                email: $scope.inscription.email
            };
        }else{
            $scope.showThis = true;
        }

        $scope.save = function() {
            if($scope.inscription.inscription_type != null) $scope.inscription.inscription_type_id = $scope.inscription.inscription_type.id;
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                id: $scope.contest.id,
                inscriptionId: inscriptionData.inscriptionId,
                user: $scope.inscription.user,
                newUser: $scope.user,
                inscription: $scope.inscription,
                selectedUser: $scope.selectedUser,
                newInscription: inscriptionData.new,
                inscriptorHasTypes : inscriptorHasTypes,
                JudgeHasTypes: JudgeHasTypes
            };

            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/inscription', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.showThis = false;
                    $scope.inscription = response.inscription;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.importList = function() {
            var modalInstance = $uibModal.open({
                //templateUrl: currentBaseUrl + 'view/contest/importList',
                templateUrl: currentBaseUrl + ($rootScope.contest ? 'view/admin/importList' : 'view/contests/importList'),
                controller: 'AdminContestsInvitationCtrl',
                resolve: {
                    contest: function () {
                        return $scope.contest;
                    },
                    contestAssets: function($http, $route) {
                        return $http.get(rootUrl + 'api/contest/' + $scope.contest.code + '/styleData').then(function(response){
                            return response.data;
                        });
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
            }, function () {});
        };

        $scope.resetPassword = function(inscription){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/resetPassword', inscription.user.id).success(function(response){
                $scope.msg = response.msg;
            });
        };
    }).controller('AdminContestsInvitationCtrl',function($scope, rootUrl, $sanitize, $location, $route, $timeout, $uibModalInstance, $http, Flash, contest, flowFactory){
        $scope.contest = contest;
        $scope.existingFlowObject = flowFactory.create({
            target: 'api/contest/' + $scope.contest.code + '/uploadFile',
            singleFile:true,
            testChunks:false
        });

        $scope.close = function(){
            $uibModalInstance.dismiss();
        };

        $scope.file = $scope.existingFlowObject.files;
        $scope.data = {};

        $scope.importList = function() {
            if($scope.file.length == 0){
                Flash.show('<i class="fa"> Seleccione un archivo </i>', 'warning', $scope);
            }
            else{
                if ($scope.modalForm) $scope.modalForm.$setPristine();
                $scope.errors = {};
                Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);

                var data = {
                    fileName: $scope.file[0].name,
                    createPassword: $scope.data['createPassword']
                };

                $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/importUserList', data).success(function(response){
                    $scope.sending = false;
                    if(response.errors){
                        $scope.errors = response.errors;
                        Flash.clear($scope);
                    }else if(response.error){
                        Flash.show(response.error, 'danger', $scope);
                    }else{
                        Flash.show(response.flash, 'success', $scope);
                        if(response.errorMails.length == 0){
                            $timeout(function(){
                                $uibModalInstance.close(response);
                            }, 1000);
                        }
                        else{

                        }
                    }
                }).error(function(data, status, headers, config){
                    $scope.sending = false;
                    Flash.show(data.error.message, 'danger', $scope);
                });
            }
        };
    })
    .controller('AdminContestDeadlinesCtrl', function($scope, rootUrl, currentBaseUrl,$window, $filter, $sanitize, $location, $routeParams, $timeout, $uibModal, Flash, $http, contest, wizardStatus){
        $scope.contest = contest;
        $scope.enableDelete = false;
        $scope.contest.wizard_status && $scope.contest.wizard_status != wizardStatus.WIZARD_FINISHED ? $scope.showThis = true : '';

        $scope.previous = function(){
            $location.path("admin/style");
        };

        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                inscription_start_at: $scope.contest.inscription_start_at,
                inscription_deadline1_at: $scope.contest.inscription_deadline1_at,
                inscription_deadline2_at: $scope.contest.inscription_deadline2_at,
                voters_start_at: $scope.contest.voters_start_at,
                voters_deadline1_at: $scope.contest.voters_deadline1_at,
                voters_deadline2_at: $scope.contest.voters_deadline2_at,
                inscription_public: $scope.contest.inscription_public,
                inscription_register_picture: $scope.contest.inscription_register_picture,
                voters_public: $scope.contest.voters_public,
                start_at: $scope.contest.start_at,
                finish_at: $scope.contest.finish_at
            };
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code +'/deadlines', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.contest = response.contest;
                    Flash.show(response.flash, 'success', $scope);
                    if($scope.contest.wizard_status && $scope.contest.wizard_status < wizardStatus.WIZARD_FINISHED){
                        $location.path("/");
                        $window.location.reload();
                    }
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };
    })
    .controller('AdminContestAllPagesCtrl', function($scope, $rootScope, rootUrl, currentBaseUrl, $uibModal, Flash, $http, contest){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.activeMenu = 'pages';
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};

        $scope.updateAllPagesList = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/allPagesData', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.pages = response.data.data;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.updateAllPagesList();
        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
        };
        $scope.delete = function(page) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + ($rootScope.contest ? 'view/admin/page/delete' : 'view/contests/delete-page'),
                controller: 'AdminContestAllPagesDeleteCtrl',
                resolve: {
                    page: function () {
                        return page;
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateAllPagesList();
            }, function () {
            });
        };
    })
    .controller('AdminContestAllPagesDeleteCtrl',function($scope, rootUrl, $route, $http, $timeout, $uibModalInstance, Flash, page, contest){
        $scope.contest = contest;
        $scope.page = page;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/page/'+page.id).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestPageCtrl', function($scope, $timeout, $filter, categoriesData, CategoryManager, currentBaseUrl, Flash, $http, rootUrl, contest, pageData){
        // Oculta los botones de cancelar y guardar
        contest.categories = categoriesData.data.categories;
        contest.children_categories = categoriesData.data.children_categories;
        CategoryManager.SetCategories(categoriesData.data.children_categories);
        $scope.inscriptionData = categoriesData.data.inscription;
        $scope.filteredCategories = categoriesData.data.filtered_categories;
        $scope.catMan = CategoryManager;
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'pages';
        $scope.contest = contest;
        $scope.page = {};
        $scope.statusFilters = [];
        $scope.query = '';
        $scope.listView = "thumbs";
        var lastEntryLoaded = 0;
        var entriesPerRow = 24;
        $scope.categories = $scope.contest.children_categories;
        $scope.categoriesList = $scope.contest.categories;

        $scope.toggleFilterBy = function(status){
            if(status != null) {
                var index = $scope.statusFilters.indexOf(status);
                if (index != -1) {
                    $scope.statusFilters.splice(index, 1);
                }
                else $scope.statusFilters.push(status);
            }else{
                $scope.statusFilters = [];
            }
        };

        $scope.selectEntries = function(){
            if(!$scope.entries){
                $http.post(currentBaseUrl + 'entries').success(function (data) {
                    $scope.setFilteredEntries(data.entries);
                });
            }else{
                $scope.setFilteredEntries($scope.entries);
            }
        };

        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.pagination = {query: ''};

        $scope.setFilteredEntries = function(entries){
            $scope.entriesRows = [];
            $scope.lastEntryShown = false;
            $scope.entries = entries;
            lastEntryLoaded = 0;
            if(!$scope.pagination.query) $scope.prefilteredEntries = $filter('entriesSearch')($scope.entries, $scope.pagination.query);
            else $scope.prefilteredEntries = $filter('entriesCommaSearch')($scope.entries, $scope.pagination.query);
            $scope.filteredEntries = $filter('entriesStatus')($scope.prefilteredEntries, $scope.statusFilters);
            $scope.inViewLoadMoreEntries(10);
        };

        $scope.$watch('pagination.query', function(){
            $scope.setFilteredEntries($scope.entries);
        });

        $scope.loadMoreEntries = function(){
            if(!$scope.filteredEntries) return;
            if(lastEntryLoaded > $scope.filteredEntries.length) return;
            $scope.entriesRows.push($scope.filteredEntries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.filteredEntries.length;
            $scope.firstTime = 0;
        };
        var loadingEntries = false;
        $scope.inViewLoadMoreEntries = function(delay){
            if(!!loadingEntries) return;
            loadingEntries = true;
            $timeout(function(){
                $scope.loadMoreEntries();
                loadingEntries = false;
            }, delay !== undefined ? delay : 1000);
        };

        $scope.deselectEntries = function(){
            $scope.filteredEntries = [];
            $scope.entries = null;
            $scope.statusFilters = [];
            $scope.setFilteredEntries($scope.entries);
        }

        if (!pageData.new) {
            $scope.page = pageData.page;
            if((pageData.entriesIds && pageData.entriesIds.length != '') || (pageData.filters && pageData.filters.length > 0)){
                $scope.page.hasEntries = 1;
                $scope.selectEntries();
                $scope.pagination.query = pageData.entriesIds;
                $scope.statusFilters = pageData.filters;
            }
        }
        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                id: $scope.contest.id,
                page: $scope.page,
                entries: $scope.filteredEntries,
                filters: $scope.statusFilters
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/page', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.page = response.page;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        }
    })
    .controller('AdminContestAssetsCtrl', function($scope, $rootScope, rootUrl, currentBaseUrl, $uibModal, Flash, $http, contest){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};

        $scope.updateAllAssetsList = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/allAssetsData', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.assets = response.data.data;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllAssetsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllAssetsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllAssetsList(); });
        $scope.$watch(function(){ return $scope.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllAssetsList(); });
        $scope.updateAllAssetsList();
        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
        };
        $scope.delete = function(asset) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/asset/delete',
                controller: 'AdminContestAllAssetsDeleteCtrl',
                resolve: {
                    asset: function () {
                        return asset;
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateAllAssetsList();
            }, function () {
            });
        };
        $scope.fileUploadUrl = currentBaseUrl + 'asset';
    })
    .controller('AdminContestAllAssetsDeleteCtrl',function($scope, rootUrl, $route, $http, $timeout, $uibModalInstance, Flash, asset, contest){
        $scope.contest = contest;
        $scope.asset = asset;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/asset/'+asset.id).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestVotingSessionsCtrl', function($scope, $rootScope, rootUrl, currentBaseUrl, inAdmin, $http, $uibModal, Flash, contest){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.activeMenu = 'voting-session';
        $scope.contest = contest;
        $scope.dataLoaded = false;

        $scope.pagination = {};
        $scope.updateAllVotingSessions = function(){
            /* TODO traer la info desde php */
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/voting/list', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.votingSessions = response.data.data;
                $scope.voteTypes = response.data.voteTypes;
                $scope.results = [];
                $scope.totalProgress = [];
                angular.forEach(response.data.votersProgress, function($voteUsers, $key){
                    $scope.totalProgress[$key] = $scope.results[$key] = $voteUsers;
                });
            });
        };

        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllVotingSessions(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllVotingSessions(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllVotingSessions(); });
        $scope.$watch(function(){ return $scope.pagination.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllVotingSessions(); });
        $scope.updateAllVotingSessions();

        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
        };

        $scope.delete = function(voting) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/voting-session/delete',
                controller: 'AdminContestVotingSessionDeleteCtrl',
                resolve: {
                    voting: function () {
                        return {voting: voting};
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateAllVotingSessions();
            }, function () {
            });
        };

    })
    .controller('AdminContestVotingSessionEditCtrl', function($scope, $rootScope, $location, rootUrl, currentBaseUrl, $http, $window, $timeout, $uibModal, CategoryManager, Flash, contest, voting, $filter){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'voting-session';
        $scope.showThis = voting.voting == null;
        $scope.isNewEdit = voting.voting == null;
        $scope.voting = voting.voting || {};
        $scope.voting.public = !!$scope.voting.public;
        $scope.voting.publicAnonymous = !!$scope.voting.publicAnonymous;
        $scope.voteTypes = voting.voteTypes;
        $scope.categories = voting.categories;
        $scope.voteCategories = voting.voteCategories;
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};
        $scope.statusFilters = [];
        $scope.shortlistArray = voting.shortList;
        $scope.superadmin = voting.superadmin;
        $scope.listOfShortLists = voting.listOfShortLists;
        $scope.firstTime = 0;
        if(voting.autoAbstains){
            if(voting.autoAbstains.length > 0){
                $scope.voting.autoAbstain = 1;
            }
        }
        if($scope.voting.config){
            $scope.voting.config.shortListConfig ? $scope.modelListShortList = $scope.voting.config.shortListConfig : $scope.modelListShortList = [];
        }

        $scope.colaborator = voting.colaborator;

        $scope.abstainsModal = function() {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/voting-session/autoAbstain',
                controller: 'AdminContestVotingSessionAutoAbstainCtrl',
                resolve: {
                    fields: function () {
                        return voting.metadataFields;
                    },
                    voting: function() {
                        return $scope.voting;
                    },
                    selected: function() {
                        return voting.autoAbstains;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                /*Flash.show(data.flash, 'success', $scope);
                $scope.updateJudges();*/
            }, function () {
            });
        };


        $scope.autoAbstainsModal = function(voting_session, judge){
            voting_session.judge = judge;
            var autoAbstainsModal = $uibModal.open({
                templateUrl: 'autoAbstainsModal.html',
                controller: 'autoAbstainsModal',
                resolve: {
                    voting_session: function () {
                        return voting_session;
                    }
                }
            });
            autoAbstainsModal.result.then(function (result) {
                    $scope.updateJudges();
            });

            $scope.close = function(){
                $uibModalInstance.close();
            };
        };

        $scope.fromShortList = function(id){
            if($scope.modelListShortList.indexOf(id) != -1)
                $scope.modelListShortList.splice($scope.modelListShortList.indexOf(id), 1);
            else
                $scope.modelListShortList.push(id);
        }

        $scope.addCriteria = function(){
            if($scope.voting.config == null) $scope.voting.config = {};
            if($scope.voting.config.criteria == null) $scope.voting.config.criteria = [];
            $scope.voting.config.criteria.push({})
        };
        $scope.removeCriteria = function(crit){
            if($scope.voting.config == null) $scope.voting.config = {};
            if($scope.voting.config.criteria == null) $scope.voting.config.criteria = [];
            if($scope.voting.config.criteria.indexOf(crit) != -1) $scope.voting.config.criteria.splice($scope.voting.config.criteria.indexOf(crit), 1);
        };

        $scope.addSelector = function(){
            if($scope.voting.config == null) $scope.voting.config = {};
            if($scope.voting.config.extra == null) $scope.voting.config.extra = [];
            if($scope.voting.vote_type == 4){//metalero
            var index = [0];
                angular.forEach($scope.voting.config.extra, function(item){
                    index.push(item.id+1);
                });

                var maxim = Math.max.apply(null, index);
                $scope.voting.config.extra.push({'id':maxim})
            }
            else $scope.voting.config.extra.push({})
        };
        $scope.removeSelector = function(crit){
            if($scope.voting.config == null) $scope.voting.config = {};
            if($scope.voting.config.extra == null) $scope.voting.config.extra = [];
            if($scope.voting.config.extra.indexOf(crit) != -1) $scope.voting.config.extra.splice($scope.voting.config.extra.indexOf(crit), 1);
        };

        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories($scope.categories);
        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.addCategory = function(category){
            if($scope.voteCategories.indexOf(category.id) == -1) {
                $scope.voteCategories.push(category.id);
            }
        };

        $scope.removeCategory = function(category_id){
            if($scope.voteCategories.indexOf(category_id) != -1) {
                $scope.voteCategories.splice($scope.voteCategories.indexOf(category_id), 1);
            }
        };

        $scope.addJudges = {groups: {}, autoUpdateJudges:false};
        $scope.inviteEmails = function(groupId){
            var newEmails = $scope.addJudges.groups[groupId].newEmails;
            if(newEmails != ''){
                if($scope.addJudges.groups[groupId] == null) $scope.addJudges.groups[groupId] = {};
                $scope.addJudges.groups[groupId].sending = true;
                $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code+'/invite', {'group':groupId, 'emails':newEmails}).success(function(response, status, headers, config){
                    $scope.addJudges.groups[groupId].sending = false;
                    if(response.errors){
                        $scope.addJudges.groups[groupId].errors = response.errors;
                        Flash.clear($scope);
                    }else if(response.error){
                        $scope.addJudges.groups[groupId].errors = response.error;
                        Flash.show(response.error, 'danger', $scope);
                    }else{
                        $scope.sent = true;
                        $scope.voting.voting_users = response.voting.voting_users || [];
                        $scope.filterJudges();
                        $scope.addJudges.groups[groupId].msg = response.msg;
                        Flash.show(response.flash, 'success', $scope);
                    }
                }).error(function(data, status, headers, config){
                    $scope.addJudges.groups[groupId].sending = false;
                    Flash.show(data.error.message, 'danger', $scope);
                });
            }
        };

        $scope.requestKeys = function(groupId, simple){
            if($scope.addJudges.groups[groupId] == null) $scope.addJudges.groups[groupId] = {};
            $scope.addJudges.groups[groupId].requesting = true;
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code+'/keys', {'group':groupId, 'simple':simple}).success(function(response, status, headers, config){
                $scope.addJudges.groups[groupId].requesting = false;
                if(response.errors){
                    $scope.addJudges.groups[groupId].errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    $scope.addJudges.groups[groupId].errors = response.error;
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    if($scope.addJudges.groups[groupId].invitationKeys == null) $scope.addJudges.groups[groupId].invitationKeys = [];
                    $scope.addJudges.groups[groupId].invitationKeys.push(response.keys);
                }
            }).error(function(data, status, headers, config){
                $scope.addJudges.groups[groupId].sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        var autoUpdateJudgesTimeout;
        $scope.updateJudges = function(){
            if(!$scope.voting.code) return;
            $http.get(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code+'/judges').success(function(response, status, headers, config){
                if(response.errors){
                }else if(response.error){
                }else{
                    $scope.voting.voting_users = response.judges || [];
                    $scope.filterJudges();
                }
                if($scope.addJudges.autoUpdateJudges){
                    autoUpdateJudgesTimeout = $timeout($scope.updateJudges, 10000);
                }
            }).error(function(data, status, headers, config){
            });
        };
        $scope.$watch(function(){ return $scope.addJudges.autoUpdateJudges; }, function(newval,oldval){
            if(newval){
                $scope.updateJudges();
            }else{
                $timeout.cancel( autoUpdateJudgesTimeout );
            }
        });
        $scope.$on('$routeChangeStart', function(next, current) {
            $timeout.cancel( autoUpdateJudgesTimeout );
        });

        $scope.addGroup = function(){
            $scope.addingGroup = true;
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/newgroup', {code:$scope.voting.code}).success(function(response, status, headers, config){
                $scope.addingGroup = false;
                if(response.errors){
                }else if(response.error){
                }else{
                    $scope.voting.voting_groups.push(response);
                }
                $scope.filterJudges();
            }).error(function(data, status, headers, config){
                $scope.addingGroup = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
        $scope.ungroupedJudges = {name:'Ungrouped'};
        $scope.toggleGroup = function(group, v){
            group.open = v == null ? !group.open : !!v;
        };
        $scope.expandAllGroups = function(groups){
            angular.forEach(groups, function(group){
                group.open = true;
            })
            $scope.ungroupedJudges.open = true;
        };

        $scope.closeAllGroups = function(groups){
            angular.forEach(groups, function(group){
                group.open = false;
            })
            $scope.ungroupedJudges.open = false;
        };

        var updatedJudges = [];
        $scope.toggleJudgeGroup = function(judge, groupId){
            if(groupId == null) judge.voting_groups = [];
            else {
                var index = judge.voting_groups.indexOf(groupId);
                if (index != -1) {
                    judge.voting_groups.splice(index, 1);
                } else {
                    judge.voting_groups.push(groupId);
                }
            }
            if(updatedJudges.indexOf(judge) == -1) updatedJudges.push(judge);
            $scope.filterJudges();
        };
        $scope.filt = $filter;
        $scope.bulks = [];
        //$scope.groupBulks = [];

        $scope.toggleBulkJudgeGroup = function(judges, groupId){
            angular.forEach(judges, function(judge){
                $scope.toggleJudgeGroup(judge, groupId);
            });
            $scope.bulks.length = 0;
            $scope.groupBulks.length = 0;
            $('input:checkbox').removeAttr('checked');
        };

        $scope.addBulkJudge = function(judge){
            var index = $scope.bulks.indexOf(judge);
            if (index != -1 || $scope.groupBulks.length == 1) {
                $scope.bulks.splice(index, 1);
            }
            if($scope.groupBulks.length == 0) $scope.bulks.push(judge);
        };

        /**** TODO arreglar cuando se selecciona todo un grupo y luego otro *****/
        $scope.groupBulks = [];
        $scope.addGroupBulkJudge = function(judges){
            angular.forEach(judges, function(judge){
                $scope.addBulkJudge(judge);
            });
        };

        $scope.bulkDeleteJudge = function(bulk){
            $scope.deleteJudge(bulk);
            $scope.bulks = [];
            //$scope.groupBulks = [];
        };

        $scope.judgesPagination = {query:'',
            sortBy:'judge',
            sortInverted:false,
            currentPage: 1,
            numPerPage: 10,
            maxSize: 10};
        //$scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.filterJudges(); });
        $scope.$watch(function(){ return $scope.judgesPagination.sortBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.filterJudges(); });
        $scope.$watch(function(){ return $scope.judgesPagination.sortInverted; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.filterJudges(); });
        $scope.$watch(function(){ return $scope.judgesPagination.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.judgesPagination.currentPage = 1;$scope.filterJudges();});
        $scope.changeJudgesOrder = function(newOrder){
            if($scope.judgesPagination.sortBy == newOrder){
                $scope.judgesPagination.sortInverted = !$scope.judgesPagination.sortInverted;
            }else{
                $scope.judgesPagination.sortBy = newOrder;
            }
        };

        $scope.sendInvitations = function(judge){
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/voting-session/sendInvites',
                controller: 'AdminContestVotingSessionSendInvitesCtrl',
                resolve: {
                    voting: function () {
                        return $scope.voting;
                    },
                    judge: function() { /*** Single or bulk invitation ***/
                        $scope.judge = [];
                        if(judge){
                            if(judge.length >= 1) $scope.judge = judge;
                            else $scope.judge[0] = judge;
                            return $scope.judge;
                        }
                    },
                    judges: function () {
                        return $scope.voting.voting_users;
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateJudges();
            }, function () {
            });
        };

        $scope.deleteJudge = function(judge) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/voting-session/deleteJudge',
                controller: 'AdminContestVotingSessionDeleteJudgeCtrl',
                resolve: {
                    voting: function () {
                        return $scope.voting;
                    },
                    judge: function () {
                        return judge;
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                //Flash.show(data.flash, 'success', $scope);
                $scope.updateJudges();
            }, function () {
            });
        };

        /*** Paginate judges ***/

        /*$scope.numPages = function () {
            return Math.ceil($scope.filteredJudges.length / $scope.judgesPagination.numPerPage);
        };*/

        $scope.filters = {query:''};
        $scope.totalProgress = 0;
        $scope.$watch(function(){ return $scope.filters.query; }, function(newVal, oldVal){
            if (typeof oldVal === 'undefined'){ return; }
            $scope.filterJudges();
        });
        $scope.filteredGroupJudges = {};
        $scope.groupsTotals = {};
        $scope.filterJudges = function(){
            var begin = (($scope.judgesPagination.currentPage - 1) * $scope.judgesPagination.numPerPage);
            var end = begin + $scope.judgesPagination.numPerPage;

            $scope.totalProgress = 0;
            $scope.filteredGroupJudges = [];
            $scope.groupsTotals = [];
            $scope.filteredJudges = $filter('judgesSearch')($scope.voting.voting_users, $scope.judgesPagination.query);
            $scope.filteredJudges = $filter('orderBy')($scope.filteredJudges, $scope.judgesPagination.sortBy, $scope.judgesPagination.sortInverted);
            $scope.filteredJudges = $filter('entriesStatus')($scope.filteredJudges, $scope.statusFilters);
            if($scope.filteredJudges.length == 0) return;
            angular.forEach($scope.filteredJudges, function(judge){
                if(judge.progress.total == 0) return;
                $scope.totalProgress += judge.progress.votes / judge.progress.total;
            });

            for(var i = 0; i<$scope.voting.voting_groups.length; i++) {
                var gid = $scope.voting.voting_groups[i].id;
                $scope.filteredGroupJudges[gid] = $filter('filterGroupJudges')($scope.filteredJudges, gid);
                $scope.groupsTotals[gid] = $scope.getGroupProgress($scope.filteredGroupJudges[gid])
            }
            $scope.filteredGroupJudges[0] = $filter('filterGroupJudges')($scope.filteredJudges.slice(begin, end), null);
            $scope.groupsTotals[0] = $scope.getGroupProgress($scope.filteredGroupJudges[0])
            $scope.totalProgress = Math.round(($scope.totalProgress/$scope.filteredJudges.length)*100 * 100)/100;
        };


        $scope.$watch(function(){ return $scope.judgesPagination.currentPage; },
            function(newVal,oldVal)
            { if (typeof oldVal === 'undefined'){ return; } $scope.filterJudges(); });
        /***********************/

        $scope.getGroupProgress = function(list){
            var totalProgress = 0;
            if(!list || list.length == 0) return 0;
            angular.forEach(list, function(judge){
                if(judge.progress.total == 0) return;
                totalProgress += judge.progress.votes / judge.progress.total;
            });
            return Math.round((totalProgress/list.length)*100 * 100)/100;
        };

        $scope.voting.shortlist = [];
        angular.forEach($scope.shortlistArray, function(value){
            $scope.voting.shortlist.push(value['entry_category_id']);
        });

        $scope.toggleFromShortlist = function(entCatId){
            if(entCatId == null){
                $scope.voting.shortlist = [];
                return;
            }
            var index = $scope.voting.shortlist.indexOf(entCatId);
            if (index == -1) {
                $scope.voting.shortlist.push(entCatId);
            }else $scope.voting.shortlist.splice(index, 1);

            var data = {
                'votingId' : $scope.voting.code,
                'shortList' : $scope.voting.shortlist
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/shortList', data).success(function(response){
            })
        };

        $scope.selectAllShortlist = function(){
            if($scope.pagination.query == ""){$scope.toggleFromShortlist(null);}
            else{
                angular.forEach($scope.filteredResults, function(entry){
                    angular.forEach(entry.entry_categories, function(categories){
                        $scope.toggleFromShortlist(categories.id);
                    })
                })
            }
        };

        $scope.countJudges = function(status){
            if(status == null) return $scope.voting.voting_users.length;
            var count = 0;
            angular.forEach($scope.voting.voting_users, function(item){
                if(item.status == status) count ++;
            });
            return count;
        };

        $scope.toggleFilterBy = function(status){
            if(status != null) {
                var index = $scope.statusFilters.indexOf(status);
                if (index != -1) {
                    $scope.statusFilters.splice(index, 1);
                }
                else $scope.statusFilters.push(status);
            }else{
                $scope.statusFilters = [];
            }
            $scope.judgesPagination.currentPage = 1;
            $scope.filterJudges();
        };

        $scope.countYesNoEntries = function(status){
            var count = 0;
            angular.forEach($scope.results, function(item){
                angular.forEach(item.votes, function(votes) {
                    if(status == 0)if(votes.totalYes < votes.totalNo) count ++;
                    if(status == 1)if(votes.totalYes > votes.totalNo) count ++;
                    if(status == 2 && votes.totalYes > 0 && votes.totalNo > 0)if(votes.totalYes == votes.totalNo) count ++;
                });
            });
            return count;
        };

        $scope.countEntries = function(status, dinamic) {
            var count = 0;
            if(!$scope.results) return;
            angular.forEach($scope.results, function(item){
                $categVotes = 0;
                /*if(status == null && status !== 0){
                    count += item.categories_id.length;
                }else{*/
                    angular.forEach(item.votes, function(votes, key) {
                        var exists = 0;
                        if($scope.voteCategories.length > 0) {
                            angular.forEach($scope.voteCategories, function (voteCat) {
                                if (+key === +voteCat) exists = 1;
                            });
                            if (exists === 0) return;
                        }
                        $categVotes ++;
                        if(dinamic === true){
                            if(votes['vote']){
                                angular.forEach(votes['vote'], function(vote){
                                    if(status === vote.name){
                                        count ++;
                                    }
                                });
                            }
                        }else{
                            switch (status) {
                                // ABSTAIN
                                case 0:
                                    if(votes.abstain === true || votes.abstains > 0 && (votes.vote && Object.keys(votes.vote).length > 0 && votes.judges > 0)){
                                    count++;
                                    }
                                    break;
                                // VOTED
                                case 1:
                                    if(votes.vote){
                                        switch($scope.voting.vote_type){
                                            /**** VERITRON ***/
                                            case 0:
                                                if (Object.keys(votes.vote).length > 0){count++;}
                                                break;
                                            /**** AVERAGE ****/
                                            case 1:
                                                if($scope.voting.config.criteria){
                                                    var showVoted = false;
                                                    if($scope.voting.config.minvotesunit && $scope.voting.config.minvotesunit > 0){
                                                        votes.total >= $scope.voting.config.minvotesunit ? showVoted = true : showVoted = false;
                                                    }
                                                    else votes.total > 0 && votes.judges > 0 ? showVoted = true : showVoted = false;
                                                    if(votes.vote && Object.keys(votes.vote).length > 0 && showVoted === true && votes.judges > 0){
                                                    if (Object.keys(votes.vote).length === $scope.voting.config.criteria.length){count++;}
                                                    }
                                                }
                                                else{
                                                    if(Object.keys(votes.vote).length > 0) count++;
                                                }
                                                break;
                                                /**** YES NO ***/
                                                case 2:
                                                    if(Object.keys(votes.vote).length > 0) count++;
                                                    break;
                                                /**** METALERO ****/
                                                case 4:
                                                    if(Object.keys(votes['vote']).length > 0) count++;
                                                    break;
                                            }
                                        }
                                    break;
                                // NO VOTED
                                case 2:
                                    switch($scope.voting.vote_type){
                                        /**** VERITRON ***/
                                        case 0:
                                            if (!votes.vote && votes.abstain != true) count++;
                                            if (votes.vote && Object.keys(votes.vote).length === 0){
                                                count++;
                                            }
                                            break;
                                        /**** AVERAGE ****/
                                        case 1:
                                            /*if (!votes.vote && votes.abstain != true){
                                                count++;
                                            }*/
                                            if(votes.vote){
                                                if($scope.voting.config.criteria){
                                                    var showVoted = false;
                                                    if($scope.voting.config.minvotesunit && $scope.voting.config.minvotesunit > 0){
                                                        votes.total < $scope.voting.config.minvotesunit ? showVoted = true : showVoted = false;
                                                    }
                                                else votes.total === 0 ? showVoted = true : showVoted = false;
                                                    if(showVoted === true && votes.abstains === 0 && Object.keys(votes.vote).length === 0){
                                                        //if (Object.keys(votes.vote).length != $scope.voting.config.criteria.length){
                                                        count++;
                                                    }
                                                }else{
                                                    if(Object.keys(votes.vote).length === 0) count++;
                                                }
                                            }
                                            break;
                                        /**** YES NO ***/
                                        case 2:
                                            if(Object.keys.length > 1)
                                                if(Object.keys(votes['vote']).length === 0) count++;
                                            break;
                                        /**** METALERO ****/
                                        case 4:
                                            if(Object.keys.length > 0)
                                                if(Object.keys(votes['vote']).length === 0) count++;
                                            break;
                                    }
                                break;
                                default: count++;
                                break;
                            }
                        }
                    });
                //}
                //if($categVotes == count) count = 1;
            });
            return count;
        };
        $scope.votingEntriesFilters = [];
        $scope.toggleEntryFilterBy = function(status){
            if(status != null) {
                var index = $scope.votingEntriesFilters.indexOf(status);
                if (index != -1) {
                    $scope.votingEntriesFilters.splice(index, 1);
                }
                else $scope.votingEntriesFilters.push(status);
            }else{
                $scope.votingEntriesFilters = [];
            }
            $scope.setFilteredEntries();
        };

        $scope.dinamicEntriesFilters = [];
        $scope.dinamicEntriesFilter = function(status){
            if(status != null) {
                var index = $scope.dinamicEntriesFilters.indexOf(status);
                if (index != -1) {
                    $scope.dinamicEntriesFilters.splice(index, 1);
                }
                else $scope.dinamicEntriesFilters.push(status);
            }else{
                $scope.dinamicEntriesFilters = [];
            }
            $scope.setFilteredEntries();
        };

        $scope.listView = "list";
        $scope.toggleListView = function(){
            $scope.listView = $scope.listView == "list" ? "thumbs" : "list";
        };

        $scope.showGrouped = true;
        $scope.toggleListGrouped = function(){
            $scope.showGrouped = !$scope.showGrouped;
        };

        $scope.toggleCat = function(cat, v, childs){
            cat.open = v == null ? !cat.open : !!v;
            cat.entriesRows = [];
            if(cat.final == 1 && cat.open){
                cat.entriesRows = [];
                //var entriesIds = [];
                cat.lastEntryShown = false;
                cat.lastEntryLoaded = 0;
                cat.filteredEntries = $filter('entriesCategory')($scope.filteredResults, cat.id);
                //console.log(cat.filteredEntries.length);
                /*angular.forEach($scope.filteredEntries, function(item){
                 entriesIds.push(item.id);
                 });
                 userInscriptions.entriesIds = entriesIds;*/
                if(!childs) $scope.inViewLoadMoreCatEntries(cat, 100);
            }
            if(!!childs && angular.isDefined(cat.children_categories)){
                toggleAll(cat.children_categories, cat.open);
            }
        };
        function toggleAll(cats, open){
            for(var c in cats){
                $scope.toggleCat(cats[c], !!open, true);
            }
        }
        $scope.expandAll = function(){
            toggleAll($scope.categories, true);
        };
        $scope.collapseAll = function(){
            toggleAll($scope.categories, false);
        };

        $scope.yesNoEntriesFilters = [];
        $scope.yesNoFilters = function(status){
            if(status != null) {
                var index = $scope.yesNoEntriesFilters.indexOf(status);
                if (index != -1) {
                    $scope.yesNoEntriesFilters.splice(index, 1);
                }
                else $scope.yesNoEntriesFilters.push(status);
            }else{
                $scope.yesNoEntriesFilters = [];
            }
            $scope.setFilteredEntries();
            if($scope.showGrouped) $scope.expandAll();
        };

        var catEntriesPerRow = 12;
        $scope.inViewLoadMoreCatEntries = function(cat, delay){
            if(!!cat.loading) return;
            cat.loading = true;
            $timeout(function(){
                $scope.loadMoreCatEntries(cat);
                cat.loading = false;
            }, delay !== undefined ? delay : 1000);
        };
        $scope.loadMoreCatEntries = function(cat){
            //console.log(cat);
            if(!cat.filteredEntries) cat.filteredEntries = [];
            if(!cat.entriesRows) cat.entriesRows = [];
            if(cat.lastEntryLoaded > cat.filteredEntries.length) return;
            cat.entriesRows.push(cat.filteredEntries.slice(cat.lastEntryLoaded, cat.lastEntryLoaded + catEntriesPerRow));
            cat.lastEntryLoaded += catEntriesPerRow;
            cat.lastEntryShown = cat.lastEntryLoaded > cat.filteredEntries.length;
        };

        $scope.entriesRows = [];
        var lastEntryLoaded = 0;
        $scope.lastEntryShown = false;

        $scope.autoUpdateResults = false;
        $scope.loadingResults = true;
        var autoUpdateResultsTimeout;
        $scope.updateResults = function(){
            if(!$scope.voting.code) return;
            $scope.loadingResults = true;
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code+'/results', {fromShortLists : $scope.modelListShortList}).success(function(response, status, headers, config){
                if(response.errors){
                }else if(response.error){
                }else{
                    $scope.results = response.results || [];
                    $scope.setFilteredEntries();
                    $scope.lastEntryShown = false;
                    lastEntryLoaded = 0;
                    $scope.entriesRows = [];
                    $scope.loadMoreEntries();
                    //$scope.filterJudges();
                }
                $scope.loadingResults = false;
                if($scope.autoUpdateResults){
                    autoUpdateResultsTimeout = $timeout($scope.updateResults, 100000);
                }
            }).error(function(data, status, headers, config){
                $scope.loadingResults = false;
            });
        };

        $scope.resultCustomSort = function(entry){
            if($scope.voting.vote_type == 4){
                return res;
            }
            var res = - $scope.voting.config.max;
            angular.forEach(entry.votes, function(v){
                if(($scope.voting.config.minvotesunit == 1 && v.total < $scope.voting.config.minvotes)
                || ($scope.voting.config.minvotesunit == 0 && ((v.total / v.judges) * 100) < $scope.voting.config.minvotes)){
                    if(v.final - $scope.voting.config.max > res) res = v.final - $scope.voting.config.max;
                }else{
                    if(v.final > res) res = v.final;
                }
            });
            return res;
        };
        $scope.pagination = {query:'', sortBy: $scope.resultCustomSort, sortInverted:true};
        $scope.setSortBy = function(by){
            if($scope.pagination.sortBy == by){
                $scope.pagination.sortInverted = !$scope.pagination.sortInverted;
            }else{
                $scope.pagination.sortBy = by;
            }
            $scope.setFilteredEntries();
        };
        $scope.setFilteredEntries = function(){
            $scope.entriesRows = [];
            $scope.lastEntryShown = false;
            lastEntryLoaded = 0;
            $scope.filteredResults = $filter('votingSessionVotes')($scope.results, $scope.votingEntriesFilters, $scope.voting.config ? $scope.voting.config.minvotesunit : null);
            if($scope.voting.vote_type === 4) $scope.filteredResults = $filter('dinamicEntriesFilter')($scope.results, $scope.dinamicEntriesFilters);
            $scope.filteredResults = $filter('orderBy')($scope.filteredResults, $scope.pagination.sortBy, $scope.pagination.sortInverted);
            if(!$scope.pagination.query) $scope.filteredResults = $filter('entriesSearch')($scope.filteredResults, $scope.pagination.query);
            else $scope.filteredResults = $filter('entriesCommaSearch')($scope.filteredResults, $scope.pagination.query);
            //$scope.filteredResults = $filter('entriesSearch')($scope.filteredResults, $scope.pagination.query);
            $scope.filteredResults = $filter('yesNoFilters')($scope.filteredResults, $scope.yesNoEntriesFilters);
            if($scope.showGrouped) {
                var allCats = CategoryManager.GetCategoriesList();
                for (var i in allCats) {
                    var cat = allCats[i];
                    if(cat.final != 1){
                        cat.entriesCount = 0;
                        continue;
                    }
                    cat.entriesRows = [];
                    cat.lastEntryShown = false;
                    cat.lastEntryLoaded = 0;
                    cat.filteredEntries = $filter('dinamicEntriesFilterCategory')(cat.filteredEntries, $scope.dinamicEntriesFilters, cat.id);
                    //$scope.filteredEntries = $filter('dinamicEntriesFilter')($scope.filteredEntries, $scope.dinamicEntriesFilters);
                    //cat.entriesCount = cat.filteredEntries.length;
                }
                for (var i in allCats) {
                    var cat = allCats[i];
                    if(cat.final != 1) continue;
                    var mCat = cat;
                    while(mCat.parent != null){
                        mCat.parent.entriesCount += cat.entriesCount;
                        mCat = mCat.parent;
                    }
                }
                if($scope.firstTime == 0){
                    $scope.categoriesCopy = angular.copy($scope.categories);
                    $scope.firstTime = 1;
                }
            }else{
                $scope.filteredEntries = $filter('dinamicEntriesFilter')($scope.filteredEntries, $scope.dinamicEntriesFilters);
                if($scope.firstTime == 0){
                    $scope.categoriesCopy = angular.copy($scope.categories);
                    $scope.firstTime = 1;
                }
            }
            $scope.inViewLoadMoreEntries(10);
            $scope.expandAll();
        };
        $scope.$watch('pagination.query', function(){
            $scope.setFilteredEntries();
        });
        $scope.getHeight = function (divider){
            return 100/divider;
        };
        var entriesPerRow = 24;
        $scope.loadMoreEntries = function(){
            if(!$scope.filteredResults) return;
            if(lastEntryLoaded > $scope.filteredResults.length) return;
            $scope.entriesRows.push($scope.filteredResults.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.filteredResults.length;
        };
        var loadingEntries = false;
        $scope.inViewLoadMoreEntries = function(delay){
            if(!!loadingEntries) return;
            loadingEntries = true;
            $timeout(function(){
                $scope.loadMoreEntries();
                loadingEntries = false;
            }, delay !== undefined ? delay : 1000);
        };
        $scope.$watch(function(){ return $scope.autoUpdateResults; }, function(newval){
            if(newval){
                $scope.updateResults();
            }else{
                $timeout.cancel( autoUpdateResultsTimeout );
            }
        });
        $scope.updateResults();
        $scope.$on('$routeChangeStart', function() {
            $timeout.cancel( autoUpdateResultsTimeout );
        });

        $scope.save = function() {
            $scope.addJudges.groups = {};
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            $scope.sending = true;
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var updatesJudgesData = {};
            for(var i=0;i<updatedJudges.length;i++){
                updatesJudgesData[updatedJudges[i].id] = updatedJudges[i].voting_groups;
            }
            if($scope.voting.config.length == 0) $scope.voting.config = {};
            $scope.voting.config.shortListConfig = $scope.modelListShortList;
            if(!$scope.voting.voting_categories) $scope.voting.voting_categories = [];
            var data = {
                id: $scope.contest.id,
                voting: $scope.voting,
                votingCategories: $scope.voteCategories,
                updatedJudges: updatesJudgesData
            };

            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    $scope.errors = response.error;
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.voting = response.voting;
                    $scope.voting.public = !!$scope.voting.public;
                    $scope.voting.publicAnonymous = !!$scope.voting.publicAnonymous;
                    $scope.showThis = false;
                    $scope.filterJudges();
                    $scope.updateResults();
                    $scope.shortList = response.shortList;
                    $scope.voting.shortlist = [];
                    if($scope.voting.autoAbstains){
                        if($scope.voting.autoAbstains.length > 0)
                            $scope.voting.autoAbstain = 1;
                    }
                    angular.forEach($scope.shortList, function(value){
                        $scope.voting.shortlist.push(value['entry_category_id']);
                    });
                    $location.path('/admin/voting-session/'+$scope.voting.code);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        $scope.checkAll = function(category) {
            if($scope.voteCategories.indexOf(category.id) == -1) $scope.voteCategories.push(category.id);
            if(angular.isDefined(category.children_categories)){
                angular.forEach(category.children_categories, function(categ) {
                    $scope.checkAll(categ);
                });
            }
        };

        $scope.toggleThis = function(category){
            if($scope.voteCategories.indexOf(category.id) == -1){
                $scope.checkAll(category);
                $scope.checkParent(category);
                category.expanded = true;
            }else{
                $scope.uncheckAll(category);
            }
        };

        $scope.checkParent = function(category){
            if($scope.voteCategories.indexOf(category.id) == -1){
                $scope.voteCategories.push(category.id);
            }
            if(!!category.parent) $scope.checkParent(category.parent);
        };

        $scope.uncheckAll = function(category){
            var spliceIndex = $scope.voteCategories.indexOf(category.id);
            if(spliceIndex != -1)
                $scope.voteCategories.splice(spliceIndex, 1);
            for (var i = $scope.voteCategories.length - 1; i >= 0; i--) {
                angular.forEach(category.children_categories, function(categ) {
                    if ($scope.voteCategories[i] == categ.id){
                        $scope.voteCategories.splice(i, 1);
                    }
                    if(categ.final == 0) {
                        angular.forEach(categ.children_categories, function (children_categ) {
                            $scope.uncheckAll(children_categ);
                        });
                    }
                });
            }
        };
        $scope.testVote = {};
        $scope.metalVote = 'metal';
        if(!$scope.isNewEdit){
            $scope.sessionEntries = $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/sessionEntries/', {voteSession: $scope.voting.code, voteCats: $scope.voteCategories}).success(function(data){});
        }

        $scope.editJudgeVotingEntries = function(user, judge){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: 'votingUserEntryCategory.html',
                controller: 'votingUserEntryCategoryModalCtrl',
                resolve: {
                    entries: function(){
                        if(user){
                            return $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/votingUserEntries/', {userId: user.id, voteSession: $scope.voting.code}).success(function(data){
                            });
                        }else{
                            return $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/votingUserEntries/', {votingUserId: judge.id, voteSession: $scope.voting.code}).success(function(data){
                            });
                        }
                    },
                    votingSessionEntries: function(){
                        return $scope.sessionEntries;
                    },
                    voteSession: function(){
                        return $scope.voting.code;
                    },
                    judge: function(){
                        return judge;
                    },
                    categories: function(){
                        return $scope.categoriesCopy;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(result){
                    if(result.progress){
                        judge.progress = result.progress;
                    }
                    if(result.newMail){
                        judge.inscription.email = result.newMail;
                        judge.status = 0;
                    }
                }
            }).finally(function(){
                modalInstance.$destroy();
            });
        };

        $scope.editVotingGroup = function(group){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: 'editVotingGroup.html',
                controller: 'editVotingGroupCtrl',
                resolve: {
                    entries: function(){
                        return $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/votingGroupEntries/', {groupId: group.id}).success(function(data){
                        });
                    },
                    group: function(){
                        return group;
                    },
                    votingSessionEntries: function(){
                        return $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/sessionEntries/', {voteSession: $scope.voting.code, voteCats: $scope.voteCategories}).success(function(data){});
                    },
                    voteSession: function(){
                        return $scope.voting.code;
                    },
                    categories: function(){
                        return $scope.categoriesCopy;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                $scope.updateJudges();
                if(result){
                    //console.log(result);
                    if(result.newGroupName) group.name = result.newGroupName;
                    group.countEntries = result.totalEntries;
                }
            });
        };

        $scope.deleteGroup = function(group){
            var modalInstance = $uibModal.open({
                backdrop : 'static',
                keyboard : false,
                templateUrl: 'deleteVotingGroup.html',
                controller: 'deleteVotingGroupCtrl',
                resolve: {
                    group: function(){
                        return group;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (result) {
                if(result){
                    $scope.voting.voting_groups = $scope.voting.voting_groups.filter(function(returnableObjects){
                        $scope.filterJudges();
                        return returnableObjects.id !== result;
                    });
                }
            });
        };

        $scope.clearView = true;
        $scope.setClearView = function(){
            $scope.clearView = !$scope.clearView;
        };

        $scope.changeMail = false;
        $scope.acceptNewMail = function(judge, newMail){

        }
        $scope.exportJudges = function(){
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/exportJudges/', {data: $scope.filteredGroupJudges[0]}).success(function(data){
                return data;
            });
        };

        $scope.openExportModal = function() {
            var modalInstance = $uibModal.open({
                templateUrl: 'exportResultsManager.html',
                controller: 'openExportModalCtrl',
                resolve: {
                    voting: function () {
                        return $scope.voting;
                    },
                    metadataFields: function () {
                        return voting.metadataFields;
                    },
                    contest: function () {
                        return $scope.contest;
                    },
                    exportTemplates: function(){
                        return voting.exportTemplates;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                //Flash.show(data.flash, 'success', $scope);
            }, function () {
            });
        };

        $scope.openRankingConfig = function(){
            var rankingModal = $uibModal.open({
                templateUrl: 'rankingModal.html',
                controller: 'rankingModalCtrl',
                resolve: {
                    voting: function () {
                        return $scope.voting;
                    },
                    metadataFields: function () {
                        return voting.metadataFields;
                    },
                    contest: function () {
                        return $scope.contest;
                    },
                    categories: function () {
                        return $scope.categories;
                    }
                }
            });
            rankingModal.result.then(function(response){
            });
        }

        $scope.userInLobby = function(judge){
            console.log(judge);
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/votingLobby/', {judgeId: judge.id})
                .success(function(data){});
        }
    })
    .controller('rankingModalCtrl', function($scope, rootUrl,$filter, $http, $window, $location, $route, $timeout, $uibModalInstance, contest, metadataFields, voting, categories){
        $scope.metadataFields = metadataFields;
        $scope.contest = contest;
        $scope.allMetadataFields = [];
        $scope.categories = categories;
        $scope.selectedMetadataArray = [];
        $scope.selectedMetadata = [];
        $scope.selectedMetadataArrayIds = [];
        $scope.selectedCategories = [];

        $scope.addMetadataRanking = function(){
            var metadata = $scope.selectedMetadata.split(',');
            $scope.selectedMetadataArray.push(metadata[1]);
            $scope.selectedMetadataArrayIds.push(metadata[0]);
        };

        $scope.unselectMetadata = function(index){
            $scope.selectedMetadataArray.splice(index, 1);
            $scope.selectedMetadataArrayIds.splice(index, 1);
        };

        $scope.checkAll = function(category) {
            if($scope.selectedCategories.indexOf(category.id) === -1) $scope.selectedCategories.push(category.id);
            if(angular.isDefined(category.children_categories)){
                angular.forEach(category.children_categories, function(categ) {
                    $scope.checkAll(categ);
                });
            }
        };

        $scope.checkParent = function(category){
            if($scope.selectedCategories.indexOf(category.id) == -1){
                $scope.selectedCategories.push(category.id);
            }
            if(!!category.parent) $scope.checkParent(category.parent);
        };

        $scope.uncheckAll = function(category){
            var spliceIndex = $scope.selectedCategories.indexOf(category.id);
            if(spliceIndex != -1)
                $scope.selectedCategories.splice(spliceIndex, 1);
            for (var i = $scope.selectedCategories.length - 1; i >= 0; i--) {
                angular.forEach(category.children_categories, function(categ) {
                    if ($scope.selectedCategories[i] == categ.id){
                        $scope.selectedCategories.splice(i, 1);
                    }
                    if(categ.final == 0) {
                        angular.forEach(categ.children_categories, function (children_categ) {
                            $scope.uncheckAll(children_categ);
                        });
                    }
                });
            }
        };

        $scope.toggleThis = function(category){
            if($scope.selectedCategories.indexOf(category.id) == -1){
                $scope.checkAll(category);
                $scope.checkParent(category);
                category.expanded = true;
            }else{
                $scope.uncheckAll(category);
            }
        };

        /*angular.forEach($scope.metadataFields, function (md) {
            $scope.allMetadataFields.push(md.id);
        });*/

        $scope.cancel = function () {
            $uibModalInstance.close();
        };

        $scope.accept = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/exportRanking',
                {
                    metadataIds: $scope.selectedMetadataArrayIds,
                    voteSession: voting,
                    metadataLabel: $scope.selectedMetadataArray,
                    categories: $scope.selectedCategories
                },
                { responseType: 'arraybuffer' })
                .success(
                function (data) {
                    var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
                    saveAs(blob, 'Ranking-'+$scope.contest.code+'-'+voting.name+".xls");
                }
            )
        }
    })
    .controller('openExportModalCtrl',function($scope, rootUrl,$filter, $http, $window, $location, $route, $timeout, $uibModalInstance, contest, metadataFields, voting, exportTemplates) {
        $scope.metadataFields = metadataFields;
        $scope.sending = false;
        $scope.contest = contest;
        $scope.cancel = function () {
            $uibModalInstance.close();
        };

        $scope.type = 0;
        $scope.allMetadataFields = [];

        angular.forEach($scope.metadataFields, function (md) {
            $scope.allMetadataFields.push(md.id);
        });
        $scope.bulks = exportTemplates;
        var allBulk = false;
        $scope.addBulkMetadata = function (metadata) {
            if (!$scope.bulks[$scope.type]) $scope.bulks[$scope.type] = [];
            var index = $scope.bulks[$scope.type].indexOf(metadata);
            if (index != -1 || $scope.groupBulks.length == 1) {
                $scope.bulks[$scope.type].splice(index, 1);
            }
            else{
                if ($scope.groupBulks.length == 0) $scope.bulks[$scope.type].push(metadata);
            }
            if(allBulk == false){
                $scope.saveTemplates($scope.bulks);
            }
        };

        /**** TODO arreglar cuando se selecciona todo un grupo y luego otro *****/
        $scope.groupBulks = [];
        $scope.addAllBulkMetadata = function (metadata) {
            allBulk = true;
            angular.forEach(metadata, function (md) {
                $scope.addBulkMetadata(md);
            });
            allBulk = false;
            $scope.saveTemplates($scope.bulks);
        };

        $scope.modelList = [];
        $scope.export = {type: 'excel'};
        $scope.hideEntryNotVoted = {enable: 0}
        $scope.saveTemplates = function (bulks) {
            var data = bulks;
            /*angular.forEach(data, function(item){
                console.log(item);
            })*/
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/saveExportTemplate', {bulks: data, voting_code: voting.code}).success(
                function (data) {

                }
            )
        };

        $scope.accept = function(){
            $responseType = null;
            if ($scope.export.type == "excel"){ $responseType = 'arraybuffer'}
            $scope.sending = true;
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/exportResults',
                {type:$scope.export.type,
                fields: $scope.bulks[$scope.type],
                votingCode: voting.code,
                hideEntryNotVoted: $scope.hideEntryNotVoted.enable
                },
                { responseType: $responseType }).success(function(data) {
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth() + 1; //January is 0!
                    var yyyy = today.getFullYear();
                    if (dd < 10) {dd = '0' + dd}
                    if (mm < 10) {mm = '0' + mm}
                    today = yyyy + '/' + mm + '/' + dd;
                    /** Genera el blob y abre popup de save ***/
                    if ($scope.export.type == "excel"){
                        var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
                        saveAs(blob, 'Resultados-' + $scope.contest.code + '-' + voting.name + '-' + today +".xls");
                    }
                    if ($scope.export.type == "jsonExport"){
                        var jsonData = JSON.stringify(data);
                        var blob = new Blob([jsonData], {type: "text/json"});
                        saveAs(blob, 'Resultados-' + $scope.contest.code + '-' + voting.name + '-' +today+'.json');
                    }
                    if ($scope.export.type == "doc"){
                        var jsonData = JSON.stringify(data);
                        var blob = new Blob([jsonData], {type: "text/html"});
                        saveAs(blob, 'Guion-' + $scope.contest.code + '-' + voting.name + '-' +today+'.html');
                    }
                    $scope.sending = false;
                    $uibModalInstance.close();
            })
        }
    }).controller('deleteVotingGroupCtrl',function($scope, rootUrl,$filter, $http, $window, $location, $route, $timeout, $uibModalInstance, Flash, group){
        $scope.group = group;

        $scope.cancel = function(){
            $uibModalInstance.close();
        };

        $scope.accept = function(){
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/votingGroup/'+$scope.group.id).success(function(response){
            });
            $uibModalInstance.close($scope.group.id);
        };
    }).controller('editVotingGroupCtrl',function($scope, rootUrl,$filter, $http, $window, CategoryManager, $location, $route, $timeout, $uibModalInstance, Flash, group, votingSessionEntries, categories, entries){
        $scope.pagination = {newGroupName: ''};
        $scope.deletedEntries = [];
        $scope.group = group;
        $scope.sessionEntries = votingSessionEntries.data;
        $scope.categories = categories;
        $scope.showSelected = 0;
        $scope.selectedCategoriesArray = [];
        $scope.groupEntries = [];
        $scope.catMan = CategoryManager;

        CategoryManager.SetCategories(categories);

        $scope.toggleCat = function(cat, v, childs){
            cat.open = v == null ? !cat.open : !!v;
            cat.entriesRows = [];
            if(cat.final == 1 && cat.open){
                cat.entriesRows = [];
                cat.lastEntryShown = false;
                cat.lastEntryLoaded = 0;
                cat.filteredEntries = $filter('entriesCategory')($scope.filteredResults, cat.id);
                if(!childs) $scope.inViewLoadMoreCatEntries(cat, 100);
            }
            if(!!childs && angular.isDefined(cat.children_categories)){
                toggleAll(cat.children_categories, cat.open);
            }
        };
        function toggleAll(cats, open){
            for(var c in cats){
                $scope.toggleCat(cats[c], !!open, true);
            }
        }

        $scope.expandAll = function(){
            toggleAll($scope.categories, true);
        };
        $scope.collapseAll = function(){
            toggleAll($scope.categories, false);
        };

        $scope.addEntriesToBulk = function (entry, cat) {
            var index = $scope.groupEntries.indexOf(entry);
            if (index != -1) {
                var indexCateg = $scope.groupEntries[index].categories_id.indexOf(cat);
                if (indexCateg != -1) {
                    if($scope.groupEntries[index].categories_id.length === 1){
                        $scope.groupEntries.splice(index, 1);
                    }
                    else{
                        $scope.groupEntries[index].categories_id.splice(indexCateg, 1);
                    }
                }else{
                    $scope.groupEntries[index].categories_id.push(cat);
                }
            }
            else{
                if(entry.categories_id.length >= 1){
                    var auxCateg_id = [];
                    auxCateg_id.push(cat);
                    var entryAux = entry;
                    entryAux.categories_id = auxCateg_id;
                    $scope.groupEntries.push(entryAux);
                }else{
                    $scope.groupEntries.push(entry);
                }
            }
        };

        $scope.selectCategory = function(category, id){
            if(category.allSelected === undefined){
                angular.forEach(category.filteredEntries, function(entry){
                    var index = $scope.groupEntries.indexOf(entry);
                    if(index != -1){
                        var indexCateg = $scope.groupEntries[index].categories_id.indexOf(id);
                        if (indexCateg != -1) {
                            if ($scope.groupEntries[index].categories_id.length === 1) {
                                $scope.groupEntries.splice(index, 1);
                            }
                            else {
                                $scope.groupEntries[index].categories_id.splice(indexCateg, 1);
                            }
                        }
                    };
                });
                category.allSelected = false;
            }
            else{
                category.allSelected = true;
            }
            angular.forEach(category.filteredEntries, function(entry){
                $scope.addEntriesToBulk(entry, id, category.allSelected);
            })
        };

        $scope.selectedCategories = function(){
            if($scope.showSelected == 0){
                angular.forEach($scope.groupEntries, function(entry){
                    angular.forEach(entry.categories_id, function(categ){
                        $scope.selectedCategoriesArray.push(categ);
                    });
                });
            }else{
                $scope.selectedCategoriesArray = [];
            }
            $scope.showSelected = !$scope.showSelected;
        }

        $scope.filterSelected = function(category){
            if($scope.selectedCategoriesArray.length > 0){
                if($scope.selectedCategoriesArray.indexOf(category) != -1) return false;
                else return true;
            }
        }

        $scope.isSelected = function(entry, cat){
            var index = $scope.groupEntries.indexOf(entry);
            if(index != -1){
                return $scope.groupEntries[index].categories_id.indexOf(cat) != -1;
            }
        };

        $scope.countGroupEntries = function(list){
            var count = 0;
            for(var entryId in list){
                count += list[entryId].categories_id.length;
            };
            return count;
        };

        $scope.cancel = function(){
            $uibModalInstance.close();
        };

        $scope.groupNameChange = false;
        $scope.changeGroupName = function(){
            $scope.groupNameChange = !$scope.groupNameChange;
        };

        $scope.accept = function(){
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/groupEntriesCategories/', {categories:$scope.groupEntries, voting_group_id: group.id, newName: $scope.pagination.newGroupName}).success(function(data){
                var data = {'newGroupName' : $scope.pagination.newMail, 'totalEntries' : $scope.countGroupEntries($scope.groupEntries)};
                $uibModalInstance.close(data);
            });
        };

        $scope.flash = false;
        $scope.pagination = {search: ''};
        $scope.entriesRows = [];
        var lastEntryLoaded = 0;
        $scope.lastEntryShown = false;
        var entriesPerRow = 10;

        if(entries.data){
            entries.data.forEach(function(entry){
                entry.categories_id.forEach(function(cat_id){
                    var category = $scope.catMan.GetCategory(cat_id);
                    var catEntry = $filter('filter')(category.filteredEntries, {id:entry.id}, true);

                    if(catEntry !== undefined && catEntry.length > 0){
                        if($scope.groupEntries.indexOf(catEntry[0]) == -1){
                            catEntry[0].categories_id = entry.categories_id;
                            $scope.groupEntries.push(catEntry[0]);
                        }
                    }
                });
            });
        }

        $scope.$watch('pagination.query', function(){
            $scope.entriesRows = [];
            $scope.filteredResults = $filter('entriesCommaSearch')($scope.results, $scope.pagination.query);
            $scope.entriesRows.push($scope.filteredResults);
            $scope.expandAll();
        });
    }).controller('votingUserEntryCategoryModalCtrl',function($scope, rootUrl,$filter, $http, $window, $location, $route, $timeout, $uibModalInstance, Flash, votingSessionEntries, voteSession, judge, categories, CategoryManager, entries){
        $scope.pagination = {newGroupName: ''};
        $scope.deletedEntries = [];
        $scope.sessionEntries = votingSessionEntries.data;
        $scope.categories = categories;
        $scope.showSelected = 0;
        $scope.selectedCategoriesArray = [];
        $scope.judgeEntries = [];
        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories($scope.categories);
        $scope.totalSelectedEntries = entries.data.totalSelectedEntries;
        $scope.entriesRows = $scope.sessionEntries;
        $scope.judgeEntries = entries.data.entries;

        $scope.toggleCat = function(cat, v, childs){
            cat.open = v == null ? !cat.open : !!v;
            cat.entriesRows = [];
            if(cat.final == 1 && cat.open){
                cat.entriesRows = [];
                cat.lastEntryShown = false;
                cat.lastEntryLoaded = 0;
                cat.filteredEntries = $filter('entriesCategory')($scope.filteredResults, cat.id);
                if(!childs) $scope.inViewLoadMoreCatEntries(cat, 100);
            }
            if(!!childs && angular.isDefined(cat.children_categories)){
                toggleAll(cat.children_categories, cat.open);
            }
        };
        function toggleAll(cats, open){
            for(var c in cats){
                $scope.toggleCat(cats[c], !!open, true);
            }
        }

        $scope.expandAll = function(){
            toggleAll($scope.categories, true);
        };
        $scope.collapseAll = function(){
            toggleAll($scope.categories, false);
        };

        $scope.addEntriesToBulk = function (entry, cat) {
            var index = $scope.judgeEntries.findIndex(judgeEntry => judgeEntry.id === entry.id);

            if (index != -1) {
                var indexCateg = $scope.judgeEntries[index].categories_id.indexOf(cat);
                if (indexCateg != -1) {
                    if($scope.judgeEntries[index].categories_id.length === 1){
                        $scope.judgeEntries.splice(index, 1);
                        $scope.totalSelectedEntries--;
                    }
                    else{
                        $scope.judgeEntries[index].categories_id.splice(indexCateg, 1);
                        $scope.totalSelectedEntries--;
                    }
                }else{
                    $scope.judgeEntries[index].categories_id.push(cat);
                    $scope.totalSelectedEntries++;
                }
            }
            else{
                if(entry.categories_id.length >= 1){
                    var auxCateg_id = [];
                    auxCateg_id.push(cat);
                    var entryAux = entry;
                    entryAux.categories_id = auxCateg_id;
                    $scope.judgeEntries.push(entryAux);
                    $scope.totalSelectedEntries++;
                }else{
                    $scope.judgeEntries.push(entry);
                    $scope.totalSelectedEntries++;
                }
            }
        };

        $scope.selectCategory = function(category, id){
            if(category.allSelected === undefined || category.allSelected === true){
                angular.forEach(category.filteredEntries, function(entry){
                    var index = $scope.judgeEntries.findIndex(judgeEntry => judgeEntry.id === entry.id);
                    if(index !== -1){
                        var indexCateg = $scope.judgeEntries[index].categories_id.indexOf(id);
                        //var indexCateg = $scope.judgeEntries[index].categories_id.findIndex(judgeEntry => judgeEntry === id);
                        if (indexCateg !== -1) {
                            if($scope.judgeEntries[index].categories_id.length === 1) {
                                $scope.judgeEntries.splice(index, 1);
                            }
                            else {
                                $scope.judgeEntries[index].categories_id.splice(indexCateg, 1);
                            }
                            $scope.totalSelectedEntries--;
                        }
                    };
                });
                category.allSelected = false;
            }
            else{
                category.allSelected = true;
                angular.forEach(category.filteredEntries, function(entry){
                    $scope.addEntriesToBulk(entry, id);
                });
            }
        };

        $scope.selectedCategories = function(){
            if($scope.showSelected == 0){
                angular.forEach($scope.judgeEntries, function(entry){
                    angular.forEach(entry.categories_id, function(categ){
                        $scope.selectedCategoriesArray.push(categ);
                    });
                });
            }else{
                $scope.selectedCategoriesArray = [];
            }
            $scope.showSelected = !$scope.showSelected;
        }

        $scope.filterSelected = function(category){
            if($scope.selectedCategoriesArray.length > 0){
                if($scope.selectedCategoriesArray.indexOf(category) != -1) return false;
                else return true;
            }
        }

        $scope.isSelected = function(entry, cat){
            var index = $scope.judgeEntries.findIndex(judgeEntry => judgeEntry.id === entry.id);
            if(index != -1){
                return $scope.judgeEntries[index].categories_id.indexOf(cat) != -1;
            }
        };

        $scope.countJudgeEntries = function(list){
            var count = 0;
            for(var entryId in list){
                count += list[entryId].categories_id.length;
            };
            return count;
        };
        $scope.judge = judge;
        $scope.mailChange = false;
        $scope.changeMail = function(){
            $scope.mailChange = !$scope.mailChange;
            //return mailChange;
        };
        $scope.cancel = function(){
            $uibModalInstance.dismiss();
        };

        $scope.accept = function(){
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+'/votingUserEntriesCategories/', {inscription: judge.inscription.id, newMail: $scope.pagination.newMail, categories: $scope.judgeEntries, voting_user_id: judge.id}).success(function(data){
                var data = {'newMail' : $scope.pagination.newMail, 'progress': data.votingUser.progress, 'countEntries' : $scope.countJudgeEntries($scope.judgeEntries)};
                $uibModalInstance.close(data);
            });
        };

        $scope.flash = false;
        $scope.entriesRows = [];
        var lastEntryLoaded = 0;
        $scope.lastEntryShown = false;
        var entriesPerRow = 10;
        $scope.loadMoreEntries = function(){
            if(lastEntryLoaded > $scope.filteredSessionEntries.length) return;
            $scope.entriesRows.push($scope.filteredSessionEntries.slice(lastEntryLoaded, lastEntryLoaded + entriesPerRow));
            lastEntryLoaded += entriesPerRow;
            $scope.lastEntryShown = lastEntryLoaded > $scope.filteredSessionEntries.length;
        };

        $scope.$watch('pagination.query', function(){
            $scope.entriesRows = [];
            $scope.filteredResults = $filter('entriesCommaSearch')($scope.sessionEntries, $scope.pagination.query);
            $scope.entriesRows.push($scope.filteredResults);
            $scope.expandAll();
        });
    })
    .controller('AdminContestVotingSessionDeleteCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, Flash, $http, voting, contest){
        $scope.contest = contest;
        $scope.voting = voting.voting;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                //Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestVotingSessionDeleteJudgeCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, Flash, $http, voting, judge, contest){
        $scope.contest = contest;
        $scope.voting = voting;
        $scope.judge = judge;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);

            if(Array.isArray(judge)){
                angular.forEach(judge, function(j){
                    $scope.delete(j.id);
                })
                $uibModalInstance.close();
            }
            else{
                $scope.delete(judge.id);
                $uibModalInstance.close();
            }
        };

        $scope.delete = function(judge){
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code+'/judge/'+judge).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    //$uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(){
                $scope.sending = false;
                //Flash.show(data.error.message, 'danger', $scope);
            });
        }
    })
    .controller('AdminContestVotingSessionAutoAbstainCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, contest, voting, Flash, $http, fields, selected) {

        $scope.contest = contest;
        $scope.fields = fields;
        $scope.selected = selected;

        $scope.close = function(){
            $uibModalInstance.dismiss();
        };

        $scope.send = function(){
            //POST DE METADATA PARA LAS ABSTENCIONES
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+voting.code+'/autoAbstains/',{fields:$scope.selected}).success(function(response, status, headers, config){

            });
            $uibModalInstance.close();
        };
    })
    .controller('AdminContestVotingSessionSendInvitesCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, Flash, $http, voting, judges, contest, judge){
        $scope.contest = contest;
        $scope.voting = voting;
        $scope.judges = judge ? judge : judges;
        $scope.reSend = false;
        if(judge) $scope.reSend = true;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.send = function(code) {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/voting/'+$scope.voting.code+'/sendInvites/',{judge:judge, code: code}).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(){
                $scope.sending = false;
            });
        };
    })
    .controller('AdminContestInvitationCtrl', function($scope, $rootScope, rootUrl, currentBaseUrl, $http, $uibModal, Flash, contest, invitationData){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.activeMenu = 'invitation';
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};
        $scope.invitations = invitationData.data;

        $scope.updateAllInvitations = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/invitationPagination', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.invitations = response.data.data;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInvitations(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInvitations(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInvitations(); });
        $scope.$watch(function(){ return $scope.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllInvitations(); });
        $scope.updateAllInvitations();
        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
        };
        $scope.delete = function(invitation) {
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + ($rootScope.contest ? 'view/admin/invitation/delete' : 'view/contests/delete-invitation'),
                controller: 'AdminContestInvitationDeleteCtrl',
                resolve: {
                    invitation: function () {
                        return {invitation: invitation};
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateAllInvitations();
            }, function () {
            });
        };
    })
    .controller('AdminContestInvitationDeleteCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, Flash, $http, invitation, contest){
        $scope.contest = contest;
        $scope.invitation = invitation.invitation;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/invitation/'+$scope.invitation.id).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                //Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestInvitationEditCtrl', function($scope, $rootScope, rootUrl, currentBaseUrl, $http, $uibModal, Flash, contest, invitationData, $filter){
        // Oculta los botones de cancelar y guardar
        $scope.activeMenu = 'invitation';
        $scope.hideSaveFooter = false;
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};
        $scope.invitationId = {}; // chequear si existe para ya traer los datos

        if(invitationData.data.length) $scope.invitationId = invitationData.data[0];

        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                invitation: $scope.invitationId
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/invitationId', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.page = response.page;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        }
    })
    .controller('AdminContestBillingCtrl', function($scope, $rootScope, $timeout, rootUrl, currentBaseUrl, $http, $uibModal, CategoryManager, Flash, contest, categoriesData, userInscriptions, adminInfo){
        $scope.hideSaveFooter = true; // Oculta los botones de cancelar y guardar
        $scope.activeMenu = 'billing';
        $scope.contest = contest;
        $scope.dataLoaded = false;
        $scope.pagination = {};

        $scope.data = adminInfo.data;
        $scope.billing = $scope.data.billing;

        $scope.billingIncomplete = $scope.billingComplete = $scope.billingError = $scope.billingProcessing =
            $scope.incompleteMoney = $scope.completeMoney = $scope.errorMoney = $scope.processingMoney = 0;
        angular.forEach($scope.billing, function(item){
            $scope.currency = item.currency;
            if(item.status == 0){
                $scope.billingIncomplete = item.total;
                $scope.incompleteMoney = item.totalBilling;
            }
            if(item.status == 1){
                $scope.billingComplete = item.total;
                $scope.completeMoney = item.totalBilling;
            }
            if(item.status == 2){
                $scope.billingError = item.total;
                $scope.errorMoney = item.totalBilling;
            }
            if(item.status == 5){
                $scope.billingProcessing = item.total;
                $scope.processingMoney = item.totalBilling;
            }
        });

        $scope.totalBilling = parseFloat($scope.incompleteMoney) + parseFloat($scope.completeMoney) + parseFloat($scope.processingMoney);

        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories(categoriesData.data.categories);
        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };

        $scope.updateAllPagesList = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/billing', $scope.pagination).then(function(response){
                $scope.billings = [];
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.billings = response.data.data;
                $scope.pagination.filters = response.data.filters;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllPagesList(); });
        $scope.$watch(function(){ return $scope.pagination.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; }$scope.updateAllPagesList();});
        $scope.updateAllPagesList();

        $scope.statusFilters = [];
        $scope.toggleFilterBy = function(status){
            var index = $scope.statusFilters.indexOf(status);
            if (index != -1) {$scope.statusFilters.splice(index, 1);}
            else $scope.statusFilters.push(status);
            $scope.pagination.filters = $scope.statusFilters;
            $scope.updateAllPagesList();
        };

        if(userInscriptions.billingSelected != null){
            $scope.toggleFilterBy(userInscriptions.billingSelected);
        }

        $scope.changeOrder = function(newOrder){
            if($scope.pagination.orderBy == newOrder){
                $scope.pagination.orderDir = !$scope.pagination.orderDir;
            }else{
                $scope.pagination.orderBy = newOrder;
            }
            $scope.updateAllPagesList();
        };
        $scope.changeStatus = function(bill, newStatus){
            var modalInstance = $uibModal.open({
                templateUrl: 'bill-statusForm.html',
                controller: 'AdminBillStatusCtrl',
                resolve: {
                    bill: function () {
                        return bill;
                    },
                    status: function(){
                        return newStatus;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (data) {
                $scope.updateAllPagesList();
            }, function () {});
        };
    })
    .controller('AdminContestBillCtrl', function($scope, Flash, $http, rootUrl, $uibModal, CategoryManager, contest, billData, categoriesData,currentBaseUrl){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'billing';
        $scope.contest = contest;
        $scope.bill = billData.bill;
        $scope.catMan = CategoryManager;
        CategoryManager.SetCategories(categoriesData.data.categories);
        $scope.getCategory = function(category_id){
            return $scope.catMan.GetCategory(category_id);
        };
        $scope.changeStatus = function(newStatus){
            var modalInstance = $uibModal.open({
                templateUrl: 'bill-statusForm.html',
                controller: 'AdminBillStatusCtrl',
                resolve: {
                    bill: function () {
                        return $scope.bill;
                    },
                    status: function(){
                        return newStatus;
                    }
                },
                scope: $scope
            });
            modalInstance.result.then(function (data) {
                $scope.bill = data;
            }, function () {});
        };
        $scope.save = function(){
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code +'/bill', {bill:$scope.bill}).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.showThis = false;
                    $scope.bill = response.bill;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show('Error. Please try again later.');
            });
        };

        $scope.showForm = function(user) {
            if(user.super == 1) return;
            var modalInstance = $uibModal.open({
                keyboard : true,
                templateUrl: currentBaseUrl+'view/inscription-form',
                controller: 'showInscriptionFormCtrl',
                resolve: {
                    fields: function () {
                        return $scope.contest.inscription_metadata_fields;
                    },
                    inscription: function ($http) {
                        return $http.post(currentBaseUrl + 'inscriptionForm', {user_id: user.id}).then(function (response) {
                            return response.data[0];
                        });
                    },
                    user: function(){
                        return user;
                    }
                },
                scope: $scope
            });
        };
    })
    .controller('AdminBillStatusCtrl', function($scope, rootUrl, $sanitize, $location, $http, $timeout, $filter, Flash, $uibModalInstance, bill, status){
        Flash.clear($scope);
        $scope.bill = bill;
        $scope.newStatus = status;
        $scope.close = function () {
            $uibModalInstance.close();
        };
        $scope.save = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                id: $scope.contest.id,
                bill: $scope.bill,
                status: $scope.newStatus
            };
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/billStatus', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.bill = response.bill;
                    $uibModalInstance.close($scope.bill);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

    })
    .controller('AdminContestMailEditCtrl', function($scope, Flash, rootUrl, $http, contest, contestStyle){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = true;
        $scope.contest = contest;
        $scope.contestAssets = contestStyle;
        $scope.registrationEmail = {};
        $scope.passRecoveryEmail = {};
        $scope.inscriptionEmail = {type: 7};
        $scope.inscriptorInvitationEmail = {type: 8};
        $scope.judgeInvitationEmail = {type: 9};
        $scope.collaboratorInvitationEmail = {type: 10};
        $scope.entryErrorEmail = {type: 11};
        $scope.entryApprovedEmail = {type: 12};
        $scope.entryFinalizedEmail = {type: 18};
        $scope.mediaErrorEmail = {type: 13};
        $scope.otherPurposesEmail = {type: 14};

        for(var i = 0; i < $scope.contestAssets.length; i++){
            var contestAsset = $scope.contestAssets[i];
            if (contestAsset.type == 7) $scope.inscriptionEmail = contestAsset;
            if (contestAsset.type == 8) $scope.inscriptorInvitationEmail = contestAsset;
            if (contestAsset.type == 9) $scope.judgeInvitationEmail = contestAsset;
            if (contestAsset.type == 10) $scope.collaboratorInvitationEmail = contestAsset;
            if (contestAsset.type == 11) $scope.entryErrorEmail = contestAsset;
            if (contestAsset.type == 12) $scope.entryApprovedEmail = contestAsset;
            if (contestAsset.type == 13) $scope.mediaErrorEmail = contestAsset;
            if (contestAsset.type == 14) $scope.otherPurposesEmail = contestAsset;
            if (contestAsset.type == 18) $scope.entryFinalizedEmail = contestAsset;
        }
        $scope.save = function(emailType) {
            $scope.errors = {};
            var data = {
                id: $scope.contest.id,
                homeData: emailType
            };

            //'saveHomeData': {url: rootUrl + 'api/contest/:id/homeData', method:'POST', params:{id:'@id'}},
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/homeData', data).success(function(response, status, headers, config){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.saveHomeOk = true;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestAllNewslettersCtrl', function($scope, Flash, rootUrl, $http, contest){
        $scope.contest = contest;
        $scope.pagination = {};

        $scope.updateAllNewslettersList = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/allNewslettersData', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.newsletters = response.data.data;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllNewslettersList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllNewslettersList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllNewslettersList(); });
        $scope.$watch(function(){ return $scope.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllNewslettersList(); });
        $scope.updateAllNewslettersList();
    })
    .controller('AdminContestNewsletterCtrl', function($scope, $uibModal, currentBaseUrl, Flash, $route, rootUrl, $http, contest, newsletterData, categoriesData){
        // Oculta los botones de cancelar y guardar
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'newsletter';
        $scope.contest = contest;
        $scope.newsletter = {};
        $scope.inscriptions = categoriesData.data.entryPerUser;
        $scope.allRoles = [];
        $scope.selectedEmails = [];
        $scope.listOfEmails = "";
        $scope.showAddEmails = false;
        $scope.bulkUsers = [];

        if (!newsletterData.new) {
            $scope.newsletter = newsletterData.newsletter;
            $scope.selectedEmails = newsletterData.selectedEmails;
        }

        $scope.updateEmails = function(user){
            $scope.selectedEmails.splice($scope.selectedEmails.indexOf(user), 1);
            /*return $http.get(rootUrl + 'api/contest/' + $scope.contest.code + '/newsletter/' + $route.current.params.newsletter).then(function(response){
                $scope.selectedEmails =  response.data.selectedEmails;
            });*/
        }

        $scope.inscriptor = 0;

        $scope.addInscription = function(inscription, bulk){
            if(bulk === 1) $scope.inscriptor = !$scope.inscriptor;
            if(bulk === 3) $scope.judge = !$scope.judge;
            if($scope.inscriptor === false) $scope.filterType = [];
            if(bulk === null){
                var index = $scope.selectedEmails.indexOf(inscription);
                if(index === -1)
                    $scope.selectedEmails.push(inscription);
                else
                    $scope.selectedEmails.splice(index, 1);
            }else{
                angular.forEach($scope.inscriptions, function(inscription){
                    if(bulk == inscription.role){
                        var index = $scope.selectedEmails.indexOf(inscription);
                        if(index === -1)
                            $scope.selectedEmails.push(inscription);
                        else
                            $scope.selectedEmails.splice(index, 1);
                    }
                    if(bulk === 'all'){
                        var index = $scope.selectedEmails.indexOf(inscription);
                        if(index === -1)
                            $scope.selectedEmails.push(inscription);
                        else
                            $scope.selectedEmails.splice(index, 1);
                    }
                });
            }
        };

        $scope.filterType = [];

        $scope.filterByType = function(type){
            $scope.selectedEmails = [];
            var $index = $scope.filterType.indexOf(type);
            if($index !== -1)
                $scope.filterType.splice($index, 1);
            else
                $scope.filterType.push(type);

            angular.forEach($scope.inscriptions, function(inscription){
                angular.forEach($scope.filterType, function(types){
                    if(inscription.inscription_type_id === types){
                    $scope.selectedEmails.push(inscription);
                    }
                })
            })
        };

        $scope.groupBulks = [];

        $scope.addAllBulk = function(){
            if($scope.bulkUsers.length > 0){
                $scope.bulkUsers = [];
                $scope.groupBulks.length = 0;
            }else{
                $scope.bulkUsers = $scope.selectedEmails;
            }
            console.log($scope.bulkUsers);
        };

        $scope.addBulk = function(user){
            var index = $scope.bulkUsers.indexOf(user);
            if(index === -1)
                $scope.bulkUsers.push(user);
            else
                $scope.bulkUsers.splice(index, 1);
            console.log($scope.bulkUsers);
        };

        $scope.save = function() {
            if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                id: $scope.contest.id,
                newsletter: $scope.newsletter,
                selectedEmails: $scope.selectedEmails,
                listOfEmails: $scope.listOfEmails
            };

            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/newsletter', data).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.newsletter = response.newsletter;
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        $scope.pagination = {all: true};

        $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/allInscriptionsData', $scope.pagination).then(function(response){
            $scope.dataLoaded = true;
            $scope.pagination = response.data.pagination;
            $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
            $scope.allRoles = response.data.allRoles;
            $scope.inscriptions = response.data.data;
            $scope.inscriptionTypes = response.data.inscriptionTypes;
        });

        $scope.sendNewsletters = function(email){
            $scope.dummyEmail = [];
            if(email !== undefined){
                $scope.dummyEmail.push({email: email, status: "0"});
            }

            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/voting-session/sendNewsletter',
                controller: 'AdminContestSendNewsletterCtrl',
                resolve: {
                    newsletter: function () {
                        return $scope.newsletter;
                    },
                    emails: function() { /*** Single or bulk invitation ***/
                    /*$scope.judge = [];
                        if(judge){
                            if(judge.length >= 1) $scope.judge = judge;
                            else $scope.judge[0] = judge;
                            return $scope.judge;
                        }*/
                        if($scope.dummyEmail.length > 0) return $scope.dummyEmail;
                        return $scope.selectedEmails;
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
            }, function () {
            });
        };

        $scope.delete = function(user){
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/newsletter/'+$scope.newsletter.id+'/newsletterUser/'+user.email).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.updateEmails(user);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(){
                $scope.sending = false;
                //Flash.show(data.error.message, 'danger', $scope);
            });
        }

    })
    .controller('AdminContestSendNewsletterCtrl',function($scope, rootUrl, $location, $route, $timeout, $uibModalInstance, Flash, $http, newsletter, emails, contest){
        $scope.contest = contest;
        $scope.newsletter = newsletter;
        $scope.emails = emails;
        $scope.reSend = false;
        //if(judge) $scope.reSend = true;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.send = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            var data = {
                newsletter: $scope.newsletter,
                users: $scope.emails
            };

            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);

            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/sendNewsletter/',{data:data}).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    //$uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                //Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestAllCollectionsCtrl', function($scope, $uibModal, $http, Flash, rootUrl, currentBaseUrl, contest){
        $scope.contest = contest;
        $scope.pagination = {};

        $scope.delete = function(collection){
            var modalInstance = $uibModal.open({
                templateUrl: currentBaseUrl + 'view/admin/collection/delete',
                controller: 'AdminContestCollectionDeleteCtrl',
                resolve: {
                    collection: function () {
                        return {collection: collection};
                    },
                    contest: function () {
                        return $scope.contest;
                    }
                }
            });
            modalInstance.result.then(function (data) {
                Flash.show(data.flash, 'success', $scope);
                $scope.updateAllCollectionsList();
            }, function () {
            });
        }

        $scope.updateAllCollectionsList = function(){
            $http.post(rootUrl + 'api/contest/' + $scope.contest.code + '/collection/list', $scope.pagination).then(function(response){
                $scope.dataLoaded = true;
                $scope.pagination = response.data.pagination;
                $scope.pagination.shownMax = Math.min($scope.pagination.page * $scope.pagination.perPage, $scope.pagination.total);
                $scope.collections = response.data.collection;
            });
        };
        $scope.$watch(function(){ return $scope.pagination.page; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllCollectionsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderBy; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllCollectionsList(); });
        $scope.$watch(function(){ return $scope.pagination.orderDir; }, function(newVal,oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllCollectionsList(); });
        $scope.$watch(function(){ return $scope.query; }, function(newVal, oldVal){ if (typeof oldVal === 'undefined'){ return; } $scope.updateAllCollectionsList(); });
        $scope.updateAllCollectionsList();
    })
    .controller('AdminContestCollectionDeleteCtrl',function($scope, $http, $location, $route, $timeout, $uibModalInstance, rootUrl, Flash, collection, contest){
        $scope.contest = contest;
        $scope.collection = collection.collection;
        $scope.close = function(){
            $uibModalInstance.dismiss();
        };
        $scope.destroy = function() {
            if ($scope.modalForm) $scope.modalForm.$setPristine();
            $scope.errors = {};
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            $http.delete(rootUrl + 'api/contest/'+ $scope.contest.code+ '/collection/'+$scope.collection.code).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $uibModalInstance.close(response);
                    Flash.show(response.flash, 'success', $scope);
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                //Flash.show(data.error.message, 'danger', $scope);
            });
        };
    })
    .controller('AdminContestCollectionCtrl', function($scope, $location, rootUrl, $http, Flash, contest, collectionData, metadataFields, votingSessions){
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'collections';
        $scope.contest = contest;
        collectionData.collection ? $scope.collection = collectionData.collection : $scope.collection = {};
        $scope.collection.metadata_config ? $scope.selectedMetadataArray = $scope.collection.metadata_config : $scope.selectedMetadataArray = [];
        $scope.votingSessions = votingSessions.data;
        $scope.metadataFields = metadataFields.data ? metadataFields.data : [];
        $scope.selectedMetadata = "";
        $scope.voteConfig = $scope.collection.vote_config;

        $scope.collection.config ? $scope.config = $scope.collection.config : $scope.config = {voteType: []};

        $scope.selectMetadata = function(selectedMetadata){
            var index = $scope.selectedMetadataArray.indexOf(selectedMetadata);
            if(index === -1){
                $scope.selectedMetadataArray.push(selectedMetadata);
                $scope.collection.metadata_config = $scope.selectedMetadataArray;
            }
        };

        $scope.unselectMetadata = function(index){
            if($scope.selectedMetadataArray)
                $scope.selectedMetadataArray.splice(index, 1);
            $scope.collection.metadata_config = $scope.selectedMetadataArray;
        };

        $scope.selectVoteType = function(vote){
            var index = $scope.config.voteType.indexOf(vote.id);
            if(index === -1){
                $scope.config.voteType.push(vote.id);
                vote.selected = true;
            }else{
                $scope.config.voteType.splice(index,1)
                vote.selected = false;
            }
        }

        $scope.save = function() {
            //if ($scope.contestForm) $scope.contestForm.$setPristine();
            $scope.errors = {};
            $scope.collection.config = $scope.config;
            Flash.show('<i class="fa fa-circle-o-notch fa-spin"></i>', 'warning', $scope);
            var data = {
                contestId: $scope.contest.id,
                collection: $scope.collection,
            };

            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/collection', data).success(function(response){
                $scope.sending = false;
                if(response.errors){
                    $scope.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    $scope.sent = true;
                    $scope.collection = response.collection;
                    console.log($scope.collection);
                    $scope.config = $scope.collection.config;
                    $location.path('/admin/collection/'+$scope.collection.code);
                    Flash.show(response.flash, 'success', $scope);
                    $scope.showThis = false;
                }
            }).error(function(data, status, headers, config){
                $scope.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        $scope.requestKeys = function(){
            $scope.collection.requesting = true;
            $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/collection/'+$scope.collection.code+'/keys').success(function(response){
                $scope.collection.requesting = false;
                if(response.errors){
                    $scope.collection.errors = response.errors;
                    Flash.clear($scope);
                }else if(response.error){
                    $scope.collection.errors = response.error;
                    Flash.show(response.error, 'danger', $scope);
                }else{
                    if($scope.collection.invitationKeys == null) $scope.collection.invitationKeys = [];
                    $scope.collection.invitationKeys.push(response.keys);
                }
            }).error(function(data, status, headers, config){
                $scope.collection.sending = false;
                Flash.show(data.error.message, 'danger', $scope);
            });
        };

        $scope.inviteEmails = function(groupId){
            var newEmails = $scope.collection.newEmails;
            if(newEmails != ''){
                $scope.collection.sending = true;
                $http.post(rootUrl + 'api/contest/'+ $scope.contest.code+ '/collection/'+$scope.collection.code+'/invite', {'emails':newEmails}).success(function(response){
                    $scope.collection.sending = false;
                    if(response.errors){
                        $scope.collection.errors = response.errors;
                        Flash.clear($scope);
                    }else if(response.error){
                        $scope.collection.errors = response.error;
                        Flash.show(response.error, 'danger', $scope);
                    }else{
                        $scope.sent = true;
                        $scope.collection.users = response.collection.users || [];
                        $scope.collection.msg = response.msg;
                        Flash.show(response.flash, 'success', $scope);
                    }
                }).error(function(data){
                    $scope.collection.sending = false;
                    Flash.show(data.error.message, 'danger', $scope);
                });
            }
        };
    }).controller('AdminContestMetaAnalysis', function($scope, $location, rootUrl, $http, Flash, contest, metadataAnalytics){
        $scope.hideSaveFooter = false;
        $scope.activeMenu = 'collections';
        $scope.contest = contest;
        $scope.metadataAnalytics = metadataAnalytics.data;
    });