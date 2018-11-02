<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('action',FatUtility::generateUrl('host','passwordSetup'));
$frm->setFormTagAttribute('id','updatePasswordForm');
$frm->setFormTagAttribute('onsubmit','updatePassword(updatePasswordFrmValidator, this); return false;');
$frm->setValidatorJsObjectName('updatePasswordFrmValidator');
$frm->setFormTagAttribute('class','form form--default form--horizontal');
$submit_btn = $frm->getField('submit_btn');//button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag']=true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right');
echo $frm->getFormHtml();
?>