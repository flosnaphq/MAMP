<div class="sectionbody space">
<?php 
$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setFormTagAttribute ( 'onsubmit', 'submitCustomLink(formValidator); return(false);' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->developerTags['fld_default_col'] = 6;
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("navigation","actionCustom") );
$frm->setRequiredStarPosition ( Form::FORM_REQUIRED_STAR_WITH_CAPTION );
echo  $frm->getFormHtml();
?>
</div>