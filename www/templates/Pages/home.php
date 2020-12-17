<?php $this->assign('title', 'FEAST | '.h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
            <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => ""]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content">

    <div class="container">
        <div class="row body-part tab-content inner-tab-content">
            <div class="col-md-8 home-left-part">
                <?php include(WWW_ROOT . '/static/html/splash.html'); ?>
                <div class='col-md-12'>
                <h2>News</h2>
                <?php echo $this->element('feed', ["active" => ""]); // Include RSS feed element ?>
                </div>
            </div>

            <div class="col-md-4 home-register">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <p class='panel-title'>Sign In</p>
                        </div>
                        <div class="panel-body">
                            <div id="login">
                                <?= $this->Flash->render(); ?>                                
                                <form id="login" method="POST" action="/user/login">
                                    <div class="form-group">
                                        <label for="username">Email or Username </label>
                                        <input name="contact_email" type="text" class="form-control" id="loginInputUsername">
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input name="password" type="password" class="form-control" id="loginInputPassword">
                                    </div>
                                    <div class='signin-action-block'><input type="submit" class="btn btn-sm btn-default" id="login-btn" value="Sign in"></div>
                                </form>
                                <div class='forgot-password-block'><p class="forgot-password">Forgot your password? <a href="/user/resetPassword">Click Here</a></p></div>
                                <div class='register-block'><p class="need-registration">Don't have an account yet?</p></div>
                                <div class='register-action-block'><button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#register-modal">Register</button></div>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id="register-modal" tabindex='-1' role='dialog' aria-labeled-by='register_model_label' aria-hidden='true'>
    <div class="modal-dialog">
        <div class="modal-content">            
            <!-- ko if: showRegisterForm -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Registration</h2>
                <p class='help-block'>* indicates required field</p>
            </div>
            <div class="modal-body">                
                <form role="form" id="register-user-form">
                    <div class="form-group">
                        <label for="reg_salutation">Salutation</label>
                        <select name="reg_salutation" class="form-control" data-bind="optionsCaption:'Choose:', options: salutations, select2: {minimumResultsForSearch: -1}, optionsText: 'description', optionsValue: 'id'"></select>                                    
                    </div>
                    <div class="form-group">
                        <label for="reg_first_name">First Name*</label>
                        <input name="reg_first_name" type="text" class="form-control" id="registerInputFname">
                    </div>
                    <div class="form-group">
                        <label for="reg_middle_name">Middle Name</label>
                        <input name="reg_middle_name" type="text" class="form-control" id="registerInputMname">
                    </div>
                    <div class="form-group">
                        <label for="reg_last_name">Last Name*</label>
                        <input name="reg_last_name" type="text" class="form-control" id="registerInputLname">
                    </div>
                    <div class="form-group email-div">
                        <label for="reg_email">Email*</label>
						<!-- ko if: errors() != null && errors().username != null -->
						<span class="text-error" data-bind="text: errors().username"></span>
						<!-- /ko -->
						<!-- ko if: errors() != null && errors().email != null -->
						<span class="text-error" data-bind="text: errors().email"></span>
						<!-- /ko -->
                        <input name="reg_email" type="email" class="form-control" id="registerInputEmail1">
                    </div>
                    <div class="form-group">
                        <label for="reg_phone">Phone</label>
                        <input name="reg_phone" type="text" class="form-control" id="registerInputMobile">
                    </div>
                    <div class="form-group">
                        <label for="reg_world_region">World Region</label>
                        <select name="reg_world_region" class="form-control" data-bind="optionsCaption:'Choose:', options: worldRegions, select2: {minimumResultsForSearch: -1}, optionsText: 'name', value: selectedWorldRegion"></select>
                    </div>                        
                    <!-- ko if: selectedWorldRegion() !== undefined -->
                    <div class="form-group">
                        <label for="reg_country">Country</label>
                        <select name="reg_country" class="form-control" data-bind="optionsCaption:'Choose:', options: selectedWorldRegion().system_country, select2: {minimumResultsForSearch: -1}, optionsText: 'name', value: selectedCountry"></select>
                    </div>
                    <!-- /ko -->
                    <div class="form-group">
                        <label for="reg_organization">Organization</label>
                        <input name="reg_organization" type="text" class="form-control" id="registerInputOrganization">
                    </div>
                    <div class="form-group">
                        <label for="reg_position">Position</label>
                        <input name="reg_position" type="text" class="form-control" id="registerInputPosition">
                    </div>             
                    <div class="form-group">
                        <label for="reg_birthdate">Birth Date</label>
                        <input name="reg_birthdate" type="text" class="form-control" id="registerInputBirthdate" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="form-group">
                        <label for="reg_gender">Gender</label>
                        <select name='reg_gender' class="form-control" data-bind="optionsCaption:'Choose:', options: genders, select2: {minimumResultsForSearch: -1}, optionsText:'description', optionsValue:'id'" ></select>
                    </div>
                    <div class="form-group password-div">
                        <label for="reg_password">Password*</label>
						<!-- ko if: errors() != null && errors().reg_password != null && errors().reg_password.minLength != null -->
						<span class="text-error" data-bind="text: errors().reg_password.minLength" ></span>
						<!-- /ko -->
						<!-- ko if: errors() != null && errors().reg_password != null && errors().reg_password.maxLength != null -->
						<span class="text-error" data-bind="text: errors().reg_password.maxLength" ></span>
						<!-- /ko -->
						<!-- ko if: errors() != null && errors().reg_password != null && errors().reg_confirm_password != null -->
						<span class="text-error" data-bind="text: errors().reg_confirm_password" ></span>
						<!-- /ko -->
						<!-- ko if: errors() != null && errors().reg_password != null && errors().password != null -->
						<span class="text-error" data-bind="text: errors().password" ></span>
						<!-- /ko -->
                        <input name="reg_password" type="password" class="form-control" id="registerInputPassword">
                    </div>                                    
                    <div class="form-group confirm-password-div">
                        <label for="reg_confirm_password">Confirm Password*</label>
                        <input name="reg_confirm_password" type="password" class="form-control" id="registerInputConfirmPassword">
                    </div>                                    
                </form>                
            </div>
            <div class="modal-footer">
                <span data-bind='text: lastRegistrationError'></span>
                <button class="btn btn-default" id="register-cancel-btn" data-dismiss='modal'>Cancel</button>
                <button class="btn btn-sm btn-primary" id="register-btn" data-bind="click: registerUser">Register</button>
            </div>
            <!-- /ko --> <!-- end showRegisterForm -->
            <!-- ko if: showRegisterSuccess -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Registration Successful</h2>
            </div>
            <div class="modal-body">       
            <?php if ($currentEntity['setting_email_confirmation_required']) { // At least need confirmation
                    if ($currentEntity['setting_approval_required']) { // Need both confirmation and approval ?>
                        <p>Thank you for registering.  Before activating your account, you will need to confirm your email address and have your registration approved by the administrator.  Please check your inbox (or spam folder) for the confirmation email.</p>
                    <?php } else { // Just need confirmation ?>
                        <p>Thank you for registering.  Before activating your account, you will need to confirm your email address.  Please check your inbox (or spam folder) for the confirmation email.</p>
                    <?php }
                  } else if ($currentEntity['setting_approval_required']) { // Just need approval ?>
                        <p>Thank you for registering.  Your registration is currently pending approval.  Expect an email from the administrator shortly.</p>
            <?php } ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" id="register-finish-btn" data-dismiss='modal'>Close</button>
            </div>
            <!-- /ko -->
        </div>
    </div>
</div>    

<?php $this->Html->script('user_access_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('user_access.js', array('block' => 'script')) ?>
<?php $this->Html->script('feed.js', array('block' => 'script')) ?>