var userProfileModel = null;

function requestFailed(jqXHR, textStatus, errorThrown, errorObservable) {
    if (jqXHR.status === 401) {
        window.location.replace("/");
    } else if (jqXHR.status === 403) {
        window.location.replace("/timeout");
    } else {
        // TODO: Clean these up and make them readable.
        errorObservable(systemErrorString);
    }
}

/**
 * This model will handle user profile operations
 * MVP: Only edit for now.
 */
function koUserProfileModel() {
    var self = this;

    // Data objects
    self.user = ko.observable();

    // Value lists
    self.genders = ko.observableArray();
    self.salutations = ko.observableArray();
    self.worldRegions = ko.observableArray();

    // State variables
    self.selectedWorldRegion = ko.observable();
    self.selectedCountry = ko.observable();

    self.lastError = ko.observable(null);
    self.initialized = ko.observable(false);
    self.reportError = function() {
        $('#errorModal').modal('show');
    };

    /*** EDIT USER **/
    self.saveUser = function() {
        var user = null;

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
        user.user_edit_id = self.user().id;
        user.user_edit_world_region = region;
        user.user_edit_country = country;

        if (user.email == "" || user.reg_first_name == "" || user.reg_password == "" || user.reg_confirm_password != user.reg_password) {
            self.lastError(stringRegInvalid);
            return;
        }

        // Save changes
        doSaveUser(user, function(data) {
            if (data.errors) {
                self.lastError(stringSystemError);
                return;
            }
            self.lastError(stringEditSuccess)
        });
    };

    /*** MODEL HOUSEKEEPING **/
    self.initialize = function() {
        self.initialized(true);

        // Get dropdown data.
        getSystemArray("/api/system/salutation/all", function(data) {
            self.salutations(data.salutation);
        });
        getSystemArray("/api/system/gender/all", function(data) {
            self.genders(data.gender);
        });
        getSystemArray("/api/system/world_region/all", function(data) {
            self.worldRegions(data.world_regions);
            getData("/api/user/current", function(data) {
                self.user(data.user);
                if (data.user.contact_region_major_free_entry != null) {
                    for (var i = 0; i < self.worldRegions().length; i++) {
                        if (self.worldRegions()[i].name == data.user.contact_region_major_free_entry) {
                            self.selectedWorldRegion(self.worldRegions()[i]);
                            break;
                        }
                    }
                }
                if (data.user.contact_country_id != null) {
                    for (var j = 0; j < self.selectedWorldRegion().system_country.length; j++) {
                        if (self.selectedWorldRegion().system_country[j].id == data.user.contact_country_id) {
                            self.selectedCountry(self.selectedWorldRegion().system_country[j]);
                            break;
                        }
                    }
                }
            });
        });

    };
}

function doSaveUser(user, callback) {
    $.ajax({
        type: "POST",
        data: user,
        url: '/api/user/' + user.user_edit_id + '/edit',
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, userProfileModel.lastError);
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
        requestFailed(jqXHR, textStatus, errorThrown, userProfileModel.lastError);
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
        requestFailed(jqXHR, textStatus, errorThrown, userProfileModel.lastError);
    });
}

$(document).ready(function() {
    userProfileModel = new koUserProfileModel();
    userProfileModel.initialize();
    ko.applyBindings(userProfileModel);
});
