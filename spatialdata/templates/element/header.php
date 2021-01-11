  <!-- Navigation -->
<!-- <nav class="navbar  navbar-fixed-top navbar-dark bg-primary" role="navigation">
    <div class="container">
        <div class="navbar-header ">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Feast Spatial Data</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <?= $this->Html->link('Posts', '/') ?>
                </li>
            </ul>
        </div>
    </div>
</nav> -->
<nav class="navbar navbar-inverse bg-warning">
        
        <div class="navbar-header ">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><h3 style="color:#ffffff;"><b>Feast Spatial Data</b></h3></a>
            
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                 <li><?= $this->Html->link(__('Site List'), ['action' => 'index']) ?></li>
                 <li><?= $this->Html->link(__('Site Points on Map'), ['action' => 'sitepoints']) ?></li>
            </ul>
        </div>
    
    </nav>  
