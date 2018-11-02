<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<div class="sectionbody space">
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl('activities','setupAdditionalInfo') );
$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return false;');
$frm->developerTags['fld_default_col'] = 6;

echo  $frm->getFormHtml();
?>	
</div>


	