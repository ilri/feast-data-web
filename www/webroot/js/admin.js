Dropzone.autoDiscover = false;
var adminModel = null;

function requestFailed(jqXHR, textStatus, errorThrown, errorObservable) {
    if (jqXHR.status === 401) {
        window.location.replace("/");
    } else if (jqXHR.status === 403) {
        window.location.replace("/timeout");
    } else {
        // TODO: Clean these up and make them readable.
        errorObservable(stringSystemError);
    }
}

/**
 * This model will handle course related operations (displaying active/available,
 * handling registrations).
 */
function koAdminModel() {
    var self = this;

    ko.utils.extend(self, new koReportModel());

    // TODO: DRY this stuff into admin.common
    self.currentUser = ko.observable();

    // Data objects
    self.users = ko.observableArray();

    // Value lists
    self.genders = ko.observableArray();
    self.salutations = ko.observableArray();
    self.worldRegions = ko.observableArray();
    self.userStatusOptions = userStatusOptions;
    self.userRoleOptions = userRoleOptions;

    // State variables
    self.selectedWorldRegion = ko.observable();
    self.selectedCountry = ko.observable();

    self.editingUser = ko.observable(null);
    self.showUserEditForm = ko.observable(true); // Form or results?    

    self.lastError = ko.observable();
    self.opCount = 0;

    self.initialized = ko.observable(false);

    self.reportError = function() {
        $('#errorModal').modal('show');
    };

    self.editUser = function(user) {
        if (user.contact_region_major != null) {
            for (var i = 0; i < self.worldRegions().length; i++) {
                if (self.worldRegions()[i].name == user.contact_region_major) {
                    self.selectedWorldRegion(self.worldRegions()[i]);
                    break;
                }
            }
        }
        if (user.contact_country_id != null && self.selectedWorldRegion() != null) {
            for (var j = 0; j < self.selectedWorldRegion().system_country.length; j++) {
                if (self.selectedWorldRegion().system_country[j].id == user.contact_country_id) {
                    self.selectedCountry(self.selectedWorldRegion().system_country[j]);
                    break;
                }
            }
        }
        self.lastError(null);
        self.showUserEditForm(true);
        self.editingUser(user);
        $('#user-edit-modal').modal('show');
    };

    self.saveUser = function() {
        var user = null;
        var operation = 'reg';

        var region = null;
        var country = null;

        // Set up regions
        if (self.selectedWorldRegion() != null) {
            region = self.selectedWorldRegion().name;
        }
        if (self.selectedCountry() != null) {
            country = self.selectedCountry().id;
        }

        // Grab form data
        user = $('#edit-user-form').serializeObject();
        user.user_edit_id = self.editingUser().id;
        user.user_edit_world_region = region;
        user.user_edit_country = country;
        operation = 'edit';

        if (user.email == "" || user.reg_first_name == "" || user.reg_password == "" || user.reg_confirm_password != user.reg_password) {
            self.lastError(stringRegInvalid);
            return;
        }

        // Save changes
        user.admin_operation = true;
        saveUser(user, operation === 'reg', function(data) {
            if (data.errors) {
                self.lastError(stringRegFailed);
                return;
            }
            self.showUserEditForm(false);
        });
    };

    self.confirmationRequestSent = {};
    self.confirmationResendSent = {};
    self.resendConfirmation = function(data, event) {
        var userId = data.id;
        if (self.confirmationResendSent[userId]) {
            alert(confirmationResendSent)
        }
        if (self.confirmationRequestSent[userId]) {
            alert(confirmationResendRequestSent);
        }
        self.confirmationRequestSent[userId] = true;
        userConfirmationResend(data.contact_email, function(data) {
            self.confirmationRequestSent[userId] = false
            if (data.message.type != 'error') {
                self.confirmationResendSent[userId] = true;
            }
            alert(data.message.text);
            console.log('resendConfirmation - REQUEST RECEIVED');
        });
        console.log('resendConfirmation - REQUEST SENT');
    };

    self.changeUserStatus = function(user, event) {
        if (event.isTrigger != null) {
            return;
        }
        var newStatus = parseInt(event.target.value);
        if (newStatus < 3) { // Can't set unconfirmed/pending.
            user.approvalStatus(user.user_approval_status_id);
            return;
        }
        saveUserStatus(user, newStatus, function(data) {
            // Optional callback.
        });
    };

    self.changeUserRole = function(user, event) {
        if (event.isTrigger != null) {
            return;
        }
        var newRole = parseInt(event.target.value);
        saveUserRole(user, newRole, function(data) {
            // Optional callback.
        });
    };

    /*** MODEL HOUSEKEEPING **/
    self.initialize = function() {
        self.isAdmin(true);
        self.getUserData(false, null, function() {
            // Optional callback... 
        });

        self.getResources();

        self.getSettings();

        // Get dropdown data.
        getSystemArray("/api/system/salutation/all", function(data) {
            self.salutations(data.salutation);
        });
        getSystemArray("/api/system/gender/all", function(data) {
            self.genders(data.gender);
        });
        getSystemArray("/api/system/world_region/all", function(data) {
            self.worldRegions(data.world_regions);
        });

        self.initialized(true);
    };

    /**
     * Sorting function for table results
     **/
    //data-sort attribute of currently sorted column
    self.sortByColumn = null;
    //true for descend sorting
    self.sortByDesc = false;
    /**
     * insert sorting symbol on currently active sorting column
     * @param  {Event} event not mandatory - if null self.sortByColumn is used as identifier
     */
    self.updateSortSymbol = function(event) {
        //prepare view toggle
        var symbolTemplate = '<span class="sort-symbol" style="display:block;float:right;">$sortSymbol</span>';
        var symbolAsc = '▼';
        var symbolDesc = '▲';
        symbolTemplate = symbolTemplate.replace('$sortSymbol', !self.sortByDesc ? symbolAsc : symbolDesc);
        //update view
        $(".sort-symbol").remove();
        if (event) {
            $(event.currentTarget).append(symbolTemplate);
        } else {
            //for default add on selected th
            $("th[data-sort='" + self.sortByColumn + "']").append(symbolTemplate);
        }
    };

    /**
     * click handler for result table header to activate sorting by given column
     * @param  {Object} data  knockout data
     * @param  {Event} event click event
     */
    self.sortBy = function(data, event) {
        var prop = null;
        if (!event) {
            prop = data;
        } else {
            prop = $(event.currentTarget).attr('data-sort');
        }
        if (!prop)
            return;
        //set ascending/desceding
        if (prop == self.sortByColumn)
            self.sortByDesc = !self.sortByDesc;
        else
            self.sortByDesc = false;
        self.sortByColumn = prop;
        self.updateSortSymbol(event);
        var propList = prop.split('-');
        prop = propList[0];
        switch (prop) {
            case 'created':
                self.users().sort(function(left, right) {
                    var l = left[prop + 'Sort'], r = right[prop + 'Sort'];
                    return l == r ? 0 : (l > r ? (self.sortByDesc ? -1 : 1) : (self.sortByDesc ? 1 : -1));
                });
                break;
            case 'name':
                self.users().sort(function(left, right) {
                    var l = left.name_last + ' ' + left.name_first, r = right.name_last + ' ' + right.name_first;
                    l = l.toLowerCase();
                    r = r.toLowerCase();
                    return l == r ? 0 : (l > r ? (self.sortByDesc ? -1 : 1) : (self.sortByDesc ? 1 : -1));
                });
                break;
            case 'contact_email':
                self.users().sort(function(left, right) {
                    return left.contact_email.toLowerCase() == right.contact_email.toLowerCase() ? 0 : (left.contact_email.toLowerCase() > right.contact_email.toLowerCase() ? (self.sortByDesc ? -1 : 1) : (self.sortByDesc ? 1 : -1))
                });
                break;
            case 'user_approval_status_id':
                self.users().sort(function(left, right) {
                    return left.user_approval_status_id == right.user_approval_status_id ? 0 : (left.user_approval_status_id > right.user_approval_status_id ? (self.sortByDesc ? -1 : 1) : (self.sortByDesc ? 1 : -1))
                });
                break;
            default:
                self.users().sort(function(left, right) {
                    return left[prop] == right[prop] ? 0 : (left[prop] > right[prop] ? (self.sortByDesc ? -1 : 1) : (self.sortByDesc ? 1 : -1))
                });
        }
        self.users.valueHasMutated();
    };

    /**
     * initialize click handler for results table headers
     */
    self.sortInit = function(ident) {
        $(ident).on("click", ".sortable", function(event) {
            var context = ko.contextFor(this);
            self.sortBy(context, event);
            return false;
        });
    };

    self.lastUsers = [];

    // Do this serially so we can have an optional callback.
    self.getUserData = function(showMore, filterString, callback) {
        getData('/api/user/current', function(data) {
            self.currentUser(data.user);

            // Deal with filtering / showing more
            var url = '/api/admin/users';

            var lastID = 0;
            if (showMore) {
                // Walk array because it's been sorted so IDs not in order
                for (var i = 0; i < self.lastUsers.length; i++) {
                    if (self.lastUsers[i].id > lastID) {
                        lastID = self.lastUsers[i].id;
                    }
                }
            } else {
                lastID = 0;
            }
            var args = showMore ? '?last=' + lastID : '';
            args += (filterString != null) ? ((args !== '' ? '&' : '?') + filterString) : '';
            args += self.isAdmin() ? ((args !== '' ? '&' : '?') + 'isAdmin=true') : '';

            url += args;

            getData(url, function(data) {
                if (!showMore) {
                    self.lastUsers = [];
                    self.users.removeAll();
                }
                self.lastUsers.extend(data.users);
                for (var i = 0; i < data.users.length; i++) {
                    data.users[i].approvalStatus = ko.observable(data.users[i].user_approval_status_id);
                    data.users[i].roleStatus = ko.observable(data.users[i].admin);
                    self.users.push(data.users[i]);
                }
            });

        });
    };

    self.selectedHideInactive = ko.observable(true);

    self.visibleUsers = ko.computed(function() {
        var visibleUsers = [];
        for (var i = 0; i < self.users().length; i++) {
            if (self.users()[i].user_approval_status_id == 4 && self.selectedHideInactive()) {
                continue;
            }
            visibleUsers.push(self.users()[i]);
        }
        return visibleUsers;
    });

    /**
     * User filtering
     */
    self.userMode = ko.observable('browse'); // browse or search
    self.toggleUserSearch = function( ) {
        self.userMode() == 'browse' ? self.userMode('search') : self.userMode('browse');
    };

    self.userFilters = ko.observableArray();
    self.newUserFilterColumn = ko.observable();
    self.newUserFilterValue = ko.observable();

    self.addUserFilter = function() {
        if (self.newUserFilterColumn() == null) {
            return;
        }
        var filter = {
            column: self.newUserFilterColumn(),
            value: self.newUserFilterValue()
        };

        self.newUserFilterColumn(null);
        self.newUserFilterValue(null);

        self.userFilters.push(filter);
        self.applyUserFilters();
    };

    self.removeUserFilter = function(filter) {
        self.userFilters.remove(filter);
        self.applyUserFilters();
    };

    self.applyUserFilters = function() {
        var filterString = "";
        if (self.userFilters().length > 0) {
            filterString += "sc=" + self.userFilters().length;
        }
        for (var i = 0; i < self.userFilters().length; i++) {
            var thisFilter = self.userFilters()[i];
            filterString += '&st' + i + '=' + thisFilter.column + '&sv' + i + '=' + thisFilter.value;
        }
        self.getUserData(false, filterString, function() {
            // Optional callback
        });
    };

    self.getResources = function() {
        getData('/api/resource/all?isAdmin=true', function(data) {
            for (var i = 0; i < data.resources.length; i++) {
                data.resources[i].visibility = ko.observable(data.resources[i].hidden);
                data.resources[i].desc = ko.observable(data.resources[i].description);
            }
            self.resources(data.resources);
        });
    };

    self.showMoreUsers = function() {
        self.getUserData(true, null, function() {
            // Optional callback
        });
    };

    self.canUpload = ko.observable(true);
    self.resources = ko.observableArray();
    self.resourceDescription = ko.observable(null);
    self.uploadMode = ko.observable(null);
    self.replaceFile = ko.observable(null);
    self.updateResource = function(resource) {
        self.canUpload(true);
        self.uploadMode('replace');
        self.replaceFile(resource);
        self.resourceDescription(resource.description);
        $('#resource-detail-modal').modal('show');
        self.activateDropzone();
    };

    self.toggleResourceVisibility = function(resource) {
        modifyResource(resource.id, resource.desc(), (resource.visibility() == 0 ? 1 : 0), function() {
            resource.visibility(resource.visibility() == 0 ? 1 : 0);
            self.getResources();
        });
    };

    self.deleteResource = function(resource) {
        if (!confirm("Are you sure you want to delete this resource?")) {
            return;
        }
        removeResource(resource.id, function() {
            self.getResources();
        });
    };

    self.addResource = function() {
        self.canUpload(true);
        self.replaceFile(null);
        self.uploadMode('new');
        self.resourceDescription(null);
        $('#resource-detail-modal').modal('show');
        self.activateDropzone();
    };
    self.showUploadButton = ko.observable(false);
    self.keepDataPrivate = ko.observable(false);
    self.uploadError = ko.observable(false);
    self.isSending = ko.observable(false);
    self.dropzone = null;
    self.activateDropzone = function() {
        if (self.dropzone != null) {
            return;
        }
        self.dropzone = new Dropzone("#file-upload-dropzone", {
            url: '/placeholder',
            autoProcessQueue: false,
            maxFiles: 1,
            dictDefaultMessage: "Click or drop a file here to upload.",
            addRemoveLinks: true,
            init: function() {
                this.on("processing", function(file) {
                    this.options.url = self.uploadMode() == 'new' ? '/api/resource/' : '/api/resource/' + self.replaceFile().id;
                });
                this.on("addedfile", function(file, response) {
                    $('.dz-success-mark').hide();
                    $('.dz-error-mark').hide();
                    self.showUploadButton(true);
                });
                this.on("removedfile", function(file, response) {
                    self.showUploadButton(false);
                });
                this.on("error", function(file, response) {
                    self.isSending(false);
                    self.uploadError('true');
                });
                this.on("success", function(file, response) {
                    self.isSending(false);
                    // Submit activity attempt for this upload if it was successful.
                    if (response.error == null) {
                        // What do we do on success?
                        $('#resource-detail-modal').modal('hide');
                        this.removeAllFiles();
                        self.canUpload(false);
                        self.uploadError(null);
                        self.keepDataPrivate(false);
                        self.showUploadButton(false);
                        self.getResources();
                        self.uploadMode(null);
                        self.dropzone = null;
                    } else {
                        self.uploadError(true)
                    }
                });
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("sending", function(file, xhr, formData) {
                    self.isSending(true);
                    // Will send the filesize along with the file as POST data.
                    formData.append("description", self.resourceDescription());
                    if (self.uploadMode() != 'new') {
                        formData.append("hidden", self.replaceFile().visibility());
                    }
                });
            }
        });
        return true; // Since this is technically a "show this element" check...
    };

    self.doResourceUpdate = function() {
        if (self.showUploadButton() === true) {
            self.dropzone.processQueue();
        } else {
            modifyResource(self.replaceFile().id, self.resourceDescription(), self.replaceFile().visibility(), function() {
                $('#resource-detail-modal').modal('hide');
                self.canUpload(false);
                self.uploadError(null);
                self.keepDataPrivate(false);
                self.showUploadButton(false);
                self.getResources();
                self.uploadMode(null);
                self.dropzone = null;
            });
        }
    };

    self.doFileUpload = function() {
        self.dropzone.processQueue();
    };

    self.settings = ko.observableArray();
    self.selectedSetting = ko.observable(null);
    self.getSettings = function() {
        getData('/api/setting/all', function(data) {
            for (var i = 0; i < data.settings.length; i++) {
                data.settings[i].setting = ko.observable(data.settings[i].setting);
                data.settings[i].value = ko.observable(data.settings[i].value);
            }
            self.settings(data.settings);
        });
    }

    self.addSetting = function() {
        $('#setting-detail-modal').modal('show');
        var newSetting = {
            id: null,
            setting: ko.observable(null),
            value: ko.observable(null),
        };
        self.selectedSetting(newSetting);
    };

    self.updateSetting = function(setting) {
        $('#setting-detail-modal').modal('show');
        self.selectedSetting(setting);

    };

    self.deleteSetting = function(setting) {
        removeSetting(setting, function() {
            self.getSettings();
        });
    };

    self.doSettingUpdate = function() {
        saveSetting(self.selectedSetting(), function() {
            $('#setting-detail-modal').modal('hide');
            self.selectedSetting(null);
            self.getSettings();
        });
    };
}

function saveSetting(setting, callback) {
    var url = null;
    var data = {};
    if (setting.id == null) {
        url = '/api/setting';
        data.setting = setting.setting();
        data.value = setting.value();
    } else {
        url = 'api/setting/' + setting.id;
        data.value = setting.value();
    }
    $.ajax({
        type: "POST",
        data: data,
        url: url,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}
;

function removeSetting(setting, callback) {
    $.ajax({
        type: "POST",
        data: {},
        url: '/api/setting/' + setting.id + '/delete',
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function removeResource(resourceID, callback) {
    $.ajax({
        type: "POST",
        data: {},
        url: '/api/resource/' + resourceID + '/delete',
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function modifyResource(resourceID, description, hidden, callback) {
    $.ajax({
        type: "POST",
        data: {'description': description, 'hidden': hidden},
        url: '/api/resource/' + resourceID + '/metadata',
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function saveUserRole(user, newRole, callback) {
    $.ajax({
        type: "POST",
        data: {newRole: newRole},
        url: '/api/user/' + user.id + '/role/' + newRole,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function saveUserStatus(user, newStatus, callback) {
    $.ajax({
        type: "POST",
        data: {newStatus: status},
        url: '/api/user/' + user.id + '/status/' + newStatus,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function userConfirmationResend(contactEmail, callback) {
    $.ajax({
        type: "POST",
        data: {contactEmail: contactEmail},
        url: '/api/user/confirmationresend',
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function saveUser(user, isNew, callback) {
    $.ajax({
        type: "POST",
        data: user,
        url: '/api/user/' + (isNew ? '' : user.user_edit_id + '/edit'),
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function getSystemArray(url, callback) {
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

function getData(url, callback) {
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, adminModel.lastError);
    });
}

$(document).ready(function() {
    adminModel = new koAdminModel();
    adminModel.initialize();
    ko.applyBindings(adminModel);
});

/**
 * Class Utils
 **/
(function(self, $, undefined) {
    /**
     * check array or knockoutjs observableArray for duplicate based on shared property
     * @param list - haystack
     * @param obj - needle
     * @param id - property used to test duplicity
     * @returns {Boolean} - true if haystack already contains needle
     */
    self.hasDuplicate = function(list, obj, id) {
        if (!list)
            return false;
        if (list instanceof Array)
            list = ko.observableArray(list);
        if (list().length < 1)
            return false;
        if (!obj)
            return false;
        for (var i = 0; i < list().length; i++) {
            if (list()[i][id] == obj[id])
                return true;
        }
        return false;
    };
}(window.utils = window.utils || {}, jQuery));
//end - Class Utils
