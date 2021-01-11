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
                <div class="col-md-8 col-md-offset-2 home-signin">
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
                                <div class='register-action-block'><a href="/signup" class="btn btn-sm btn-default">Sign-up</a></div>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>