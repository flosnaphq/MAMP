<main id="MAIN" class="site-main">
            <div class="site-main__body">
                <section class="section section--light payment__section" id="cart">
                     <div class="section__body">
                         <div class="container container--static">
                             <div class="span__row">
                                 <div class="span span--7 span--center">
                                    <h5 class="heading-text text--center text--green">
                                        <span class="search__icon">
                                            <svg class="icon icon--check">
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-check"></use>
                                            </svg>
                                        </span>
                                        <span><?php echo Info::t_lang('THANK_YOU!')?></span>
                                    </h5>
                                    <h6 class="sub-heading-text text--center"><?php echo Info::t_lang('YOUR_ORDER_HAS_BEEN_PLACED');?></h6>
                                    <p class="regular-text text--center"><?php echo Info::t_lang('PLEASE_CHECK_YOUR_INBOX_FOR_EMAIL_CONFIRMATION.');?></p>
                                    <div class="block summary__block clearfix">
                                         <h6 class="block__heading-text text--green"><small><?php echo Info::t_lang('ORDER_DETAILS');?></small></h6>
                                         <div class="clearfix summary__sub">
                                             <span class="fl--left"><?php echo Info::t_lang('order_id')?></span>
                                             <span class="fl--right"><?php echo $order['order_id']?></span>
                                         </div>
                                         <div class="clearfix summary__tax">
                                             <span class="fl--left"><?php echo Info::t_lang('ORDERED_ON');?></span>
                                             <span class="fl--right"><?php echo FatDate::format($order['order_date'])?></span>
                                         </div>
                                         <div class="clearfix summary__tax">
                                             <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYBLE');?></span>
                                             <span class="fl--right"><?php echo Currency::displayPrice($order['order_total_amount'])?></span>
                                         </div>
										 <div class="clearfix summary__tax">
                                             <span class="fl--left"><?php echo Info::t_lang('PAYMENT_STATUS');?></span>
                                             <span class="fl--right"><?php echo Info::getPaymentStatus($order['order_payment_status'])?></span>
                                         </div>
                                    </div>
                                    <hr>
                                    <p class="regular-text text--center"><?php echo Info::t_lang('THANK_YOU_BOOKING_ACTIVITIES_ISLAND')?></p>
                                    <div class="buttons_group text--center">
                                        <a href="<?php echo FatUtility::generateUrl('traveler',"detail",array($order['order_id']))?>" class="button button--fill button--green"><?php echo Info::t_lang('REVIEW');?></a>
                                    <?php /* <a href="/" class="button button--fill button--blue">Edit</a> */ ?>
                                    </div>
                                 </div>
                             </div>
                         </div>
                    </div>
                </section>
            </div>
        </main>
<?php $track_fn = TrackingCode::getTrackingCode(6);
if(!empty($track_fn)){
	$track_data = json_encode($track_data);
	$track_fn = str_replace('track_data', $track_data, $track_fn);
}
?>
<script>
<?php echo $track_fn?>;
</script>