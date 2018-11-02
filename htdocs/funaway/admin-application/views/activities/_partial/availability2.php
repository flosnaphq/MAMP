<div class="sectionbody space">


<div class="d-calendar">
	<div style="display:none">
	<span class="current-mon"><?php echo $month?></span>
	<span class="current-yr"><?php echo $year?></span>
	</div>
	<header class="d-calendar__toolbar">
		<?php if($prev) { ?>
		<a href="javascript:;" onclick = 'prevMonth(<?php echo $year?>,<?php echo $month?>)' class="fl--left"><?php echo Info::t_lang("PREV");?></a>
		<?php } ?>
		<?php if($next) { ?>
		<a href="javascript:;" onclick = 'nextMonth(<?php echo $year?>,<?php echo $month?>)' class="fl--right"><?php echo Info::t_lang("NEXT");?></a>
		<?php } ?>
		<h6 class="d-calendar__heading text--center"><?php echo $year;?> <?php echo $showmonth;?> </h6>
	</header>
	<table class="d-calendar__view">
		<thead class="d-calendar__view__head">
			<tr>
				<th><span>Sun</span></th>
				<th><span>Mon</span></th>
				<th><span>Tue</span></th>
				<th><span>Wed</span></th>
				<th><span>Thu</span></th>
				<th><span>Fri</span></th>
				<th><span>Sat</span></th>
				
			</tr>
		</thead>
		<tbody class="d-calendar__view__body">
		
		<?php foreach($calendar as $k=>$cal){
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
									
									<?php foreach($cal['events'] as $event){ ?>
										<?php if($event['activityevent_anytime'] == 0){ ?>
										<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo date('H:i',strtotime($event['activityevent_time']))?></span></li>
										<?php }else{ 
											$addNew =false;
										?>
										<li><span class="time <?php if($event['activityevent_confirmation_requrired'] == 1){ echo "required"; } ?>"><?php echo Info::t_lang('FULL_DAY')?></span></li>
										<?php } ?>
									<?php } ?>	
								
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
	<footer class="d-calendar__toolbar">
		<?php if($prev) { ?>
		<a href="javascript:;" onclick = 'prevMonth(<?php echo $year?>,<?php echo $month?>)' class="fl--left"><?php echo Info::t_lang("PREV");?></a>
		<?php } ?>
		<?php if($next) { ?>
		<a href="javascript:;" onclick = 'nextMonth(<?php echo $year?>,<?php echo $month?>)' class="fl--right"><?php echo Info::t_lang("NEXT");?></a>
		<?php } ?>
		<h6 class="d-calendar__heading text--center"><?php echo $year;?> <?php echo $showmonth;?></h6>
	</footer>
</div>
</div>

