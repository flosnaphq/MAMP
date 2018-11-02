<div class="span span--12">
	<?php if(!empty($arr_listing)){?>
    <div class="scrollable--x">
		<table class="table table--bordered table--responsive">
			<thead>
				<th><?php echo Info::t_lang('Sr. No.');?></th>
				<th><?php echo Info::t_lang('AMOUNT');?></th>
				<th><?php echo Info::t_lang('REQUEST_TIME');?></th>
				<th><?php echo Info::t_lang('STATUS');?></th>
				<th><?php echo Info::t_lang('ACTION');?></th>
			</thead>
			<tbody>
			<?php 
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach($arr_listing as $row){
			$sr_no++;
			?>
			<tr class="info">
				<td class="info__details" data-label="<?php echo Info::t_lang('Sr. No.');?>"><?php echo $sr_no;?></td>
				<td data-label="<?php echo Info::t_lang('AMOUNT');?>"><?php echo Currency::displayPrice($row['withdrawalrequest_amount'])?></td>
				
				<td data-label="<?php echo Info::t_lang('REQUEST_TIME');?>"><?php echo FatDate::format($row['withdrawalrequest_datetime'],true) ?></td>
				<td data-label="<?php echo Info::t_lang('STATUS');?>">
				<?php echo Info::getWithdrawalRequestStatusByKey($row['withdrawalrequest_status']); ?>
				</td>
				<td data-label="<?php echo Info::t_lang('ACTION');?>">
			
                                
                                                     <a href="<?php echo FatUtility::generateUrl('host','withdrawalRequestDetails',array($row['withdrawalrequest_id']))?>" class="button button--square button--fill button--dark button--small"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-view"></use></svg></a>
				</td>
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
	echo Helper::noRecord(Info::t_lang('NO_RECORD_FOUND'));
	 } ?>
</div>