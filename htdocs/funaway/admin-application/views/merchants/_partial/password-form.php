<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Update Password</h4><a html="javascript:;" onclick = "closeForm()" class="close-form"><i class="ion-close-round"></i></a>
</div>
<div class="sectionbody space">
						
	<?php 
	$frm->setValidatorJsObjectName ( 'formValidator' );
	$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return(false);' );
	$frm->setFormTagAttribute ( 'class', 'web_form' );
	$frm->setFormTagAttribute ( 'id', 'action_form' );
	$frm->developerTags['fld_default_col'] = 6;
	$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("merchants","update-password") );
	echo  $frm->getFormHtml();
	?>	
</div>

</section>						