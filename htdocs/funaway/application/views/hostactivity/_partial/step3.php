<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'step1');
$frm->setFormTagAttribute('action', 'setup1');
$frm->setValidatorJsObjectName('step1Validator');
$frm->setFormTagAttribute('onsubmit', 'actionStep1(step1Validator); return(false);');
$frm->developerTags['fld_default_col'] =6;
echo $frm->getFormHtml();
?>