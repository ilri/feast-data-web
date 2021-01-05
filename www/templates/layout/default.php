<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= h($this->fetch('title')) ?></title>        
        

        <?= $this->Html->css('bootstrap.min.css') ?>
        <?= $this->Html->css('font-awesome.min.css') ?>  
     <!--    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=GOOGLE_KEY&callback=initMap&libraries=&v=weekly" defer></script>   -->
        <?php
        /*
         * TODO: There's currenty no separation in the stylesheet between "CORE"
         * and "entity" styles, so we'll assume the entity contains all relevant
         * styles for now rather than including both stylesheets which would be
         * mostly redundant.
         */
        echo $this->Html->css('/css/select2.min.css');
        echo $this->Html->css('/css/style.css?v=2.2');
        echo $this->Html->meta('icon','/img/favicon.ico');
        ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>

<!--New EU privecy poicy script start here -->

<!-- <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
			<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
			<script>
			window.addEventListener("load", function(){
			window.cookieconsent.initialise({
			  "palette": {
				"popup": {
				  "background": "#216942",
				  "text": "#b2d192"
				},
				"button": {
				  "background": "#77240d"
				}
			  },
			  "theme": "edgeless",
			  "type": "opt-out",
			  "content": {
				"message": "This site uses cookies. By continuing to use this site you agree to our use of cookies.",
				"dismiss": "Agree",
				"href": "https://www.ilri.org/privacy-cookies-statement"
			  }
			})});



			</script> -->

<!-- Ends here-->

    </head>
    <body>
        <!--Start wrapper -->
        <div class="wrapper">
            <!-- Start Header -->
            <nav class="navbar navbar-default navbar-toggleable-xs fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a href="http://ilri.org/feast" target="_blank" class="navbar-brand">                    
                            <img src="/img/sonata_logo.png" alt="sonata_logo.png" />
                        </a>
                    </div>

                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-right navbar-nav">
                            <?php if (isset($authedUser) && $authedUser) { ?>
                            <li>
                                <a href="/profile"><?= $authedUser['name_first']?></a>
                            </li>
                             <li class="v-line hidden-xs"></li> 
                            <li>
                                <a href="/user/logout">Logout</a>
                            </li>
                            <!-- <li class="v-line"></li> -->
                            <?php } else { ?>

                        
                           <!--  <li class="v-line"></li> -->
                            <li>
                                <a href="/about"><strong>About Feast</strong></a>
                            </li>
                            <li class="v-line hidden-xs"></li>
                            <li>
                                <a href="/news"><strong>News</strong></a>
                            </li>
                            
                            <li class="v-line hidden-xs"></li>
                            <?php } ?>
                           <!--  <li><a class="help-link" href="/help" target="_blank">Help</a></li> -->
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Header -->

            <!-- Start Content -->
            <div class="content">
                <?= $this->fetch('content') ?>
            </div>
            <!-- End Content -->
        </div>
        <!-- End wrapper -->
        <!-- Start Footer -->
        <div class="container footer"> <!-- all content -->
            <div class="row">
                <div class="col-md-12 footer-contents">
                <?php
                if (isset($currentEntity)) {
                    include(WWW_ROOT . '/static/html/footer.html');
                }
                ?>        
                </div>
            </div>
        <!-- End Footer -->
        </div><!-- end all content -->            

        <div id="modal">
            <div class="modal-box">
                <div class="content">

                    <div class="ui-bar">
                        <button class="btn-close">X</button>
                    </div>

                </div>
            </div>
        </div>    
        <?= $this->Html->script('knockout-3.3.0.js') ?>
        <?= $this->Html->script('jquery-2.1.4.min.js') ?>
        <?= $this->Html->script('select2.min.js'); ?>
        <?= $this->Html->script('knockout-select2.js'); ?>
        <?= $this->Html->script('bootstrap.min.js') ?>
        <?= $this->Html->script('moment-with-locales.min.js') ?>
        <?= $this->Html->script('jquery.animateNumber.min.js') ?>
        <?= $this->Html->script('jquery.waypoints.min.js') ?>
        <?= $this->Html->script('jquery.counterup.min.js') ?>
        <?= $this->Html->script('aos.js') ?>
        <?= $this->Html->script('main.js') ?>
        <?= $this->fetch('script') ?>
        <script type='text/javascript'>

        $(document).ready(function(){
             $(".navbar-toggle").click(function() {
                $("#navbar").addClass("navbar-card");
              });

              $('.counter').counterUp({
                 delay: 10,
                 time: 1000,
                offset: '100%'
             });
        });
    
            moment.locale(navigator.languages? navigator.languages[0] : (navigator.language || navigator.userLanguage));
        </script>
        <?php if (!empty($currentEntity['google_analytics_code'])) { ?>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '<?= $currentEntity['google_analytics_code']?>', 'auto');
             if(getUserConsentState() === true){
	    ga('set', 'anonymizeIp', undefined);
                } else {
                 ga('set', 'anonymizeIp', true);
                }
            ga('send', 'pageview');
        </script>
        <?php } ?>

    </body>
</html>
