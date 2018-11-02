<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Bank Account </h4></a>
</div>
<div class="sectionbody space">
						
	<?php 
	$frm->setValidatorJsObjectName ( 'formValidator' );
	$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"bank-account"); return(false);' );
	$frm->setFormTagAttribute ( 'class', 'web_form' );
	$frm->setFormTagAttribute ( 'id', 'bank-account' );
	$frm->developerTags['fld_default_col'] = 6;
	$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("users","setupBankAccount") );
	echo  $frm->getFormHtml();
	?>	
</div>

</section>						