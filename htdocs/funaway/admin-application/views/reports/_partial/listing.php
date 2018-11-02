<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

?>
<table class="table table-responsive" width="100%">
	<thead>
		
			<th><?php echo $report_heading;?></th>
			<th><?php echo Info::t_lang('CANCELLED');?></th>
			<th><?php echo Info::t_lang('CANCELLED_AMOUNT');?></th>
			
			<th><?php echo Info::t_lang('TOTAL_BOOKINGS');?></th>
			<th><?php echo Info::t_lang('TOTAL_AMOUNT');?></th>
			
		
	</thead>
	<tbody>
	<?php 
	$cancelled =0;
	$cancelled_amount =0;
	$total_net_records =0;
	$total_net_amount =0;
	$total_donation =0;
	$total_fee =0;
	foreach($arr_listing as $key => $row){
		$cancelled += $row['total_cancelled_booking'];
		$cancelled_amount += $row['total_cancelled_booking_amount'];
		$total_net_records += $row['total_net_records'];
		$total_net_amount += $row['total_net_amount'];
	
	?>
	<tr class="info">
		
		<td data-label="<?php echo $report_heading;?>"><?php echo $row['report_key']; ?></td>
		<td data-label="<?php echo Info::t_lang('CANCELLED');?>"><?php echo $row['total_cancelled_booking']; ?></td>
		<td data-label="<?php echo Info::t_lang('CANCELLED_AMOUNT');?>"><?php echo Currency::displayPrice($row['total_cancelled_booking_amount']); ?></td>
	
		<td data-label="<?php echo Info::t_lang('TOTAL_BOOKINGS');?>"><?php echo $row['total_net_records']; ?></td>
		<td data-label="<?php echo Info::t_lang('TOTAL_AMOUNT');?>"><?php echo Currency::displayPrice($row['total_net_amount']); ?></td>
		
	</tr>
	<?php } ?>
	<tr class="info">
		
		<th style="background-color:#ddd" data-label="<?php echo Info::t_lang('TOTALS');?>"><?php echo Info::t_lang('TOTALS'); ?></th>
		<th style="background-color:#ddd"  data-label="<?php echo Info::t_lang('TOTAL_CANCELLED');?>"><?php echo $cancelled; ?></td>
		<th style="background-color:#ddd"  data-label="<?php echo Info::t_lang('TOTAL_CANCELLED_AMOUNT');?>"><?php echo Currency::displayPrice($cancelled_amount); ?></th>
		
		
		<th style="background-color:#ddd"  data-label="<?php echo Info::t_lang('TOTAL_BOOKINGS');?>"><?php echo $total_net_records; ?></th>
		<th style="background-color:#ddd"  data-label="<?php echo Info::t_lang('TOTAL_AMOUNT');?>"><?php echo Currency::displayPrice($total_net_amount); ?></th>
		
	</tr>
	</tbody>
</table>




