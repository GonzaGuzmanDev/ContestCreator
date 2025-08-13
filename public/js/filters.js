OxoAwards.filter('bytes', function() {
    return function(bytes, precision) {
        if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) return '-';
        if (typeof precision === 'undefined') precision = 1;
        var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'],
            number = Math.floor(Math.log(bytes) / Math.log(1024));
        return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
    }
}).filter('percentage', ['$filter', function ($filter) {
    return function (input, decimals) {
        return $filter('number')(input * 100, decimals) + '%';
    };
}]).filter('zpad', function() {
    return function(input, n) {
        if(input === undefined)
            input = ""
        if(input.length >= n)
            return input
        var zeros = "0".repeat(n);
        return (zeros + input).slice(-1 * n)
    };
}).filter('inArray', function($filter){
    return function(list, arrayFilter, element){
        if(arrayFilter && arrayFilter.length){
            return $filter("filter")(list, function(listItem){
                return arrayFilter.indexOf(parseInt(listItem[element])) != -1;
            });
        }else{
            return list;
        }
    };
}).filter('filterGroupJudges', function($filter){
    return function(list, groupId){
        return $filter("filter")(list, function(listItem){
            return groupId == null ? (!listItem.voting_groups || listItem.voting_groups.length == 0) : listItem.voting_groups.indexOf(groupId) != -1;
        });
        //return groupId == null ? input;
    };
}).filter('startFrom', function() {
    return function(input, start) {
        if (input) {
            start = +start;	// parse to int
            return input.slice(start);
        }
        return [];
    }
}).filter('entriesSearch', function() {
    return function(list, filter) {
        filter = filter.toLowerCase();
        var result = [];
        angular.forEach(list, function(item, key){
            var name = ""+item.name;
            if(name.toLowerCase().indexOf(filter) != -1){
                result.push(item);
            }else if((""+item.id).indexOf(filter) != -1){
                result.push(item);
            }else if(angular.isDefined(item.user)){
                if((item.user.first_name+' '+item.user.last_name).toLowerCase().indexOf(filter) != -1){
                    result.push(item);
                }
            }
        });
        return result;
    }
}).filter('entriesCommaSearch', function() {
    return function(list, filters) {
        if(!filters) return list;
        var splits = [];
        if (filters.indexOf(' ') > -1 || filters.indexOf(',') > -1) {
            splits = filters.split(/[\s,]+/);
        }else{
            splits[0] = filters;
        }
        var result = [];
        angular.forEach(splits,function (filter){
            if(filter.length == 0) return;
            filter = filter.toLowerCase();
            angular.forEach(list, function(item, key){
                var name = ""+item.name;
                if(name.toLowerCase().indexOf(filter) != -1){
                    result.push(item);
                }else if((""+item.id).indexOf(filter) != -1){
                    result.push(item);
                }else if(angular.isDefined(item.user)){
                    if((item.user.first_name+' '+item.user.last_name).toLowerCase().indexOf(filter) != -1){
                        result.push(item);
                    }
                }
            });
        });
        return result;
    }
}).filter('entriesCategory', function() {
    return function(list, filter) {
        var result = [];
        angular.forEach(list, function(item, key){
            if(item.categories_id.indexOf(filter) != -1){
                result.push(item);
            }
        });
        return result;
    }
}).filter('judgesSearch', function() {
    return function(list, filter) {
        filter = filter.toLowerCase();
        var result = [];
        angular.forEach(list, function(item, key){
            if(item.inscription){
                if (angular.isDefined(item.inscription.email) && item.inscription.email.toLowerCase().indexOf(filter) != -1) {
                    result.push(item);
                } else if (item.inscription.user != null) {
                    if ((item.inscription.user.first_name + ' ' + item.inscription.user.last_name + ' ' + item.inscription.user.email).toLowerCase().indexOf(filter) != -1) {
                        result.push(item);
                    }
                }
            }
        });
        return result;
    }
}).filter('entriesStatus', function() {
    return function(list, filter) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item, key){
            if(filter.indexOf(item.status) != -1){
                result.push(item);
            }
        });
        return result;
    }
}).filter('entriesVote', function() {
    return function(list, filter) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item){
            angular.forEach(filter, function(filt){
                angular.forEach(item.votes, function(votes) {
                    switch(filt){
                        case 0: if(votes.abstain == true){
                                    if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            break;
                        case 1: if(votes.vote){
                                    if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            break;
                        case 2:
                                if(!votes.vote && votes.abstain != true && votes.vote != 0 && (Object.keys(votes).length !== 0 || Object.keys(item.votes).length === 1)){
                                if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            break;
                    }
                })
            })
        });
        return result;
    }
}).filter('entriesVoteCategory', function() {
    return function(list, filter, catId) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item){
            angular.forEach(filter, function(filt){
                angular.forEach(item.votes, function(votes, key) {
                    if(key != catId) return;
                    switch(filt){
                        case 0:
                            if(votes.abstain == true){
                                    if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            break;
                        case 1: if(votes.vote){
                                    if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            break;
                        case 2: console.log(votes);
                            if(!votes.vote && votes.abstain != true && votes.vote != 0){
                                if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            break;
                    }
                })
            })
        });
        return result;
    }
}).filter('dinamicEntriesFilter', function() {
    return function(list, filter) {
        if(filter.length === 0) return list;
        var result = [];
        angular.forEach(list, function(item){
            angular.forEach(filter, function(filt){
                var voted = false;
                angular.forEach(item.votes, function(categs) {
                    angular.forEach(categs.vote, function(vote) {
                        angular.forEach(vote, function(name, key) {
                            if(key === "name"){
                                if(filt === name) {
                                    voted = true;
                                }
                            }
                        })
                    })
                });
                if(voted){
                    if(result.indexOf(item) == -1 )
                        result.push(item);
                }
            })
        });
        return result;
    }
}).filter('dinamicEntriesFilterCategory', function() {
    return function(list, filter, catId) {
        if(filter.length === 0) return list;
        var result = [];
        angular.forEach(list, function(item){
            angular.forEach(filter, function(filt){
                angular.forEach(item.votes, function(votes, key) {
                    if(catId != key) return;
                    if(votes['vote']){
                        if(filt == votes['vote'].name){
                            result.push(item);
                        }
                    }
                })
            })
        });
        return result;
    }
}).filter('yesNoFilters', function() {
    return function(list, filter) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item){
            angular.forEach(filter, function(filt){
                angular.forEach(item.votes, function(votes) {
                    if(votes.totalYes || votes.totalNo){
                        if(filt == 0)if(votes.totalYes < votes.totalNo) result.push(item);
                        if(filt == 1)if(votes.totalYes > votes.totalNo) result.push(item);
                        if(filt == 2 && votes.totalYes > 0 && votes.totalNo > 0){
                            if(votes.totalYes == votes.totalNo) result.push(item);
                        }
                    }
                    else if(filt == votes.vote){
                        result.push(item);
                    }
                })
            })
        });
        return result;
    }
}).filter('showWinners', function() {
    return function(list, filter) {
        if(filter == false) return list;
        var result = [];
        angular.forEach(list, function(item){
            var voted = false;
            angular.forEach(item.votes, function(votes) {
                if(votes['vote']){
                    item.groupBy = votes['vote']['score'];
                    voted = true;
                }
            })
            if(voted) result.push(item);
        });
        return result;
    }
}).filter('votingSessionVotes', function() {
    return function(list, filter, minVotes) {
        if(filter.length === 0) return list;
        var result = [];
        angular.forEach(list, function(item, itemKey){
            var catVotes = null;
            angular.forEach(filter, function(filt){
                catVotes = [];
                angular.forEach(item.votes, function(votes, key) {
                    switch(filt){
                        //Abstain
                        case 0:
                            if(votes.abstains > 0 && votes.judges > 0){
                                if(result.indexOf(item) === -1){
                                        catVotes[key] = votes;
                                    }
                                }
                        break;
                        //Voted
                        case 1:
                            var showVoted = false;
                            if(minVotes !== null && minVotes > 0) votes.total >= minVotes ? showVoted = true : showVoted = false;
                            else votes.total > 0 && votes.judges > 0 ? showVoted = true : showVoted = false;
                            if(votes.vote && Object.keys(votes.vote).length > 0 && showVoted === true && votes.judges > 0){
                                    if(result.indexOf(item) === -1){
                                        catVotes[key] = votes;
                                        //result.push(item);
                                    }
                                }
                        break;
                        //No voted
                        case 2:
                            var showVoted = false;
                            if(minVotes !== null  && minVotes > 0) votes.total < minVotes ? showVoted = true : showVoted = false;
                            else votes.total === 0 ? showVoted = true : showVoted = false;
                            if(showVoted === true && votes.abstains === 0 && Object.keys(votes.vote).length === 0){
                                if(result.indexOf(item) === -1){
                                        catVotes[key] = votes;
                                        //result.push(item);
                                    }
                                }
                        break;
                    }
                });
            });
            if(Object.keys(catVotes).length > 0){
                angular.forEach(catVotes, function(catVote,catVoteKey){
                    delete list[itemKey].votes[catVoteKey];
                    if(Object.keys(catVote).length > 1){
                        list[itemKey].votes[catVoteKey] = catVote;
                    }
                });
                result.push(item);
            }
        });
        return result;
    }
}).filter('unreadEntries', function() {
    return function(list, filter) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item, key){
            if(filter.indexOf(item.id) != -1){
                result.push(item);
            }
        });
        return result;
    }
}).filter('filterYesNoEntries', function() {
    return function(list, filter, category){
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(val1){
            if(val1.categories_id[0] == category){
                angular.forEach(filter, function(val2){
                    if(val1.id == val2.id){
                        console.log(val1, val2);
                        val1.votes = val2.votes;
                        //result.push(val1);
                    }
                });
            }
        });
        /*console.log(list);
        console.log(filter);
        list = filter;*/
        return list;
    }
}).filter('entriesBilling', function() {
    return function(list, filter) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item){
            if(filter.indexOf(4) != -1 && item.billings.length == 0 && result.indexOf(item) == -1){
                result.push(item);
            }else{
            if(item.billings.length > 0){
                    angular.forEach(item.billings, function(val){
                        if(filter.indexOf(val.status) != -1 && result.indexOf(item) == -1){
                            result.push(item);
                        }
                    });
                }
            }
        });
        return result;
    }
})
    .filter('singleDecimal', function ($filter) {
        return function (input) {
            if (isNaN(input)) return input;
            return Math.round(input * 10) / 10;
        };
    })

    .filter('filesSearch', function() {
    return function(list, filter) {
        filter = filter.toLowerCase();
        var result = [];
        angular.forEach(list, function(item, key){
            var name = ""+item.name;
            if(name.toLowerCase().indexOf(filter) != -1){
                result.push(item);
            }else if((""+item.id).indexOf(filter) != -1){
                result.push(item);
            }else{
                angular.forEach(item.entry_metadata_values, function(entry, key){
                    if(entry.id.indexOf(filter) != -1){
                        result.push(item);
                    }else if((""+entry.id).indexOf(filter) != -1){
                    result.push(item);
                    }
                })
            }
        });
        return result;
    }
}).filter('filesStatus', function() {
        return function(list, filter) {
            if(filter.length == 0) return list;
            var result = [];
            angular.forEach(list, function(item, key){
                angular.forEach(filter, function(filterIndex){
                    if(item.tech_status.indexOf(filterIndex) != -1){
                        result.push(item);
                    }
                })
            });
            return result;
        }
    }).filter('filesTypes', function() {
    return function(list, filter) {
        if(filter.length == 0) return list;
        var result = [];
        angular.forEach(list, function(item, key){
            angular.forEach(filter, function(filterIndex){
                if(item.type.indexOf(filterIndex) != -1){
                    result.push(item);
                }
            })
        });
        return result;
    }})
    .filter('filesInEntry', function() {
            return function (list, filter) {
                if (filter != true) return list;
                var result = [];
                angular.forEach(list, function (item) {
                    if (item.entry_metadata_values.length > 0) {
                        result.push(item);
                    }
                });
                return result;
            }
        })
    .filter('filesErrorEncode', function() {
            return function (list, filter) {
                if (filter != true) return list;
                var result = [];
                angular.forEach(list, function (item) {
                    if (item.status != 2) {
                        result.push(item);
                    }
                });
                return result;
            }
        })
    .filter('filesMetadataValueIndex', function() {
        return function (list) {
            var result = [];
            angular.forEach(list, function (item) {
                if(item.entry_metadata_values.length > 0){
                    angular.forEach(item.entry_metadata_values, function (metaValue) {
                        if(result.indexOf(metaValue.label) == -1){
                            result.push(metaValue.label);
                        }
                    });
                }
            });
            return result;
        }
    })
    .filter('filesMetadataValueId', function() {
            return function (list, filters) {
                if (filters.length == 0) return list;
                var result = [];
                angular.forEach(list, function (item) {
                    if(item.entry_metadata_values.length > 0){
                        angular.forEach(item.entry_metadata_values, function (metaValue) {
                            angular.forEach(filters, function (filter) {
                                if(metaValue.label == filter){
                                    if(result.indexOf(item) == -1){
                                        result.push(item);
                                    }
                                }
                            });
                        });
                    }
                });
                return result;
            }
        })
    .filter('echoswitch', function () {
    return function(values, input) {
        var ret = input;
        angular.forEach(values, function(value, key) {
            if(input == key) ret = value;
        });
        return ret;
    };
}).filter('nl2br', function($sce){
    return function(msg,is_xhtml) {
        var is_xhtml = is_xhtml || true;
        var breakTag = (is_xhtml) ? '<br />' : '<br>';
        var msg = (msg + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
        return $sce.trustAsHtml(msg);
    }
}).filter('decode', function() {
    return function(text) {
        return angular.element('<div>' + text + '</div>').text();
    };
}).filter('iif', function () {
    return function(input, trueValue, falseValue) {
        return input ? trueValue : falseValue;
    };
}).filter('unique', function() {
    return function(collection, keyname) {
        var output = [],
            keys = [];

        angular.forEach(collection, function(item) {
            var key = item[keyname];
            if(keys.indexOf(key) === -1) {
                keys.push(key);
                output.push(item);
            }
        });

        return output;
    };
});