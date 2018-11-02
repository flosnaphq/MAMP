<?php if (!empty($arr_listing)) { ?>
    <?php foreach ($arr_listing as $acts) { ?>
        <?php
        if ($acts['booking_status'] == 1) {
            $status = "open";
        } elseif ($acts['booking_status'] == 2) {
            $status = "upcoming";
        } elseif ($acts['booking_status'] == 0) {
            $status = "close";
        }
        ?>
        <div class="activity-card <?php if ($status == "open") { ?>activity-card--open <?php } else { ?> activity-card--closed<?php } ?>">

            <div class="activity-card__image">
                <ul class="list list--vertical no--margin activity-card__float">

                    <li>
                        <a href="<?php echo FatUtility::generateUrl('share', 'share-activity', array($acts['activity_id'])) ?>" title="Share on Social" class="float__icon float__icon--share modaal-ajax" >
                            <svg class="icon icon--heart"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-share"></use></svg>
                        </a>
                    </li>
                    <li>
                        <a title="Add to Wishlist" class="float__icon float__icon--heart <?php if (isset($acts['wishlist_activity_id']) && $acts['wishlist_activity_id'] != "") echo 'has--active' ?>" onclick = 'wishlist(this,<?php echo $acts['activity_id'] ?>);' href="javascript:;">
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
                <a href="<?php echo Route::getRoute('activity', 'detail', array($acts['activity_id'])); ?>" title="<?php echo Info::subContent($acts['activity_name'], 100) ?>">
                    <img src="<?php echo FatUtility::generateUrl('image', 'activity', array($acts['activity_image_id'], 600, 450)) ?>" alt="<?php echo Info::subContent($acts['activity_name'], 100) ?>">
                </a>
            </div>

            <div class="activity-card__content">

                <div class="activity-card__content-basic clearfix">

                    <h5 class="activity-card__heading"><a href="<?php echo Route::getRoute('activity', 'detail', array($acts['activity_id'])) ?>" class=""><?php echo Info::subContent($acts['activity_name'], 100) ?></a></h5>                    
                    <h6 class="activity-card__cat"><a href="<?php echo Route::getRoute('services'); ?>" title="<?php echo $acts['parentservice_name'] ?> / <?php echo $acts['childservice_name'] ?>"><?php echo $acts['parentservice_name'] ?> / <?php echo $acts['childservice_name'] ?></a></h6>

                    <div class="activity-card__price">
                        <span class="activity-card__price-number"><?php echo Currency::displayPrice($acts['activity_price']); ?>
                            <?php if ($acts['activity_display_price'] > 0): ?>
                                <del> <?php echo Currency::displayPrice($acts['activity_display_price']) ?> </del>
                            <?php endif; ?>

                        </span>
                        <span class="activity-card__price-text"><?php echo Info::activityTypeByKey($acts['activity_price_type']); ?></span>
                    </div>


                    <div class="activity-card__rating">
                        <?php
                        $rating = 0;
                        if ($acts['ratingcounter']) {
                            $rating = $acts['rating'] / $acts['ratingcounter'];
                        }
                        echo Info::rating($rating, false, 'rating--lightest')
                        ?>
                        <span><?php echo $acts['reviews'] ?> <?php echo Info::t_lang('REVIEWS ') ?></span>
                    </div>

                </div>
                
                <div class="activity-card__content-addition">
                    <ul class="list list--vertical">
                        <li>
                            <span><?php echo Info::t_lang('MAX_PARTICIPANTS'); ?> : <?php echo $acts['activity_members_count'] ?></span>
                        </li>
                        <?php
                        if (trim($acts['act_lang']) != "") {
                            $lang = explode(',', $acts['act_lang']);
                            ?>

                            <?php if (!empty($lang)) { ?>
                                <li class="hidden-on--mo">

                                    <strong><?php echo Info::t_lang('GOOD_FOR') ?></strong>

                                    <ul class="list list--horizontal">

                                        <?php foreach ($lang as $k) { ?>
                                            <li><?php echo $k; ?></li>
                                        <?php } ?>
                                    </ul>
                                </li>

                            <?php } ?>
                        <?php } else { ?>
                            <li class="hidden-on--mo">
                                <strong><?php echo Info::t_lang('GOOD_FOR') ?></strong>

                                <ul class="list list--horizontal">
                                    <li><?php echo Info::getDefaultLang(); ?></li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>                                                    
                </div>
                
                
            </div>

        </div>


    <?php } ?>
    <?php
} elseif ($page <= 1) {
    echo Helper::noRecord(Info::t_lang('activities_search_no_records'));
} else {
    echo Info::t_lang('NO_MORE_RECORD');
}
?>