<?php 
//$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'step1');
$frm->setFormTagAttribute('action', 'setup1');
$frm->setValidatorJsObjectName('step1Validator');
$frm->setFormTagAttribute('onsubmit', 'actionStep1(step1Validator); return(false);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>
<?php if(!empty($start_date_end_ins)){ ?>
<div id="date-instruction" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo html_entity_decode($start_date_end_ins['block_title'])?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo html_entity_decode($start_date_end_ins['block_content'])?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?php if(!empty($commission_chart)){ ?>
<div id="commission_rate" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('COMMISSION_CHART')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<table class="commission-chart table table--fixed table--bordered table--responsive info-table ">
					<thead>
					<tr>
						<th><?php echo Info::t_lang('LISTING_PRICE')?></th>
						<th><?php echo Info::t_lang('SITE_FEE')?></th>
					</tr>
					</thead>
					<tbody>
				<?php foreach($commission_chart as $comm){ ?>
					<tr>
						<td data-label="<?php echo Info::t_lang('LISTING_PRICE')?>"><?php echo Currency::displayPrice($comm['min_amount']);?>  <span> <?php echo Info::t_lang('-');?></span>  <?php echo ($comm['max_amount'] <= 0)?Info::t_lang('>'):Currency::displayPrice($comm['max_amount']);?></td>
						
						<td data-label="<?php echo Info::t_lang('SITE_FEE')?>"><?php echo $comm['commission_rate']; ?>%</td>
					</tr>
				<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<div id="status-info" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('STATUS')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<ul>
					<li><?php echo Info::t_lang("SELECT_ACTIVE_TO_SEE_LISTING")?></li>
					<li><?php echo Info::t_lang("SELECT_INACTIVE_IF_YOU_DON'T_WANT_TO_SEE_YOUR_LISTING_LIVE_ON_YET.")?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div id="booking-status-info" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('AVAILABLE_FOR_BOOKING')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<ul>
					<li><?php echo Info::t_lang("SELECT_YES_TO_ALLOW_BOOKINGS")?></li>
					<li><?php echo Info::t_lang("SELECT_NO_CLOSE_ALL_BOOKINGS.")?></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div id="activity-display-price" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('ACTIVITY_DISPLAY_PRICE_HELP_TITLE')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<p><?php echo Info::t_lang("ACTIVITY_DISPLAY_PRICE_HELP_TEXT")?>
				</p>
			</div>
		</div>
	</div>
</div>
