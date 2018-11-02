<?php if(!empty($arr_listing)){ ?>
 <table class="table table--bordered table--responsive activities-table">
	<tbody>
	<?php foreach($arr_listing as $activity){?>
	<tr class="activity">
		<td class="activity__details" data-label="Details">
			<h6 class="activity__heading"><a href="<?php echo FatUtility::generateUrl('activity','detail',array($activity['activity_id']))?>"><?php echo $activity['activity_name']?></h6>
			<p class="regular-text"><?php echo Info::subContent($activity['activity_desc'],100)?></p>
		</td>
		<td class="activity__gallery" data-label="Gallery">
			<?php if($activity['activity_images'] !=""){ ?>
			<div class="gallery gallery--3">
				<?php $images = explode(',',$activity['activity_images']);
					foreach($images as $img){ 
				?>
				<a href="javascript:;" class="gallery__item"><img src="<?php echo FatUtility::generateUrl('image','activity',array($img,100,100))?>" alt=""></a>
			  <?php }?>
			</div>
			<?php }?>
		</td>
		<td class="activity__actions" data-label="Action">
			<nav class="buttons__group" role="navigation">
				<?php if(User::getLoggedUserId() == $activity['activity_user_id']){ ?>
				<a href="<?php echo FatUtility::generateUrl('hostactivity','update',array($activity['activity_id']))?>" class="button button--fill button--blue"><?php echo Info::t_lang('UPDATE')?></a>
				<?php } ?>
                                <a href="javascript:;" title="Delete" onclick="deleteActivity('<?php echo Info::t_lang('DO_YOU_WANT_TO_DELETE?')?>','<?php echo $activity['activity_id']?>')" title="<?php echo Info::t_lang('DELETE_FROM_WISHLIST')?>" class="button button--square button--small button--fill button--red thumb__delete"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg></a>

			</nav>
		</td>
	</tr>
	<?php } ?>
	</tbody>
 </table>
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
}	
}
else{
	echo Helper::noRecord(Info::t_lang('NO_RECORD_FOUND'));
}

?>
                          