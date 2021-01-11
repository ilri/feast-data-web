<?php $this->assign('title', 'Session Timeout | '.h($currentEntity['portal_title'])); ?>

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
            <div class="col-md-6 col-md-offset-3" id='session-timeout-login'>
                <h2>Session Timeout</h2>       
                <?= $this->Flash->render(); ?>                                
                <form id="timeout-login-form" method="POST" action="/timeout">
                    <div class="form-group">
                        <label for="username">Email or Username </label>
                        <input name="username" type="text" class="form-control" id="loginInputUsername">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input name="password" type="password" class="form-control" id="loginInputPassword">
                    </div>
                    <input type="submit" class="btn btn-sm btn-default" id="login-btn" value="Sign in">
                    <div style="clear:both;"></div>
                </form>
                <p class="forgot-password pull-right">Forgot your password? <a href="/user/resetPassword">Click Here</a></p>
            </div>
        </div>
    </div>
</div>