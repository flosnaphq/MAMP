<?php defined('SYSTEM_INIT') or die('Invalid Usage.');
// echo '<pre>' . print_r($user_data, true) . '</pre>'; exit;
?>
<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section order__section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text"><?php echo Info::t_lang('BOOKING_DETAILS') ?></h6>
                        <p class="regular-text text--uppercase no--margin">
							<?php echo Info::t_lang('HOST_ORDER_DETAIL_BOOKING_BY'); ?>: <span class="text--red"><?php echo ucfirst($user_data['user_firstname']) . ' ' . ucfirst($user_data['user_lastname']); ?></span>
						</p>
						<p class="regular-text text--uppercase no--margin">
							<?php echo Info::t_lang('BOOKING_ID'); ?>: <span class="text--red"><?php echo $activities['oactivity_booking_id'] ?></span>
						</p>
                    </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <?php /*
                              <div class="span span--3">
                              <div class="block summary__block clearfix">
                              <h6 class="block__heading-text text--blue"><small><?php echo Info::t_lang('BOOKING_SUMMARY');?></small></h6>
                              <div class="clearfix summary__sub">
                              <span class="fl--left"><?php echo Info::t_lang('TOTAL')?></span>
                              <span class="fl--right"><?php echo Currency::displayPrice($order['order_total_amount']);?></span>
                              </div>

                              <div class="clearfix summary__total">
                              <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYBLE');?></span>
                              <span class="fl--right"><?php echo Currency::displayPrice($order['order_net_amount'])?></span>
                              </div>
                              </div>
                              </div>
                             */ ?>
                            <div class="span span--9">
                                <div class="block booking__block clearfix">
                                    <h6 class="block__heading-text"><small><?php echo Info::t_lang('YOUR_BOOKING') ?></small></h6>
                                    <div class="booking-media__list">

                                        <div class="media booking-media">
                                            <div class="media__figure booking-media__image">
                                                <img src="<?php echo FatUtility::generateUrl('image', 'dactivity', array($activities['oactivity_activity_id'], 100, 75)); ?>" alt="">
                                            </div>
                                            <div class="media__body media--middle booking-media__content">
                                                <h6 class="booking-media__heading"><a ><?php echo $activities['oactivity_activity_name'] ?></a></h6>
                                                <p class="booking-media__text"><?php echo Info::t_lang('ON') ?> <span class="text--blue"><?php echo date('j M Y', strtotime($activities['oactivity_event_timing'])) ?></span> <?php echo Info::t_lang('FOR') ?> <?php echo $activities['oactivity_members'] ?> <?php echo Info::activityTypeLabelByKey($activities['activity_price_type']) ?></p>
                                                <small class="text--red"><?php echo Info::t_lang('BOOKING_ID') ?>: <?php echo $activities['oactivity_booking_id'] ?></small>
                                                <?php if (!empty($activities['addons'])) { ?>
                                                    <h6><?php echo Info::t_lang('TRAVELER') . ' : ' . $user_data['user_firstname'] . ' ' . $user_data['user_lastname'] ?></h6>
                                                    <h6 class="booking-media__heading no--margin"><small><?php echo Info::t_lang('ADDONS'); ?></small></h6>
                                                    <ul class="list list--horizontal booking-media__addon">
                                                        <?php foreach ($activities['addons'] as $ac) { ?>
                                                            <li><span href="/" class="tag">
                                                                    <span class="tag__label"><?php echo $ac['oactivityadd_addon_name'] ?></span>
                                                                    <span class="tag__remove"><?php echo $ac['oactivityadd_quantity'] ?></span>
                                                                </span></li>
    <?php } ?>	 
                                                    </ul>
                                                    <?php } ?>
                                                <br>
                                                <span>
                                                    <a href="#send-msg" onclick="sendMsg('<?php echo $activities['oactivity_booking_id'] ?>')" class="button button--fill button--red button--small js-send-msg"><?php echo Info::t_lang('MESSAGE_CUSTOMER'); ?></a>
                                                </span>
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
    </div>
</main>

<div id="send-msg" style="display:none;"></div>