<?php 
	defined('SYSTEM_INIT') or die('Invalid Usage');
	$frm->setFormTagAttribute('class', 'form form--default form--vertical');
	$frm->setFormTagAttribute('action', FatUtility::generateUrl('reviews','setup'));
	$frm->setFormTagAttribute('id', 'reviewForm');
	$frm->setValidatorJsObjectName('rvalidate');
	$frm->setFormTagAttribute('onsubmit', 'submitReview(rvalidate); return(false);');
?>

<div class="modal share-card">
	<div class="modal__header text--center">
		<h6 class="modal__heading"><?php echo Info::t_lang('WRITE_A_REVIEW')?></h6>
	</div>
	<div class="modal__content share-card__image">
		<?php echo $frm->getFormHtml(); ?>
	</div>
</div>