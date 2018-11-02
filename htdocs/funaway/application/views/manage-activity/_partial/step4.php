<?php 
//$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'step4');
$frm->setFormTagAttribute('action', FatUtility::generateUrl("manage-activity",'saveActivityBrief'));
$frm->setValidatorJsObjectName('step4Validator');
$frm->setFormTagAttribute('ng-submit', 'saveActivityBrief($event);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>
<?php if(!empty($cancellation_ins)){ ?>
<div id="cancellation-policy-instruction" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo html_entity_decode($cancellation_ins['block_title'])?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo html_entity_decode($cancellation_ins['block_content'])?>
			</div>
		</div>
	</div>
</div>
<?php } ?>

