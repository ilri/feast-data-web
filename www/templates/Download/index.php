<?php $this->assign('title', 'Download Data | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => "downloads"]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content">
    <div class="container <?= (isset($currentEntity['switch_shiny_download']) && isset($currentEntity['shiny_download']) && $currentEntity['switch_shiny_download'] == 1) ? 'shiny' : '' ?>">
        <div class="upload-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <?php if ($authedUser) { ?>
                    <li role="presentation" class="active"><a href="#download-pane" aria-controls="data-sets" role="tab" data-toggle="tab">Download Data Sets</a></li>
                <?php } ?>
                <li role="presentation" class="<?= $authedUser ? '' : 'active' ?>"><a href="#directory-pane" aria-controls="data-sets" role="tab" data-toggle="tab">Directory</a></li>
                <li role="presentation"><a href="#feast-tool" aria-controls="feast-tool" role="tab" data-toggle="tab">Documentation</a></li>
            </ul>
        </div>
        <div class='row tab-content inner-tab-content' style='display:none;' data-bind='visible:true'>
            <?php if ($authedUser) { ?>
            <?php if (isset($currentEntity['switch_shiny_download']) && isset($currentEntity['shiny_download']) && $currentEntity['switch_shiny_download'] == 1) { ?>
                <div role ='tabpanel' class='tab-pane active col-md-8' id='download-pane'>
                    <div class='page-prompt row'>
                        <h2 class='col-md-12'>DOWNLOAD > DOWNLOAD DATA SETS</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-12 iframe-container">
                            <iframe  frameborder="0" scrolling="no" src="<?php echo $currentEntity['shiny_download']; ?>" ></iframe>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div role ='tabpanel' class='tab-pane active col-md-8' id='download-pane'>
                    <div class='page-prompt row'>
                        <h2 class='col-md-12'>DOWNLOAD > DOWNLOAD DATA SETS</h2>
                        <p class='col-md-12 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#download-help">Watch a tutorial video</a></p>
                        <p class='col-md-7'>Download aggregated data sets in various formats, based on data from all users or only your own data.</p>
                    </div>
                    <div class="download-filter row">
                        <div class="col-md-12">
                            <h4>Data Exports</h4>                        
                            <label style="vertical-align:top">Filter By:</label>
                            <select name="filter_type" data-bind="value: filterType, select2: {minimumResultsForSearch: -1}">
                                <option value="none">&lt;No Filter&gt;</option>
                                <option value="region">World Region</option>
                                <option value="project">Project</option>
                            </select>
                            <div id="downloadFilters" data-bind="if: filterType() != 'none'">                            
                                <div class="row">                                
                                    <!-- ko if: filterType() == "region" -->
                                    <div class="filter-block pull-left col-md-4">
                                        <label style="vertical-align:top">World Region:</label><select name="world_region" multiple="true" size="5" data-bind="options: worldRegions, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedWorldRegion"></select>
                                    </div>
                                    <div class="filter-block pull-left col-md-4">
                                        <label style="vertical-align:top">Country:</label><select name="country" multiple="true" size="5" data-bind="options: countries, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedCountry"></select>
                                    </div>
                                    <div class="filter-block pull-left col-md-4">
                                        <label style="vertical-align:top">Site:</label><select name="site" multiple="true" size="5" data-bind="options: sites, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedSite"></select>
                                    </div>
                                    <!-- /ko -->
                                    <!-- ko if: filterType() == "project" -->
                                    <div class="filter-block pull-left col-md-6">
                                        <label style="vertical-align:top">Project:</label><select name="project" multiple="true" size="5" data-bind="options: projects, select2: {minimumResultsForSearch: -1}, optionsText: 'title', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedProject"></select>
                                    </div>
                                    <div class="filter-block pull-left col-md-6">
                                        <label style="vertical-align:top">Site:</label><select name="site_project" multiple="true" size="5" data-bind="options: sites, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedSite"></select>
                                    </div>
                                    <!-- /ko -->
                                    <p class="help-block" style="clear: both;margin-bottom: 5px;margin-left: 15px;">Select filters above</p>
                                </div>
                            </div>
                            <label style="vertical-align:top;margin-top: 10px;">Data Type:</label>
                            <select name="data_type" data-bind="value: dataType, select2: {minimumResultsForSearch: -1}">
                                <option value="rdata">RDATA</option>
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel</option>
                            </select>
                            <label style="margin-top: 15px;"><input name="mine_only" type="checkbox" data-bind="checked: mineOnly, click: $root.mineOnlyChange" />&nbsp;&nbsp;My Data Only</label>
                            <a target='_blank' class="btn btn-success" style="margin-top: 10px;" data-bind="attr: {href: getData}">Download</a>
                            <!--<ul style="clear: both">
                                <li><a target='_blank' data-bind="attr: {href: getAllCSV}"><span data-bind="if: filterType() != 'none'">Filtered</span><span data-bind="if: filterType() == 'none'">All</span> Public Data (CSV)</a></li>
                                <li><a target='_blank' data-bind="attr: {href: getMyCSV}">My Data (CSV)</a></li>
                            </ul>-->
                        </div>
                    </div>
                    <?php if ($isAdmin) { ?>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Full Database Download</h4>
                            <ul>
                                <li><a target='_blank' data-bind="attr: {href: getAllSQL}">All Public Data (SQLite 3 .db)</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            <?php } } ?>
            <div role ='tabpanel' class='tab-pane <?= $authedUser ? '' : 'active' ?> col-md-8' id='directory-pane'>
                <div class='page-prompt row'>
                    <h2 class='col-md-12'>DOWNLOAD > DIRECTORY</h2>
                    <p class='col-md-7'>Below you can browse the available data sets by country and project.</p>
                    <?php if (!$authedUser) { ?>
                        <p class='col-md-7'><a href='/'>Sign in or register</a> for an account to download FEAST data sets.</p>
                    <?php } ?>
                </div>
                <div class='filter-row col-md-12'>
                    <label style="vertical-align:top">Filter By:</label><select data-bind="value: filterType, select2: {minimumResultsForSearch: -1}">
                        <option value="none">&lt;No Filter&gt;</option>
                        <option value="region">World Region</option>
                        <option value="project">Project</option>
                    </select>
                    <div id="downloadFilters" data-bind="if: filterType() != 'none'">                            
                        <div class="row">                                
                            <!-- ko if: filterType() == "region" -->
                            <div class="filter-block pull-left col-md-4">
                                <label style="vertical-align:top">World Region:</label><select multiple="true" size="5" data-bind="options: worldRegions, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedWorldRegion"></select>
                            </div>
                            <div class="filter-block pull-left col-md-4">
                                <label style="vertical-align:top">Country:</label><select multiple="true" size="5" data-bind="options: countries, select2: {minimumResultsForSearch: -1}, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedCountry"></select>
                            </div>
                            <!-- /ko -->                    
                            <!-- ko if: filterType() == "project" -->                                
                            <div class="filter-block pull-left col-md-6">
                                <label style="vertical-align:top">Project:</label><select multiple="true" size="5" data-bind="options: projects, select2: {minimumResultsForSearch: -1}, optionsText: 'title', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedProject"></select>
                            </div>
                            <!-- /ko -->
                            <p class="help-block" style="clear: both">Hold CTRL while clicking to select more than one option.</p>                            
                        </div>
                    </div>
                </div>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Site</th>
                            <th>Project</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: sites'>
                        <tr>
                            <td data-bind='text: name'></td>
                            <td data-bind='text: project_view.title'></td>
                            <td data-bind='text: system_country.name'></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div role ='tabpanel' class='tab-pane col-md-8' id='feast-tool'>
                <div class='page-prompt row'>
                    <h2 class='col-md-12'>DOWNLOAD > DOCUMENTATION</h2>
                </div>
                <table class='table'>
                    <thead>
                        <tr>
                            <td>File Name</td>
                            <td>Description</td>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: resources'>
                        <tr>
                            <td><a data-bind='text: filename, attr: { href : "/api/resource/file/"+filename}'></a></td>
                            <td data-bind='text:description'></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div><!-- end .container -->
</div><!-- end .actual-content -->
<?php $this->Html->script('dropzone.js', array('block' => 'script')) ?>
<?php $this->Html->script('download_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('download.js?v=1', array('block' => 'script')) ?>
