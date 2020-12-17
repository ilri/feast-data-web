<?php $this->assign('title', 'Password Change | '.h($currentEntity['portal_title'])); ?>

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
            <div class="col-md-6 col-md-offset-3" id='change-password-container'>
                <h2>Reset Your Password</h2>
                <?php if (!$passwordChanged) { ?>         
                    <?php if ($hasErrors) { ?>
                     <p class='text-warning'>Your new password must be at least 5 characters and your confirmation password must match.</p>
                    <?php } ?>
                    <p>You may enter a new password below.
                    <form id='change-password-form' role='form' method='POST' action='/user/changePassword'>
                        <div class="form-group password-div">
                            <label for="new_password">New Password</label>
                            <input name="new_password" type="password" class="form-control" id="newInputPassword">
                        </div>                                    
                        <div class="form-group confirm-password-div">
                            <label for="new_password_confirm">Confirm Password</label>
                            <input name="new_password_confirm" type="password" class="form-control" id="newInputConfirmPassword">
                        </div>                     
                        <div class='form-group'>
                            <input type="submit" class="btn btn-sm btn-default" id="newPasswordSubmitButton" value="Change Password">
                        </div>
                    </form>
                <?php } else { ?>
                    <p>You've successfully changed your password. <a href='/'>Click here to log in.</a></p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>