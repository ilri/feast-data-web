<?php $this->assign('title', 'Sign-In | '.h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
            <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
    </div>
</div>

<div class="actual-content">
<div class="container">
<div class='row tab-content inner-tab-content'>
<div class="col-md-10 col-md-offset-1 home-signin">
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <p class='panel-title'>Register for a Feast Account</p>
            <p class='help-block'>* indicates required field</p>
        </div>
        <div class="panel-body">
              <span class="message default text-center" data-bind='text: lastRegistrationError' ></span>
            <div id="register">
                               
               <form role="form" id="register-user-form">
                    <div class="row">
                         <div class="form-group col-md-6">
                        <label for="reg_salutation">Salutation</label>
                        <select name="reg_salutation" class="form-control" data-bind="optionsCaption:'Choose:', options: salutations, select2: {}, optionsText: 'description', optionsValue: 'id'" style="width:100%;"></select> 
                    </div>

                      <div class="form-group col-md-6">
                        <label for="reg_gender">Gender</label>
                        <select name='reg_gender' class="form-control" data-bind="optionsCaption:'Choose:', options: genders, select2: {}, optionsText:'description', optionsValue:'id'" ></select>
                    </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="form-group col-md-6">
                        <label for="reg_first_name">First Name*</label>
                        <input name="reg_first_name" type="text" class="form-control" id="registerInputFname">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="reg_middle_name">Middle Name</label>
                        <input name="reg_middle_name" type="text" class="form-control" id="registerInputMname">
                    </div>
                    </div>

                    <div class="row">
                           <div class="form-group col-md-6">
                        <label for="reg_last_name">Last Name*</label>
                        <input name="reg_last_name" type="text" class="form-control" id="registerInputLname">
                    </div>
                    <div class="form-group email-div col-md-6">
                        <label for="reg_email">Email*</label>
                        <!-- ko if: errors() != null && errors().username != null -->
                        <span class="text-error" data-bind="text: errors().username"></span>
                        <!-- /ko -->
                        <!-- ko if: errors() != null && errors().email != null -->
                        <span class="text-error" data-bind="text: errors().email"></span>
                        <!-- /ko -->
                        <input name="reg_email" type="email" class="form-control" id="registerInputEmail1">
                    </div>
                    </div>

                    <div class="row">
                         <div class="form-group col-md-6">
                        <label for="reg_phone">Phone</label>
                        <input name="reg_phone" type="text" class="form-control" id="registerInputMobile">
                    </div>

                      <div class="form-group col-md-6">
                        <label for="reg_birthdate">Birth Date</label>
                        <input name="reg_birthdate" type="text" class="form-control" id="registerInputBirthdate" placeholder="YYYY-MM-DD">
                    </div>
                    </div>
                    <hr>

                    <div class="row">
                     <div class="form-group col-md-6">
                        <label for="reg_world_region">World Region</label>
                        <select name="reg_world_region" class="form-control" data-bind="optionsCaption:'Choose:', options: worldRegions, select2: {}, optionsText: 'name', value: selectedWorldRegion"></select>
                    </div>                        
                    <!-- ko if: selectedWorldRegion() !== undefined -->
                    <div class="form-group col-md-6">
                        <label for="reg_country">Country</label>
                        <select name="reg_country" class="form-control" data-bind="optionsCaption:'Choose:', options: selectedWorldRegion().system_country, select2: {}, optionsText: 'name', value: selectedCountry"></select>
                    </div>
                     <!-- /ko -->
                    </div>

                    <div class="row">
                            <div class="form-group col-md-6">
                        <label for="reg_organization">Organization</label>
                        <input name="reg_organization" type="text" class="form-control" id="registerInputOrganization">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="reg_position">Position</label>
                        <input name="reg_position" type="text" class="form-control" id="registerInputPosition">
                    </div>             
                    </div>
                    <hr>

                    <div class="row">
                             <div class="form-group password-div col-md-6">
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
                    <div class="form-group confirm-password-div col-md-6">
                        <label for="reg_confirm_password">Confirm Password*</label>
                        <input name="reg_confirm_password" type="password" class="form-control" id="registerInputConfirmPassword">
                    </div>            
                    </div>
              </form>
                    <hr>
                    <div class="row text-center">
                         
               
                <button class="btn btn-lg btn-success" id="register-btn" data-bind="click: registerUser">Register</button>
                    </div>

                    
                
              
            </div>                            
        </div>
    </div>
</div>
</div>
</div>
    </div>
</div>

<?php $this->Html->script('user_access_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('user_access.js', array('block' => 'script')) ?>