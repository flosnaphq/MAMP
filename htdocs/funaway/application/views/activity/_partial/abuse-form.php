<?php 
	defined('SYSTEM_INIT') or die('Invalid Usage');
	$frm->setFormTagAttribute('class', 'form form--default form--vertical');
	$frm->setFormTagAttribute('action', FatUtility::generateUrl('activityAbuse','markAsAbuse'));
	$frm->setFormTagAttribute('id', 'abuseReviewForm');
	$frm->setValidatorJsObjectName('rvalidate');
	$frm->setFormTagAttribute('onsubmit', 'submitAbuseReport(rvalidate); return(false);');
	$submit_btn = $frm->getField('submit_btn');
	$submit_btn->setFieldTagAttribute('class','button button--fill button--red');
	//echo $frm->getFormHtml();
?>

<div class="modal share-card text--center">
	<div class="modal__header">
		<h6 class="modal__heading"><?php Info::t_lang("WRITE_ABUSE_REASON")?></h6>
	</div>
	<div class="modal__content share-card__image">
		<?php
		echo  $frm->getFormHtml();
		?>
	</div>
</div>