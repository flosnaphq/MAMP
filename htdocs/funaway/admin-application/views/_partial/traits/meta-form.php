<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->developerTags['fld_default_col'] = 12;
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'web_form');
$frm->setFormTagAttribute('action', FatUtility::generateUrl($handlerName, 'metaTagAction'));
$frm->setFormTagAttribute('onsubmit', "jQuery.fn.submitForm(metatag_validator,'metaFrm'); return false;");
$frm->setFormTagAttribute('id', 'metaFrm');
?>

<div class="sectionbody space"><?php echo $frm->getFormHtml(); ?></div>

