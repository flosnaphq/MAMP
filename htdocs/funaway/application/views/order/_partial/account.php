<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('class','form form--vertical form--default');
$frm->setFormTagAttribute('action',FatUtility::generateUrl('order','setupAccountInfo'));
$submit_btn = $frm->getField('submit_btn');//button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag']=true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right');
$frm->setFormTagAttribute('onsubmit','updateProfile(profileUpdateFrm, this); return false;');
$frm->setValidatorJsObjectName('profileUpdateFrm');

?>
<div class="cotainer container--fluid">
<div class="span__row">
	<div class="span span--7 span--center">
<h5 class="heading-text text--center"><?php echo Info::t_lang('ACCOUNT')?></h5>
<h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('KINDLY_INPUT_YOUR_ACCOUNT_DETAILS')?></h6>
<hr>
<?php echo $frm->getFormHtml(); ?>
	</div>
</div>
</div>
