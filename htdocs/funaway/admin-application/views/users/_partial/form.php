<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Add/Update </h4>
</div>
<div class="sectionbody space">
						
	<?php 
	$frm->setValidatorJsObjectName ( 'formValidator' );
	$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return(false);' );
	$frm->setFormTagAttribute ( 'class', 'web_form' );
	$frm->developerTags['fld_default_col'] = 6;
	$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("users","update") );
	echo  $frm->getFormHtml();
	?>	
</div>

</section>						