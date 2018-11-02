
<main id="MAIN" class="site-main site-main--light">
    <header class="site-main__header site-main__header--dark main-carousel__list js-main-carousel">
	
	</header>
	  <div class="site-main__body">
		
		<p style="height:100px;" >
			<center><a href="<?php echo FatUtility::generateUrl('croper','load');?>" class="modaal-ajax">Upload</a></center>
		</p>	
	  </div>
	</main>
	
	<script>
		$('.modaal-ajax').modaal({
    type: 'ajax'
});
	</script>