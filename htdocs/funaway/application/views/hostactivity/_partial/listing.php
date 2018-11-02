<?php if(!empty($arr_listing)){ ?>
 <table class="table table--bordered table--responsive activities-table">
	<tbody>
	<?php foreach($arr_listing as $activity){?>
	<tr class="activity">
		<td class="activity__details" data-label="Details">
			<h6 class="activity__heading"><?php echo $activity['activity_name']?></h6>
			<p class="regular-text"><?php echo Info::subContent($activity['activity_desc'],100)?></p>
		</td>
		<td class="activity__gallery" data-label="Gallery">
			<?php if($activity['activity_images'] !=""){ ?>
			<div class="gallery gallery--3">
				<?php $images = explode(',',$activity['activity_images']);
					foreach($images as $img){ 
				?>
				<a class="gallery__item"><img src="<?php echo FatUtility::generateUrl('image','activity',array($img,100,100))?>" alt=""></a>
			  <?php }?>
			</div>
			<?php }?>
		</td>
		<td class="activity__actions" data-label="Action">
			<nav class="buttons__group" role="navigation">
				<?php if($activity['activity_state'] < 2){ ?>
					<a href="javascript:;" class="button button--small button--label button--dark"><?php echo Info::t_lang("DRAFT");?>
					<span><svg class="icon icon--stop"><use xlink:href="#icon-stop"/></svg></span>
					</a>
			
				<?php }else if($activity['activity_confirm'] == 0){ ?>
					<a href="javascript:;" class="button button--small button--label button--dark"><?php echo Info::getActivityConfirmStatusByKey($activity['activity_confirm'])?>
					<span><svg class="icon icon--stop"><use xlink:href="#icon-stop"/></svg></span>
					</a>
				<?php } elseif($activity['activity_confirm'] == 1){ ?>
					<a href="javascript:;" class="button button--small button--label button--green"><?php echo Info::getActivityConfirmStatusByKey($activity['activity_confirm'])?>
					<span><svg class="icon icon--check"><use xlink:href="#icon-check"/></svg></span>
					</a>
				<?php } elseif($activity['activity_confirm'] == 2){ ?>
					<a href="javascript:;" class="button button--small button--label button--red"><?php echo Info::getActivityConfirmStatusByKey($activity['activity_confirm'])?>
					<span><svg class="icon icon--cross"><use xlink:href="#icon-cross"/></svg></span>
					</a>
				<?php } ?>
				<a href="<?php echo FatUtility::generateUrl('hostactivity','update',array($activity['activity_id']))?>" title="Edit"  class="button button--square button--small button--fill button--blue"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-edit"></use></svg></a>
				
				
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
                            <ul class="list list--horizontal no--margin-bottom ">
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
	echo Helper::noRecord(Info::t_lang('NO_ACTIVITY'));
}

?>
                          