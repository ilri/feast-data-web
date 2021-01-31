<?php $this->assign('title', 'Upload Data | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
            <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => "uploads"]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content">
    <div class="container-fluid">
        <div class="upload-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#upload-data" aria-controls="upload" role="tab" data-toggle="tab">Upload</a></li>
                <li role="presentation"><a href="#manage-data" aria-controls="manage-data" role="tab" data-toggle="tab">Manage My Data</a></li>
                <li role="presentation"><a href="#data-privacy" aria-controls="data-privacy" role="tab" data-toggle="tab">Data Privacy</a></li>
            </ul>
        </div>
        <div class='row tab-content inner-tab-content' style='display:none' data-bind='visible:true'>
            <div role ='tabpanel' class='tab-pane active col-md-8' id='upload-data'>
                <div class='page-prompt row'>
                    <h2 class='col-md-12'>MY DATA > UPLOAD</h2>
                    <p class='col-md-12 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#upload-help">Watch a tutorial video</a></p>
                    <p class='col-md-7'>Upload data from .zlib files exported by the FEAST Data Application.</p>
                </div>
                <!-- ko if: $root.canUpload() -->
                <form class="dropzone" id='file-upload-dropzone' style='clear:both;' data-bind="visible: $root.activateDropzone()">
                    <div class="dropzone-previews"></div>
                    <div class="fallback" id='file-upload-fallback'>
                        <input name="file" type="file" />
                    </div>
                </form>
                <!-- ko if: $root.isSending() == false -->
                <button type="button" class="btn btn-default file-upload-button" data-bind="visible: $root.showUploadButton, click: $root.doFileUpload">Upload File</button>
                <!-- /ko -->
                <!-- ko if: $root.isSending() == true -->
                <i class="fa fa-circle-o-notch fa-spin"></i>
                <!-- /ko -->
                <div class="checkbox keep-private-checkbox clearfix">
                    <label>
                        <input type="checkbox" name="keep_private" data-bind='checked:$root.keepDataPrivate'> Keep my data private for 1 year.
                    </label>
                    <p class="help-block">Why only 1 year? Read more about ILRI/CGIAR's <a target='_blank' href='http://www.cgiar.org/resources/open/'>Open Data Policy</a></p>
                    <!-- ko if: $root.uploadError -->
                    <p class='text-warning'>There was a problem uploading or processing your data. Try again, or contact an administrator for assistance.</p>
                    <!-- /ko -->
                    <!-- ko if: $root.uploadError && $root.uploadError() != true && $root.uploadError() != false -->
                    <div class="message error" data-bind="text: $root.uploadError"></div>
                    <!-- /ko -->
                </div>                
                <!-- /ko -->
                <!-- ko if: !$root.canUpload() -->
                <p class="file-upload-pending">Your upload has been processed.</p>
                <!-- /ko -->

            </div>
            <div role ='tabpanel' class='tab-pane col-md-12' id='manage-data'>
                <div class='page-prompt row'>
                    <h2 class='col-md-7'>MY DATA > MANAGE MY DATA</h2>
                    <p class='col-md-7 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#exclude-help">Watch a tutorial video: Excluding Records</a></p>
                    <p class='col-md-7 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#consolidate-help">Watch a tutorial video: Consolidating Records</a></p>
                    <p class='col-md-7'>View your uploaded data by table, selectively exclude records or consolidate entries for certain lookup tables (e.g., have records for "rabbet" and "rabbits" redirect to a single record for "rabbit").</p>                    
                    <p class='col-md-7'>Common table fields include: <strong>U</strong>: User ID, <strong>P</strong>: Project ID, <strong>S</strong>: Site ID, <strong>F</strong>: Focus Group ID, <strong>R</strong>: Respondent ID</p>
                    <p class='col-md-7'>Tables marked with <strong>*</strong> may be empty if you have not entered any custom data for these tables.</p>
                    </ul>
                </div>
                <div class='col-md-12'>
                    <!--<select data-bind="options: tableFilter, optionsText: function(item) {return item.tableName + (isCustomizableTable(item) ? '*' : '') ;}, optionsCaption:'Choose a Table:', value: selectedTable"></select>-->
                    <select data-bind="foreach: tableList, value: selectedTable">
                        <option data-bind="visible: $index() < 1, text: 'Choose a Table:', value: ''"></option>
                        <optgroup data-bind="attr: {label: label}, foreach: tables">
                            <option data-bind="text: $data.tableName, value: $data"></option>
                        </optgroup>
                    </select>
                </div>
                <!-- ko if: loadingData -->
                <div class='col-md-12 load-spinner'>
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
                <!-- /ko -->
                <!-- ko if: currentReport -->
                <div class='col-md-6'>
                    <p class='result-count'><span class='result-count-number' data-bind='text:totalResults'></span> Results</p>
                    <button data-bind='click: toggleSearch'>Search/Filter</button>
                    <!-- ko if: reportMode() === 'search' -->
                    <div class='search-div'>
                        <div data-bind='foreach: filters'>
                            <p><span data-bind='text: column'></span>: <span data-bind='text: value'></span> <button type='button' data-bind='click: $root.removeFilter'>[x]</button></p>
                        </div>
                        <div class="col-md-12">
                            <select data-bind="options: currentReport().headers, select2: {minimumResultsForSearch: -1}, optionsCaption:'Choose a Column:', value: newFilterColumn"></select>
                            <input type='input' data-bind='value: newFilterValue' />
                            <button type="button" data-bind='click: addFilter'>Add</button>
                        </div>
                        <div class="col-md-12">
                            <button type="button" data-bind='click: cancelFilter'>Cancel</button>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
                <div class='col-md-6'>
                    <p><em>Bulk Actions</em><p>
                        <select data-bind='options: bulkActions, select2: {minimumResultsForSearch: -1}, optionsCaption: "Choose Action", value: selectedAction'></select>
                        <button data-bind='click: applyAction, enable: (selectedAction() != null)'>Submit</button>
                </div>
                <table class='table'>
                    <thead>
                        <tr data-bind='foreach: currentReport().headers'>
                            <th data-bind='text: $data'></th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: {data: currentReport().data, as: "reportRow"}'>
                        <tr data-bind='foreach: {data: reportRow, as: "reportField"}'>
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'usermodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#user-detail-modal" data-bind='text: reportField.id, click: $root.showUserDetails'></a></td>
                            <!-- /ko -->
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'respondentmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#respondent-detail-modal" data-bind='text: reportField.id, click: $root.showRespondentDetails.bind(reportField, reportRow)'></a></td>
                            <!-- /ko -->                     
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'replacedmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#replaced-by-modal" data-bind='text: reportField.value(), click: $root.showReplacedModal.bind(reportField, reportRow)'></a></td>
                            <!-- /ko -->                                                    
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'checkbox' -->
                            <td><input type='checkbox' data-bind='checked: $data.value, attr: { disabled: $data.key != "selectRow"}' /></td>
                            <!-- /ko -->
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'text' -->
                            <td><input type='text' data-bind='event: {change: $root.applyChange.bind()}, value: $data.value, id: $data.key+"_"+$data.rowID' /></td>
                            <!-- /ko -->
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'tooltip' -->
                            <td><a href="javascript:void;" data-bind='tooltip: {title: $data.title}, text: $data.text' /></a></td>
                            <!-- /ko -->
                            <!-- ko if: $data == null || ($data != null && $data.action == null) -->
                            <td data-bind='text: reportField'></td>
                            <!-- /ko -->
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'mapmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#map-modal" data-bind='click: $root.showMapModal.bind(reportRow)'><i class="fa fa-map-marker fa-2x"></i></a></td>
                            <!-- /ko -->
                        </tr>
                    </tbody>
                </table>
                <!-- ko if: reportMode() === 'browse' && resultsLeft() > 0 -->
                <button data-bind='click: showMore'>Show More Results</button>
                <!-- /ko -->
                <!-- /ko -->
            </div>
            <div role ='tabpanel' class='tab-pane col-md-8' id='data-privacy'>
                <div class='page-prompt row'>
                    <h2 class='col-md-12'>MY DATA > DATA PRIVACY</h2>
                    <p class='col-md-12 video-link'><i class="fa fa-play-circle-o"></i> <a target="_blank" href="/help#upload-help">Watch a tutorial video</a></p>
                    <p class='col-md-7'>If you previously opted to keep the data from any of your projects private, you may use this form to share it with the community.</p>
                </div>
                <div class='publish-data-controls col-md-6'>
                    <button class='make-data-public-btn' data-bind='click: makePublic'>Make Public</button>
                </div>
                <div class='col-md-6'>                    
                </div>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Project Title</th>
                            <th>Country</th>
                            <th>Sites</th>                            
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: privateProjects'>
                        <tr>
                            <td data-bind='text:title'></td>
                            <td data-bind='text:system_country == null ? "N/A" : system_country.name'></td>
                            <td data-bind='text:site.length'></td>
                            <td><input type='checkbox' data-bind='checked: isSelected'/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div><!-- end .container -->
</div><!-- end .actual-content -->

<div class='modal fade' id="consolidation-modal" tabindex='-1' role='dialog' aria-labeled-by='register_model_label' aria-hidden='true'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Consolidate records</h2>
            </div>
            <!-- ko if: currentReport && consolidateReport -->
            <div class='consolidate-old-list'>
                <table class='table'>
                    <thead>
                        <tr data-bind='foreach: currentReport().headers'>
                            <th data-bind='text: $data'></th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: {data: selectedRows(), as: "consolidateRow"}'>
                        <tr data-bind='foreach: {data: consolidateRow, as: "consolidateField"}'>
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'usermodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#user-detail-modal" data-bind='text: consolidateField.id, click: $root.showUserDetails'></a></td>
                            <!-- /ko -->
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'respondentmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#respondent-detail-modal" data-bind='text: consolidateField.id, click: $root.showRespondentDetails.bind(consolidateField, consolidateRow)'></a></td>
                            <!-- /ko -->                            
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'replacedmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#replaced-by-modal" data-bind='text: consolidateField.value(), click: $root.showReplacedModal.bind(consolidateField, consolidateRow)'></a></td>
                            <!-- /ko -->                                                    
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'checkbox' -->
                            <td><input type='checkbox' data-bind='checked: $data.value, attr: { disabled: $data.key != "selectRow"}' /></td>
                            <!-- /ko -->
                            <!-- ko if: $data == null || ($data != null && $data.action == null) -->
                            <td data-bind='text: consolidateField'></td>
                            <!-- /ko -->
                        </tr>
                    </tbody>
                </table>   

            </div>
            <div class='consolidate-new-list'>                
                <!-- ko if: loadingConsolidationData -->
                <div class='col-md-12 load-spinner'>
                <i class="fa fa-spinner fa-spin"></i>
                </div>
                <!-- /ko -->
                <div class='col-md-6'>
                    <p class='result-count'><span class='result-count-number' data-bind='text:totalConsolidateResults'></span> Results</p>
                    <button data-bind='click: toggleConsolidateSearch'>Search/Filter</button>                
                    <!-- ko if: consolidateMode() === 'search' -->         
                    <div class='search-div'>                    
                        <div data-bind='foreach: consolidateFilters'>
                            <p><span data-bind='text: column'></span>: <span data-bind='text: value'></span> <button type='button' data-bind='click: $root.removeConsolidateFilter'>[x]</button></p>
                        </div>
                        <select data-bind="options: consolidateReport().headers, select2: {minimumResultsForSearch: -1}, optionsCaption:'Choose a Column:', value: newConsolidateFilterColumn"></select>
                        <input type='input' data-bind='value: newConsolidateFilterValue' />
                        <button data-bind='click: addConsolidateFilter'>Add</button>
                    </div>
                    <!-- /ko -->
                </div>
                <!-- ko if: consolidateReport -->
                <table class='table'>
                    <thead>
                        <tr data-bind='foreach: consolidateReport().headers'>
                            <th data-bind='text: $data'></th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: {data: consolidateReport().data, as: "consolidateRow"}'>
                        <tr data-bind='foreach: {data: consolidateRow, as: "consolidateField"}'>
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'usermodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#user-detail-modal" data-bind='text: consolidateField.id, click: $root.showUserDetails'></a></td>
                            <!-- /ko -->
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'respondentmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#respondent-detail-modal" data-bind='text: consolidateField.id, click: $root.showRespondentDetails.bind(consolidateField, consolidateRow)'></a></td>
                            <!-- /ko -->                          
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'replacedmodal' -->
                            <td><a class="modal-anchor" data-toggle="modal" data-target="#replaced-by-modal" data-bind='text: consolidateField.value(), click: $root.showReplacedModal.bind(consolidateField, consolidateRow)'></a></td>
                            <!-- /ko -->                                                    
                            <!-- ko if: $data != null && $data.action != null && $data.action == 'checkbox' -->
                            <td><button type='button' class='btn btn-default' data-bind='click: $root.consolidateToRecord'>Replace With This</button></td>
                            <!-- /ko -->
                            <!-- ko if: $data == null || ($data != null && $data.action == null) -->
                            <td data-bind='text: consolidateField'></td>
                            <!-- /ko -->
                        </tr>
                    </tbody>
                </table>
                <!-- /ko -->
                <!-- ko if: consolidateMode() === 'browse' && consolidateResultsLeft > 0 -->
                <button data-bind='click: showMoreConsolidate'>Show More Results</button>
                <!-- /ko -->
            </div>                      
            <!-- /ko -->
            <div class="modal-footer">
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="user-detail-modal" tabindex='-1' role='dialog' aria-labeled-by='register_model_label' aria-hidden='true'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">User Information</h2>
            </div>
            <!-- ko if: userInfo() != null -->
            <div class="modal-body">
                <p>Name: <span data-bind='text:userInfo().name_first'></span> <span data-bind='text: userInfo().name_last'></span></p>
                <p>Email: <a data-bind='attr: {href: "mailto:"+userInfo().contact_email}'><span data-bind='text: userInfo().contact_email'></a></p>
            </div>
            <!-- /ko -->
            <div class="modal-footer">
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="replaced-by-modal" tabindex='-1' role='dialog' aria-labeled-by='replaced-by_model_label' aria-hidden='true'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Replaced By Row</h2>
            </div>
            <!-- ko if: replacedByRow() == null -->
            <p>Unable to locate replacing row in visible data. Try loading more data first.</p>
            <!-- /ko -->
            <table class='table' data-bind='if:currentReport'>
                <thead>
                    <tr data-bind='foreach: currentReport().headers'>
                        <th data-bind='text: $data'></th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-bind='foreach: {data: replacedByRow(), as: "replacingField"}'>
                        <!-- ko if: $data != null && $data.action != null && $data.action == 'usermodal' -->
                        <td><a class="modal-anchor" data-toggle="modal" data-target="#user-detail-modal" data-bind='text: replacingField.id, click: $root.showUserDetails'></a></td>
                        <!-- /ko -->
                        <!-- ko if: $data != null && $data.action != null && $data.action == 'respondentmodal' -->
                        <td><a class="modal-anchor" data-toggle="modal" data-target="#respondent-detail-modal" data-bind='text: replacingField.id, click: $root.showRespondentDetails.bind(replacingField, $root.replacedByRow)'></a></td>
                        <!-- /ko -->                            
                        <!-- ko if: $data != null && $data.action != null && $data.action == 'replacedmodal' -->
                        <td><a class="modal-anchor" data-toggle="modal" data-target="#replaced-by-modal" data-bind='text: replacingField.value(), click: $root.showReplacedModal.bind(replacingField, $root.replacedByRow)'></a></td>
                        <!-- /ko -->                                                    
                        <!-- ko if: $data != null && $data.action != null && $data.action == 'checkbox' -->
                        <td><input type='checkbox' data-bind='checked: $data.value, attr: { disabled: $data.key != "selectRow"}' /></td>
                        <!-- /ko -->
                        <!-- ko if: $data == null || ($data != null && $data.action == null) -->
                        <td data-bind='text: replacingField'></td>
                        <!-- /ko -->
                    </tr>
                </tbody>
            </table>   
            <div class="modal-footer">
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="respondent-detail-modal" tabindex='-1' role='dialog' aria-labeled-by='repsondent-detail_model_label' aria-hidden='true'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Respondent Information</h2>
            </div>            
            <!-- ko if: projectInfo() != null -->
            <div class="modal-body">
                <p>Project: <span data-bind='text:projectInfo().title'></span></p>
            </div>            
            <!-- /ko -->            
            <!-- ko if: siteInfo() != null -->
            <div class="modal-body">
                <p>Site: <span data-bind='text:siteInfo().name'></span></p>
            </div>
            <!-- /ko -->            
            <!-- ko if: focusGroupInfo() != null -->
            <div class="modal-body">
                <p>Focus Group: <span data-bind='text:focusGroupInfo().community'></span> <span data-bind='text:focusGroupInfo().venue_name'></span> <span data-bind='text:focusGroupInfo().meeting_date_time'></span></p>
            </div>
            <!-- /ko -->
            <!-- ko if: respondentInfo() != null -->
            <div class="modal-body">
                <p>Respondent Unique Identifier: <span data-bind='text:respondentInfo().uniqueID'></span></p>
            </div>
            <!-- /ko -->
            <div class="modal-footer">
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="map-modal" tabindex='-1' role='dialog' aria-labeled-by='map_model_label' aria-hidden='true'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content container shiny">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Spatial Data</h2>
            </div>
            <!-- ko if: mapId() != null && selectedTable -->
                <div class="row">
                    <div class="col-md-12 iframe-container">
                        <!-- ko if: selectedTable().dbTableName == 'site' -->
                        <iframe  frameborder="0" scrolling="no" data-bind="attr: {src: '<?php echo $currentEntity["spatial_site_url"]; ?>' + mapId()}" ></iframe>
                        <!-- /ko -->
                        <!-- ko if: selectedTable().dbTableName == 'focus_group' -->
                        <iframe  frameborder="0" scrolling="no" data-bind="attr: {src: '<?php echo $currentEntity["spatial_focus_group_url"]; ?>' + mapId()}" ></iframe>
                        <!-- /ko -->
                    </div>
                </div>
            <!-- /ko -->
  
            <div class="modal-footer">
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->Html->script('dropzone.js?v=1', array('block' => 'script')) ?>
<?php $this->Html->script('upload_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('report_common.js?v=3.7', array('block' => 'script')) ?>
<?php $this->Html->script('upload.js?v=1.1', array('block' => 'script')) ?>
<?php $this->Html->script('knockstrap.min.js', array('block' => 'script')) ?>
