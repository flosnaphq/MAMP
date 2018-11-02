<div class="span span--12">
	<?php if(!empty($arr_listing)){ ?>
    <div class="scrollable--x">
		<table class="table table--bordered table--responsive ">
			<thead>
				<th><?php echo Info::t_lang('No');?></th>
				<th><?php echo Info::t_lang('DATE');?></th>
				<th><?php echo Info::t_lang('DEBIT');?></th>
				<th><?php echo Info::t_lang('CREDIT');?></th>
				<!--<th><?php //echo Info::t_lang('BALANCE');?></th>-->
				<th><?php echo Info::t_lang('DETAILS');?></th>
				<th><?php echo Info::t_lang('ACTION');?></th>
			</thead>
			<tbody>
			<?php 
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach($arr_listing as $wallet){
			$sr_no++;
			?>
			<tr class="info">
				
				<td class="info__details" data-label="<?php echo Info::t_lang('Sr. No.');?>"><?php echo $sr_no;?></td>
				<td class="info__details" data-label="<?php echo Info::t_lang('DATE');?>"><?php echo FatDate::format($wallet['wtran_date']);?></td>
				<td data-label="<?php echo Info::t_lang('DEBIT');?>"><?php echo $wallet['wtran_amount'] < 0?Currency::displayPrice(abs($wallet['wtran_amount'])):'--'?></td>
				<td data-label="<?php echo Info::t_lang('CREDIT');?>"><?php echo $wallet['wtran_amount'] >= 0?Currency::displayPrice(abs($wallet['wtran_amount'])):'--'?></td>
				<!-- <td data-label="<?php echo Info::t_lang('BALANCE');?>"> ---------- </td> -->
				<td data-label="<?php echo Info::t_lang('DETAILS');?>"><?php echo $wallet['wtran_desc']?></td>
				<td data-label="<?php echo Info::t_lang('ACTION');?>">
				<?php if($wallet['wtran_booking_id'] != ""){ ?>
				
                                        <a href="<?php echo FatUtility::generateUrl('host','booking-detail',array($wallet['wtran_booking_id']))?>" class="button button--square button--fill button--dark button--small"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-view"></use></svg></a>
				<?php } ?>
				</td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php   
echo FatUtility::createHiddenFormFromData ( $postedData, array (
		'name' => 'frmUserSearchPaging','id'=>'pretend_search_form' 
) );	
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