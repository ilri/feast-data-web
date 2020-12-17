var topReportModel = null;
var bottomReportModel = null;

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
 * This model will handle report
 */
function koReportModel(canvas) {
    var self = this;

    self.canvas = canvas;
    
    self.currentUser = null;

    self.lastError = ko.observable(null);
    self.initialized = ko.observable(false);
    self.reportError = function() {
        $('#errorModal').modal('show');
    };

    self.selectedGrouping = ko.observable('country');

    self.isFiltering = ko.observable(false);

    self.selectedChartType = ko.observable('Dry Matter Composition Of Diet');
    self.chartTypes = ['Crop Areas', 'Dry Matter Composition Of Diet', 'Income Sources', 'Livestock Holdings (TLU)', 'Rainfall'];

    self.availableGroupings = ko.observableArray([
        {name: 'Country', value: 'country'},
        {name: 'World Region', value: 'world_region'},
        {name: 'Gender', value: 'gender'},
        {name: 'Project', value: 'project'},
        {name: 'Site', value: 'site'},
    ]);

    self.selectedProject = ko.observable();
    self.selectedSite = ko.observable();
    self.selectedWorldRegion = ko.observable();
    self.selectedCountry = ko.observable();
    self.selectedGender = ko.observable();
    self.showMyData = ko.observable(false);

    self.selectedChartType.subscribe(function(newValue) {
        self.isFiltering(true);
        self.selectedProject(null);
        self.selectedSite(null);
        self.selectedWorldRegion(null);
        self.selectedCountry(null);
        self.selectedGender(null);
        self.showMyData(null);
        self.isFiltering(false);
        self.getReport();
    });

    self.projectList = ko.observableArray();
    self.projects = ko.computed(function() {
        var projectList = [];
        var projectKeys = [];
        // If world region is selected, then show all projects with with site IDs in that world region.
        for (var i = 0; i < self.projectList().length; i++) {
            var thisProject = self.projectList()[i];
            if (thisProject.exclude) {
                continue;
            }
            if (!self.showMyData()) {
                if (thisProject.user == null && thisProject.keep_private == true) { // we're logged out
                    continue;
                } else if (thisProject.user === self.currentUser && thisProject.keep_private === true) {
                    continue;
                }
            }
            if (self.selectedWorldRegion() != null || self.selectedCountry() != null) {
                for (var j = 0; j < self.siteList().length; j++) {
                    var thisSite = self.siteList()[j];
                    if (self.selectedCountry() != null && thisSite.system_country.id === self.selectedCountry() && thisSite.id_project === thisProject.id && !projectKeys[thisProject.id]) {
                        projectKeys[thisProject.id] = true;
                        projectList.push(thisProject);
                    } else if (self.selectedCountry() == null && thisSite.system_country.id_world_region === self.selectedWorldRegion() && thisSite.id_project === thisProject.id && !projectKeys[thisProject.id]) {
                        projectKeys[thisProject.id] = true;
                        projectList.push(thisProject);
                    }
                }
            } else {
                if (!projectKeys[thisProject.id]) {
                    projectKeys[thisProject.id] = true;
                    projectList.push(thisProject);
                }
            }
        }
        return projectList;
    });
    self.siteList = ko.observableArray();
    self.sites = ko.computed(function() {
        var siteList = [];
        for (var i = 0; i < self.siteList().length; i++) {
            var thisSite = self.siteList()[i];
            if (thisSite.exclude) {
                continue;
            }
            if (self.selectedProject() != null && thisSite.id_project !== self.selectedProject()) {
                continue;
            }
            if (self.selectedWorldRegion() != null && thisSite.system_country.id_world_region != self.selectedWorldRegion()) {
                continue;
            }
            siteList.push(thisSite);
        }
        return siteList;
    });
    self.worldRegionList = ko.observableArray();
    self.worldRegions = ko.computed(function() {
        //For each project, get all sites->countries->world regions
        var regionList = [];
        var selectedSite = null;
        for (var i = 0; i < self.siteList().length; i++) {
            var thisSite = self.siteList()[i];
            if (self.selectedProject() != null && thisSite.id_project !== self.selectedProject()) {
                continue;
            }
            if (self.selectedSite() != null && thisSite.id !== self.selectedSite()) {
                continue;
            } else if (self.selectedSite() != null && thisSite.id === self.selectedSite()) {
                selectedSite = thisSite;
            }
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
        if (selectedSite != null) {
            self.isFiltering(true);
            self.selectedWorldRegion(selectedSite.system_country.id_world_region);
            self.isFiltering(false);
        }
        return results;
    });

    self.countries = ko.computed(function() {
        var countries = [];

        // List countries for which we have data.
        var countryKeys = [];
        var selectedSite = null;
        for (var i = 0; i < self.siteList().length; i++) {
            var thisSite = self.siteList()[i];
            if (self.selectedProject() != null && thisSite.id_project !== self.selectedProject()) {
                continue;
            }
            if (self.selectedWorldRegion() != null && thisSite.system_country.id_world_region != self.selectedWorldRegion()) {
                continue;
            }
            if (self.selectedSite() != null && thisSite.id !== self.selectedSite()) {
                continue;
            } else if (self.selectedSite() != null && thisSite.id === self.selectedSite()) {
                selectedSite = thisSite;
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
        if (selectedSite != null) {
            self.isFiltering(true);
            self.selectedCountry(selectedSite.system_country.id);
            self.isFiltering(false);
        }
        return results;
    });
    self.genders = [{name: 'Female', value: 1}, {name: 'Male', value: 2}];

    self.showMyData.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });

    self.selectedGrouping.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        self.getReport();
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });

    self.selectedProject.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        if (newValue == null) {
            self.selectedSite(null);
            self.selectedCountry(null);
            self.selectedWorldRegion(null);
        }
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });
    self.selectedSite.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        if (newValue == null) {
            self.selectedCountry(null);
            self.selectedWorldRegion(null);
        }
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });
    self.selectedGender.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });
    self.selectedCountry.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });
    self.selectedWorldRegion.subscribe(function(newValue) {
        var applyFilters = false;
        if (!self.isFiltering()) {
            applyFilters = true;
            self.isFiltering(true);
        }
        if (newValue == null && !self.selectedSite()) {
            self.selectedCountry(null);
        }
        if (applyFilters) {
            self.applyFilters();
            self.isFiltering(false);
        }
    });

    /*** MODEL HOUSEKEEPING **/
    self.initialize = function() {
        self.initialized(true);
        getData('/api/data/project/all', function(data) {
            self.currentUser = data.results.currentUser;
            self.projectList(data.results.data);
        });
        getData('/api/data/site/all', function(data) {
            self.siteList(data.results.data);
        });
        getData('/api/system/world_region/all', function(data) {
            self.worldRegionList(data.world_regions);
        });
        self.getReport();
    };

    self.applyFilters = function() {
        var filterList = [];
        if (self.selectedCountry() != null) {
            filterList.push('c=' + self.selectedCountry());
        }
        if (self.selectedWorldRegion() != null) {
            filterList.push('w=' + self.selectedWorldRegion());
        }
        if (self.selectedProject() != null) {
            filterList.push('p=' + self.selectedProject());
        }
        if (self.selectedSite() != null) {
            filterList.push('s=' + self.selectedSite());
        }
        if (self.selectedGender() != null) {
            filterList.push('g=' + self.selectedGender());
        }
        if (self.showMyData()) {
            filterList.push('mine=1');
        }

        self.getReport(filterList.join('&'));
    };

    self.getReport = function(filters) {
        switch (self.selectedChartType()) {
            case 'Crop Areas':
                self.getCropAreaReport(filters);
                break;
            case 'Dry Matter Composition Of Diet':
                self.getDMReport(filters);
                break;
            case 'Income Sources':
                self.getIncomeSourceReport(filters);
                break;
            case 'Livestock Holdings (TLU)':
                self.getLivestockHoldingReport(filters);
                break;
            case 'Rainfall':
                self.getRainfallReport(filters);
                break;
            default:
                self.getDMReport(filters);
        }
    };

    self.lastReportURL = ko.observable(null);

    self.noData = ko.observable(false);

    self.getLivestockHoldingReport = function(filters) {
        var url = "/api/reports/livestockholdings/" + self.selectedGrouping();
        if (filters != null && filters.length > 0) {
            url += '?' + filters;
        }
        if (url == self.lastReportURL()) {
            return;
        } else {
            self.lastReportURL(url);
        }
        getData(url, function(data) {
            var reportData = [];
            var pivotedData = {};
            self.noData(data.report.length < 1);
            if (data.report.length < 1) {
                self.noData(true);
            } else {
                self.noData(false);
            }
            var categories = [];
            for (var i = 0; i < data.report.length; i++) {
                var thisRow = data.report[i];
                if (thisRow.category_of_animal == null) {
                    continue;
                }
                var thisDesc = thisRow.description.length < 16 ? thisRow.description : thisRow.description.substring(0, 15) + '...';
                categories.push(thisDesc);
                if (pivotedData[thisRow.category_of_animal] == null) {
                    pivotedData[thisRow.category_of_animal] = {
                        dataPoints: []
                    };
                }
                pivotedData[thisRow.category_of_animal].dataPoints.push({y: ((thisRow.average_tlus == null) ? 0 : parseFloat(thisRow.average_tlus)), label: thisDesc});
            }

            // Make sure there's at least one datapoint for each crop type for each segment
            for (var h = 0; h < categories.length; h++) {
                for (var segmentName in pivotedData) {
                    if (pivotedData.hasOwnProperty(segmentName)) {
                        var foundType = false;
                        for (var k = 0; k < pivotedData[segmentName].dataPoints.length; k++) {
                            if (pivotedData[segmentName].dataPoints[k].label == categories[h]) {
                                foundType = true;
                            }
                        }
                        if (!foundType) {
                            pivotedData[segmentName].dataPoints.push({y: 0, label: categories[h]});
                        }
                    }
                    pivotedData[segmentName].dataPoints.sort(function(a, b) {
                        return (a.label > b.label ? -1 : ((b.label > a.label) ? 1 : 0));
                    });
                }
            }

            for (var segmentName in pivotedData) {
                if (pivotedData.hasOwnProperty(segmentName)) {
                    pivotedData[segmentName].type = "stackedBar";
                    pivotedData[segmentName].showInLegend = true;
                    pivotedData[segmentName].name = segmentName;
                }
                reportData.push(pivotedData[segmentName]);
            }

            var chart = new CanvasJS.Chart(self.canvas,
                    {
                        title: {
                            text: "Livestock Holdings"
                        },
                        axisY: {
                            title: "Average TLUs"
                        },
                        axisY2: {
                            margin: 10
                        },
                        exportEnabled: true,
                        animationEnabled: true,
                        toolTip: {
                            shared: true,
                            content: "{label} {name}: {y} - <strong>#percent%</strong>",
                        },
                        data: reportData
                    });
            chart.render();
            self.makePDFButton("Livestock Holdings");
        });
    };

    self.makePDFButton = function(title) {
        $('#' + self.canvas + ' div.canvasjs-chart-toolbar > div > div:first').clone().text('Save as PDF').addClass('make-pdf-btn').appendTo('div.canvasjs-chart-toolbar > div');
        $("#" + self.canvas + " .make-pdf-btn").hover(
                function() {
                    $(this).css('background-color', '#eeeeee');
                }, function() {
            $(this).css('background-color', '');
        });
        $("#" + self.canvas + " .make-pdf-btn").click(function() {
            self.exportToPDF(title);
            $('#' + self.canvas + ' div.canvasjs-chart-toolbar > div').hide();
        });
    };

    self.exportToPDF = function(title) {
        var canvas = $("#" + self.canvas + " .canvasjs-chart-canvas").get(0);
        var width = $("#" + self.canvas + " .canvasjs-chart-canvas").width();
        var height = $("#" + self.canvas + " .canvasjs-chart-canvas").height();
        var dataURL = canvas.toDataURL();
        var pdf = new jsPDF('l', 'pt', [width, height]);
        pdf.addImage(dataURL, 'JPEG', 0, 0, width, height);
        pdf.save(title + ".pdf");
    };

    self.getCropAreaReport = function(filters) {
        var url = "/api/reports/cropareas/" + self.selectedGrouping();
        if (filters != null && filters.length > 0) {
            url += '?' + filters;
        }
        if (url == self.lastReportURL()) {
            return;
        } else {
            self.lastReportURL(url);
        }
        getData(url, function(data) {
            var reportData = [];
            var pivotedData = {};
            var cropTypes = [];
            if (data.report.length < 1) {
                self.noData(true);
            } else {
                self.noData(false);
            }
            for (var i = 0; i < data.report.length; i++) {
                var thisRow = data.report[i];
                cropTypes.push(thisRow.type_of_crop);
                if (pivotedData[thisRow.description] == null) {
                    pivotedData[thisRow.description] = {
                        dataPoints: []
                    };
                }
                pivotedData[thisRow.description].dataPoints.push({y: ((thisRow.average_ha == null) ? 0 : parseFloat(thisRow.average_ha)), label: thisRow.type_of_crop});
            }

            // Make sure there's at least one datapoint for each crop type for each segment
            for (var h = 0; h < cropTypes.length; h++) {
                for (var segmentName in pivotedData) {
                    if (pivotedData.hasOwnProperty(segmentName)) {
                        var foundType = false;
                        for (var k = 0; k < pivotedData[segmentName].dataPoints.length; k++) {
                            if (pivotedData[segmentName].dataPoints[k].label == cropTypes[h]) {
                                foundType = true;
                            }
                        }
                        if (!foundType) {
                            pivotedData[segmentName].dataPoints.push({y: 0, label: cropTypes[h]});
                        }
                    }
                    pivotedData[segmentName].dataPoints.sort(function(a, b) {
                        return (a.label > b.label ? -1 : ((b.label > a.label) ? 1 : 0));
                    });
                }
            }

            for (var segmentName in pivotedData) {
                if (pivotedData.hasOwnProperty(segmentName)) {
                    pivotedData[segmentName].type = "stackedBar";
                    pivotedData[segmentName].showInLegend = true;
                    pivotedData[segmentName].name = segmentName;
                }
                pivotedData[segmentName].dataPoints.sort(function(a, b) {
                    return (a.label > b.label ? -1 : ((b.label > a.label) ? 1 : 0));
                });
                reportData.push(pivotedData[segmentName]);
            }

            var chart = new CanvasJS.Chart(self.canvas,
                    {
                        title: {
                            text: "Crop Areas"
                        },
                        axisY: {
                            title: "Average ha"
                        },
                        exportEnabled: true,
                        animationEnabled: true,
                        toolTip: {
                            shared: true,
                            content: "{label} {name}: {y} - <strong>#percent%</strong>",
                        },
                        data: reportData
                    });
            chart.render();
            self.makePDFButton("Crop Areas");
        });
    };

    self.getIncomeSourceReport = function(filters) {
        var url = "/api/reports/incomesources/" + self.selectedGrouping();
        if (filters != null && filters.length > 0) {
            url += '?' + filters;
        }
        if (url == self.lastReportURL()) {
            return;
        } else {
            self.lastReportURL(url);
        }
        getData(url, function(data) {
            var reportData = [];
            var pivotedData = {};
            if (data.report.length < 1) {
                self.noData(true);
            } else {
                self.noData(false);
            }
            var incomeTypes = [];
            for (var i = 0; i < data.report.length; i++) {
                var thisRow = data.report[i];
                var thisDesc = thisRow.description.length < 16 ? thisRow.description : thisRow.description.substring(0, 15) + '...';
                incomeTypes.push(thisDesc);
                if (pivotedData[thisRow.income_category] == null) {
                    pivotedData[thisRow.income_category] = {
                        dataPoints: []
                    };
                }
                pivotedData[thisRow.income_category].dataPoints.push({y: ((thisRow.percentage == null) ? 0 : parseFloat(thisRow.percentage)), label: thisDesc});
            }

            // Make sure there's at least one datapoint for each income type for each segment
            for (var h = 0; h < incomeTypes.length; h++) {
                for (var segmentName in pivotedData) {
                    if (pivotedData.hasOwnProperty(segmentName)) {
                        var foundType = false;
                        for (var k = 0; k < pivotedData[segmentName].dataPoints.length; k++) {
                            if (pivotedData[segmentName].dataPoints[k].label == incomeTypes[h]) {
                                foundType = true;
                            }
                        }
                        if (!foundType) {
                            pivotedData[segmentName].dataPoints.push({y: 0, label: incomeTypes[h]});
                        }
                    }
                    pivotedData[segmentName].dataPoints.sort(function(a, b) {
                        return (a.label > b.label ? -1 : ((b.label > a.label) ? 1 : 0));
                    });
                }
            }

            for (var segmentName in pivotedData) {
                if (pivotedData.hasOwnProperty(segmentName)) {
                    pivotedData[segmentName].type = "stackedBar100";
                    pivotedData[segmentName].showInLegend = true;
                    pivotedData[segmentName].name = segmentName;
                }
                reportData.push(pivotedData[segmentName]);
            }

            var chart = new CanvasJS.Chart(self.canvas,
                    {
                        title: {
                            text: "Income Sources"
                        },
                        axisY: {
                            title: "percent",
                            interval: 10,
                            maximum: 103, // hack for cut-off "100"
                            lineThickness: 0 // ""
                        },
                        exportEnabled: true,
                        animationEnabled: true,
                        toolTip: {
                            shared: true,
                            content: "{label} {name}: {y} - <strong>#percent%</strong>",
                        },
                        data: reportData
                    });
            chart.render();
            self.makePDFButton("Income Sources");
        });
    };

    self.getDMReport = function(filters) {
        var url = "/api/reports/drymatter/" + self.selectedGrouping();
        if (filters != null && filters.length > 0) {
            url += '?' + filters;
        }
        if (url == self.lastReportURL()) {
            return;
        } else {
            self.lastReportURL(url);
        }
        getData(url, function(data) {
            var reportData = [];
            if (data.report.length < 1) {
                self.noData(true);
            } else {
                self.noData(false);
            }
            var pivotedData = {
                "Crop Residue": {dataPoints: []},
                "Cultivated Fodder": {dataPoints: []},
                "Purchased Feed": {dataPoints: []},
                "Collected Fodder": {dataPoints: []},
                "Grazing": {dataPoints: []},
            };
            for (var i = 0; i < data.report.length; i++) {
                var thisRow = data.report[i];
                var thisDesc = thisRow.description.length < 16 ? thisRow.description : thisRow.description.substring(0, 15) + '...';
                pivotedData["Crop Residue"].dataPoints.push({y: ((thisRow.dm_crop_residue == null) ? 0 : parseFloat(thisRow.dm_crop_residue)), label: thisDesc});
                pivotedData["Cultivated Fodder"].dataPoints.push({y: ((thisRow.dm_cultivated_fodder == null) ? 0 : parseFloat(thisRow.dm_cultivated_fodder)), label: thisDesc});
                pivotedData["Purchased Feed"].dataPoints.push({y: ((thisRow.dm_purchased_feed == null) ? 0 : parseFloat(thisRow.dm_purchased_feed)), label: thisDesc});
                pivotedData["Collected Fodder"].dataPoints.push({y: ((thisRow.dm_collected_fodder == null) ? 0 : parseFloat(thisRow.dm_collected_fodder)), label: thisDesc});
                pivotedData["Grazing"].dataPoints.push({y: ((thisRow.dm_grazing == null) ? 0 : parseFloat(thisRow.dm_grazing)), label: thisDesc});
            }

            for (var segmentName in pivotedData) {
                if (pivotedData.hasOwnProperty(segmentName)) {
                    pivotedData[segmentName].type = "stackedBar100";
                    pivotedData[segmentName].showInLegend = true;
                    pivotedData[segmentName].name = segmentName;
                }
                pivotedData[segmentName].dataPoints.sort(function(a, b) {
                    return (a.label > b.label ? -1 : ((b.label > a.label) ? 1 : 0));
                });
                reportData.push(pivotedData[segmentName]);
            }

            var chart = new CanvasJS.Chart(self.canvas,
                    {
                        title: {
                            text: "Dry Matter Composition of Diet"
                        },
                        axisY: {
                            title: "percent",
                            interval: 10,
                            maximum: 103, // hack for cut-off "100"
                            lineThickness: 0 // ""
                        },
                        exportEnabled: true,
                        animationEnabled: true,
                        toolTip: {
                            shared: true,
                            content: "{label} {name}: {y} - <strong>#percent%</strong>",
                        },
                        data: reportData
                    });
            chart.render();
            self.makePDFButton("Dry Matter");
        });
    };

    self.getRainfallReport = function(filters) {
        var url = "/api/reports/rainfall/all";
        if (filters != null && filters.length > 0) {
            url += '?' + filters;
        }
        if (url == self.lastReportURL()) {
            return;
        } else {
            self.lastReportURL(url);
        }
        getData(url, function(data) {
            var tempData = {};
            var dataArr = [];
            if (data.report.length < 1) {
                self.noData(true);
            } else {
                self.noData(false);
            }
            data.report.forEach(function(el) {
                //if (el.id_focus_group != "1") {return;}
                var type = el.resource_type;
                var monthOrder = parseInt(el.order_of_month);
                tempData[type] = tempData[type] || {
                    type: (type !== "Rainfall") ? "stackedColumn" : "line",
                    lineThickness: "3",
                    axisYType: (type !== "Rainfall") ? "primary" : "secondary",
                    showInLegend: true,
                    name: el.resource_type,
                    dataPoints: []
                };

                if (tempData[type].dataPoints[monthOrder - 1]) {
                    var newY = 0;
                    var oldRaw = tempData[type].dataPoints[monthOrder - 1].y * tempData[type].dataPoints[monthOrder - 1].count;
                    tempData[type].dataPoints[monthOrder - 1].count++;
                    var newY = (oldRaw + parseFloat(el.numerical_value)) / tempData[type].dataPoints[monthOrder - 1].count;
                    tempData[type].dataPoints[monthOrder - 1].y = newY;
                } else {
                    tempData[type].dataPoints[monthOrder - 1] = {
                        y: parseFloat(el.numerical_value),
                        x: new Date(2015, monthOrder - 1),
                        count: 1
                    };
                }


            });

            for (var x in tempData) {
                if (x !== 'Rainfall') {
                    dataArr.push(tempData[x]);
                }
            }

            dataArr.push(tempData['Rainfall']);//assuming that puts rainfall 'on top'


            var chart = new CanvasJS.Chart(self.canvas,
                    {
                        colorType: 'alternating',
                        title: {
                            text: "Rainfall and Feed Availability",
                        },
                        exportEnabled: true,
                        animationEnabled: true,
                        toolTip: {
                            shared: true,
                            content: function(e) {
                                var str = '';
                                var total = 0;
                                var str3;
                                var str2;
                                for (var i = 0; i < e.entries.length; i++) {

                                    if (e.entries[i].dataSeries.name !== 'Rainfall') {
                                        var str1 = "<span style= 'color:" + e.entries[i].dataSeries.color + "'> " + e.entries[i].dataSeries.name + "</span>: <strong>" + unfloatRound(e.entries[i].dataPoint.y, 2) + "</strong><br/>";
                                        total = e.entries[i].dataPoint.y + total;
                                        str = str.concat(str1);
                                    }

                                }
                                str2 = "<span style = 'color:DodgerBlue; '><strong>" + (e.entries[0].dataPoint.x).getFullYear() + "</strong></span><br/>";

                                total = unfloatRound(total, 2);
                                str3 = "<span style = 'color:Tomato '>Total:</span><strong> " + total + "</strong><br/>";

                                return (str2.concat(str)).concat(str3);
                            }
                        },
                        axisY: {
                            title: "Availability of feed (0-10)",
                            titleFontSize: 16
                        },
                        axisY2: {
                            title: "Rainfall (0-5)",
                            maximum: 5,
                            titleFontSize: 16,
                            gridThickness: 0
                        },
                        axisX: {
                            interval: 1,
                            intervalType: "month",
                            valueFormatString: "MMMM",
                            labelFontStyle: "italic",
                            labelMaxWidth: 100,
                            labelWrap: true,
                            labelAngle: -45
                        },
                        data: dataArr
                    });
            chart.render();
            self.makePDFButton("Rainfall");
        });
    };
}

function postData(url, data, callback) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json"
    }).done(function(data) {
        callback(data);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        requestFailed(jqXHR, textStatus, errorThrown, topReportModel.lastError);
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
        requestFailed(jqXHR, textStatus, errorThrown, topReportModel.lastError);
    });
}

$(document).ready(function() {
    topReportModel = new koReportModel('report-top-canvas');
    topReportModel.initialize();
    ko.applyBindings(topReportModel, document.getElementById('report-top-controls'));
});

function addReport() {
    bottomReportModel = new koReportModel('report-bottom-canvas');
    bottomReportModel.initialize();
    ko.applyBindings(bottomReportModel, document.getElementById('report-bottom-controls'));
    $('#add-report-btn').hide();
    $('#report-bottom-controls').show();
}

function unfloatRound(value, decimalPoint) {
    var dpAdjust = Math.pow(10, decimalPoint);
    return Number((Math.round(value * dpAdjust) + 'e2') / dpAdjust + 'e-2');
}