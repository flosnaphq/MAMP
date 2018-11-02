<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'frmVideo');
$frm->setFormTagAttribute('action', 'setup3');
$frm->setValidatorJsObjectName('setup3Validator');
$frm->setFormTagAttribute('onsubmit', 'actionStep3(setup3Validator); return(false);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>