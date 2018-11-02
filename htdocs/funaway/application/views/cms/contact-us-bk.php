<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('action','contacts');
$frm->setFormTagAttribute('class','form form--vertical form--theme');
$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
$name = $frm->getField('name');
//$name->setFieldTagAttribute('placeholder',Info::t_lang('NAME'));
$name->developerTags['col'] = 6;

$email = $frm->getField('email');
$email->developerTags['col'] = 6;
//$email->setFieldTagAttribute('placeholder',Info::t_lang('EMAIL_ADDRESS'));

$option = $frm->getField('option');
$option->developerTags['col'] = 12;
$option->developerTags['noCaptionTag'] = true;
//$option->setFieldTagAttribute('placeholder',Info::t_lang('SELECT_OPTION'));

$message = $frm->getField('message');
$message->developerTags['col'] = 12;
//$message->setFieldTagAttribute('placeholder',Info::t_lang('MESSAGE'));

$security_code = $frm->getField('security_code');
$security_code->developerTags['col'] = 12;
//$security_code->setFieldTagAttribute('placeholder',Info::t_lang('MESSAGE'));

$btn_submit = $frm->getField('btn_submit');
$btn_submit->developerTags['noCaptionTag'] = true;
$btn_submit->setFieldTagAttribute('class','button button--fill button--secondary');

?>

<main id="MAIN" class="site-main site-main--light">
	<header class="site-main__header site-main__header--dark main-carousel__item">
		<div class="site-main__header__image">
			<div class="img"><img src="<?php echo CONF_WEBROOT_URL; ?>images/contact-us.jpg" alt=""></div>
		</div>
		<div class="site-main__header__content">
			<div class="section section--vcenter">
				<div class="section__body">
					<div class="container container--static">
						<div class="span__row">
							<div class="span span--10 span--center text--center">
								<hgroup style="margin-bottom:2em;">
									<h5 class="main-carousel__special-heading"><?php echo Info::t_lang("WE'RE_HERE_FOR_YOU");?></h5>
									<h6 class="main-carousel__sub-heading"><?php echo FatApp::getConfig('conf_website_name'); echo Info::t_lang("_CONTACT");?></h6>
								</hgroup>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</header>
	<div class="site-main__body">
		<section class="section tip__section" id="tips">
			<div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php echo Info::t_lang('CONTACT_DETAILS')?></h5>
								 
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
											<h6 class="tip__heading"><?php echo Info::t_lang('EMAIL')?></h6>
											<p class="tip__text"><?php echo FatApp::getConfig('CONTACT_US_EMAIL_ID')?></p>
										</div>
									</div>
									<div class="media tip__item">
										<div class="media__figure tip__image">
											<div class="tip__icon">
												<img src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/viber.svg" alt="">
											</div>
										</div>
										<div class="media__body media--middle tip__content">
											<h6 class="tip__heading"><?php echo Info::t_lang('VIBER_LINE')?></h6>
											<p class="tip__text"><?php echo FatApp::getConfig('VIBER_LINE')?></p>
										</div>
									</div>
									<div class="media tip__item">
										<div class="media__figure tip__image">
											<div class="tip__icon">
												<img src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/skype.svg" alt="">
											</div>
										</div>
										<div class="media__body media--middle tip__content">
											<h6 class="tip__heading"><?php echo Info::t_lang('SKYPE')?></h6>
											<p class="tip__text"><?php echo FatApp::getConfig('SKYPE_ID')?></p>
										</div>
									</div>
								 </div>
							 </article>
						 </div>
					 </div>
				 </div>
			 </div>
		 </section>
		 <?php if(!empty($offices)){ ?>
		 <section class="section why-choose__section" id="whyChooseUs">
			<div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php echo Info::t_lang("VISIT_HEADQUARTERS");?></h5>
							 </hgroup>
						 </div>
					 </div>
				</div>   
			</div>
		<div class="section__body">
		<div class="container container--static"> 
			<div class="span__row"> 
				<div class="span span--10 span--center"> 
					<article class="wcu"> 
						<div class="point__list wcu-point__list">
							<?php if(isset($offices[1])){
								$office = $offices[1];
							?>
							<div class="media point__item">
								<div class="media__figure point__image">
									<div class="point__icon"><img alt="" src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/thailand.svg" />
									</div>
								</div>
								<div class="media__body media--middle point__content">
									<h6 class="point__heading"><?php echo $office['office_country']?></h6>
									<ul class="list list--vertical">
										<li><?php echo nl2br($office['office_address']); ?></li>   
									</ul>
								</div>
							</div>
							<?php } ?>
							<?php if(isset($offices[2])){
								$office = $offices[2];
							?>
							<div class="media point__item">
								<div class="media__figure point__image">
									<div class="point__icon"><img alt="" src="<?php echo CONF_WEBROOT_URL; ?>images/graphics/singapore.svg" /></div>
								</div>
								<div class="media__body media--middle point__content">
									<h6 class="point__heading"><?php echo $office['office_country']?></h6>
									<ul class="list list--vertical">
										<li><?php echo nl2br($office['office_address']); ?></li>
									</ul>
								</div>
							</div> 
							<?php } ?>
						</div> 
					</article>
				</div> 
			</div> 
		</div> 
	</div>
		 </section>
		 <?php } ?>
		 <section class="section section--top-border category__section" id="islands">
			<div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--8 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php echo Info::t_lang('CONTACT_US');?></h5>
								 <?php if(!empty($contact_desc)){ ?>
								 <div class=" text--center">
								 <div class="innova-editor">
									<?php echo html_entity_decode($contact_desc['block_content']);?>
								 </div>
								 </div>
								 <?php } ?>
							 </hgroup>
						 </div>
					 </div>
				</div> 
			 </div>
			 <div class="section__body">
				 <div class="container container--static">
					<div class="span__row">
						 <div class="span span--8 span--center">
							 <?php echo $frm->getFormHtml();?>
						</div>
					 </div>
				 </div>
			 </div> 
		 </section>
	</div>
</main>