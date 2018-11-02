<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal c-file-upload');
$frm->setFormTagAttribute('style', 'margin-top:1.25em;');
$frm->setFormTagAttribute('id', 'frmPhoto');
$frm->setFormTagAttribute('action', 'setup2');
$frm->developerTags['fld_default_col'] =6;
$frm->setValidatorJsObjectName('setup2Validator');
$frm->setFormTagAttribute('onsubmit', 'actionStep2(setup2Validator); return(false);');
echo $frm->getFormHtml();
?>