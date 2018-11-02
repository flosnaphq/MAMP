<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>

<div class="payment-page">
	<div class="cc-payment">
		<div class="payment-from">
			<?php 
			if (!isset($error))
			{
				$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
				$frm->setFormTagAttribute('class', 'form form--vertical form--default');
				$frm->setFormTagAttribute('id', 'omiseForm');
				$frm->setFormTagAttribute('action', FatUtility::generateUrl('OmisePay','process'));
				$frm->setValidatorJsObjectName('step1Validator');
				$frm->setFormTagAttribute('onsubmit', 'actionForm(step1Validator); return(false);');
				$frm->developerTags['fld_default_col'] =6;
				$btn_submit = $frm->getField('btn_submit');
				$btn_submit->setFieldTagAttribute('class','button button--fill button--green');
				$cc_number = $frm->getField('cc_number');
				$cc_number->setFieldTagAttribute('class','empty');
				$cc_owner = $frm->getField('cc_owner');
				$cc_owner->setFieldTagAttribute('class','empty');
				$cc_expire_date_month = $frm->getField('cc_expire_date_month');
				$cc_expire_date_year = $frm->getField('cc_expire_date_year');
				$cc_cvv = $frm->getField('cc_cvv');
				$btn_submit = $frm->getField('btn_submit');
				$btn_submit->setFieldTagAttribute('class', 'button button--fill button--green');
				echo $frm->getFormTag();?>
					<div class="cotainer container--fluid">
						<div class="span__row">
							<div class="span span--12">
								<div class="form-element no--margin-top">
									<div class="form-element__control">
										<label class="form-element__label"><?php echo $cc_number->getCaption()?></label>
										<?php echo $cc_number->getHtml(); ?>
									</div>
								</div>
							</div>
							<div class="span span--12">
								<div class="form-element no--margin-top">
									<div class="form-element__control">
										<label class="form-element__label"><?php echo $cc_owner->getCaption()?></label>
										<?php echo $cc_owner->getHtml(); ?>
									</div>
								</div>
							</div>
							<div class="span span--6">
								<div class="form-element no--margin-top">
									<div class="form-element__control">
										<label class="form-element__label"><?php echo Info::t_lang('EXPIRE_ON')?></label>
										<div class="container container--fluid">
											<div class="span__row">
												<div class="span span--6">
													<?php echo $cc_expire_date_month->getHtml(); ?>
												</div>
												<div class="span span--6">
													<?php echo $cc_expire_date_year->getHtml(); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="span span--6">
								<div class="form-element no--margin-top">
									<div class="form-element__control">
										<label class="form-element__label"><?php echo $cc_cvv->getCaption(); ?></label>
										<?php echo $cc_cvv->getHtml(); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="text--center">
						<div class="buttons_group">
							<input type="cancel" name="cancel" value="Cancel" class="button button--fill button--dark" onclick="window.location='<?php echo FatUtility::generateUrl('cart')?>'">
							
							<?php echo $btn_submit->getHtml()?>
							
						</div>
					</div>
				</form>
				<?php echo $frm->getExternalJS();
			}
			else
			{
			?>
				<div class=""><?php echo $error;?></div>
			<?php }?>
			<div id="ajax_message"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(function(){
        $('form[name="frmPaymentForm"]').bind('submit', function(event){
				event.preventDefault();
				var me=$(this);
				if ( me.data('requestRunning') ) {
					return;
				}
				var frm=this;
				el = $('#ajax_message');
				v = me.attr('validator');
				window[v].validate();
				if (!window[v].isValid()) return;
				me.data('requestRunning', true);
				showHtmlElementLoading(el);
				var data = getFrmData(frm);
				data += '&outmode=json&is_ajax_request=yes';
				callAjax(me.attr('action'), data, function(response){
					var json = parseJsonData(response);
					me.data('requestRunning', false);
					if (json['error']) {
						el.html('<div class="alert alert-danger">'+json['error']+'<div>');
					}
					if (json['redirect']) {
						$(location).attr("href",json['redirect']);
					}
				});
						
				
		
		return false;					
      });
    })
</script>
</body>
</head>