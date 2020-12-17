var userAccessModel = null;

function requestFailed(jqXHR, textStatus, errorThrown, errorObservable) {    
    if (jqXHR.status === 401) {
        window.location.replace("/");
    } else {
        // TODO: Clean these up and make them readable.
        errorObservable(jqXHR.responseText);
    };
}

/**
 * This model will handle pre-auth user access operations (register/login)
 * MVP Stage Notes:
 * 1) Not worrying about "no email" because client ensures email
 * 2) Not worrying about full postal addresses
 * 3) Not worrying about alt/supervisor contacts
 * 4) Not worrying about Skype, 
 */
function koUserAccessModel() {
    var self = this;
    self.genders = ko.observableArray();
    self.salutations = ko.observableArray();
    self.worldRegions = ko.observableArray();
    
    self.lastError = ko.observable();
    self.lastRegistrationError = ko.observable();
    self.lastSigninError = ko.observable();
    
    self.selectedWorldRegion = ko.observable();
    self.selectedCountry = ko.observable();
    
    self.showRegisterForm = ko.observable(true);
    self.showRegisterSuccess = ko.observable(false);
    
    self.initialize = function() {
        // Get dropdown data.
        getSystemArray("/api/system/salutation/all", function (data) {
           self.salutations(data.salutation); 
        });
        getSystemArray("/api/system/gender/all", function (data) {
           self.genders(data.gender); 
        });
        getSystemArray("/api/system/world_region/all", function (data) {
           self.worldRegions(data.world_regions); 
        });
    };
    
    /**
     * TODO: Client-side validation. Ideally the "register" button won't even 
     * be enabled unless all of the values look sane.
     */
    self.registerUser = function() {        
        var user = $('#register-user-form').serializeObject();
        if (user.email == "" || user.reg_first_name == "" || user.reg_last_name == "" || user.reg_password == "" || user.reg_confirm_password != user.reg_password) {
            self.lastRegistrationError(stringRegInvalid);
            return;
        }
        if (self.selectedWorldRegion() != null) {
            user.reg_world_region = self.selectedWorldRegion().name;
        }
        if (self.selectedCountry() != null) {
            user.reg_country = self.selectedCountry().id;
        }
        
        registerUser(user, function(result) {            
            self.lastRegistrationError(null);
            
            if(result.message && result.message.type == "success") {
                if (result.message.action == 'login') {
                    // TODO: redirect to /home
                } else {
                    self.showRegisterForm(false);
                    self.showRegisterSuccess(true);
                }
            } else {
                if (result.errors) {
                    self.lastRegistrationError(stringRegFailed);
                }
            }
            
        });
    };
}

function registerUser(user, callback) {
    $.ajax({
        type: "POST",
        data: user,
        url: '/api/user',
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        userAccessModel.lastRegistrationError(stringRegFailed);
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
        requestFailed(jqXHR, textStatus, errorThrown, userAccessModel.lastError);
    });
}

$(document).ready(function() {
    userAccessModel = new koUserAccessModel();
    userAccessModel.initialize();
    ko.applyBindings(userAccessModel, document.getElementById('register-modal'));
    
    // TODO: Focus input in modal if necessary
    $('#register-modal').on('shown.bs.modal', function () {
        $('#register-user-form')[0].reset();
        userAccessModel.lastRegistrationError(null);
    });
});
