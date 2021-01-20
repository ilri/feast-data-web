<?php $this->assign('title', 'Password Reset | '.h($currentEntity['portal_title'])); ?>

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
            <div class="col-md-6 col-md-offset-3" id='reset-form-container'>
                <h2>Reset Your Password</h2>
                <?php if (!$resetLinkSent) { ?>
                <?php if (isset($noUser)) { ?>
                    <p class="alert alert-danger">User with this email not found.</p>
                <?php } ?>
                <p>To reset your password, enter your email address below.</p>
                <p>An email will be sent to you with further instructions.</p>
                <form id='reset-form' role='form' method='POST' action='/user/resetPassword'>
                    <div class="form-group email-div">
                        <label for="email">Email*</label>
                        <input name="email" type="email" class="form-control" id="resetInputEmail">
                    </div>
                    <div class='form-group'>
                        <input type="submit" class="btn btn-sm btn-default" id="resetSubmitButton" value="Request Reset">
                    </div>
                </form>
                <?php } else { ?>
                <p>Your request to reset your password has been received. You should receive an email with further instructions in the next few minutes. <a href="/">Click here</a> to return to the main page.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>