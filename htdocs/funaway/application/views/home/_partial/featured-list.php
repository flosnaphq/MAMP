<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$first_city = 0;

if (!empty($featured_cities)) {
    ?> 
    <div class="scrollable--x">
        <nav style="white-space:nowrap;" class="tab__nav menu">
            <ul class="list list--horizontal js-featured-island" data-featured-cities-count="<?php echo count($featured_cities);?>">
                <?php
                $cityCounter = 0;
				$displayShowMore = false;
                foreach ($featured_cities as $city) {
                    if($cityCounter > 6) {
						$displayShowMore = true;
						break;
					}
					$cityCounter++;
                    ?>
                    <li>
                        <a class="<?php echo $first_city == 0 ? 'is--active' : '';?>" href="javascript:void(0);" onclick="showFeatureActivities(this, <?php echo $city['city_id'] ?>)" title="<?php echo $city['city_name'] ?>">
                            <?php echo $city['city_name'] ?>
                        </a>
                    </li>
                    <?php
                    if ($first_city == 0) {
                        $first_city = $city['city_id'];
                    }
                }                
                if (true === $displayShowMore) {
                ?>
					<li>
						<a href="<?php echo Route::getRoute('search', 'index') ?>" class="hidden-on--mobile"><?php echo Info::t_lang('SHOW_MORE') ?></a>
					</li>
				<?php 
				}
                ?>
            </ul>
        </nav>
    </div>
    <?php
    
    foreach ($featured_cities as $city) {
        $featured_act = (!empty($featured_activities[$city['city_id']]['activities']) ? $featured_activities[$city['city_id']]['activities'] : array());
        $featured_act = array_slice($featured_act, 0, 8);
        $featured_cat = (!empty($featured_activities[$city['city_id']]['categories']) ? $featured_activities[$city['city_id']]['categories'] : array());

        if (!empty($featured_cat)) {
            $array_part = ceil(count($featured_cat) / 3);
            $featured_cat = array_chunk($featured_cat, $array_part);
        }
	?>

        <div class="tab__container js-feature-tab"  id="js-feature-tab-<?php echo $city['city_id'] ?>" style="display:<?php echo $first_city == $city['city_id'] ? 'block' : 'none'; ?>;" >
            <div id="tab1" class="tab__content">
                <div id="js-activity-list">
                    <?php
                    foreach ($featured_act as $activity) {
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
                        <div class="activity-card activity-card--<?= $status ?> ">
                            <div class="activity-card__image">

                                <ul class="list list--vertical no--margin activity-card__float">
                                    <li>
                                        <a href="<?php echo FatUtility::generateUrl('share', 'share-activity', array($activity['activity_id'])) ?>" title="<?php echo Info::t_lang('SHARE'); ?>" class="float__icon float__icon--share modaal-ajax"  title="Share Ib">
                                            <svg class="icon icon--heart"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-share"></use></svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a title="Add to Wishlist" class="float__icon float__icon--heart <?php if (isset($activity['wishlist_activity_id']) && $activity['wishlist_activity_id'] != "") echo 'has--active' ?>" onclick = 'wishlist(this,<?php echo $activity['activity_id'] ?>);' href="javascript:void(0);">
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

                            <div class="activity-card__content">
                                <div class="activity-card__content-basic clearfix">
                                    <h5 class="activity-card__heading">
                                        <a class="" href="<?php echo $activityUrl ?>"><?php echo $activity['activity_name'] ?></a></h5>
                                    <h6 class="activity-card__cat"><a href="<?php echo Route::getRoute('services') ?>"><?php echo $activity['service_name'] ?></a></h6>

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
                                        echo Info::rating($activity['rating'], false, 'rating--lightest');
                                        ?>
                                        <span><?php echo $activity['reviews'] ?> <?php echo Info::t_lang('REVIEWS ') ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}
?>  
<script>
    $('.modaal-ajax').modaal({
        type: 'ajax',
    });
</script>   


