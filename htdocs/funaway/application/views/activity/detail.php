<script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
<link href="https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.css" rel="stylesheet">
<main id="MAIN" class="site-main site-main--light">
    <span style="display:none;" id ="activityFld">
        <?php echo $activity['activity_id'] ?>
    </span>
    <div class="activity">
        <header class="site-main__header site-main__header--dark main-carousel__list">
            <div class="main-carousel__item">
                <div class="site-main__header__image">
                    <div class="js-main-carousel">
                        <?php foreach ($images as $img) { ?>
                            <div>
                                <div class="img">
                                    <img class="js-img-parallax" data-speed="1" src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'activity', array($img['afile_id'], 1600, 900)), 10000, '.jpg'); ?>" alt="<?php echo $activity['activity_name'] ?>">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="site-main__header__content">
                    <div class="section section--vcenter">
                        <div class="section__body">
                            <div class="container container--static">
                                <div class="span__row">
                                    <div class="span span--10 span--center">
                                        <hgroup>
                                            <h1 class="main-carousel__special-heading text--center"> <?php echo $activity['activity_name'] ?></h1>
                                        </hgroup>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="section__footer">
                            <div class="container container--static">
                                <div class="span__row">
                                    <div class="span span--12">
										
										<?php if (count($images) > 1): ?>
											<a href="javascript:void(0);" onclick="openGallery()" class="fl--right main-carousel__gallery modaal">
												<small><?php echo Info::t_lang('VIEW') ?></small>
												<span><?php echo Info::t_lang('PHOTOS') ?></span>
											</a>
											<?php foreach ($images as $img) { ?>
												<a href="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'activity', array($img['afile_id'], 1600, 900)), 10000, '.jpg'); ?>" rel="gallery" class="gallery"></a>
											<?php } ?>
										
										<?php endif;?>
										
										<?php if (count($videos) > 0): ?>
										
                                            <a href="javascript:void(0)"  onclick="openVideoGallery()" class="fl--left main-carousel__video modaal">
                                                <span class="main-carousel__video__icon"></span>
                                                <label class="main-carousel__video__text">
                                                    <small><?php echo Info::t_lang('WATCH') ?></small>
                                                    <span><?php echo Info::t_lang('VIDEO') ?></span>
                                                </label>
                                            </a>

                                            <?php foreach ($videos as $video) {
                                                if ($video['activityvideo_type'] == 2) {
                                                    $videoUrl = "https://player.vimeo.com/video/" . $video['activityvideo_videoid'];
                                                } else {
                                                    $videoUrl = "https://www.youtube.com/embed/" . $video['activityvideo_videoid'];
                                                }
                                                ?>
                                                <a href="<?php echo $videoUrl; ?>" rel="video-gallery" class="video-gallery"></a>
                                            <?php } ?>
											
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </header>
        <div class="site-main__body">
            <div class="menu-bar js-sticky-remove" data-sticky-offset="80" data-sticky-responsive="true">
                <div class="container container--static">
                    <nav class="fl--left" role="navigation">
                        <p id="bread-crumb-label" class="assistive__text"><?php echo Info::t_lang('YOU_ARE_HERE'); ?>:</p>
                        <ol class="breadcrumb list list--horizontal" aria-labelledby="bread-crumb-label">
                            <li class="text-heading--label"><a href="<?php echo Route::getRoute(); ?>"><?php echo Info::t_lang('Home') ?></a></li>
                            <li class="text-heading--label"><a href="<?php echo Route::getRoute('search'); ?>"><?php echo Info::t_lang('ACTIVITY') ?></a></li>
                            <li class="text-heading--label"><a class=""><?php echo $activity['activity_name'] ?></a></li>
                        </ol>
                    </nav>
                    <nav class="menu fl--right" role="navigation">
                        <ul class="list list--horizontal">
                            <li><a href="javascript:void(0)" data-moveto="#HIGHLIGHTS" onclick="scrollToSection(this);return false;" data-offset="160"><?php echo Info::t_lang('HIGHLIGHTS') ?></a></li>
                            <li><a href="javascript:void(0)" data-moveto="#DETAILS" onclick="scrollToSection(this);return false;"  data-offset="160"><?php echo Info::t_lang('DETAILS') ?></a></li>
                            <li><a href="javascript:void(0)" data-moveto="#DIRECTION" onclick="scrollToSection(this);return false;"  data-offset="160"><?php echo Info::t_lang('DIRECTION') ?></a></li>
                            <?php if ($activity['ratingcounter'] > 0): ?>
                                <li><a href="javascript:void(0)" data-moveto="#review__block" onclick="scrollToSection(this);return false;"  data-offset="160"><?php echo Info::t_lang('REVIEWS'); ?></a></li>
                            <?php endif; ?>
                            <li><a href="javascript:void(0)" data-moveto="#HOST" onclick="scrollToSection(this);return false;"  data-offset="160"><?php echo Info::t_lang('HOST'); ?></a></li>
                            <?php if ($loggedUserId && $loged_user_type != 1): ?>
								 <li><a href="#activity-abuse"  data-offset="160" class="activity-abuse" onclick="markAsInappropriate(<?php echo $activity['activity_id'] ?>)"><?php echo Info::t_lang('REPORT_ABUSE'); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo Route::getRoute('share', 'share-activity', array($activity['activity_id'])) ?>" class="button button--circle button--non-fill button--green share-activity"><svg class="icon icon--share"><use xlink:href="#icon-share" xmlns:xlink="http://www.w3.org/1999/xlink"></svg></a></li>
                            <li><a href="javascript:;" onclick="wishlist(this,<?php echo $activity['activity_id']; ?>);" class="button button--circle button--non-fill button--red <?php if (isset($activity['wishlist_activity_id']) && $activity['wishlist_activity_id'] != "") echo 'has--active' ?>"><svg class="icon icon--heart"><use xlink:href="#icon-heart" xmlns:xlink="http://www.w3.org/1999/xlink"></svg></a></li>
                            <?php if (count($images) + count($videos) > 0) { ?>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <section class="section highlights__section" >
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <?php
                            $spanSize = 8;
                            if (!$isUserLogged || $user_type == 0) {
                                $spanSize = 8;
                                ?>

                                <aside class="span span--4 span--last visible-on--desktop js-sticky" id="booking" data-sticky-offset="160" data-sticky-responsive="false" style="padding-left:60px; max-width: 375px;">
                                    <?php if ($activity['activity_booking_status'] == 1) { ?>
                                        
                                        <?php if($activity['date_available'] <1):?>
                                    <div class="book-card text--center">
                                            <div class="book-card__block active">
                                                <div class="book-card__block__body">
                                                    <div class="book-card__price priceOpt">

                                                        <h6 class="book-card__price__heading"><?php echo Info::t_lang("Booking_Dates_Not_Available"); ?></h6>
                                                        <p class="regular-text"><?php echo Info::t_lang("Booking_Dates_Not_Available_REASON"); ?></p>
                                                     
                                                    </div>
                                                </div>
                                            </div>
                                        </div>	
                                        <?php else:?>
                                        <?php require_once("_partial/booking-box.php") ?>
                                    
                                        <?php endif;?>

                                    <?php } else { ?>
                                        <div class="book-card text--center">
                                            <div class="book-card__block active">
                                                <div class="book-card__block__body">
                                                    <div class="book-card__price priceOpt">

                                                        <h6 class="book-card__price__heading"><?php echo Info::t_lang("Booking_Closed"); ?></h6>
                                                        <p class="regular-text"><?php echo Info::t_lang("Booking_Closed_Reason"); ?></p>
                                                        <button  class="button button--large button--fit button--disabled button--fill button--dark"><?php echo INFO::t_lang('BOOKING_CLOSE_TEXT') ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>	

                                    <?php } ?>
                                </aside>
                            <?php } ?>
                            <div class="span span--<?php echo $spanSize; ?>">
                                <div class="container container-fluid no--padding">
                                    <div class="span__row">
                                        <div class="span span--12 block activity__block">
                                            <h1 class="activity__title"> <?php echo $activity['activity_name'] ?></h1>
											<div class="activity__location" id="ratingBlock">

												<?php
												$rating = 0;
												if ($activity['ratingcounter']) {
													$rating = $activity['rating'] / $activity['ratingcounter'];
												}
												echo Info::rating($rating, $activity['ratingcounter'], false, '')
												?>
												<span id="activityReview"><?php echo $activity['reviews'] ?></span>
												<span> <?php echo Info::t_lang('REVIEWS') ?></span>
											</div>
											<div class="activity__price">
												<div class="fl--left">
													<span class="activity__price-number"><?php echo Currency::displayPrice($activity['activity_price']);
														if($activity['activity_display_price'] > 0) {
														?>
														<del> <?php echo Currency::displayPrice($activity['activity_display_price']) ?> </del>
														<?php } ?>
														</span>
													
													<span class="activity__price_type"><?php echo Info::activityTypeByKey($activity['activity_price_type']) ?></span>
												</div>
												<a href="#booking" data-modaal-type="inline" data-modaal-animation="fade" class="modaal button button--fill button--red button--large fl--right hidden-on--desktop">Book Now</a>
											</div>
                                        </div>
                                        <div class="span span--12 block activity__block" id="HIGHLIGHTS">
                                            <h6 class="block__heading-text"><?php echo Info::t_lang('ACTIVITY_HIGHLIGHTS'); ?></h6>
                                            <div class="innova-editor">
                                                <?php #echo html_entity_decode($activity['activity_highlights']);
												
												echo nl2br(strip_tags($activity['activity_highlights'])); ?>
                                            </div>
                                        </div>

                                        <div class="span span--12 block activity__block">
                                            <h6 class="block__heading-text"><?php echo Info::t_lang('ACTIVITY_DESCRIPTION'); ?></h6>
                                            <div class="innova-editor">
                                                <?php #echo html_entity_decode($activity['activity_desc']);
												
												echo nl2br(strip_tags($activity['activity_desc'])); ?>
                                            </div>
                                        </div>
                                        <div class="span span--12 block activity__block additional-info__block" id="DETAILS">
                                            <h6 class="block__heading-text"><?php echo Info::t_lang('DETAILS'); ?></h6>
                                            <ul class="list list--vertical additional-info__list">
                                                <li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('DETAIL_ACTIVITY_DURATION'); ?></span></li>
                                                        <li>
                                                            <span class="additional-info__heading"><?php 
																if($activity['activity_duration'] > 24) {
																	$days = $activity['activity_duration'] / 24;
																	echo sprintf(Info::t_lang('DETAIL_ACTIVITY_DURATION_DAYS_%s'), $days);
																} else {
																
																	echo sprintf(Info::t_lang('DETAIL_ACTIVITY_DURATION_HOURS_%s'), $activity['activity_duration']); 
																} ?></span>

                                                        </li>
                                                    </ul>
                                                </li>
												<li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('CITY'); ?></span></li>
                                                        <li>
                                                            <span class="additional-info__heading"><?php echo $activity['city_name'] ?></span>

                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('TYPE'); ?></span></li>
                                                        <li>
                                                            <span class="additional-info__heading"><?php echo $activity['parentservice_name'] ?> / <?php echo $activity['childservice_name'] ?></span>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('HOST'); ?></span></li>
                                                        <li><?php echo $activity['user_firstname'] ?> <?php echo $activity['user_lastname'] ?></li>
                                                    </ul>
                                                </li>


                                                <?php if (!empty($cancellation_policy['cancellationpolicy_name'])): ?>
                                                    <li>
                                                        <ul class="list list--fit">
                                                            <li><span class="additional-info__heading"><?php echo Info::t_lang('CANCELLATION') ?></span></li>
                                                            <li><?php echo $cancellation_policy['cancellationpolicy_name'] ?>  &nbsp;<a class="link" href="<?php echo Route::getRoute('cancellation-policy') ?>" target="_blank"><?php echo Info::t_lang('MORE'); ?></a></li>
                                                        </ul>
                                                    </li>
                                                <?php endif; ?>
                                                <li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('BOOKINGS_ACCEPTED') ?></span></li>
                                                        <li><?php
                                                            if (Info::activityBookingsByKey($activity['activity_booking'])) {
                                                                echo Info::activityBookingsByKey($activity['activity_booking']);
                                                            } else {
                                                                echo ceil(($activity['activity_booking'] / 24)) . Info::t_lang('_DAY(S)_PRIOR_TO_ACTIVITY');
                                                            }
                                                            ?>  </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('MAX_PARTICIPANTS') ?></span></li>
                                                        <li><?php echo $activity['activity_members_count'] ?></li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    <ul class="list list--fit">
                                                        <li><span class="additional-info__heading"><?php echo Info::t_lang('ACTIVITY_LANGUAGES') ?></span></li>
                                                        <li><?php echo Helper::str_replace_last(",", " & ",$activity['act_lang']);  ?></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="span span--12 block activity__block requirements__block">
                                            <h6 class="block__heading-text"><?php echo Info::t_lang('REQUIREMENTS'); ?></h6>
                                            <?php
                                            $activity['activity_requirements'] = trim($activity['activity_requirements']);
                                            $requirements = explode(PHP_EOL, $activity['activity_requirements']);
                                            $requirements = array_filter($requirements);
                                            if (count($requirements) > 0) {
                                                ?>
                                                <ul class="list list--vertical list--bullet">
                                                    <?php foreach ($requirements as $rs) { ?>
                                                        <li><?php echo htmlentities($rs); ?></li>
                                                    <?php } ?>
                                                </ul>
                                                <?php
                                            } else {
                                                echo Info::t_lang('NA');
                                            }
                                            ?>
                                        </div>
                                        <div class="span span--12 block activity__block inclusions__block">
                                            <h6 class="block__heading-text"><?php echo Info::t_lang('INCLUSIONS'); ?></h6>
                                            <?php
                                            $activity['activity_inclusions'] = trim($activity['activity_inclusions']);
                                            $inclusions = explode(PHP_EOL, $activity['activity_inclusions']);
                                            $inclusions = array_filter($inclusions);
                                            if (count($inclusions) > 0) {
                                                ?>
                                                <ul class="list list--vertical list--bullet">
                                                    <?php foreach ($inclusions as $in) { ?>
                                                        <li><?php echo htmlentities($in); ?></li>
                                                    <?php } ?>
                                                </ul>
                                                <?php
                                            } else {
                                                echo Info::t_lang('NA');
                                            }
                                            ?>
                                        </div>
                                        <div class="span span--12 block activity__block inclusions__block" id="DIRECTION">
                                            <h6 class="block__heading-text"><?php echo Info::t_lang('DIRECTION'); ?></h6>
                                            <div class="mapbox-container">
                                                <div id='map' style="height: 300px;" ></div>
                                            </div>
                                        </div>


                                        <div class="span span--12 block activity__block review__block" id="review__block"></div>

                                        <div class="span span--8 block activity__block host__block" id="HOST">
                                            <div class="media host">
                                                <div class="media__figure">
                                                    <a href="<?php echo Route::getRoute('activity', 'host', array($activity['user_firstname'], $activity['user_id'])); ?>" class="host__avatar">
                                                        <img src="<?php echo FatUtility::generateUrl('image', 'user', array($activity['activity_user_id'], 219, 219)) ?>" alt="">
                                                    </a>
                                                </div>
                                                <div class="media__body media--middle">
                                                    <label class="host__label"><?php echo Info::t_lang('HOSTED_BY') ?></label>
                                                    <h6 class="host__name"> <a href="<?php echo Route::getRoute('activity', 'host', array($activity['user_firstname'], $activity['user_id'])); ?>"><?php echo $activity['user_firstname'] . ' ' . $activity['user_lastname'] ?></a></h6>
                                                    <p class="host__desc"><?php echo Info::subContent($activity['user_description'], 100) ?></p>
                                                    <?php if ($user_id != $activity['activity_user_id']) {
                                                        ?>
                                                        <?php if ($user_id <= 0) { ?>
                                                            <a href="<?php echo Route::getRoute('guest-user', 'login-form') ?>"  class="reply-msg button button--small button--fill  button--red " title="<?php echo Info::t_lang('ASK_QUESTION') ?>"><?php echo Info::t_lang('ASK_QUESTION') ?></a>
                                                        <?php } else { ?>
                                                            <a href="#send-msg" onclick="sendMsg(<?php echo $activity['activity_id'] ?>)" class="reply-msg button button--small button--fill button--red send-msg" title="<?php echo Info::t_lang('ASK_QUESTION') ?>"><?php echo Info::t_lang('ASK_QUESTION') ?></a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="section section--light"  id="activities" style="display:none" >
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12 block activity__block">
                                <div class="section__header">
                                    <div class="container container--static">
                                        <div class="span__row">
                                            <div class="span span--12">
                                                <hgroup>
                                                    <h5 class="heading-text"><?php echo Info::t_lang('TOP_ESCAPADES') ?></h5>
                                                    <h6 class="sub-heading-text text--red"><?php echo Info::t_lang('TAVELERS_FAVORITE') ?></h6>
                                                </hgroup>
                                                <a class="see-all-activity" style="display:none" href="<?php echo Route::getRoute('search') . '?city=' . $cityId ?>" class="button button--non-fill button--dark"><?php echo Info::t_lang('SHOW_MORE') ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section__body">
                                    <div class="container container--static">
                                        <div class="span__row">
                                            <div class="span span--12">
                                                <div class="activity-card__list grid--style" id="island-activities-list"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
<div id="send-msg" style="display:none;"></div>
<div id="write-review" style="display:none;"></div>
<div id='more-review' style="display:none;" ></div>
<div id='activity-abuse' style="display:none;" ></div>
<script>
    var mapbox_access_token = '<?php echo FatApp::getConfig('mapbox_access_token') ?>';
    var city_id = <?php echo FatUtility::int($cityId) ?>;
    var activityMemberCount = '<?php echo $activity['activity_members_count']; ?>';
    function facebookTrackEvent() {
<?php echo TrackingCode::getTrackingCode(1); ?>
    }
    function facebookWishListTrack() {
<?php echo TrackingCode::getTrackingCode(3); ?>
    }
    $('.gallery').modaal({
        type: 'image',
		animation: 'fade',
	});
    $('.video-gallery').modaal({
        type: 'video',
		animation: 'fade',
        /*before_image_change: function (current_item, incoming_item) {
            $(current_item).find('iframe').attr('src', '');
            var nextLink = $(incoming_item).find('iframe').attr('data-src');
            $(incoming_item).find('iframe').attr('src', nextLink);

        }, */
    });
    function openGallery() {
        $('.gallery:eq(0)').click();

    }
    function openVideoGallery() {
        $('.video-gallery:eq(0)').click();

    }

    showMap(<?php echo $lat ?>,<?php echo $long ?>);

</script>