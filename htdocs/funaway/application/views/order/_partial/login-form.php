<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('class','form form--default form--horizontal');
$frm->setFormTagAttribute('action',FatUtility::generateUrl('order','setupLogin'));
$submit_btn = $frm->getField('submit_btn');//button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag']=true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right');
$frm->setFormTagAttribute('onsubmit','orderLogin(orderLoginFrm, this); return false;');
$frm->setValidatorJsObjectName('orderLoginFrm');
$back_btn = $frm->getField('back_btn');//button button--fill button--green fl--right
$back_btn->developerTags['noCaptionTag']=true;
$back_btn->setFieldTagAttribute('class', 'button button--fill button--dark fl--left');
$back_btn->setFieldTagAttribute('onclick', 'paymentTab()');
$submit_btn->attachField($back_btn);
$frm->developerTags['fld_default_col'] =3;

?>
<div class="cotainer container--fluid">
<div class="span__row">
	<div class="span span--7 span--center">
<h5 class="heading-text text--center"><?php echo Info::t_lang('ACCOUNT')?></h5>
<h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('KINDLY_INPUT_YOUR_PASSWORD')?></h6>
<hr>
<?php echo $frm->getFormHtml(); ?>
	</div>
</div>
</div>
