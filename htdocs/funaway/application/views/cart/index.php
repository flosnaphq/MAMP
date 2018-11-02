<main id="MAIN" class="site-main">
    <header class="site-main__header">
        <div class="site-main__header__content">
            <div class="section section--vcenter">
                <div class="container container--static">
                    <h5 class="special-heading-text text--center"><?php echo Info::t_lang('CART') ?></h5>
                    <h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('WHAT_YOU_HAVE_IN_CART') ?></h6>
                </div>
            </div>
        </div>
    </header>
    <div class="site-main__body">
        <section class="section cart__section no--padding-top" id="cart">
            <div id="cartListing"></div>
        </section>
    </div>
</main>
<script>
<?php echo TrackingCode::getTrackingCode(4); ?>
</script>