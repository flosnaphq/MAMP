
<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'frmAddons');
$frm->setFormTagAttribute('action', FatUtility::generateUrl("manage-activity",'saveAddon'));
$frm->setValidatorJsObjectName('setup7Validator');
$frm->setFormTagAttribute('ng-submit', 'saveAddon($event);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>
