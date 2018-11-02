<main id="MAIN" class="site-main">
    <div class="site-main__body">
        <section class="section section--light payment__section" id="cart">
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--7 span--center">
                            <h5 class="heading-text text--center text--red">
                                <span class="search__icon">
                                    <svg class="icon icon--check">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                                    </svg>
                                </span>
                                <span><?php echo Info::t_lang('ORDER FAILED!') ?></span>
                            </h5>
                            <h6 class="sub-heading-text text--center text--red"><?php echo $error; ?></h6>

                            <div class="block summary__block clearfix">
                                <h6 class="block__heading-text text--green"><small><?php echo Info::t_lang('ORDER_DETAILS'); ?></small></h6>
                                <div class="clearfix summary__sub">
                                    <span class="fl--left"><?php echo Info::t_lang('order_id') ?></span>
                                    <span class="fl--right"><?php echo $order['order_id'] ?></span>
                                </div>
                                <div class="clearfix summary__tax">
                                    <span class="fl--left"><?php echo Info::t_lang('ORDERED_ON'); ?></span>
                                    <span class="fl--right"><?php echo FatDate::format($order['order_date']) ?></span>
                                </div>
                                <div class="clearfix summary__tax">
                                    <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYBLE'); ?></span>
                                    <span class="fl--right"><?php echo Currency::displayPrice($order['order_total_amount']) ?></span>
                                </div>
                           
                            </div>
                            <hr>
                           
                            <div class="buttons_group text--center">
                                <a href="<?php echo FatUtility::generateUrl('traveler', "detail", array($order['order_id'])) ?>" class="button button--fill button--green"><?php echo Info::t_lang('REVIEW'); ?></a>
                                <?php /* <a href="/" class="button button--fill button--blue">Edit</a> */ ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
