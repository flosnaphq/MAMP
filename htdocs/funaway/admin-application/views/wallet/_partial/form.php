<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Add/Update </h4>
</div>
<div class="sectionbody space">
	Use (-) For Deduct Amount From Wallet					
	<?php 
	$frm->setValidatorJsObjectName ( 'formValidator' );
	$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"walletFrm"); return(false);' );
	$frm->setFormTagAttribute ( 'id', 'walletFrm' );
	$frm->setFormTagAttribute ( 'class', 'web_form' );
	$frm->developerTags['fld_default_col'] = 12;
	$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("wallet","action") );
	echo  $frm->getFormHtml();
	?>	
</div>

</section>						