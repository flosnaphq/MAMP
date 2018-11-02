
<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--vertical');
$frm->setFormTagAttribute('id', 'act-event');
$frm->setValidatorJsObjectName('step1Validator');
$frm->setFormTagAttribute('onsubmit', ' return(false);');
echo $frm->getFormHtml();
?>