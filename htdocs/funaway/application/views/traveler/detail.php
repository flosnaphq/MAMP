<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'traveler/common/order-menu.php') ?>	
        <section class="section order__section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text"><?php echo Info::t_lang('ORDER_DETAILS') ?></h6>
                        <p class="regular-text text--uppercase no--margin"><?php echo Info::t_lang('ORDER_ID'); ?>: <span class="text--red"><?php echo $order['order_id'] ?></span> | <?php echo Info::t_lang('ORDER_ON'); ?> <?php echo FatDate::format($order['order_date']); ?></p>
                        <a target="_blank" href="<?php echo FatUtility::generateUrl('traveler', 'printInvoice', array($order['order_id'])); ?>" class="button button--fill button--red button--small fl--right"><?php echo Info::t_lang('PRINT_INVOICE') ?></a>
                    </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--3">
                                <div class="block summary__block clearfix">
                                    <h6 class="block__heading-text text--blue"><small><?php echo Info::t_lang('ORDER_SUMMARY'); ?></small></h6>
                                    <div class="clearfix summary__sub">
                                        <span class="fl--left"><?php echo Info::t_lang('SUB_TOTAL') ?></span>
                                        <span class="fl--right"><?php echo Currency::displayDefaultPrice($order['order_net_amount']); ?></span>
                                    </div>


                                    <div class="clearfix summary__total">
                                        <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYBLE'); ?></span>
                                        <span class="fl--right"><?php echo Currency::displayDefaultPrice($order['order_total_amount']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="span span--9">
                                <div class="block booking__block clearfix">
                                    <h6 class="block__heading-text"><small><?php echo Info::t_lang('YOUR_BOOKINGS') ?></small></h6>
                                    <div class="booking-media__list">
                                        <?php foreach ($activities as $acts) { ?>
                                            <div class="media booking-media">
                                                <div class="media__figure booking-media__image">
                                                    <img src="<?php echo FatUtility::generateUrl('image', 'dactivity', array($acts['oactivity_activity_id'], 100, 75)); ?>" alt="">
                                                </div>
                                                <div class="media__body media--middle booking-media__content">
                                                    <h6 class="booking-media__heading">
                                                        <a href="<?php echo Route::getRoute('activity', 'detail', array($acts['oactivity_activity_id'])); ?>">
                                                            <?php echo $acts['oactivity_activity_name']; ?>
                                                        </a>
														
														<span class="cancel-status"> 
															<?php 
															if(!empty($acts['cancel_data'])) {
																$orderCancelId = FatUtility::int($acts['cancel_data']['ordercancel_id']);
																if($orderCancelId > 0) {
																	echo sprintf(Info::t_lang('TRAVELER_ORDER_DETAIL_CANCELLATION_STATUS_%s'), Info::getOrderCancelRequestStatusByKey($acts['cancel_data']['ordercancel_status']));
																}
															}
															?>
														</span>
                                                    </h6>
                                                    <p class="booking-media__text"><?php echo Info::t_lang('ON') ?> <span class="text--blue"> <?php echo date('j M Y', strtotime($acts['oactivity_event_timing'])) ?> </span><?php echo Info::t_lang('FOR') ?> <?php echo $acts['oactivity_members'] ?> <?php echo Info::activityTypeLabelByKey($acts['activity_price_type']) ?> </p>
                                                    <small class="text--red"> <?php echo Info::t_lang('BOOKING_ID') ?>: <?php echo $acts['oactivity_booking_id'] ?></small>
                                                    <?php
                                                    if (!empty($acts['addons'])) {
                                                        ?>
                                                        <h6 class="booking-media__heading no--margin"><small><?php echo Info::t_lang('ADDONS'); ?></small></h6>
                                                        <ul class="list list--horizontal booking-media__addon">
                                                            <?php
                                                            foreach ($acts['addons'] as $ac) {
                                                                ?>
                                                                <li>
                                                                    <span class="tag">
                                                                        <span class="tag__label"><?php echo $ac['oactivityadd_addon_name'] ?></span>
                                                                        <span class="tag__remove"><?php echo $ac['oactivityadd_quantity'] ?></span>
                                                                    </span>
                                                                </li>
                                                                <?php
                                                            }
                                                            ?>	 
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php if (empty($acts['cancel_data']) && $acts['can_cancel'] || $order['order_payment_status'] == 1) { ?>
                                                        <nav class="buttons__group">
                                                            <?php if (empty($acts['cancel_data']) && $acts['can_cancel']) { ?>
                                                                <a class="button button--fill button--dark button--small js-order-cancel" href="<?php echo FatUtility::generateUrl('traveler', 'cancelBooking', array($acts['oactivity_booking_id'])) ?>"><?php echo Info::t_lang('CANCEL_BOOKING'); ?></a>

                                                            <?php } ?>
                                                            <?php if ($order['order_payment_status'] == 1) { ?>
                                                                <a href="#send-msg" onclick="sendMsg('<?php echo $acts['oactivity_booking_id'] ?>')" class="button button--fill button--red button--small js-send-msg"><?php echo Info::t_lang('MESSAGE_HOST'); ?></a>
                                                            <?php } ?>

                                                            <?php if (!empty($acts['can_review']) && $acts['can_review']) { ?>
                                                                <a href="#write-review" onclick="writeReview('<?php echo $acts['oactivity_booking_id'] ?>')" class="button button--fill button--red button--small write-review"><?php echo Info::t_lang('MESSAGE_REVIEW'); ?></a>
                                                            <?php } ?>
                                                        </nav>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<div id="send-msg" style="display:none;"></div>
<div id="write-review" style="display:none;"></div>