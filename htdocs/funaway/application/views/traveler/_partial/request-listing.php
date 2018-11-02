<div class="span span--12">
<?php # Info::test($arr_listing);exit;?>
	<?php if(!empty($arr_listing)){?>
    <div class="scrollable--x">
		<table class="table table--bordered table--responsive">
			<thead>
				
				<th><?php echo Info::t_lang('ACTIVITY');?></th>
				<th><?php echo Info::t_lang('UNITS');?></th>
				<th><?php echo Info::t_lang('DATE');?></th>
				<th><?php echo Info::t_lang('STATUS');?></th>
				<th><?php echo Info::t_lang('ACTION');?></th>
			</thead>
			<tbody>
			<?php foreach($arr_listing as $acts){?>
			<tr class="info">
				<td class="info__details" data-label="<?php echo Info::t_lang('ACTIVITY');?>"><h6 class="info__heading"><?php echo $acts['activity_name']?></h6></td>
			    <td data-label="<?php echo Info::t_lang('UNITS');?>"><?php echo $acts['requestevent_members']?></td>
				<td data-label="<?php echo Info::t_lang('DATE');?>"><?php echo FatDate::format($acts['requestevent_date']) ?></td>
				<td data-label="<?php echo Info::t_lang('STATUS');?>"><?php echo Info::getRequestStatusByKey($acts['requestevent_status'])?></td>
				
				<td data-label="<?php echo Info::t_lang('ACTION');?>">
				<a class="button button--fill button--red button--small" href="<?php echo FatUtility::generateUrl('activity','detail',array($acts['activity_id']))?>"><?php echo Info::t_lang('Detail')?></a>
				<?php if($acts['requestevent_is_order'] == 1){ ?>
					<a class="button button--fill button--red button--small" href="<?php echo FatUtility::generateUrl('traveler','request-detail',array($acts['requestevent_id']))?>"><?php echo Info::t_lang('ORDER_DETAIL')?></a>
				<?php }else{ ?>
				
				<?php if($acts['requestevent_status'] == 1){ ?>
				<a class="button button--fill button--red button--small" href="javascript:;" onclick = "addRequestToCart(<?php echo $acts['requestevent_id']?>)" ><?php echo Info::t_lang('ADD_TO_CART')?></a>
				<?php } ?>
				<?php } ?>
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
	echo Helper::noRecord(Info::t_lang('NO_ORDERS'));
	 } ?>
</div>