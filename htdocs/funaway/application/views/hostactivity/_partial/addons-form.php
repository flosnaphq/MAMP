<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'frmAddons');
$frm->setFormTagAttribute('action', 'setup7');
$frm->setValidatorJsObjectName('setup7Validator');
$frm->setFormTagAttribute('onsubmit', 'actionStep7(setup7Validator); return(false);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>
