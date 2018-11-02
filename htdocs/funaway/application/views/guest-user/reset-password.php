<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('id', 'resetForm');
$frm->setFormTagAttribute('class', 'form form--vertical form--theme form--inverse');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('guest-user','resetPasswordSetup',array($tocken, $user_id)));
$frm->setValidatorJsObjectName('formValidator');
$frm->setFormTagAttribute('onsubmit', 'submitForm(formValidator); return(false);');


$btn_submit = $frm->getField('btn_submit');
$btn_submit->setFieldTagAttribute('class','button button--fill button--red');




?>
   <main id="MAIN" class="site-main site-main--darkest">
		<div class="site-main__body">
		   <div class="section section--vcenter no--margin section__forgot">
				<div class="section__body">
					<div class="container container--static">
						<div class="span__row">
							<div class="span span--6 span--center text--center"  style="max-width:500px">
								
								<?php
								
								echo $frm->getFormHtml();
								/*  echo $frm->getFormTag();?>
									<div class="form-element">
										<div class="form-element__control">
											<?php echo $frm->getFieldHtml('useremail');?>
											<label class="form-element__label"><?php echo Info::t_lang('EMAIL_ADDRESS')?></label>
										</div>
									</div>
									
									<?php echo $frm->getFieldHtml('btn_submit');?>
									<?php echo $frm->getExternalJs(); */ ?>
								
							</div>
						</div>
					</div>
			   </div>
					<p class="regular-text disclaimer"><?php echo sprintf(Info::t_lang('BY_PROCEEDING,_YOU_AGREE_TO_%s'),FatApp::getConfig('conf_website_name'))?> <a href="<?php echo Info::generateCustomUrl('cms', 'terms', array('privacy'))?>"> <?php echo Info::t_lang('PRIVACY_POLICY')?> </a><?php echo Info::t_lang("AND")?><a href="<?php echo Info::generateCustomUrl('cms','terms')?>"> <?php echo Info::t_lang('TERMS_OF_USE')?></a>.</p>
			</div>
		</div>
	</main>