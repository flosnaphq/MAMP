<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$frm->setFormTagAttribute('action','setupPartnerships');
$frm->setValidatorJsObjectName('partnershipsFrmValidator');
$frm->getField('partner_country')->developerTags['noCaptionTag'] = true;
$frm->setFormTagAttribute('onsubmit','submitForm(partnershipsFrmValidator, this); return false');
$frm->setFormTagAttribute('class','form form--vertical form--theme');
$frm->developerTags['fld_default_col'] =12;
$btn_submit = $frm->getField('btn_submit');
$btn_submit->developerTags['noCaptionTag'] = true;
$btn_submit->setFieldTagAttribute('class','button button--fill button--secondary');

?>
<main id="MAIN" class="site-main site-main--light">
	<header class="site-main__header <?php if(isset($cms_data['cms_show_banner']) && $cms_data['cms_show_banner'] == 1) echo 'site-main__header--dark';?> main-carousel__item">
<!-- hide -->
<?php if(isset($cms_data['cms_show_banner']) && $cms_data['cms_show_banner'] == 1){ ?>
<div class="site-main__header__image">
	<div class="img"><img src="<?php echo FatUtility::generateUrl('image','cms-image',array($cms_data['cms_id']))?>" alt=""></div>
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
							<h5 class="main-carousel__special-heading"><?php echo $cms_data['cms_name']; ?></h5>
							<h6 class="main-carousel__sub-heading"><?php echo $cms_data['cms_sub_heading']; ?></h6>
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
		<?php if(!empty($cms_data['cms_content'])){
                  
                    ?>
		<section class="section section--top-border founder__section" id="founder">
			 
			 <div class="section__body">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--10 span--center">
					 <div class="cms-content">
						<div class="innova-editor">
							<?php echo html_entity_decode($cms_data['cms_content']); ?>
						</div>
					 </div>
						 </div>
					 </div>
				 </div>
			</div>
		</section>
		<?php } ?>
		<section class="section section--top-border category__section" id="islands">
			
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