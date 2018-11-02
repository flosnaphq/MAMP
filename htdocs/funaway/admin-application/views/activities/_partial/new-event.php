<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'web_form');
$frm->setFormTagAttribute('id', 'new-event');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('activities','event-action'));
$frm->setFormTagAttribute('onsubmit', 'addNewEvent(); return(false);');
$frm->developerTags['fld_default_col'] =4;
$frm->getField('btn_submit')->developerTags['col'] = 4;
?>
<script>
onChangeTime('<?php echo $frm->getField('service_type')->value?>');
</script>
<?php echo $frm->getFormHtml(); ?>