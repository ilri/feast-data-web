<?php $this->assign('title', 'FEAST | '.h($currentEntity['portal_title'])); ?>
<section class="main">
	<div class="layer">
		<!---728x90--->
		<div class="bottom-grid">
			<div class="logo">
				<h1> <a href="./"><img class='logo-image' src="/img/brand.png"></a></h1>
			</div>
		</div>
    </div>

<div class="container col-md-12">
       // <?php echo $this->element('geosites', ["active" => ""]); ?>
    </div>    
</section>
<div class="untree_co-section-2 count-numbers">
<div class="container">
<div class="row">
		<div class="col-lg-12 mx-auto">
		<div class="row row-2">
				<div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
					<div class="counter" data-aos="fade-up" data-aos-delay="100">
						<strong class="d-block number" data-number="<?= $stat->countries; ?>">0</strong><br>
						<span class="d-block caption"> Countries</span>
					</div> 
				</div>	

			  <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
					<div class="counter" data-aos="fade-up" data-aos-delay="100">
						<strong class="d-block number" data-number="<?= $stat->sites; ?>">0</strong><br>
						<span class="d-block caption">Sites</span>
					</div> 
				</div>	

				<div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
					<div class="counter" data-aos="fade-up" data-aos-delay="100">
						<strong class="d-block number" data-number="<?= $stat->focus_groups; ?>">0</strong><br>
						<span class="d-block caption">Focus Groups</span>
					</div> 
				</div>	

				<div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
					<div class="counter" data-aos="fade-up" data-aos-delay="100">
						<strong class="d-block number" data-number="<?= $stat->fodder_cultivated; ?>">0</strong><br>
						<span class="d-block caption">Fodder Hectares</span>
					</div> 
				</div>	

			</div> 
		</div>
	</div>
</div>
</div>



<script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-23581568-13');
    </script>


