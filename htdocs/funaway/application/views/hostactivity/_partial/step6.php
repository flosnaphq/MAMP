<?php

?>
<form id = "time-slot-form" method = 'post' onsubmit = 'actionStep6();return false;' action = '<?php echo FatUtility::generateUrl('hostactivity','setup6')?>' class="form form--default form--horizontal">
	<h2 class="block-heading-text"><?php echo Info::t_lang('UPDATE_YOUR_AVAILABILITY_FOR_SELECTED_MONTH')?></h2>
	<!--<h6 class="regular-text regular-text--large">Availability And Confirmation Requrirements...</h6> -->
	<div class="form-element">
		<label class="form-element__label"> <?php echo Info::t_lang('SERVICE')?> <a href="#available-instruction" class="available-instruction-popup"><svg class="icon icon--info   "><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use>
                           </svg></a></label>
		
	
		<div class="form-element__control">
			<label class="radio">
				<input type="radio" class="service-type" onchange="serviceChange();" name= 'service_type' value = "1" > 
				<span class="radio__icon"></span>
				<span class="radio__label"><?php echo Info::t_lang('YES')?></span>
			</label>
			<label class="radio">
				<input type="radio" class="service-type" onchange="serviceChange();" name= 'service_type' value = "0" checked>  
				<span class="radio__icon"></span>
				<span class="radio__label"><?php echo Info::t_lang('NO')?></span>
			</label>
			
		</div>
		
	</div>
	<div class="form-element">
		<label class="form-element__label"><?php echo Info::t_lang('PRIOR_CONFIRMATION_REQUIRED')?>
			<a href="#prior-instruction" class="prior-instruction-popup">
				<svg class="icon icon--info   ">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use>
				</svg>
			</a>
		</label>
		<div class="form-element__control">
			<label class="radio">
				<input type="radio" class="confirm-type"  name= 'confirm_type' value = "1" > 
				<span class="radio__icon"></span>
				<span class="radio__label"><?php echo Info::t_lang('YES')?></span>
			</label>
			<label class="radio">
				<input type="radio" class="confirm-type" name= 'confirm_type' value = "0" checked>  
				<span class="radio__icon"></span>
				<span class="radio__label" ><?php echo Info::t_lang('NO')?></span>
			</label>
		</div>
	</div>
	
	<!--h3 class="block-heading-text block-heading-text--small"> Bulk Entries... </h3-->
	<div class="form-element">
		<label class="form-element__label"><?php echo Info::t_lang('BULK_ENRIES')?>  	<a href="#bulk-entry-instruction" class="bulk-entry-instruction-popup"><svg class="icon icon--info   "><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-info"></use>
                           </svg></a></label>
		<div class="form-element__control">
			<select name = 'entry_type' class="entry-type" onchange = "entryOption(this)" >
				<option value=""> <?php echo Info::t_lang('SELECT_OPTION')?></option>
				<option value="1"> <?php echo Info::t_lang('DAILY')?> </option>
				<option value="2"> <?php echo Info::t_lang('WEEKLY')?> </option>
			</select>
		
		</div>
		
	</div>
	<div class="form-element" style="display:none;" id="week-slot">
		<label class="form-element__label">Week Days :: </label>
		<div class="form-element__control">
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[1]' value = "1"> 
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Monday</span>
			</label>
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[2]' value = "2">  
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Tuesday</span>
			</label>
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[3]' value = "3">  
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Wednesday</span>
			</label>
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[4]' value = "4"> 
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Thursday</span>
			</label>
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[5]' value = "5"> 
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Friday</span>
			</label>
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[6]' value = "6"> 
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Saturday</span>
			</label>
			<label class="checkbox">
				<input type="checkbox" class="weekdays" name= 'weekdays[7]' value = "0"> 
				<span class="checkbox__icon"></span>
				<span class="checkbox__label">Sunday</span>
			</label>
		</div>
	</div>
	<div class="form-element" style="display:none;" id="time-slot">
		<label class="form-element__label">Time Slot :: </label>
		<div class="form-element__control">
			<div class="container container--fluid">
				<div class="span__row slots--1">
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
					
				</div>
			</div>
			<div class="buttons__group"  style="margin-top:0.625em;">
				<a href = "javascript:;" onclick ="addMoreTimeSlot()" class="button button--small button--fill button--blue">More Slot</a>
				<input type = "submit" name = "submit" value="Add" class="button button--small button--fill button--green">
			</div>
			
			
		</div>
		
	</div>
	<div class="form-element" style="display:none;" id="notime-slot">
		<div class="buttons__group"   style="margin-top:0.625em;">
				<input type = "submit" name = "submit" value="Add" class="button button--small button--fill button--green">
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
			
				<td class="d-calendar__date <?php  echo $cal['class']?> have-event--<?php echo count($cal['events']);?>">
						<span class="d-calendar__digit "><?php echo $cal['date']?></span>
							
							
							 <div class="d-calendar__action">
							 
								<ul class="list list--vertical ">
								<?php 
									$addNew = true;
									if(!empty($cal['events'])){
								?>
									
									<?php 
									
									foreach($cal['events'] as $event){ ?>
										<?php if($event['activityevent_anytime'] == 0){ ?>
												<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo date('H:i',strtotime($event['activityevent_time']))?>
												
												<a href="<?php echo FatUtility::generateUrl('hostactivity','editEvent',array($event['activityevent_id']))?>" class="time__edit modaal-ajax"  >EDIT</a> <a href="javascript:;" class="time__del" onclick="deleteEvent(this,<?php echo $event['activityevent_id']?>)">x</a></span></li>
												 
										<?php }
											else{ 
												$addNew =false;
												?>
												<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo Info::t_lang('FULL_DAY')?><a href="<?php echo FatUtility::generateUrl('hostactivity','editEvent',array($event['activityevent_id']))?>" class="time__edit modaal-ajax" >EDIT</a><a href="javascript:;" class="time__del" onclick="deleteEvent(this,<?php echo $event['activityevent_id']?>)">x</a></span></li>
												<?php  ?>
												
										<?php } ?>
									<?php } ?>	
								
								<?php } ?>
								<?php if($addNew){ ?>
								<li><a href="<?php echo FatUtility::generateUrl('hostactivity','new-event',array(urlencode($cal['fulldate'])))?>" class="add modaal-ajax">Add New</a></li>
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
	</footer>
</div>
<input type="button" value="<?php echo Info::t_lang("PREV_STEP");?>" name="button" data-fatreq="{&quot;required&quot;:false}" title="" onclick="step5();" class="button button--small button--fill button--dark fl--left" style="margin-top:1.25em;">	
<input type="button" value="<?php echo Info::t_lang("NEXT_STEP");?>" name="button" data-fatreq="{&quot;required&quot;:false}" title="" onclick="step7();" class="button button--small button--fill button--dark fl--right" style="margin-top:1.25em;">

<?php if(!empty($prior_ins)){ ?>
<div id="prior-instruction" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo html_entity_decode($prior_ins['block_title'])?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo html_entity_decode($prior_ins['block_content'])?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?php if(!empty($available_ins)){ ?>
<div id="available-instruction" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo html_entity_decode($available_ins['block_title'])?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo html_entity_decode($available_ins['block_content'])?>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<?php if(!empty($bulk_entry_ins)){ ?>
<div id="bulk-entry-instruction" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo html_entity_decode($bulk_entry_ins['block_title'])?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo html_entity_decode($bulk_entry_ins['block_content'])?>
			</div>
		</div>
	</div>
</div>
<?php } ?>