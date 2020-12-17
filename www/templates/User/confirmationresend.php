<?php $this->assign('title', 'Account Confirmation Resend | '.h($currentEntity['portal_title'])); ?>

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
                <h2>Resend account confirmation email</h2>
                <div><?= $this->Flash->render(); ?></div>
                <?php if (!$confirmationResendSent) { ?>
                <p>To resend your account confirmation email, enter your contact email address below.</p>
                <p>An email will be sent to you with further instructions.</p>
                <form id='reset-form' role='form' method='POST' action='/user/confirmationresend'>
                    <div class="form-group email-div">
                        <label for="email">Email*</label>
                        <input name="contactEmail" type="email" class="form-control" id="contactEmail">
                    </div>
                    <div class='form-group'>
                        <input type="submit" class="btn btn-sm btn-default" id="resetSubmitButton" value="Request Confirmation Resend">
                    </div>
                </form>
                <?php } else { ?>
                <p>Please check you contact email inbox. You should receive an email with further instructions in the next few minutes. <a href="/">Click here</a> to return to the main page.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>