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
		<?php if(isset($cms_data['cms_show_banner']) && $cms_data['cms_show_banner'] == 1){ ?>
		<div class="site-main__header__image">
			<div class="img"><img src="<?php echo FatUtility::generateUrl('image','cms-image',array($cms_data['cms_id']))?>" alt=""></div>
		</div>
		<?php } ?>
		<div class="site-main__header__content">
	<div class="section section--vcenter">
		<div class="section__body">
			<div class="container container--static">
				<div class="span__row">
					<div class="span span--10 span--center text--center">
						<hgroup style="margin-bottom:2em;">
							<h5 class="main-carousel__special-heading"><?php echo $cms_data['cms_name']; ?></h5>
							
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
		<?php echo Helper::fat_shortcode("[fat_contactinfo]"); ?>
		<?php echo Helper::fat_shortcode("[fat_offices]"); ?>
		<?php echo Helper::fat_shortcode("[fat_contactform]"); ?>
		
		<!--<section class="section section--top-border category__section" id="islands">
			<div class="section__header">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--8 span--center">
							 <hgroup>
								 <h5 class="heading-text text--center"><?php #echo Info::t_lang('CONTACT_US');?></h5>
								 <?php if(!empty($cms_data['cms_content'])){ ?>
								 <div class=" text--center">
								 <div class="innova-editor">
									<?php #echo html_entity_decode($cms_data['cms_content']);?>
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
							<?php #echo $frm->getFormHtml();?>
							<?php #echo Helper::fat_shortcode("[fat_contactform]"); ?>
						</div>
					 </div>
				 </div>
			 </div> 
		 </section>-->
	</div>
</main>