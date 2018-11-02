<div id="calendar-page">
	<div class="control-nav">
		<a class="prev" data-ng-click="ctrl.prev()">prev</a> 
		<span class="monthname ng-binding">June 2016</span>
		<a class="next" data-ng-click="ctrl.next()">next</a>
	</div>
	<table class="calendar-tble">
		<tbody>
			<tr>
				<th>Sun</th>
				<th>Mon</th>
				<th>Tue</th>
				<th>Wed</th>
				<th>Thu</th>
				<th>Fri</th>
				<th>Sat</th>
			</tr>
			<?php foreach($calendar as $k=>$cal){
			if($k % 7 == 0){ echo '<tr>';}	
			?>
			
				<td>
					<div class="cl-content othermonth">
						<div class="cl-day-num <?php  echo $cal['class']?>"><?php echo $cal['date']?></div>
							<?php if(!empty($cal['events'])){?>
							<ul class="task-list">
								<?php foreach($events as $event){?>
									<li> <?php echo $event['activityevent_time']?></li>
								<?php } ?>
							</ul>
							<?php } ?>
					</div>
				</td>
			<?php 
			if($k % 7 == 6){ echo '</tr>';}
			}	
			?>

		</tbody>
	</table>
</div>