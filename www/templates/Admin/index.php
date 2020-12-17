<?php $this->assign('title', 'Administration | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => "admin"]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content">
    <div class="container-fluid">
        <div class="upload-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#manage-users" aria-controls="manage-users" role="tab" data-toggle="tab">Users</a></li>
                <li role="presentation"><a href="#manage-data" aria-controls="manage-data" role="tab" data-toggle="tab">Data</a></li>
                <li role="presentation"><a href="#manage-resources" aria-controls="manage-resources" role="tab" data-toggle="tab">Resources</a></li>
                <li role="presentation"><a href="#manage-settings" aria-controls="manage-settings" role="tab" data-toggle="tab">Settings</a></li>
            </ul>
        </div>
        <div class='row tab-content inner-tab-content' style='display:none' data-bind='visible:true'>
            <div role="tabpanel" class="col-md-8 tab-pane active" id="manage-users">
                <div>
                    <h3>Select User:</h3>
                    <button data-bind='click: toggleSearch'>Search/Filter</button>                
                    <!-- ko if: reportMode() === 'search' -->         
                    <div class='search-div'>                    
                        <div data-bind='foreach: userFilters'>
                            <p><span data-bind='text: column'></span>: <span data-bind='text: value'></span> <button type='button' data-bind='click: $root.removeUserFilter'>[x]</button></p>
                        </div>
                        <select data-bind="value: newUserFilterColumn">
                            <option value='name'>Name</option>
                            <option value='email'>Email</option>
                            <option value='status'>Status</option>
                            <option value='role'>Role</option>
                        </select>
                        <!-- ko if: newUserFilterColumn() != 'status' && newUserFilterColumn() != 'role' -->
                        <input type='input' data-bind='value: newUserFilterValue' />
                        <!-- /ko -->
                        <!-- ko if: newUserFilterColumn() == 'status' -->
                        <select name='status_change' data-bind="options: $root.userStatusOptions, optionsText: 'title', optionsValue: 'status_id', value: newUserFilterValue"></select>
                        <!-- /ko -->
                        <!-- ko if: newUserFilterColumn() == 'role' -->
                        <select name='status_change' data-bind="options: $root.userStatusOptions, optionsText: 'title', optionsValue: 'status_id', value: newUserFilterValue"></select>
                        <!-- /ko -->
                        <button data-bind='click: addUserFilter'>Add</button>
                    </div>
                    <!-- /ko -->
                    <div class='checkbox'>
                        <label for="HideInactive">
                            <input name="HideInactive" type="checkbox" data-bind='checked:selectedHideInactive'>Hide Inactive
                        </label>
                    </div>
                </div>

                <table id='admin-user-table' class='table table-striped table-hover' style='word-wrap: break-word;'>
                    <thead>
                        <tr>
                            <th class="sortable" data-sort="name">Name</th>
                            <th class="sortable" data-sort="contact_email">Email</th>
                            <th class="sortable" data-sort="created_at">Created</th>
                            <th class="sortable" data-sort="user_approval_status_id">Status</th>
                            <th class="sortable" data-sort="admin">Role</th>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach:visibleUsers'>
                        <tr data-bind='click: $root.editUser, clickBubble: false'>
                            <td data-bind='text:(name_last != null ? name_last + ", " : "[], ")  + name_first'></td>
                            <td data-bind='text:contact_email'></td>
                            <td data-bind='text: created_at == null ? "N/A" : moment(created_at).format("L LT")'></td>
                            <td>
                                <select name='status_change' class="form-control" data-bind="click: function(){}, clickBubble: false, event: {change: $root.changeUserStatus}, options: $root.userStatusOptions, optionsText: 'title', optionsValue: 'status_id', value: approvalStatus"></select>
                            </td>
                            <td>
                                <select name='role_change' class="form-control" data-bind="click: function(){}, clickBubble: false, event: {change: $root.changeUserRole}, options: $root.userRoleOptions, optionsText: 'title', optionsValue: 'role_id', value: roleStatus"></select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- ko if: userMode() === 'browse' -->
                <button data-bind='click: showMoreUsers'>Show More Results</button>
                <!-- /ko -->
                <a target='_blank' class='report-csv-button' href='/api/file/export/users/csv'>Get User Data [CSV]</a>
            </div><!-- end #manageUsers -->
            <div role ='tabpanel' class='tab-pane col-md-12' id='manage-data'>
                <div class='col-md-12'>
                    <select data-bind="options: tableList, optionsText:'tableName', optionsCaption:'Choose a Table:', value: selectedTable"></select>
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
                            <select data-bind="options: availableFilters, optionsCaption:'Choose a Column:', value: newFilterColumn"></select>
                            <input type='input' data-bind='value: newFilterValue' />
                            <button data-bind='click: addFilter'>Add</button>
                        </div>
                        <div class="col-md-12">
                            <button type="button" data-bind='click: cancelFilter'>Cancel</button>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
                <div class='col-md-6'>
                    <p><em>Bulk Actions</em><p>
                        <select data-bind='options: bulkActions, optionsCaption: "Choose Action", value: selectedAction'></select>
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
                            <!-- ko if: $data == null || ($data != null && $data.action == null) -->
                            <td data-bind='text: reportField'></td>
                            <!-- /ko -->
                        </tr>
                    </tbody>
                </table>
                <!-- ko if: reportMode() === 'browse' && resultsLeft() > 0 -->
                <button data-bind='click: showMore'>Show More Results</button>
                <!-- /ko -->
                <!-- /ko -->
            </div>
            <div role ='tabpanel' class='tab-pane col-md-8' id='manage-resources'>
                <button data-bind='click: addResource'>Add Resource</button>
                <table class='table'>
                    <thead>
                        <tr>
                            <td>File Name</td>
                            <td>Description</td>
                            <td>Uploaded</td>
                            <td>Visible</td>
                            <td>Actions</td>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: resources'>
                        <tr>
                            <td><a data-bind='text: filename, attr: { href : "/api/resource/file/"+filename}'></a></td>
                            <td data-bind='text:desc'></td>
                            <td data-bind='text:moment(created).format("L LT")'></td>
                            <td data-bind='text:hidden == 0 ? "Yes" : "No"'></td>
                            <td><button data-bind='click: $root.updateResource'>Update</button><button data-bind='click: $root.toggleResourceVisibility, text: hidden == 0 ? "Hide":"Show"'></button><button data-bind='click: $root.deleteResource'>Delete</button></td>
                        </tr>
                    </tbody>
                </table>                
            </div>
            <div role ='tabpanel' class='tab-pane col-md-8' id='manage-settings'>                
                <button data-bind='click: addSetting'>Add Setting</button>
                <table class='table'>
                    <thead>
                        <tr>
                            <td>Setting</td>
                            <td>Value</td>
                            <td>Actions</td>
                        </tr>
                    </thead>
                    <tbody data-bind='foreach: settings'>
                        <tr>
                            <td data-bind='text:setting'></td>
                            <td data-bind='text:value'></td>
                            <td><button data-bind='click: $root.updateSetting'>Update</button><button data-bind='click: $root.deleteSetting'>Delete</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div><!-- end .container -->
</div><!-- end .actual-content -->

<div class='modal fade' id="setting-detail-modal" tabindex='-1' role='dialog' aria-labeled-by='setting_modal_label' aria-hidden='true'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Setting Detail</h2>
            </div>
            <div class="modal-body" data-bind='with:selectedSetting'>
                <h3>Setting Detail</h3>    
                <!-- ko if: id == null -->
                <div class="form-group"> 
                    <label for="setting-key">Setting Name</label>
                    <input type="text" id="setting-key" data-bind='value:setting'>
                </div>      
                <!-- /ko -->
                <div class="form-group"> 
                    <label for="setting-value">Value</label>
                    <input type="text" id="setting-value" data-bind='value:value'>
                </div>                
                <!-- ko if: id == null -->
                <button type="button" class="btn btn-default setting-update-button" data-bind="click: $root.doSettingUpdate">Add</button>
                <!-- /ko -->            
                <!-- ko if: id != null -->
                <button type="button" class="btn btn-default setting-update-button" data-bind="click: $root.doSettingUpdate">Update</button>
                <!-- /ko -->            
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="resource-detail-modal" tabindex='-1' role='dialog' aria-labeled-by='resource_modal_label' aria-hidden='true' data-bind='if: uploadMode() != null'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Resource Detail</h2>
            </div>
            <div class="modal-body">
                <h3>Resources</h3>    
                <!-- ko if: $root.uploadMode() == 'new' -->
                <p>Upload a new resource:</p>
                <!-- /ko -->
                <div class="form-group">
                    <label for="resource-description">Description</label>
                    <input type="text" id="resource-description" data-bind='value:resourceDescription'>
                </div>                                    
                <!-- ko if: $root.uploadMode() != 'new' -->
                <p>Upload a new file to replace this resource:</p>
                <!-- /ko -->
                <!-- ko if: $root.canUpload() -->
                <form class="dropzone" id='file-upload-dropzone'>
                    <div class="dropzone-previews"></div>
                    <div class="fallback" id='file-upload-fallback'>
                        <input name="file" type="file" />
                    </div>                                    
                </form>
                <!-- ko if: $root.isSending() == true -->
                <i class="fa fa-circle-o-notch fa-spin"></i>
                <!-- /ko -->
                <!-- ko if: $root.uploadError -->
                <p class='text-warning'>There was a problem uploading or processing your data. Try again, or contact an administrator for assistance.</p>
                <!-- /ko -->
                <!-- /ko -->
                <!-- ko if: !$root.canUpload() -->
                <p class="file-upload-pending">Your upload has been processed.</p>
                <!-- /ko -->            
            </div>
            <div class="modal-footer">
                <!-- ko if: $root.uploadMode() != 'new' && !$root.isSending() == true -->
                <button type="button" class="btn btn-default resource-update-button" data-bind="click: $root.doResourceUpdate">Update</button>
                <!-- /ko -->
                <!-- ko if: $root.uploadMode() == 'new' && !$root.isSending() == true -->
                <button type="button" class="btn btn-default resource-upload-button" data-bind="visible: $root.showUploadButton, click: $root.doFileUpload">Upload</button>
                <!-- /ko -->
                <button class="btn btn-default" id="user-detail-finish-btn" data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="consolidation-modal" tabindex='-1' role='dialog' aria-labeled-by='register_model_label' aria-hidden='true'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Consolidate records</h2>
            </div>
            <div class="modal-body">
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
                            <select data-bind="options: availableConsolidateFilters, optionsCaption:'Choose a Column:', value: newConsolidateFilterColumn"></select>
                            <input type='input' data-bind='value: newConsolidateFilterValue' />
                            <button data-bind='click: addConsolidateFilter'>Add</button>
                        </div>
                        <!-- /ko -->
                    </div>
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
                    <!-- ko if: consolidateMode() === 'browse' && consolidateResultsLeft > 0 -->
                    <button data-bind='click: showMoreConsolidate'>Show More Results</button>
                    <!-- /ko -->
                </div>                      
                <!-- /ko -->
            </div>
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

<div class='modal fade' id="user-edit-modal" tabindex='-1' role='dialog' aria-labeled-by='user_edit_modal_label' aria-hidden='true'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <!-- ko if: showUserEditForm() -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Edit User</h2>
                <p class='help-block'>* indicates required field</p>
            </div>
            <div class="modal-body">                
                <form role="form" id="edit-user-form" data-bind="with: editingUser">
                    <div class="form-group">
                        <label for="user_edit_salutation">Salutation</label>
                        <select name="user_edit_salutation" class="form-control" data-bind="optionsCaption:'Choose:', options: $root.salutations, optionsText: 'description', optionsValue: 'id', value: name_salutation_id"></select>                                    
                    </div>
                    <div class="form-group">
                        <label for="user_edit_first_name">First Name*</label>
                        <input name="user_edit_first_name" type="text" class="form-control" id="userEditInputFname" data-bind="value: name_first">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_middle_name">Middle Name</label>
                        <input name="user_edit_middle_name" type="text" class="form-control" id="userEditInputMname" data-bind="value: name_middle">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_last_name">Last Name</label>
                        <input name="user_edit_last_name" type="text" class="form-control" id="userEditInputLname" data-bind="value: name_last">
                    </div>
                    <div class="form-group email-div">
                        <label for="user_edit_email">Email*</label>
                        <input name="user_edit_email" type="email" class="form-control" id="userEditInputEmail1" data-bind="value: contact_email">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_phone">Phone</label>
                        <input name="user_edit_phone" type="text" class="form-control" id="userEditInputMobile" data-bind="value: contact_telephone">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_world_region">World Region</label>
                        <select name="user_edit_world_region" class="form-control" data-bind="optionsCaption:'Choose:', options: $root.worldRegions, optionsText: 'name', value: $root.selectedWorldRegion"></select>
                    </div>                        
                    <!-- ko if: $root.selectedWorldRegion() !== undefined -->
                    <div class="form-group">
                        <label for="user_edit_country">Country</label>
                        <select name="user_edit_country" class="form-control" data-bind="optionsCaption:'Choose:', options: $root.selectedWorldRegion().system_country, optionsText: 'name', value: $root.selectedCountry"></select>
                    </div>
                    <!-- /ko -->
                    <div class="form-group">
                        <label for="user_edit_organization">Organization</label>
                        <input name="user_edit_organization" type="text" class="form-control" id="userEditInputOrganization" data-bind="value: affiliation">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_position">Position</label>
                        <input name="user_edit_position" type="text" class="form-control" id="userEditInputPosition" data-bind="value: position_title">
                    </div>
                    <div class="form-group">
                        <label for="user_edit_gender">Gender</label>
                        <select name='user_edit_gender' class="form-control" data-bind="optionsCaption:'Choose:', options: $root.genders, optionsText:'description', optionsValue:'id', value: gender_id" ></select>
                    </div>
                    <div class="form-group password-div">
                        <label for="user_edit_password">Password*</label>
                        <input name="user_edit_password" type="password" class="form-control" id="userEditInputPassword">
                    </div>                                    
                    <div class="form-group confirm-password-div">
                        <label for="user_edit_confirm_password">Confirm Password*</label>
                        <input name="user_edit_confirm_password" type="password" class="form-control" id="userEditInputConfirmPassword">
                    </div>
                </form>                
            </div>
            <div class="modal-footer">
                <span data-bind='text: lastError'></span>
                <button class="btn btn-default" id="user-edit-cancel-btn" data-dismiss='modal'>Cancel</button>
                <button class="btn btn-sm btn-primary" id="user-edit-btn" data-bind='click: saveUser'>Save Changes</button>
            </div>
            <!-- /ko --> <!-- end showUserEditForm -->
            <!-- ko if: !showUserEditForm() -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">User Edit Successful</h2>
            </div>
            <div class="modal-body">       
                <p>Changes successfully saved.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" id="register-finish-btn" data-dismiss='modal'>Close</button>
            </div>
            <!-- /ko -->
        </div>
    </div>
</div>

<?php $this->Html->script('dropzone.js', array('block' => 'script')) ?>
<?php $this->Html->script('admin_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('report_common.js', array('block' => 'script')) ?>
<?php $this->Html->script('admin.js', array('block' => 'script')) ?>