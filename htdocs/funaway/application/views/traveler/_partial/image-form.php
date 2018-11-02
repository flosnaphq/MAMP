<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
/* image upload form*/
//$frm->setFormTagAttribute('class','siteForm formhorizontal profile-image');
$frm->setFormTagAttribute('class','form form--horizontal form--default text--center profile-image');

$frm->setFormTagAttribute('id','profile-img-form');
$fld = $frm->getField('photo')->setFieldTagAttribute('onchange',"popupImage('profile-img-form')");
$fld = $frm->getField('remove_img')->value ='<a href="javascript:;" onclick="removeImage()" class="button button--small button--fill button--red">'.Info::t_lang('REMOVE_IMAGE').'</a>';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('action',FatUtility::generateUrl('image','uploadDemoPhoto'));
$frm->setValidatorJsObjectName('myAccountFormValidator');
$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
echo $frm->getFormHtml(); 

/* profile upload form*/
?>