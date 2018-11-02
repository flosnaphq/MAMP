<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
<div class="sectionhead">
<h4>Add-Transaction</h4><a html="javascript:;" onclick = "closeForm()" class="close-form"><i class="ion-close-round"></i></a>
</div>
<div class="sectionbody space">
						
	<?php 
	
	
	$frm->setValidatorJsObjectName ( 'formValidator' );
	$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return(false);' );
	$frm->setFormTagAttribute ( 'class', 'web_form' );
	$frm->setFormTagAttribute ( 'id', 'action_form' );
	$amount = $frm->getField('amount');
	$amount->developerTags['col']=6;
	$comment = $frm->getField('comment');
	$comment->developerTags['col']=12;
	$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
	$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("orders","transactionformAction") );
	echo  $frm->getFormHtml();
	?>	
</div>

</section>						