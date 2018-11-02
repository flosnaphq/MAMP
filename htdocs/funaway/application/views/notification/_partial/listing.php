	<?PHP if(!empty($notifications)) {?>
 <div class="scrollable--x">
	
	<table class="table table--bordered table--responsive info-table">

		<tbody>
		<?php 
		foreach($notifications as $notify){
		?>
		<tr class="info <?php if($notify['notification_is_read'] == 0) echo 'unread';?>">
			<th class="info__details info__wrap" data-label="Activity">
			<?php if(!empty($notify['notification_url'])){ ?>
				<a href="<?php  echo $notify['notification_url']?>" target="_blank">
				<p class="regular-text"><?php echo $notify['notification_content'];?></p>
				
				</a>
			<?php }else{ ?>
				<p class="regular-text"><?php echo $notify['notification_content'];?></p>
			<?php } ?>
			</th>
			<td class="info__actions">
				<nav class="buttons__group" role="navigation">
                                    <a href="javascript:;" title="Delete" onclick="deleteNotification('<?php echo Info::t_lang('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS?')?>',<?php echo $notify['notification_id']?>)" class="button button--square button--small button--fill button--red thumb__delete"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg></a>
				
				</nav>
			</td>
			
			
		</tr>
		<?php 
		} 
		?>
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
	echo Helper::noRecord(Info::t_lang('NO_NOTIFICATIONS'));
	 } ?>