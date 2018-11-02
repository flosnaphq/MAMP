<?php
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--vertical form--default');
$frm->setFormTagAttribute('id', 'omiseForm');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('OmisePay','charge'));
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

?>
<main id="MAIN" class="site-main">
	<div class="site-main__body">
		<section class="section section--light payment__section" id="cart">
			 <div class="section__body">
				 <div class="container container--static">
					 <div class="span__row">
						 <div class="span span--7 span--center">
							<h5 class="heading-text text--center"><?php echo Info::t_lang('PAYMENT')?></h5>
							<h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('KINDLY_INPUT_YOUR_CREDIT_CARD_DETAILS')?></h6>
							<div class="block summary__block clearfix">
								 <h6 class="block__heading-text"><small><?php echo Info::t_lang('ORDER_SUMMARY')?></small></h6>
								 <div class="clearfix summary__sub">
									<span class="fl--left"><?php echo Info::t_lang('SUB_TOTAL')?></span>
									<span class="fl--right"><?php echo Currency::displayPrice($sub_total)?></span>
								 </div>
							
								 <h6 class="block__heading-text summary__total">
									<span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYABLE')?></span>
									<span class="fl--right"><?php echo Currency::displayPrice($total)?></span>
								 </h6>
							</div>
							<hr>
							<div class="payment-card">
							
							<?php echo $frm->getFormTag();?>
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
										<?php if(!empty($attributes)){ 
										foreach($attributes as $attr_id=>$attr){
										
										?>
											<div class="span span--12">
												<div class="form-element no--margin-top">
													<div class="form-element__control">
														<label class="checkbox">
														<input checked disabled type="checkbox" checked="checked" value="1" name="attr[<?php echo $attr_id; ?>]"  title="<?php echo $attr['details']['caption']?>" onchange="selectAttr(this)" >
														<span class="checkbox__icon"></span>
														<span class="checkbox__label"><?php echo $attr['details']['caption']?></span>
														</label>
														<?php 
														if($attr['details']['file_required'] == 1){
														foreach($attr['activities'] as $acts){ ?>
															<a class="link" target="_blank" href="<?php echo FatUtility::generateUrl('image','attribute',array($attr_id, $acts['activity_id']))?>" title="<?php echo $acts['name']?>">
															<?php echo $acts['name'].' ( '.$acts['file_name'].' )'?>
															</a>
														<?php } ?>
														<?php } ?>
													</div>
												</div>
											</div>
										<?php } ?>
										<?php } ?>
									</div>
								</div>
								<div class="text--center">
									<p>
										<span class="search__icon">
											<svg class="icon icon--check">
												<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-check"></use>
											</svg>
										</span>
										<span><?php echo Info::t_lang('PAYMENT_PROCESSED_BY_STRIPE')?></span>
									</p>
									<div class="buttons_group">
										<input type="cancel" name="cancel" value="Cancel" class="button button--fill button--dark" onclick="window.location='<?php echo FatUtility::generateUrl('cart')?>'">
										
										<?php echo $btn_submit->getHtml()?>
										
									</div>
								</div>
							</form>
							<?php echo $frm->getExternalJS();?>
							</div>
						 </div>
					 </div>
				 </div>
			</div>
		</section>
	</div>
</main>
<script>
<?php echo TrackingCode::getTrackingCode(5);?>
</script>
