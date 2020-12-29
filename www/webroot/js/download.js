var downloadModel = null;

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
 * This model will handle download operations
 */
function koDownloadModel() {
    var self = this;

    // Data objects
    self.resources = ko.observableArray();

    self.lastError = ko.observable(null);
    self.initialized = ko.observable(false);

    self.isFiltering = ko.observable(false);

    self.filterType = ko.observable("none");

    self.dataType = ko.observable("rdata");

    self.mineOnly = ko.observable(false);

    self.selectedProject = ko.observableArray();
    self.selectedSite = ko.observableArray();
    self.selectedWorldRegion = ko.observableArray();
    self.selectedCountry = ko.observableArray();

    self.filterType.subscribe(function(newValue) {
        if (newValue == null) {
            return;
        }

        self.selectedWorldRegion(null);
        self.selectedProject(null);
        self.selectedSite(null);
        self.selectedCountry(null);
    });

    self.selectedDataType = ko.computed(function() {
        //self.dataType = ko.observable("csv");
        return self.dataType();
    });

    self.selectedMineOnly = ko.computed(function() {
        return self.mineOnly();
    });

    self.noSelectedOptions = function(array) {
        if (array().length == 1 && array()[0] == undefined) {
            return true;
        }
    };

    self.projectList = ko.observableArray();
    self.projects = ko.computed(function() {
        var projectList = [];
        var projectKeys = [];
        // If world region is selected, then show all projects with with site IDs in that world region.
        for (var i = 0; i < self.projectList().length; i++) {
            var thisProject = self.projectList()[i];
            if ((self.selectedWorldRegion() != null && !self.noSelectedOptions(self.selectedWorldRegion) && self.selectedWorldRegion().length > 0)) {
                for (var j = 0; j < self.siteList().length; j++) {
                    var thisSite = self.siteList()[j];
                    for (var k = 0; k < self.selectedWorldRegion().length; k++) {
                        if (thisSite.system_country.id_world_region === self.selectedWorldRegion()[k] && thisSite.id_project === thisProject.id && !projectKeys[thisProject.id]) {
                            projectKeys[thisProject.id] = true;
                            projectList.push(thisProject);
                        }
                    }
                }
            } else {
                if (!projectKeys[thisProject.id]) {
                    projectKeys[thisProject.id] = true;
                    projectList.push(thisProject);
                }
            }
        }
        projectList.sort(function(a,b) {return (a.title > b.title) ? 1 : ((b.title > a.title) ? -1 : 0);} ); 
        return projectList;
    });
    self.siteList = ko.observableArray();
    self.sites = ko.computed(function() {
        var siteList = [];
        for (var i = 0; i < self.siteList().length; i++) {
            var thisSite = self.siteList()[i];
            if (self.selectedProject() != null && !self.noSelectedOptions(self.selectedProject) && self.selectedProject().length > 0) {
                var inProject = false;
                for (var j = 0; j < self.selectedProject().length; j++) {
                    if (thisSite.id_project == self.selectedProject()[j]) {
                        inProject = true;
                    }
                }
                if (!inProject) {
                    continue;
                }
            }
            if (self.selectedCountry() != null && !self.noSelectedOptions(self.selectedCountry) && self.selectedCountry().length > 0) {
                var inCountry = false;
                for (var j = 0; j < self.selectedCountry().length; j++) {
                    if (thisSite.system_country.id == self.selectedCountry()[j]) {
                        inCountry = true;
                    }
                }
                if (!inCountry) {
                    continue;
                }
            }
            
            if ((self.selectedWorldRegion() != null && !self.noSelectedOptions(self.selectedWorldRegion) && self.selectedWorldRegion().length > 0)) {
                var inRegion = false;
                for (var k = 0; k < self.selectedWorldRegion().length; k++) {
                    if (thisSite.system_country.id_world_region === self.selectedWorldRegion()[k]) {
                        inRegion = true;
                    }
                }
                if (!inRegion) {
                    continue;
                }
            }

            siteList.push(thisSite);
        }
        siteList.sort(function(a,b) {return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);} ); 
        return siteList;
    });

    self.worldRegionList = ko.observableArray();
    self.worldRegions = ko.computed(function() {
        //For each project, get all sites->countries->world regions
        var regionList = [];
        var selectedSite = [];
        for (var i = 0; i < self.siteList().length; i++) {
            var thisSite = self.siteList()[i];
            regionList.push(thisSite.system_country.id_world_region);
        }
        var results = [];
        var resultKeys = [];
        for (var j = 0; j < self.worldRegionList().length; j++) {
            for (var k = 0; k < regionList.length; k++) {
                var thisRegion = self.worldRegionList()[j];
                if (thisRegion.id === regionList[k]) {
                    if (resultKeys[thisRegion.id]) {
                        continue;
                    }
                    resultKeys[thisRegion.id] = true;
                    results.push(thisRegion);
                }
            }
        }
        if (selectedSite.length > 0) {
            self.isFiltering(true);
            self.selectedWorldRegion(selectedSite);
            self.isFiltering(false);
        }
        results.sort(function(a,b) {return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);} ); 
        return results;
    });

    self.countries = ko.computed(function() {
        var countries = [];

        // List countries for which we have data.
        var countryKeys = [];
        var selectedSite = [];
        for (var i = 0; i < self.siteList().length; i++) {
            var thisSite = self.siteList()[i];

            if (self.selectedWorldRegion() != null && !self.noSelectedOptions(self.selectedWorldRegion) && self.selectedWorldRegion().length > 0) {
                var inWorldRegion = false;
                for (var j = 0; j < self.selectedWorldRegion().length; j++) {
                    if (thisSite.system_country.id_world_region == self.selectedWorldRegion()[j]) {
                        inWorldRegion = true;
                    }
                }
                if (!inWorldRegion) {
                    continue;
                }
            }

            countryKeys.push(thisSite.system_country.id);
        }

        var results = [];
        var resultKeys = [];
        for (var l = 0; l < self.worldRegionList().length; l++) {
            var thisRegion = self.worldRegionList()[l];
            for (var m = 0; m < thisRegion.system_country.length; m++) {
                var thisCountry = thisRegion.system_country[m];
                for (var n = 0; n < countryKeys.length; n++) {
                    if (thisRegion.system_country[m].id === countryKeys[n]) {
                        if (resultKeys[thisCountry.id]) {
                            continue;
                        }
                        resultKeys[thisCountry.id] = true;
                        results.push(thisCountry);
                    }
                }
            }
        }
        if (selectedSite.length > 0) {
            self.isFiltering(true);
            self.selectedCountry(selectedSite);
            self.isFiltering(false);
        }
        results.sort(function(a,b) {return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);} ); 
        return results;
    });

    self.getQuery = ko.computed(function() {

        var hasQuery = false;
        var query = [];
        if (self.selectedWorldRegion() != null && !self.noSelectedOptions(self.selectedWorldRegion) && self.selectedWorldRegion().length > 0) {
            hasQuery = true;
            query.push('w=' + self.selectedWorldRegion());
            if (self.selectedCountry() != null && !self.noSelectedOptions(self.selectedCountry) && self.selectedCountry().length > 0) {
                hasQuery = true;
                query.push('c=' + self.selectedCountry());
            }
        }
        if (self.selectedProject() != null && !self.noSelectedOptions(self.selectedProject) && self.selectedProject().length > 0) {
            hasQuery = true;
            query.push('p=' + self.selectedProject());
        }

        if (self.selectedSite() != null && !self.noSelectedOptions(self.selectedSite) && self.selectedSite().length > 0) {
            hasQuery = true;
            query.push('s=' + self.selectedSite());
        }

        if (self.selectedDataType() != null) {
            hasQuery = true;
            query.push('d=' + self.selectedDataType());
        }

        if (self.selectedMineOnly() != null) {
            hasQuery = true;
            query.push('m=' + self.selectedMineOnly());
        }

        if (hasQuery) {
            return "?" + query.join("&");
        } else {
            return '';
        }
    });


    self.getMyCSV = ko.computed(function() {
        var URL = '/api/file/export/key/csv/mine';
        return URL + self.getQuery();
    });

    self.getAllCSV = ko.computed(function() {
        var URL = '/api/file/export/key/csv/all';
        return URL + self.getQuery();
    });

    self.getAllSQL = ko.computed(function() {
        var URL = '/api/file/export/all/sqlite';
        return URL + self.getQuery();
    });

    self.getData = ko.computed(function() {
        var URL = '/api/file/export/data';
        return URL + self.getQuery();
    });

    /*** MODEL HOUSEKEEPING **/
    self.initialize = function() {
        getData('/api/resource/all', function(data) {
            self.resources(data.resources);
        });
        getData('/api/data/project/all', function(data) {
            self.projectList(data.results.data);
        });
        getData('/api/data/site/all', function(data) {
            self.siteList(data.results.data);
        });
        getData('/api/system/world_region/all', function(data) {
            self.worldRegionList(data.world_regions);
        });
        self.initialized(true);
    };

}

function getData(url, callback) {
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, downloadModel.lastError);
    });
}

$(document).ready(function() {
    downloadModel = new koDownloadModel();
    downloadModel.initialize();
    ko.applyBindings(downloadModel);
});
