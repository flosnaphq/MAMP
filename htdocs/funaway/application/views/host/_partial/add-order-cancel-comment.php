<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 

?>
<div class="modal">
	<div class="modal__header">
		<h6 class="modal__heading"><?php echo Info::t_lang('BOOKING_CANCELLATION -').' '.$booking_id?></h6>
	</div>
	<div class="modal__content share-card__image">
		<?php
		$frm->setFormTagAttribute('action',FatUtility::generateUrl('host','setupOrderCancelComment'));
	
		$frm->setFormTagAttribute('onsubmit','submitCancelForm(FrmValidator, this); return false;');
		$frm->setValidatorJsObjectName('FrmValidator');
		$frm->setFormTagAttribute('class','form form--default form--vertical');
		$submit_btn = $frm->getField('submit_btn');//button button--fill button--green fl--right
		$submit_btn->developerTags['noCaptionTag']=true;
		$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right');
		echo $frm->getFormHtml();
		?>
	</div>
	
</div>

