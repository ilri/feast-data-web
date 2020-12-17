<?php $this->assign('title', 'Dashboard | ' . h($currentEntity['portal_title'])); ?>

<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='/'><img class='logo-image' src="/img/brand.png"></a>
    </div>
    <div class="outer-tab-panel col-md-9">
        <?php echo $this->element('primary_nav', ["active" => ""]); // Include primary navbar element ?>
    </div>
</div>

<div class="actual-content">
    <div class="container">
        <div class='row tab-content inner-tab-content'>
            <div class="col-md-8">
                <div class='page-prompt'>
                    <h2>Welcome</h2>
                    <p>The FEAST Data Aggregator allows you to upload data exported from your copy of the FEAST Data Application, then view reports and download data sets based on data uploaded by all FEAST users, worldwide.</p>
                    <p>To begin, choose one of the following:</p>
                </div>
                <ul class="dash-link-list">
                    <li><a href="/uploads">Upload and Manage your FEAST Data</a></li>
                    <li><a href="/downloads">Download aggregated data sets</a></li>
                    <li><a href="/reports">View Reports</a></li>
                    <li><a href="/help">View Help and Tutorials</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h2>News</h2>
                <?php echo $this->element('feed', ["active" => ""]); // Include RSS feed element ?>
            </div>
        </div>
    </div><!-- end .container -->
</div><!-- end .actual-content -->
<?php $this->Html->script('upload_strings.js', array('block' => 'script')) ?>
<?php $this->Html->script('feed.js', array('block' => 'script')) ?>