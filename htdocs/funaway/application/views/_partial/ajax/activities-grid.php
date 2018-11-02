<?php
if (!empty($activities)) {

    foreach ($activities as $activity) {
        $booking_status = Activity::isActivityOpen($activity);
        if ($booking_status == 1) {
            $status = "open";
        } elseif ($booking_status == 2) {
            $status = "upcoming";
        } elseif ($booking_status == 0) {
            $status = "close";
        }

        $activityUrl = Route::getRoute('activity', 'detail', array($activity['activity_id']));
		
        ?>
        <div class="activity-card activity-card--open ">
            <div class="activity-card__image">
                <ul class="list list--vertical no--margin activity-card__float">
                    <li>
                        <a href="<?php echo FatUtility::generateUrl('share', 'share-activity', array($activity['activity_id'])) ?>" title="Share on Social" class="float__icon float__icon--share share-ajax" data-modaal-type="ajax" >
                            <svg class="icon icon--heart"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-share"></use></svg>
                        </a>
                    </li>
                    <li>
                        <a title="Add to Wishlist" class="float__icon float__icon--heart <?php if (isset($activity['wishlist_activity_id']) && $activity['wishlist_activity_id'] != "") echo 'has--active' ?>" onclick = 'wishlist(this,<?php echo $activity['activity_id'] ?>);' href="javascript:;">
                            <svg class="icon icon--heart"><use xlink:href="#icon-heart"/></svg>
                        </a>
                    </li>
                    <li>
                        <?php if ($status == "open") { ?>
                            <a href="javascript:void(0);" title="Open Activity" class="float__icon float__icon--status open" >
                                <svg class="icon icon--check"><use xlink:href="#icon-check" /></svg>
                            </a>
                        <?php } elseif ($status == 'close') { ?>
                            <a href="javascript:void(0);" title="Closed Activity" class="float__icon float__icon--status close" >
                                <svg class="icon icon--stop"><use xlink:href="#icon-stop" /></svg>
                            </a>
                        <?php } elseif ($status == 'upcoming') { ?>
                            <a href="javascript:void(0);" title="Upcoming Activity" class="float__icon float__icon--status upcoming" >
                                <svg class="icon icon--stop"><use xlink:href="#icon-stop" /></svg>
                            </a>
                        <?php } ?>
                    </li>
                </ul>
                <a  href="<?php echo $activityUrl; ?>">
                    <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('Image', 'activity', array($activity['activity_image_id'], 579, 434)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $activity['activity_name'] ?>" title="<?php echo $activity['activity_name'] ?>">
                </a>
            </div>
            <div class="activity-card__content" >
                <div class="activity-card__content-basic clearfix">
                    <h5 class="activity-card__heading">
                        <a class="" href="<?php echo $activityUrl ?>" title="<?php echo $activity['activity_name'] ?>"><?php echo $activity['activity_name'] ?></a></h5>
                    <h6 class="activity-card__cat"><a href="<?php echo Route::getRoute('services') ?>"><?php echo $activity['parentservice_name'] ?></a></h6>
                    
                    <div class="activity-card__price">
                        <span class="activity-card__price-number"><?php echo Currency::displayPrice($activity['activity_price']) ?>
                            <?php if ($activity['activity_display_price'] > 0): ?>
                                <del> <?php echo Currency::displayPrice($activity['activity_display_price']) ?> </del>
                            <?php endif; ?>


                        </span>
                        <span class="activity-card__price-text"><?php echo Info::activityTypeByKey($activity['activity_price_type']) ?></span>
                    </div>
                    
                    <div class="activity-card__rating">
                        <?php
                        if ($activity['rating'] != 0) {
                            $activity['rating'] = $activity['rating'] / $activity['reviews'];
                        }
                        echo Info::rating($activity['rating'], false, 'rating--lightest')
                        ?>
                        <span><?php echo $activity['reviewcounter'] ?> <?php echo Info::t_lang('REVIEWS ') ?></span>
                    </div>
                </div>
            </div>



        </div>
        <?php
    }
} else {
    echo Helper::noRecord(Info::t_lang('NO_RECORDS'));
}
?>

