Dropzone.autoDiscover = false;
var uploadModel = null;

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
 * This model will handle upload
 */
function koUploadModel() {
    var self = this;
    
    ko.utils.extend(self, new koReportModel());

    self.lastError = ko.observable(null);
    self.initialized = ko.observable(false);
    self.reportError = function() {
        $('#errorModal').modal('show');
    };

    /**
     * File uploading
     */

    self.canUpload = ko.observable(true);
    self.showUploadButton = ko.observable(false);
    self.keepDataPrivate = ko.observable(false);
    self.uploadError = ko.observable(false);
    self.isSending = ko.observable(false);
    self.dropzone = null;
    self.activateDropzone = function() {
        self.dropzone = new Dropzone("#file-upload-dropzone", {
            url: "/api/file/import",
            autoProcessQueue: false,
            maxFiles: 1,
            dictDefaultMessage: "Click or drop a file here to upload.",
            addRemoveLinks: true,
            init: function() {
                this.on("addedfile", function(file, response) {
                    $('.dz-success-mark').hide();
                    $('.dz-error-mark').hide();
                    self.showUploadButton(true);
                });
                this.on("removedfile", function(file, response) {
                    console.log(response)
                    self.showUploadButton(false);
                });
                this.on("error", function(file, response) {
                    console.log(response)
                    self.isSending(false);
                    self.uploadError(true);
                });
                this.on("success", function(file, response) {
                    console.log(response)
                    self.isSending(false);
                    // Submit activity attempt for this upload if it was successful.
                    if (response.error == null) {
                        // What do we do on success?
                        this.removeAllFiles();
                        self.canUpload(false);
                        self.uploadError(null);
                        self.keepDataPrivate(false);
                        self.showUploadButton(false);
                        self.getPrivateProjects();
                    } else {
                        self.uploadError(response.error);
                    }
                });
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
                this.on("sending", function(file, xhr, formData) {
                    self.isSending(true);
                    // Will send the filesize along with the file as POST data.
                    formData.append("keep_private", self.keepDataPrivate());
                });
            }
        });
        return true; // Since this is technically a "show this element" check...
    };

    self.doFileUpload = function() {
        self.dropzone.processQueue();
    };

    /*** MODEL HOUSEKEEPING **/
    self.initialize = function() {
        self.initialized(true);
        
        self.getPrivateProjects();
    };
    
    self.privateProjects = ko.observableArray();
    self.getPrivateProjects = function() {
        getData('api/project/private', function(data) {
            for (var i=0;i < data.results.length; i++) {
                var thisProject = data.results[i];
                thisProject.isSelected = ko.observable(false);
            }
            self.privateProjects(data.results);
        });
    };
    
    self.makePublic = function() {
        if (!confirm("Are you sure you want to publish this data? This action cannot be undone.")) {
            return;
        }
        
        for (var i=0; i < self.privateProjects().length; i++) {
            var thisProject = self.privateProjects()[i];
            if (thisProject.isSelected()) {
                var data = {
                    projectID : thisProject.id
                };
                postData('/api/user/project/'+thisProject.id+'/publish', data, function(result) {
                    if (!result.error) {
                        thisProject.isSelected(false);
                        self.privateProjects.remove(thisProject);
                    }
                });
            }
        }
    };
}

function isCustomizableTable(tableInfo) {
    var customList = ["animal_category","animal_species","animal_type","community_type","crop_type","feed_source","fodder_crop_type","income_activity_type","purchased_feed_type","unit_area","unit_mass_weight"]
    return (customList.indexOf(tableInfo.dbTableName) !== -1);
};

function postData(url, data, callback) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, uploadModel.lastError);
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
        requestFailed(jqXHR, textStatus, errorThrown, uploadModel.lastError);
    });
}

$(document).ready(function() {
    uploadModel = new koUploadModel();
    uploadModel.initialize();
    ko.applyBindings(uploadModel);
});

/**
 * Use instead of "concat" to preserve reference.
 * See @jcdude's answer: http://stackoverflow.com/questions/1374126/how-to-extend-an-existing-javascript-array-with-another-array
 */
Array.prototype.extend = function (other_array) {
    /* you should include a test to check whether other_array really is an array */
    other_array.forEach(function(v) {this.push(v)}, this);    
};