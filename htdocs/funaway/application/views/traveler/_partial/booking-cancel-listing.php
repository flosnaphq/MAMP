<?php //Info::test($arr_listing); ?>
<div class="span span--12">
	<?php if(!empty($arr_listing)){
		
	?>
    <div class="scrollable--x">
		<table class="table table--bordered table--responsive">
			<thead>
				<th><?php echo Info::t_lang('BOOKING_ID');?></th>
				<th><?php echo Info::t_lang('ACTIVITY');?></th>
				<th><?php echo Info::t_lang('REQUEST_BY');?></th>
				<th><?php echo Info::t_lang('STATUS');?></th>
			</thead>
			<tbody>
			<?php foreach($arr_listing as $order){
				$user_type = Info::getUserTypeByKey($order['user_type']);
			?>
			<tr class="info">
				<th data-label="<?php echo Info::t_lang('BOOKING_ID');?>"><h6 class="info__heading"><?php echo $order['oactivity_booking_id']?></h6></th>
				<td data-label="<?php echo Info::t_lang('ACTIVITY');?>"><?php echo str_replace('[-]','<br>',$order['ordered'])?></td>
				<td data-label="<?php echo Info::t_lang('REQUEST_BY');?>"><?php echo $user_type;?></td>
				<td data-label="<?php echo Info::t_lang('STATUS');?>"><?php echo Info::getOrderCancelRequestStatusByKey($order['ordercancel_status']);?></td>
				
				
				
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php                         
	if($totalPage>1){
	?>
	
	
	 <nav class="pagination text--center">
                            
                                <ul class="list list--horizontal no--margin-bottom">
                                    <?php
	echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', 
	$totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, 
	'<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
	' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
	'<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>', 
	'<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>');
	?>
                                </ul>
                            </nav>
	
	<?php
	}}else{
	echo Helper::noRecord(Info::t_lang('NO_ORDERS'));
	 } ?>
</div>