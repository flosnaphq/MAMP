<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$emailFrm->setFormTagAttribute('action',FatUtility::generateUrl('traveler','updateEmail'));
$emailFrm->setFormTagAttribute('onsubmit','updateEmail(emailUpdateFrm, this); return false;');
$emailFrm->setValidatorJsObjectName('emailUpdateFrm');
$emailFrm->setFormTagAttribute('class','form form--default form--horizontal');
$submit_btn = $emailFrm->getField('submit_btn');//button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag']=true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right');
//$emailFrm->developerTags['fld_default_col'] =12;
echo $emailFrm->getFormHtml();
?>