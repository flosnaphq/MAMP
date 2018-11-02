<?php if(!empty($arr_listing)) { ?>
<?php foreach($arr_listing as $acts){?>
<?php if(strtotime($acts['activity_end_date'])>= strtotime(Info::currentDate())){  
		  $status = "open";
	  }else{
		  $status = "close";
	  }
?>
<div class="activity-card <?php if($status =="open"){?>activity-card--open <?php } else { ?> activity-card--closed<?php } ?>">
	<div class="activity-card__image">
		<ul class="list list--vertical no--margin activity-card__float">
            
			<li>
				 <a href="<?php echo FatUtility::generateUrl('share','share-activity',array($acts['activity_id']))?>" class="float__icon float__icon--share modaal-ajax" >
				<svg class="icon icon--heart"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-share"></use></svg>
                </a>
			</li>
			<li>
				<a class="float__icon float__icon--heart <?php if(isset($acts['wishlist_activity_id']) && $acts['wishlist_activity_id']  !="") echo 'has--active'?>" onclick = 'wishlist(this,<?php echo $acts['activity_id']?>);' href="javascript:;">
				<svg class="icon icon--heart"><use xlink:href="#icon-heart"/></svg>
				</a>
			</li>
		</ul>
		<img src="<?php echo FatUtility::generateUrl('image','activity',array($acts['activity_image_id'],600,450))?>" alt="">
	</div>
	<div class="activity-card__content">
		<div class="activity-card__content-basic">
			<h5 class="activity-card__heading"><a href="<?php echo FatUtility::generateUrl('activity','detail',array($acts['activity_id']))?>" target="_BLANK" class=""><?php echo $acts['activity_name']?></a></h5>
			<h6 class="activity-card__cat"><a href="<?php echo FatUtility::generateUrl('services')?>"><?php echo $acts['parentservice_name']?> / <?php echo $acts['childservice_name']?></a></h6>
			<span class="activity-card__host"><?php echo Info::t_lang("HOST_BY")?><a href="<?php echo FatUtility::generateUrl('activity','host',array(Info::getSlugFromName($acts['user_firstname']),$acts['user_id']))?>"> <?php echo $acts['user_firstname']?></a></span>
			<p class="activity-card__desc"><?php echo Info::subContent($acts['activity_desc'],100)?>   <a href="<?php echo FatUtility::generateUrl('activity','detail',array($acts['activity_id']))?>" class="link"> <strong> <?php echo Info::t_lang('MORE');?> </strong></a></p>
		</div>
		<div class="activity-card__content-addition">
			<ul class="list list--vertical">
				<li>
					<span><?php echo Info::t_lang('MAX_PARTICIPANTS');?> : <?php echo $acts['activity_members_count']?></span>
				</li>
				<?php
				if(trim($acts['act_lang']) !=""){
				$lang = explode(',',$acts['act_lang']); ?>
				
				<?php if(!empty($lang)){ ?>
				<li class="hidden-on--mo">
					
					<strong><?php echo Info::t_lang('GOOD_FOR')?></strong>
					
					<ul class="list list--horizontal">
					
					<?php foreach($lang as $k){?>
					<li><img width="48" src='<?php echo FatUtility::generateUrl('image','flag',array($k,40,40));?>' /></li>
					<?php } ?>
					
					
						
					</ul>
				</li>
				
			<?php } ?>
			<?php }else{ ?>
				<li class="hidden-on--mo">
					
					<strong><?php echo Info::t_lang('GOOD_FOR')?></strong>
					
					<ul class="list list--horizontal">
						<li><img width="48" src='<?php echo FatUtility::generateUrl('image','flag',array(1,40,40));?>' /></li>
					</ul>
				</li>
				<?php } ?>
			</ul>                                                    
		</div>
	</div>
	<div class="activity-card__footer">
		<div class="activity-card__footer-inner">
			
			<?php if($status =="open"){  ?>
			
			
			<div class="activity-card__status status--open  a">
				<span class="activity-card__status-icon"><svg class="icon icon--check"><use xlink:href="#icon-check" /></svg></span>
				<span class="activity-card__status-text"><?php echo Info::t_lang('OPEN')?></span>
			</div>
			<?php } else { ?>
			<div class="activity-card__status status--closed">
				<span class="activity-card__status-icon"><svg class="icon icon--stop"><use xlink:href="#icon-stop" /></svg></span>
				<span class="activity-card__status-text"><?php echo Info::t_lang('CLOSED')?></span>
			</div>
			<?php } ?>
			<div class="activity-card__price">
				<span class="activity-card__price-number"><?php echo Currency::displayPrice($acts['activity_price']);?></span>
				<span class="activity-card__price-text">/<?php echo Info::activityTypeByKey($acts['activity_price_type']);?></span>
			</div>
		</div>
	</div>
</div>


<?php } ?>
<?php } 
elseif($page <= 1){
?>
<?php echo Helper::noRecord(Info::t_lang('NO_RECORDS'));
}
else{
	echo Info::t_lang('NO_MORE_RECORD');
}
if(!$more_record){
?>
<script>
	$(".showMoreButton").hide();
</script>
<?php }else{ ?>
<script>
	$(".showMoreButton").show();
</script>
<?php } ?>