<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<?php if ($page <= 1) { ?>
    <h6 class="block__heading-text"><?php echo $review_total['total_count'] ?> <?php echo Info::t_lang('REVIEWS') ?></h6>
<?php } ?>
<?php if (!empty($reviews)) { ?>
    <?php if ($page <= 1): ?>
        <ul class="list list--vertical review__list activity-review" >
        <?php endif; ?>    
        <?php
        foreach ($reviews as $review) {
            $reviewId = $review[Reviews::DB_TBL_PREFIX . 'id'];
            $reviewUserId = $review[Reviews::DB_TBL_PREFIX . 'user_id'];
            $reviewedBy = $reviewUserId ? $review[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $review[User::DB_TBL_PREFIX . 'lastname'] : $review[Reviews::DB_TBL_PREFIX . 'user_name'];
            $reviewedOn = $review[Reviews::DB_TBL_PREFIX . 'date'];
            $reviewedContent = $review[Reviews::DB_TBL_PREFIX . 'content'];
            $rating = $review[Reviews::DB_TBL_PREFIX . 'rating'];

            $image = $reviewUserId ? FatUtility::generateUrl('image', 'user', array($reviewUserId, 80, 80)) : 'https://dummyimage.com/80x80/e8e8e8/000.png&text=' . strtoupper($reviewedBy[0]);
            ?>
            <li>
                <div class="review">
                    <div class="review__image">
                        <img src="<?php echo $image; ?>"/>
                    </div>

                    <span class="review__label">Reviewed by</span>
                    <div class="review__name">
                        <span><?php echo $reviewedBy ?></span>
                    </div>
                    <div class="rating__block">
                        <?php echo Info::rating($review[Reviews::DB_TBL_PREFIX . 'rating'], false, 'rating--xsmall') ?>
                        <span class="review__date"> <?php echo date('M d, Y', strtotime($reviewedOn)) ?></span>
                    </div>
                    <div class="review__text">
                        <p><?php echo Helper::truncateString($reviewedContent, 300, '<a href="#more-review" onclick="showReviewDetail(' . $reviewId . ')" class="link more-review"><strong>' . Info::t_lang('READ_FULL') . '</strong></a></p>') ?></p>
                    </div>
                    <?php if(isset($messages[$review['review_id']])){ ?>
                    <div class="more-link">
						<a class='link' href="javascript:void(0);" onclick="$('div.review'+'<?php echo $review['review_id']; ?>').slideToggle();">Replies</a>
                    </div>
                    <?php } ?>
                <?php if(isset($messages[$review['review_id']])){?>
					<div class="review<?php echo $review['review_id']; ?>" hidden='hidden' style="margin-top:1em;">
                    <?php foreach($messages[$review['review_id']] as $message){
                        if($message['reviewmsg_user_id']){
                            /* $image = $message['reviewmsg_user_id'] ? FatUtility::generateUrl('image', 'user', array($message['reviewmsg_user_id'], 80, 80)) : 'https://dummyimage.com/80x80/e8e8e8/000.png&text='.($message['user_full_name']?:$message['admin_name']); */
                        ?>

                        <div class="review replied" >
							
							<?php /* <div class="review__image">
								<img src="<?php echo $image; ?>"/>
							</div> */ ?>
							
                            <div class="review__name" style="text-transform:none">
                                <span class="" style="font-weight:normal">Replied by</span> <span><?php if($message['user_id']) echo $message['user_full_name']; elseif($message['admin_id']) echo $message['admin_name']; ?></span>
                            </div>
                            <div class="rating__block">
                                <span class="review__date"> <?php echo date('M d, Y H:i A', strtotime($message['reviewmsg_added_on'])); ?></span>
                            </div>
                            <div class="review__text" style="margin-top:1em;">
                                <p><?php echo $message['reviewmsg_message'];?></p>
                            </div>
                            
                        </div>
                            <?php } ?>
                        <?php } ?>
					</div>
				<?php } ?>
                </div>
            </li>
        <?php } if ($page <= 1): ?>
        </ul>

    <?php endif;
}
?>


<?php if ($page <= 1): ?>
    <nav class="text--center" style="margin-top: 1.2em; display: none;" id="more-review-result">
        <a href="javascript:;" onclick="loadMoreReviews()" class="button button--fill button--dark"><?php echo Info::t_lang('LOAD_MORE') ?></a>
        <?php if ($pages >= 1) { /* ?>
          <a href="<?php echo Route::getRoute('reviews', 'activity', array($activity_data['activity_id'])) ?>" class="button button--fill button--dark"><?php echo Info::t_lang('LOAD_MORE') ?></a>
          <?php */
        }
        ?>
    </nav>
<?php endif; ?>
