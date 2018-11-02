<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'new-event');
$frm->setFormTagAttribute('action', Route::getRoute('UserRequest','saveRequest'));
$frm->setValidatorJsObjectName('createRequestValidator');
$frm->setFormTagAttribute('onsubmit', 'jQuery.fn.submitForm(createRequestValidator,"new-event",afterRequestSucess); return false;');
$frm->developerTags['fld_default_col'] =6;
?>
<div class="modal share-card">
	<div class="modal__header text--center">
            <h6 class="modal__heading"><?php echo $formHeader ?></h6>
	</div>
	<div class="modal__content share-card__image">
		<?php echo $frm->getFormHtml(); ?>
	</div>
</div>
<script type="text/javascript">
    
    afterRequestSucess = function(){
             $('.modaal-ajax').modaal('close');
    } 
    
</script>    