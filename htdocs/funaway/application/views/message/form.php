
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('class','form form--default form--vertical');
$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
$frm->developerTags['fld_default_col'] = 12;
$btn_submit = $frm->getField('btn_submit');
$btn_submit->setFieldTagAttribute('class','button button--fill button--red fl--right');
?>

<div class="modal share-card text--center">
	<div class="modal__header">
		<h6 class="modal__heading"><?php echo !empty($message_heading)?$message_heading:Info::t_lang('WRITE_YOUR_REPLY')?></h6>
	</div>
	<div class="modal__content share-card__image">
		<?php
		echo  $frm->getFormHtml();
		?>
	</div>
</div>
	
					


	