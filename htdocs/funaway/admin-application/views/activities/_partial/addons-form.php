<section class="section">
<div class="sectionhead">
<h4>Add/Update </h4><a html="javascript:;" onclick = "closeForm()" class="close-form"><i class="ion-close-round"></i></a>
</div>
<div class="sectionbody space">
<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'web_form');
$frm->setFormTagAttribute('id', 'action_form');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('activities','setup7'));
$frm->setValidatorJsObjectName('setup7Validator');
$frm->setFormTagAttribute('onsubmit', 'submitForm(setup7Validator); return(false);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>
</div>

</section>
