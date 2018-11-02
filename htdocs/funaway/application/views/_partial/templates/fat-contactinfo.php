<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section <?php echo $cls;?>" id="<?php echo $id; ?>">
	<div class="section__header">
		 <div class="container container--static">
			 <div class="span__row">
				 <div class="span span--10 span--center">
					 <hgroup>
						 <h5 class="heading-text text--center"><?php echo Info::t_lang('CONTACT_DETAILS');?></h5>
					 </hgroup>
				 </div>
			 </div>
		</div>   
	</div>
	<div class="section__body">
		<div class="container container--static">
			 <div class="span__row">
				 <div class="span span--10 span--center">
					 <article class="island-tip">
						 <div class="tip__list island-tip__list">
							<div class="media tip__item">
								<div class="media__figure tip__image">
									<div class="tip__icon">
										<img src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/mail.svg" alt="">
									</div>
								</div>
								<div class="media__body media--middle tip__content">
									<h6 class="tip__heading"><?php echo Info::t_lang('EMAIL');?></h6>
									<p class="tip__text"><?php echo FatApp::getConfig('CONTACT_US_EMAIL_ID');?></p>
								</div>
							</div>
							<div class="media tip__item">
								<div class="media__figure tip__image">
									<div class="tip__icon">
										<img src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/viber.svg" alt="">
									</div>
								</div>
								<div class="media__body media--middle tip__content">
									<h6 class="tip__heading"><?php echo Info::t_lang('VIBER_LINE');?></h6>
									<p class="tip__text"><?php echo FatApp::getConfig('VIBER_LINE');?></p>
								</div>
							</div>
							<div class="media tip__item">
								<div class="media__figure tip__image">
									<div class="tip__icon">
										<img src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/skype.svg" alt="">
									</div>
								</div>
								<div class="media__body media--middle tip__content">
									<h6 class="tip__heading"><?php echo Info::t_lang('SKYPE');?></h6>
									<p class="tip__text"><?php echo FatApp::getConfig('SKYPE_ID');?></p>
								</div>
							</div>
						 </div>
					 </article>
				 </div>
			 </div>
		 </div>
	 </div>
 </section>