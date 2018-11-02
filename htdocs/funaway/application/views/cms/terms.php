<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
?>
<main id="MAIN" class="site-main site-main--dark">
	<!--<header class="site-main__header site-main__header--light">
	   <div class="site-main__header__content">
			<div class="section section--vcenter">
				<div class="container container--static">
					<h5 class="special-heading-text text--center"><?php //echo $cms_data['cms_sub_heading']?></h5>
					<!--<h6 class="sub-heading-text text--center text--red">For Traveller</h6>-->
				<!--
				</div>
			</div>
		</div>
	</header> -->
	<div class="site-main__body">
		<section class="section cancel__section no--padding-top">
			<div class="cancellation__tab ">
				<div class="section__header menu-bar text--center">
					 <nav class="menu tab__nav">
						<?php if(!empty($terms_pages)){ ?>
						<ul class="list list--horizontal">
							<?php foreach($terms_pages as $page){ ?>
								<li><a href="<?php echo FatUtility::generateUrl($page['cms_slug'],'')?>" class="button button--fill <?php if($slug == $page['cms_slug']){ ?> current <?php } ?>"><?php echo $page['cms_name']?></a></li>
							<?php } ?>
							
						 </ul>
						 <?php } ?>
					 </nav>
				 </div>
				 <div class="section__body">
					 <div class="container container--static">
						 <div class="span__row">
							 <div class="span span--10 span--center block cancellation__block">
								<div class="innova-editor">
									<?php echo (isset($cms_data['cms_content']) ? html_entity_decode($cms_data['cms_content']) : '');?>
								</div>
							 </div>
						 </div>
						
					 </div>
				 </div>
			 </div>
		</section>                
	</div>
</main>