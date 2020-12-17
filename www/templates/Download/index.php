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
    <div class="container">
        <div class="upload-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <?php if ($authedUser) { ?>
                    <li role="presentation" class="active"><a href="#download-pane" aria-controls="data-sets" role="tab" data-toggle="tab">Download Data Sets</a></li>
                <?php } ?>
                <li role="presentation" class="<?= $authedUser ? '' : 'active' ?>"><a href="#directory-pane" aria-controls="data-sets" role="tab" data-toggle="tab">Directory</a></li>
                <li role="presentation"><a href="#feast-tool" aria-controls="feast-tool" role="tab" data-toggle="tab">Documentation</a></li>
            </ul>
        </div>
        <div class='row tab-content inner-tab-content' style='display:none' data-bind='visible:true'>
            <?php if ($authedUser) { ?>
                <div role ='tabpanel' class='tab-pane active col-md-8' id='download-pane'>
                    <div class='page-prompt row'>
                        <h2 class='col-md-12'>DOWNLOAD > DOWNLOAD DATA SETS</h2>
                        <p class='col-md-12 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#download-help">Watch a tutorial video</a></p>
                        <p class='col-md-7'>Download aggregated data sets in various formats, based on data from all users or only your own data.</p>
                    </div>
                    <div class="download-filter row">
                        <div class="col-md-12">
                            <h4>CSV Data Exports</h4>                        
                            <label style="vertical-align:top">Filter By:</label><select data-bind="value: filterType">
                                <option value="none">&lt;No Filter&gt;</option>
                                <option value="region">World Region</option>
                                <option value="project">Project</option>
                            </select>
                            <div id="downloadFilters" data-bind="if: filterType() != 'none'">                            
                                <div class="row">                                
                                    <!-- ko if: filterType() == "region" -->
                                    <div class="filter-block pull-left col-md-4">
                                        <label style="vertical-align:top">World Region:</label><select multiple="true" size="5" data-bind="options: worldRegions, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedWorldRegion"></select>
                                    </div>
                                    <div class="filter-block pull-left col-md-4">
                                        <label style="vertical-align:top">Country:</label><select multiple="true" size="5" data-bind="options: countries, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedCountry"></select>
                                    </div>
                                    <div class="filter-block pull-left col-md-4">
                                        <label style="vertical-align:top">Site:</label><select multiple="true" size="5" data-bind="options: sites, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedSite"></select>
                                    </div>
                                    <!-- /ko -->                    
                                    <!-- ko if: filterType() == "project" -->                                
                                    <div class="filter-block pull-left col-md-6">
                                        <label style="vertical-align:top">Project:</label><select multiple="true" size="5" data-bind="options: projects, optionsText: 'title', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedProject"></select>
                                    </div>                                
                                    <div class="filter-block pull-left col-md-6">
                                        <label style="vertical-align:top">Site:</label><select multiple="true" size="5" data-bind="options: sites, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedSite"></select>
                                    </div>
                                    <!-- /ko -->
                                    <p class="help-block" style="clear: both">Hold CTRL while clicking to select more than one option.</p>                            
                                </div>
                            </div>
                            <ul style="clear: both">
                                <li><a target='_blank' data-bind="attr: {href: getAllCSV}"><span data-bind="if: filterType() != 'none'">Filtered</span><span data-bind="if: filterType() == 'none'">All</span> Public Data (CSV)</a></li>
                                <li><a target='_blank' data-bind="attr: {href: getMyCSV}">My Data (CSV)</a></li>
                            </ul>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Full Database Download</h4>
                            <ul>
                                <li><a target='_blank' data-bind="attr: {href: getAllSQL}">All Public Data (SQLite 3 .db)</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div role ='tabpanel' class='tab-pane <?= $authedUser ? '' : 'active' ?> col-md-8' id='directory-pane'>
                <div class='page-prompt row'>
                    <h2 class='col-md-12'>DOWNLOAD > DIRECTORY</h2>
                    <p class='col-md-7'>Below you can browse the available data sets by country and project.</p>
                    <?php if (!$authedUser) { ?>
                        <p class='col-md-7'><a href='/'>Sign in or register</a> for an account to download FEAST data sets.</p>
                    <?php } ?>
                </div>
                <div class='filter-row col-md-12'>
                    <label style="vertical-align:top">Filter By:</label><select data-bind="value: filterType">
                        <option value="none">&lt;No Filter&gt;</option>
                        <option value="region">World Region</option>
                        <option value="project">Project</option>
                    </select>
                    <div id="downloadFilters" data-bind="if: filterType() != 'none'">                            
                        <div class="row">                                
                            <!-- ko if: filterType() == "region" -->
                            <div class="filter-block pull-left col-md-4">
                                <label style="vertical-align:top">World Region:</label><select multiple="true" size="5" data-bind="options: worldRegions, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedWorldRegion"></select>
                            </div>
                            <div class="filter-block pull-left col-md-4">
                                <label style="vertical-align:top">Country:</label><select multiple="true" size="5" data-bind="options: countries, optionsText: 'name', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedCountry"></select>
                            </div>
                            <!-- /ko -->                    
                            <!-- ko if: filterType() == "project" -->                                
                            <div class="filter-block pull-left col-md-6">
                                <label style="vertical-align:top">Project:</label><select multiple="true" size="5" data-bind="options: projects, optionsText: 'title', optionsValue: 'id', optionsCaption: 'All', selectedOptions: selectedProject"></select>
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
                            <td data-bind='text: project.title'></td>
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
<?php $this->Html->script('download.js', array('block' => 'script')) ?>
