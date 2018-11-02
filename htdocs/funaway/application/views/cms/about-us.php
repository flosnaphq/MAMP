<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<main id="MAIN" class="site-main site-main--light">
	<header class="site-main__header <?php if(isset($cms_data['cms_show_banner']) && $cms_data['cms_show_banner'] == 1) echo 'site-main__header--dark';?> main-carousel__item">
<!-- hide -->
<?php if(isset($cms_data['cms_show_banner']) && $cms_data['cms_show_banner'] == 1){ ?>
<div class="site-main__header__image">
	<div class="img"><img src="<?php echo FatUtility::generateUrl('image','cms-image', array($cms_data['cms_id']))?>" alt=""></div>
</div>
<?php } ?>
<!-- hide -->
<div class="site-main__header__content">
	<div class="section section--vcenter">
		<div class="section__body">
			<div class="container container--static">
				<div class="span__row">
					<div class="span span--10 span--center text--center">
						<hgroup style="margin-bottom:2em;">
							<?php if(!empty($cms_data)){?>
							<h5 class="main-carousel__special-heading"><?php echo $cms_data['cms_name']; ?></h5>
							<h6 class="main-carousel__sub-heading"><?php echo $cms_data['cms_sub_heading']; ?></h6>
							<?php } ?>
						</hgroup>
						<?php if(isset($cms_data['cms_show_banner']) && $cms_data['cms_show_banner'] == 1 && !empty($cms_data['cms_banner_content'])){ ?>
						<p class="main-carousel__regular"><?php echo $cms_data['cms_banner_content']; ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</header>
	<div class="site-main__body">
   
		<section class="section section--top-border founder__section" id="founder">
			 
			 <div class="section__body">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
					 <div class="cms-content">
						<div class="innova-editor">
							<?php echo (isset($cms_data['cms_content']) ? html_entity_decode($cms_data['cms_content']) : ''); ?>
						</div>
					 </div>
						 </div>
					 </div>
				 </div>
			</div>
		</section>
		<?php if(isset($cms_data['cms_id']) && $cms_data['cms_id'] == 5){ ?>
		<?php if(!empty($founders)){ ?>
		<section class="section section--top-border founder__section" id="founder">
			 <div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php echo Info::t_lang('MEET_THE_FOUNDERS');?></h5>
								 <h6 class="sub-heading-text text--center text--green"><?php echo Info::t_lang('WHO_MADE_IDEA_REAL');?></h6>
							 </hgroup>
						 </div>
					 </div>
				</div> 
			 </div>
			 <div class="section__body">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
					 <div class="founder__list">
						<?php foreach($founders as $founder){ ?>
							<div class="media founder__item">
								 <div class="media__figure media--left founder__image">
									 <img src="<?php echo FatUtility::generateUrl('image','founder',array($founder['founder_id'],480,480))?>" alt="">
								 </div>
								 <div class="media__body founder__content">
									 <h6 class="founder__name"><?php echo $founder['founder_name']?></h6>
									 <span class="founder__desi"><?php echo $founder['founder_designation']?></span>
									 <div class="founder__desc">
										 <div class="innova-editor">
											<?php echo html_entity_decode($founder['founder_content'])?>
										 </div>
									 </div>
								 </div>
							 </div>
						<?php } ?>
						
						 
					 </div>
						 </div>
					 </div>
				 </div>
			</div>
		</section>
		<?php } ?>
		<?php if(!empty($investors)){ ?>
		<section class="section section--light investor__section" id="investor">
			 <div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--12">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php echo Info::t_lang('OUR_INVESTORS');?></h5>
								 <h6 class="sub-heading-text text--center text--green"><?php echo Info::t_lang('WHO_BELIEVE_US');?></h6>
							 </hgroup>
						 </div>
					 </div>
				</div> 
			 </div>
			 <div class="section__body">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--12">
							<div class="investor__list">
							<?php foreach($investors as $investor){ ?>
								<a href="<?php echo $investor['investor_link'];?>" target="_blank" class="investor__item">
									 <img src="<?php echo FatUtility::generateUrl('image','investor',array($investor['investor_id'],155,103))?>" alt="">
								 </a>
							<?php } ?>
							
							</div>
						 </div>
					 </div>
				 </div>
			</div>
		</section>
		<?php } ?>
		<section class="section seen__section" id="asSeenOn">
			<div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php echo Info::t_lang('AS_SEEN_ON')?></h5>
							 </hgroup>
						 </div>
					 </div>
				</div>   
			</div>
			<div class="section__body">
				<div class="container container--static">
					 <div class="span__row">
						 <div class="span span--6 text--center">
							 <p class="regular-text regular-text--large">“<?php echo Info::t_lang("FOOLOOS_HELPS_ENSURE_YOU'LL_HAVE_THE_MOMENTS_THAT_MAKE_TRAVEL_MEMORABLE")?>”</p>
							 <a href="/"><img src="<?php echo CONF_WEBROOT_URL; ?>images/the_new_york_times_logo.png" alt=""></a>
						 </div>
						 <div class="span span--6 text--center">
							 <p class="regular-text regular-text--large">“<?php echo Info::t_lang('A_TRAVEL_GUIDE-BOOK-MEETS-HOTEL_CONCIERGE._A_TRAVEL_GUIDE-BOOK-MEETS-HOTEL_CONCIERGE')?>”</p>
							 <a href="/"><img src="<?php echo CONF_WEBROOT_URL; ?>images/the_guardian_logo.png" alt=""></a>
						 </div>
					</div>
				</div>
			 </div>
		</section>
		<?php } ?>
	</div>
</main>