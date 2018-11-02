<?php defined('SYSTEM_INIT') or die('Invalid Usage');?>
<div class="modal review">
	<div class="modal__content">
		<span class="review__label">Reviewed by</span>
		<div class="review__name">
		    <span><?php echo $review[Reviews::DB_TBL_PREFIX.'user_id']?$review['user_firstname'].' '.$review['user_lastname']:$review[Reviews::DB_TBL_PREFIX.'user_name']?></span>
		</div>
		<div class="rating__block">
			<?php echo Info::rating($review[Reviews::DB_TBL_PREFIX.'rating'])?>
			<span class="review__date"> <?php echo date('M d, Y',strtotime($review[Reviews::DB_TBL_PREFIX.'date']))?></span>
		</div>
		<div class="review__text">
			<p><?php echo $review[Reviews::DB_TBL_PREFIX.'content']?></p>
	   </div>
	</div>
</div>