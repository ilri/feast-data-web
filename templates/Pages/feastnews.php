<?php $this->assign('title', 'Feast News | ' . h($currentEntity['portal_title'])); ?>
<style type="text/css">
    #news img {
  max-width: 100%;
  height: auto;
}
</style>
<div class='container inner-navbar'>
    <div class="logo col-md-3">
        <a href='./'><img class='logo-image' src="/img/brand.png"></a>
    </div>
   
</div>

<div class="actual-content">
    <div class="container">
        <div class="row tab-content inner-tab-content">
            <div class="col-md-10" id="news">
                <h2><strong>News</strong></h2>
                <?php echo $this->element('feed', ["active" => ""]); // Include RSS feed element ?>
            </div>
        </div>
    </div><!-- end .container -->
</div><!-- end .actual-content -->

<?php $this->Html->script('feed.js', array('block' => 'script')) ?>