<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
// Info::test($frm);exit;
?>
<div class="areabody">
<?php 
$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return(false);' );
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->getField('comment')->developerTags['col'] = 12;
$frm->developerTags['fld_default_col'] = 3;
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("order-cancel-requests","setup") );
echo  $frm->getFormHtml();
?>	
</div>