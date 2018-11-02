<?php

?>
<div class="sectionbody space">
<form id = "time-slot-form" method = 'post' onsubmit = 'actionStep6(); return false;' action = '<?php echo FatUtility::generateUrl('activities','setup6')?>' class="web_form">
	<h2 class="block-heading-text"><?php echo Info::t_lang('UPDATE_YOUR_AVAILABILITY_FOR_SELECTED_MONTH')?></h2>
	<!--<h6 class="regular-text regular-text--large">Availability And Confirmation Requrirements...</h6> -->
	<div class="row">
	<div class="col-4">
		<div class="field-set">
			<div class="caption-wraper"><label class="field_label"><?php echo Info::t_lang('SERVICE')?></label></div>
			<div class="field-wraper">
				<div class="field_cover">
				<ul class="list list--1 list--horizontal">
					<li>
					<label><span class="radio">
					<input type="radio" class="service-type" onchange="serviceChange(this.value);" name= 'service_type' value = "1" ><i class="input-helper"></i></span><?php echo Info::t_lang('YES')?></label>
					</li>
					<li><label><span class="radio">
					<input type="radio" class="service-type" onchange="serviceChange(this.value);" name= 'service_type' value = "0" checked><i class="input-helper"></i></span><?php echo Info::t_lang('NO')?></label></li>
				</ul>
			</div>
		</div>
	</div>
	</div>
	
	
	<div class="col-4">
		<div class="field-set">
			<div class="caption-wraper"><label class="field_label">
				<?php echo Info::t_lang('PRIOR_CONFIRMATION_REQUIRED') ?>
				</label></div>
			<div class="field-wraper">
				<div class="field_cover">
				<ul class="list list--1 list--horizontal">
					<li>
					<label><span class="radio">
					<input type="radio" class="confirm-type"  name= 'confirm_type' value = "1" ><i class="input-helper"></i></span><?php echo Info::t_lang('YES')?></label>
					</li>
					<li><label><span class="radio">
					<input type="radio" class="confirm-type"  name= 'confirm_type' value = "0" checked><i class="input-helper"></i></span><?php echo Info::t_lang('NO')?></label></li>
				</ul>
			</div>
		</div>
	</div>
	</div>
	
	<div class="col-4">
		<div class="field-set">
			<div class="caption-wraper">
				<label class="field_label"><?php echo Info::t_lang('BULK_ENRIES')?></label>
			</div>
			<div class="field-wraper">
				<div class="field_cover">
					<select name = 'entry_type' class="entry-type" onchange = "entryOption(this.value)">
				
					<option value="1"> <?php echo Info::t_lang('DAILY')?> </option>
					<option value="2" checked> <?php echo Info::t_lang('WEEKLY')?> </option>
					</select>
				</div>
				</div>
			</div>
	</div>
	</div>
	<div class="row"  style="display:none;" id="week-slot">
		<div class="col-12">
			<div class="field-set">
				<div class="caption-wraper">
				<label class="field_label"><?php echo Info::t_lang('WEEK_DAYS'); ?></label>
				</div>
				<div class="field-wraper">
					<div class="field_cover">
						<ul class="three-col">
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[1]' value = "1">
										
										<i class="input-helper"></i>
									</span>
									Monday
								</label>
								<input type="text" class="" name= 'traveller[1]'>
							</li>
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[2]' value = "2">
										<i class="input-helper"></i>
									</span>
									Tuesday
								</label>
							</li>
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[3]' value = "3">
										<i class="input-helper"></i>
									</span>
									Wednesday
								</label>
							</li>
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[4]' value = "4">
										<i class="input-helper"></i>
									</span>
									Thursday
								</label>
							</li>
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[5]' value = "5">
										<i class="input-helper"></i>
									</span>
									Friday
								</label>
							</li>
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[6]' value = "6">
										<i class="input-helper"></i>
									</span>
									Saturday
								</label>
							</li>
							<li>
								<label>
									<span class="checkbox">
										<input type="checkbox" class="weekdays" name= 'weekdays[0]' value = "0">
										<i class="input-helper"></i>
									</span>
									Sunday
								</label>
							</li>
							
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row time-slot-wrapper" style="display:none">
	<div class="col-12 time-slot">
		<div class="field-set time-slot-in" >
			<div class="caption-wraper">
				<label class="field_label"><?php echo Info::t_lang('Time Slot ::')?></label>
			</div>
			<div class="field-wraper">
				<div class="field_cover">
					<select name='hour_slot[]'>
					<?php foreach(Info::hours() as $k=>$v){ ?>
						<option value="<?php echo $k?>"> <?php echo $v?> </option>
					<?php } ?>
					</select>
					<select name = 'minute_slot[]'>
						<?php foreach(Info::minutes() as $k=>$v){ ?>
							<option value="<?php echo $k?>"> <?php echo $v?> </option>
						<?php } ?>
					</select>
					<input name = "travellers[]" type = "text" value = "">
				</div>
				</div>
		</div>
	</div>
	</div>
	
	<div class="row time-slot-wrapper-add-more" style="display:none">
	<div class="col-12">
		<div class="field-set">
			
			<div class="field-wraper">
				<div class="field_cover">
					<input type="button" class="themebtn btn-grey" value="Remove Slot" style="display:none" id="remove-slot" onclick ="removeTimeSlot()" name="btn_submit" >
					<input type="button" value="More Slot" onclick ="addMoreTimeSlot()" name="btn_submit" >
					
					
				</div>
			</div>
		</div>
	</div>
	</div>

	<div class="row"  >
	<div class="col-12">
		<div class="field-set">
			
			<div class="field-wraper">
				<div class="field_cover">
					
					<input type="submit" value="Add" name="btn_submit" >
				</div>
			</div>
		</div>
	</div>
	</div>
	
	
	
		
</form>
<div class = 'time-slot-section' style="display:none;">
	<div class="span__row slots" style="margin-top:0.625em;">
		<div class="span span--5">
			<select name = 'hour_slot[]'>
				<?php foreach(Info::hours() as $k=>$v){ ?>
					<option value="<?php echo $k?>"> <?php echo $v?> </option>
				<?php } ?>
			</select>
		</div>
		<div class="span span--5">
			<select name = 'minute_slot[]'>
				<?php foreach(Info::minutes() as $k=>$v){ ?>
					<option value="<?php echo $k?>"> <?php echo $v?> </option>
				<?php } ?>
			</select>
		</div>
		<div class="span span--2 text--right">
			<button class="s-button remove-slot">
				<svg class="icon icon--cross"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg>
			</button>
		</div>
	</div>
</div>
<div class="d-calendar">
	<div style="display:none">
	<span class="current-mon"><?php echo $month?></span>
	<span class="current-yr"><?php echo $year?></span>
	</div>
	<header class="d-calendar__toolbar">
		<?php if($prev) { ?>
			<a href="javascript:;" onclick = 'prevMonth(<?php echo $year?>,<?php echo $month?>)' class=""><?php echo Info::t_lang("PREV");?></a>
		<?php }else{ ?>
			<a href="javascript:;" style="opacity:0;"><?php echo Info::t_lang("PREV");?></a>
		<?php } ?>
		<h6 class="d-calendar__heading text--center"><?php echo $year;?> <?php echo $showmonth;?></h6>
		<?php if($next) { ?>
			<a href="javascript:;" onclick = 'nextMonth(<?php echo $year?>,<?php echo $month?>)' class=""><?php echo Info::t_lang("NEXT");?></a>
		<?php }else{ ?>
			<a href="javascript:;" style="opacity:0;"><?php echo Info::t_lang("NEXT");?></a>
		<?php } ?>
		<a href="javascript:;" onclick="cleareMonthRecord()" class="button button--small button--fill button--red d-calendar__toolbar__del" style="font-size:0.65em;"><?php echo Info::t_lang('DELETE_ALL')?></a>
		
	</header>
<div class="scrollable--x">
	<table width="100%" class="d-calendar__view">
		<thead class="d-calendar__view__head">
			<tr>
				<th><span><?php echo Info::t_lang('SUN')?></span></th>
				<th><span><?php echo Info::t_lang('MON')?></span></th>
				<th><span><?php echo Info::t_lang('TUE')?></span></th>
				<th><span><?php echo Info::t_lang('WED')?></span></th>
				<th><span><?php echo Info::t_lang('THU')?></span></th>
				<th><span><?php echo Info::t_lang('FRI')?></span></th>
				<th><span><?php echo Info::t_lang('SAT')?></span></th>
				
            </tr>
		</thead>
		<tbody class="d-calendar__view__body">
		
		<?php
		
		foreach($calendar as $k=>$cal){
			if($k % 7 == 0){ echo '<tr class="d-calendar__week">';}	
			?>
			
				<td class="d-calendar__date <?php  echo $cal['class']?>">
						<span class="d-calendar__digit"><?php echo $cal['date']?></span>
							
							
							 <div class="d-calendar__action">
							 
								<ul class="list list--vertical">
								<?php 
									$addNew = true;
									if(!empty($cal['events'])){
								?>
									
									<?php 
									
									foreach($cal['events'] as $event){ ?>
										<?php if($event['activityevent_anytime'] == 0){ ?>
												<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo date('H:i',strtotime($event['activityevent_time']))?>
                                                                                                        <?php if($canEdit){?>
     <a href="javascript:;popupView('<?php echo FatUtility::generateUrl('activities','editEvent',array($event['activityevent_id']))?>')" class="time__edit modaal-ajax"  >EDIT</a><a href="javascript:;" class="time__del" onclick="deleteEvent(this,<?php echo $event['activityevent_id']?>)">x</a></span>
                                                                                                        <?php }?>
                                                                                                </li>
												<?php /* ?><li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo date('H:i',strtotime($event['activityevent_time']))?><a href="javascript:;" class="time__del" onclick="deleteEvent(this,<?php echo $event['activityevent_id']?>)">x</a></span></li> <?php  */?>
										<?php }
											else{ 
												$addNew =false;
												?>
												<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo Info::t_lang('FULL_DAY')?>
                     <?php if($canEdit){?>                                                                                         <a href="javascript:;popupView('<?php echo FatUtility::generateUrl('activities','editEvent',array($event['activityevent_id']))?>')" class="time__edit modaal-ajax" >EDIT</a><a href="javascript:;" class="time__del" onclick="deleteEvent(this,<?php echo $event['activityevent_id']?>)">x</a>
                         <?php }?>
                                                                                                    </span></li>
												<?php /* ?>
												<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo Info::t_lang('FULL_DAY')?><a href="javascript:;" class="time__del" onclick="deleteEvent(this,<?php echo $event['activityevent_id']?>)">x</a></span></li> <?php */?>
										<?php } ?>
									<?php } ?>	
								
								<?php } ?>
								<?php if($addNew && $canEdit){ ?>
								<li><a href="javascript:;popupView('<?php echo FatUtility::generateUrl('activities','new-event',array(urlencode($cal['fulldate'])))?>')" class="add modaal-ajax">Add New</a></li>
								<?php } ?>
								
							
								</ul>
								
							</div>
				</td>			
							
				
				
			<?php 
			if($k % 7 == 6){ echo '</tr>';}
			}	
			?>
		   
			
		</tbody>
	</table>
</div>
	<footer class="d-calendar__toolbar">
		<?php if($prev) { ?>
		<a href="javascript:;" onclick = 'prevMonth(<?php echo $year?>,<?php echo $month?>)' class="fl--left"><i class="btn-prev ion-android-arrow-back"></i></a>
		<?php } ?>
		<?php if($next) { ?>
		<a href="javascript:;" onclick = 'nextMonth(<?php echo $year?>,<?php echo $month?>)' class="fl--right"><i class="ion-android-arrow-forward"></i></a>
		<?php } ?>
		<h6 class="d-calendar__heading text--center"><?php echo $year;?> <?php echo $showmonth;?></h6>
	</footer>
</div>
</div>
	
