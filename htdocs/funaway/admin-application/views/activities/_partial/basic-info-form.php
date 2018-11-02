<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<div class="sectionbody space">
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setValidatorJsObjectName ( 'formValidator' );
/* $frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE); */
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl('activities','setupBasicInfomation') );
$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return false;');
$frm->developerTags['fld_default_col'] = 6;
$duration_days = $frm->getField('duration_days');
$duration_days->setWrapperAttribute('class','duration_days_wrapper');
$booking_days = $frm->getField('booking_days');
$booking_days->setWrapperAttribute('class','booking_days_wrapper');
echo  $frm->getFormHtml();
?>	
</div>
<script>
	changeBooking('<?php echo $frm->getField('activity_booking')->value?>');
	changeDuration('<?php echo $frm->getField('activity_duration')->value?>');
</script>

	