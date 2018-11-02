<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<div class="sectionbody space">
<link href='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css' rel='stylesheet' />
<div class="mapbox-container">
	<div id='map' style="height:600px"></div>
</div>
<?php
 defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl('activities','setupMap') );
$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return false;');
$frm->developerTags['fld_default_col'] = 6;

echo  $frm->getFormHtml(); 
?>	

</div>

<script>

showMap(<?php echo $lat?>,<?php echo $long?>);

</script>


	

