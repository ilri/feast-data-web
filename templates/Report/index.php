<?php $this->assign('title', 'View Reports | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => "reports"]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content">
    <div class="container-fluid report-page-container <?= (isset($currentEntity['switch_shiny_visualize']) && isset($currentEntity['shiny_visualize']) && $currentEntity['switch_shiny_visualize'] == 1) ? 'shiny' : '' ?>">
        <div class='row tab-content inner-tab-content'>
            <?php if (isset($currentEntity['switch_shiny_visualize']) && isset($currentEntity['shiny_visualize']) && $currentEntity['switch_shiny_visualize'] == 1) { ?>
            <div class='page-prompt col-md-12'>
                <h2 class='col-md-12'>Visualisations</h2>
            </div>
            <div class="body-part">
                <div class='col-md-12 iframe-container'>
                    <iframe  frameborder="0" scrolling="no" src="<?php echo $currentEntity['shiny_visualize']; ?>" ></iframe>
                </div>
            </div>
            <?php } else { ?>
            <div class='page-prompt col-md-12'>
                <h2 class='col-md-12'>REPORTS</h2>
                <p class='col-md-12 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#report-help">Watch a tutorial video</a></p>
                <p class='col-md-7'>View data visualizations and compare key metrics for different sets of data.</p>
            </div>
            <div class="body-part">
                <div class='col-md-12'>
                    <div id='report-top-controls' class='report-controls col-md-6'>
                        <h3>Report</h3>
                        <div class="report-type">
                            Report: <select data-bind="options: chartTypes, select2: {minimumResultsForSearch: -1}, value: selectedChartType"></select>
                            <button id='add-report-btn' class='button btn-default' data-bind='click: addReport'>Add Second Report</button>
                        </div>
                        <div class="report-groupings" data-bind="if: selectedChartType() != 'Rainfall'">
                            Group By: <select data-bind="options: availableGroupings, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'value', value: selectedGrouping"></select>
                        </div>
                        <div class="report-filters">
                            <?php if ($authedUser) { ?>
                            <div class="checkbox">
                                <label>
                                    <input type='checkbox' data-bind="checked: showMyData"> Only My Data
                                </label>
                            </div>
                            <?php } ?>
                            Gender: <select data-bind="options: genders, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'value', optionsCaption: 'All', value: selectedGender"></select>
                            Project: <select data-bind="options: projects, select2: {minimumResultsForSearch: -1}, optionsText: 'title', optionsValue: 'id', optionsCaption: 'All', value: selectedProject"></select>
                            <!-- ko if: selectedProject() != null -->
                            Site: <select data-bind="options: sites, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', value: selectedSite"></select>
                            <!-- /ko -->
                            World Region: <select data-bind="options: worldRegions, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', value: selectedWorldRegion"></select>
                            <!-- ko if: selectedWorldRegion() != null -->
                            Country: <select data-bind="options: countries, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', value: selectedCountry"></select>
                            <!-- /ko -->
                            <div class='no-data-warning' data-bind='visible: noData'>
                                <p>No data is available for your selected filters.</p>
                            </div>
                            <div class='no-data-warning' data-bind='visible: !noData()'>
                                <a target='_blank' class='report-csv-button' data-bind='attr: {href: lastReportURL() + (lastReportURL().indexOf("?") < 1 ? "?csv=1": "&csv=1")}'>Get Source Data [CSV]</a>
                            </div>
                        </div>
                    </div>

                    <div id='report-bottom-controls' class='report-controls col-md-6' style='display:none'>
                        <h3>Bottom Report</h3>
                        <div class="report-type">
                            Report: <select data-bind="options: chartTypes, select2: {minimumResultsForSearch: -1}, value: selectedChartType"></select>
                        </div>
                        <div class="report-groupings" data-bind="if: selectedChartType() != 'Rainfall'">
                            Group By: <select data-bind="options: availableGroupings, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'value', value: selectedGrouping"></select>
                        </div>
                        <div class="report-filters">
                            <?php if ($authedUser) { ?>
                            <div class="checkbox">
                                <label>
                                    <input type='checkbox' data-bind="checked: showMyData"> Only My Data
                                </label>
                            </div>
                            <?php } ?>
                            Gender: <select data-bind="options: genders, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'value', optionsCaption: 'All', value: selectedGender"></select>
                            Project: <select data-bind="options: projects, select2: {minimumResultsForSearch: -1}, optionsText: 'title', optionsValue: 'id', optionsCaption: 'All', value: selectedProject"></select>
                            <!-- ko if: selectedProject() != null -->
                            Site: <select data-bind="options: sites, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', value: selectedSite"></select>
                            <!-- /ko -->
                            World Region: <select data-bind="options: worldRegions, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', value: selectedWorldRegion"></select>
                            <!-- ko if: selectedWorldRegion() != null -->
                            Country: <select data-bind="options: countries, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', value: selectedCountry"></select>
                            <!-- /ko -->
                            <div class='no-data-warning' data-bind='visible: noData'>
                                <p>No data is available for your selected filters.</p>
                            </div>
                            <div class='no-data-warning' data-bind='visible: !noData()'>
                                <a target='_blank' class='report-csv-button' data-bind='attr: {href: lastReportURL() + (lastReportURL().indexOf("?") < 1 ? "?csv=1": "&csv=1")}'>Get Source Data [CSV]</a>
                            </div>
                        </div>
                    </div>
                    <div id='report-top-canvas' class='col-md-12' style="height:500px;">

                    </div>

                    <div id='report-bottom-canvas' class='col-md-12' style="height:500px;">

                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php $this->Html->script('canvasjs.min.js', array('block' => 'script')) ?>
<?php $this->Html->script('jspdf.min.js', array('block' => 'script')) ?>
<?php $this->Html->script('report_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('report.js', array('block' => 'script')) ?>