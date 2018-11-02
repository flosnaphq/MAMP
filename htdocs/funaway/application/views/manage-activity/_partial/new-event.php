<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'new-event');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('hostactivity','event-action'));
$frm->setFormTagAttribute('onsubmit', 'addNewEvent(); return(false);');
$frm->developerTags['fld_default_col'] =6;
?>
<script>
onChangeTime('<?php echo $frm->getField('service_type')->value?>');
</script>
<div class="modal share-card">
	<div class="modal__header text--center">
		<h6 class="modal__heading"><?php echo $formHeader?></h6>
	</div>
	<div class="modal__content share-card__image">
		<?php echo $frm->getFormHtml(); ?>
	</div>
</div>