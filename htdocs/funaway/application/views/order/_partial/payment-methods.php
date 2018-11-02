<?php
defined('SYSTEM_INIT') or die(t_lang('INVALID_ACCESS'));
?>
<h5 class="heading-text text--center"><?php echo Info::t_lang('PAYMENT') ?></h5>
<h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('KINDLY_PROCEED_TO_MAKE_PAYMENT') ?></h6>
<div class="block summary__block clearfix">
    <h6 class="block__heading-text"><small><?php echo Info::t_lang('ORDER_SUMMARY') ?></small></h6>
    <div class="clearfix summary__sub">
        <span class="fl--left"><?php echo Info::t_lang('SUB_TOTAL') ?></span>
        <span class="fl--right"><?php echo Currency::displayPrice($sub_total) ?></span>
    </div>

    <h6 class="block__heading-text summary__total">
        <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYABLE') ?></span>
        <span class="fl--right"><?php echo Currency::displayPrice($total) ?></span>
    </h6>
</div>
<hr>

<?php
if (!empty($attributes)) {
    ?>
    <h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('OTHER_REQUIRED_ATTRIBUTES') ?></h6>
    <div class="block summary__block clearfix">
        <?php
        foreach ($attributes as $attr_id => $attr) {
            ?>
            <div class="span span--12">
                <div class="form-element no--margin-top">
                    <div class="form-element__control">
                        <label class="checkbox">
                            <input checked disabled type="checkbox" checked="checked" value="1" name="attr[<?php echo $attr_id; ?>]"  title="<?php echo $attr['details']['caption'] ?>" onchange="selectAttr(this)" >
                            <span class="checkbox__icon"></span>
                            <span class="checkbox__label"><?php echo $attr['details']['caption'] ?></span>
                        </label>
                        <?php
                        if ($attr['details']['file_required'] == 1) {
                            foreach ($attr['activities'] as $acts) {
                                ?>
                                <a class="link" target="_blank" href="<?php echo FatUtility::generateUrl('image', 'attribute', array($attr_id, $acts['activity_id'])) ?>" title="<?php echo $acts['name'] ?>">
                                    <?php echo $acts['name'] . ' ( ' . $acts['file_name'] . ' )' ?>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <hr>
    <?php
}
?>
<div class="cotainer container--fluid">
    <div class="span__row">
        <div class="span span--4" style="margin-bottom:1em;">
            <nav class="menu menu--large menu--bordered">
                <ul class="list list--vertical" id="payment_methods_tab">
                    <?php
                    $i = 1;
                    foreach ($paymentMethods as $key => $val) {
                        
                        $paymentGateway=$val['pmethod_code'] . '-pay';
                        /*if($val['pmethod_code']=='Sagepay'){
                            $paymentGateway=$val['pmethod_code'];
                        }*/                        
                        ?>
                        <li data-filter="<?php echo $key; ?>">
                            
                            <a class="<?php echo (($i == 1) ? 'active' : '' ); ?>" href="<?php echo FatUtility::generateUrl($paymentGateway, 'charge', array($key)); ?>">
                                <?php echo $val['pmethod_name']; ?>
                            </a>
                        </li>
                        <?php
                        $i++;
                    }
                    ?>
                </ul>
            </nav>						
        </div>
        <div class="span span--8">
            <div class="payment-card">
                <div id="tabs-container">	
                    <?php echo Info::t_lang('Loading_please_wait') ?> 
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var containerId = '#tabs-container';

    var tabsId = '#payment_methods_tab';

    $(document).ready(function () {

        if ($(tabsId + ' li a.active').length > 0) {

            loadTab($(tabsId + ' li a.active'));

        }

        $(tabsId + ' a').click(function () {
            if ($(this).hasClass('active')) {
                return false;
            }
            $(tabsId + ' li a.active').removeClass('active');
            $(containerId).html("<?php echo Info::t_lang('Loading_please_wait') ?>");
            $(this).addClass('active');
            loadTab($(this));
            return false;
        });
    });

    function loadTab(tabObj) {
        if (!tabObj || !tabObj.length) {
            return;
        }

        $(containerId).load(tabObj.attr('href'), function () {
            $(containerId).html();
            $(containerId).fadeIn('fast');
        });
    }
</script>