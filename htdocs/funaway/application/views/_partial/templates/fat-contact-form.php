<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('action','');
$frm->setFormTagAttribute('id','frmContact');
$frm->setFormTagAttribute('class','form form--vertical form--theme');
$frm->setValidatorJsObjectName('frmContactObj');
$frm->setFormTagAttribute('onsubmit','sendInquiry(this, frmContactObj); return(false);');
$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);

$name = $frm->getField('name');
$name->developerTags['col'] = 6;

$email = $frm->getField('email');
$email->developerTags['col'] = 6;

$option = $frm->getField('option');
$option->developerTags['col'] = 12;
$option->developerTags['noCaptionTag'] = true;

$message = $frm->getField('message');
$message->developerTags['col'] = 12;

$security_code = $frm->getField('security_code');
$security_code->developerTags['col'] = 12;

$btn_submit = $frm->getField('btn_submit');
$btn_submit->developerTags['noCaptionTag'] = true;
$btn_submit->setFieldTagAttribute('class','button button--fill button--secondary');

?>
<section class="section section--top-border category__section" id="islands">
	<div class="section__header">
		<div class="container container--static">
			<div class="span__row">
				<div class="span span--8 span--center">
					<hgroup>
						<h5 class="heading-text text--center"><?php echo Info::t_lang('YOUR_DETAILS');?></h5>
					</hgroup>
				</div>
			</div>
		</div> 
	</div>
	<div class="section__body">
		<div class="container container--static">
			<div class="span__row">
				<div class="span span--8 span--center">
					<?php echo $frm->getFormHtml();?>
				</div>
			</div>
		</div>
	 </div> 
</section>

<script type="text/javascript">
	(function($){ 
		sendInquiry = function(frm, v)
		{
			v.validate();
			if (!v.isValid()) return;
			
			fcom.ajax(fcom.makeUrl('cms', 'submitInquiry'), fcom.frmData(frm), function(json)
			{
				json = $.parseJSON(json);
				if(json.status == 1)
				{
					jsonSuccessMessage(json.msg);
					frm.reset();
				}
				else
				{
					jsonErrorMessage(json.msg);
				}
				grecaptcha.reset();
			});
		}
	})(jQuery);
</script>