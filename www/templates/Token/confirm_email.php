<?php $this->assign('title', 'Email Confirmation | '.h($currentEntity['portal_title'])); ?>

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
            <div class="col-md-12">
                <div class="body-part">
                <?php if ($confirmStatus == 1) {?>            
                    <?php if ($approvalRequired == 1) { ?>
                        <p>Your email address has been confirmed, and your registration is pending approval.  Expect an email from the administrator shortly.</p>
                    <?php } else { ?>
                        <p> Your email address has been confirmed. Click <a href='/'>here</a> to return to the home page and log in.</p>
                    <?php } // end approvalRequired if block ?>            
                <?php } else { ?>            
                    <p>There was a problem confirming your email address.</p>
                    <p>If you believe you've reached this page in error, please contact <a href='mailto:<?= $supportEmail ?>'><?= $supportEmail ?></a></p>                                
                <?php } // end confirmStatus if block ?>
                </div>
            </div>
        </div>
    </div>
</div>