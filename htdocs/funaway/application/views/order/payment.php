
<main id="MAIN" class="site-main">
    <div class="site-main__body">
        <section class="section   no--padding-top payment__section" id="cart">
            <div class="cancellation__tab">
                <div class="section__header menu-bar text--center ">
                    <nav class="menu tab__nav">
                        <ul class="list list--horizontal js-pay-ul">
                            <li><a class="button button--fill " href="javascript:;" id="js-account-tab" onclick="paymentTab(1)"><?php echo Info::t_lang('ACCOUNT') ?></a></li>
                            <li><a class="button button--fill " href="javascript:;" id="js-payment-tab" <?php if ($loggedUserId == 0) echo 'disabled="disabled"'; ?> onclick="paymentTab(2)"><?php echo Info::t_lang('PAYMENT') ?></a></li>
                        </ul>
                    </nav>
                </div>

                <div class="section__body">	
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--10 span--center" id="form-tab"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<script>
<?php
//echo TrackingCode::getTrackingCode(5);
// print_r($_SESSION);
?>
    $(document).ready(function () {
        paymentTab(<?php echo $active_tab; ?>);
    });
</script>
