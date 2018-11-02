<?php 	$link = FatUtility::generateFullUrl('activity','detail',array($activity['activity_id']));

                $pic = FatCache::getCachedUrl(FatUtility::generateFullUrl('Image', 'activity', array($activity['activity_image_id'], 579, 434)), CONF_DEF_CACHE_TIME, '.jpg');
				$activity_name = $activity['activity_name'];
?>	
	<div class="modal share-card text--center">
		<div class="modal__header">
			<h6 class="modal__heading">Share your activity card</h6>
		</div>
		<div class="modal__content share-card__image">
			<img src = "<?php echo $pic;?>" alt="loading...">
		</div>
		<div class="modal__footer share-card__action">
			<nav class="buttons__group">		
				<a href="javascript:;" onclick="<?php echo "graphStreamPublish('{$link}','{$pic}','{$activity_name}','')" ?>;" class="button button--fill button--facebook" ><span class="hidden-on--mobile">Share on </span>facebook</a>
				<a href="javascript:;" onclick="twitterShare('<?php echo FatUtility::generateFullUrl('share','twitter',array($activity['activity_id']))?>')"  class="button button--fill button--twitter"><span class="hidden-on--mobile">Share on </span>twitter</a>
				<a href="javascript:;" onclick="<?php echo "pinit('{$link}','{$pic}','')" ?>;" class="button button--fill button--pinterest"><span class="hidden-on--mobile">Share on </span>pinterest</a>
			</nav>
		</div>
	</div>