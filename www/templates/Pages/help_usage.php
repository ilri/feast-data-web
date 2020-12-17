<?php $this->assign('title', 'Help | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php
        if ($authedUser) {
            echo $this->element('primary_nav', ["active" => ""]);
        } // Include primary navbar element  
        ?>
    </div>
</div>

<div class="actual-content">
    <div class="container help-page-container">
        <div class='row body-part tab-content inner-tab-content'>
            <div class='col-md-12'>
                <h2><a href="/help">HELP</a> > USING THE FEAST DATA APPLICATION</h2>
                <div id="usage-help">
                    <div class="help-control">
                        <a role="button" href="#usage-help-contents" aria-expanded="false" aria-controls="usage-help-contents">USING THE FEAST DATA APPLICATION</a>
                    </div>
                    <div id="usage-help-contents">

<div id="usage-player"></div>
<script src="https://luwes.github.io/vimeowrap.js/vimeowrap.js"></script>
<script src="https://luwes.github.io/vimeowrap.js/vimeowrap.playlist.js"></script>
<script>
    vimeowrap('usage-player').setup({
        urls: [
            'https://vimeo.com/129937075',
            'https://vimeo.com/131382042',
            'https://vimeo.com/131382038',
            'https://vimeo.com/131382036',
            'https://vimeo.com/131382037',
            'https://vimeo.com/131382035',
            'https://vimeo.com/181896122',
            'https://vimeo.com/131382157'
        ],
        plugins: {
            'playlist':{}
        }
    });
</script>                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<?php $this->Html->script('help.js', array('block' => 'script')) ?>
