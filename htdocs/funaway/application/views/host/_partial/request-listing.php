<div class="span span--12">
<?php # Info::test($arr_listing);exit;?>
	<?php if(!empty($arr_listing)){?>
    <div class="scrollable--x">
		<table class="table table--bordered table--responsive">
			<thead>
				<th><?php echo Info::t_lang('ACTIVITY');?></th>
				<th><?php echo Info::t_lang('NUMBERS');?></th>
				<th><?php echo Info::t_lang('DATE');?></th>
				<th><?php echo Info::t_lang('ACTIVITY_BOOKED_DATE');?></th>
				<th><?php echo Info::t_lang('REQUESTED_BY');?></th>
				<th><?php echo Info::t_lang('STATUS');?></th>
				<th><?php echo Info::t_lang('ACTION');?></th>
			</thead>
			<tbody>
			<?php foreach($arr_listing as $acts){?>
			<tr class="info">
				<td class="info__details" data-label="<?php echo Info::t_lang('ACTIVITY');?>"><h6 class="info__heading"><?php echo $acts['activity_name']?></h6></td>
			    <td data-label="<?php echo Info::t_lang('NUMBERS');?>"><?php echo $acts['member']?></td>
				<td data-label="<?php echo Info::t_lang('DATE');?>"><?php echo FatDate::format($acts['requestevent_date']) ?></td>
				<td data-label="<?php echo Info::t_lang('ACTIVITY_BOOKED_DATE');?>">
				<?php echo ($acts['activityevent_anytime'])?FatDate::format($acts['activityevent_time']):FatDate::format($acts['activityevent_time'],true);?> </td>
				<td data-label="<?php echo Info::t_lang('REQUESTED_BY');?>"><?php echo $acts['traveler_name']?></td>
				<td data-label="<?php echo Info::t_lang('STATUS');?>">
				<?php 
					$status = Info::getRequestStatus();
					$attr = '';
					if(1 == $acts['requestevent_is_order']) {
						echo Info::t_lang('Ordered');
					} else {
				?>	
					<select rel = "<?php echo $acts['requestevent_status']?>" onchange = updateStatus(this,<?php echo $acts['requestevent_id']?>)>
						<?php foreach($status as $k=>$v){?>
							<option value='<?php echo $k?>' <?php if($acts['requestevent_status'] == $k) echo 'selected'?>>
									<?php echo $v?>
							</option>
						<?php } ?>
					</select>
					<?php } ?>
			    </td>
				
				<td data-label="<?php echo Info::t_lang('ACTION');?>">
                                     <a href="<?php echo FatUtility::generateUrl('activity','detail',array($acts['activity_id']))?>" class="button button--square button--fill button--dark button--small"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-view"></use></svg></a>
				
				
				
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