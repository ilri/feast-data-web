<?php $this->assign('title', 'Account | '.h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
            <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => ""]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content" style='display:none' data-bind='visible: true'>
    <div class="container">
        <div class='row tab-content inner-tab-content'>
            <div class='col-md-12'>
                <h4>User Profile</h4>                
            </div>
            <div class='col-md-12 user-profile-editor'>
                <form role="form" id="edit-user-form" data-bind="with: user">
                    <div class="form-group">
                        <label for="user_edit_salutation">Salutation</label>
                        <select name="user_edit_salutation" class="form-control" data-bind="optionsCaption:'Choose:', options: $root.salutations, select2: {minimumResultsForSearch: -1}, optionsText: 'description', optionsValue: 'id', value: name_salutation_id"></select>                                    
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
                        <select name="user_edit_world_region" class="form-control" data-bind="optionsCaption:'Choose:', options: $root.worldRegions, select2: {minimumResultsForSearch: -1}, optionsText: 'name', value: $root.selectedWorldRegion"></select>
                    </div>                        
                    <!-- ko if: $root.selectedWorldRegion() !== undefined -->
                    <div class="form-group">
                        <label for="user_edit_country">Country</label>
                        <select name="user_edit_country" class="form-control" data-bind="optionsCaption:'Choose:', options: $root.selectedWorldRegion().system_country, select2: {minimumResultsForSearch: -1}, optionsText: 'name', value: $root.selectedCountry"></select>
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
                    <!--
                    <div class="form-group">
                        <label for="user_edit_birthdate">Birth Date</label>
                        <input name="user_edit_birthdate" type="text" class="form-control" id="userEditInputBirthdate" placeholder="YYYY-MM-DD" data-bind="value: birthdate">
                    </div>
                    -->
                    <div class="form-group">
                        <label for="user_edit_gender">Gender</label>
                        <select name='user_edit_gender' class="form-control" data-bind="optionsCaption:'Choose:', options: $root.genders, select2: {minimumResultsForSearch: -1}, optionsText:'description', optionsValue:'id', value: gender_id" ></select>
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
                <div class='col-md-12'>
                    <span data-bind='text: lastError'></span>
                    <button class="btn btn-sm btn-primary" id="user-edit-btn" data-bind='click: saveUser'>Save Changes</button>
                </div>
            </div>
        </div>
    </div><!-- end .container -->
</div><!-- end .actual-content -->

<div class="modal fade" id="errorModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">System Error</h4>
            </div>
            <div class="modal-body">
                <p data-bind="text: lastError"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php $this->Html->script('user_profile_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('user_profile.js', array('block' => 'script')) ?>