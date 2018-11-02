
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Send message </h4>
</div>
<div class="sectionbody space">
<?php 
	$frm->setValidatorJsObjectName ( 'formValidator' );
	$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
	$frm->setFormTagAttribute ( 'class', 'web_form' );
	$frm->developerTags['fld_default_col'] = 12;
	
	echo  $frm->getFormHtml();
?>	
</div>

</section>						


	