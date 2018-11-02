<?php defined('SYSTEM_INIT') or die('Invalid Usage'); ?>
<div class="payment-page">
    <div class="cc-payment">
        <div class="payment-from">
            <?php if (!isset($error)): ?>
                <div class="text--center">
                    <div class="buttons__group">
                        <input type="cancel" name="cancel" value="Cancel" class="button button--fill button--dark" onclick="window.location = '<?php echo FatUtility::generateUrl('cart') ?>'">
                        <a class='button button--fill button--green' href="javascript:void(0);" onClick="processOrder()">
                            <?php echo Info::t_lang('PAY_NOW'); ?>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class=""><?php echo $error ?></div>
            <?php endif; ?>
            <div id="ajax_message"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function ($) {
        var runningAjaxReq = false;
        processOrder = function () {
            if (runningAjaxReq == true)
            {
                return;
            }
            fcom.updateWithAjax(fcom.makeUrl('paypalstandardPay', 'process'), '', function (t) {
                runningAjaxReq = false;
                if (t.status == 1)
                {
                    if (t.error == '')
                    {
                        $('.payment-from').html(t.frm);
                    } else
                    {
                        $('#ajax_message').html(t.error);
                    }
                } else
                {
                    $('#ajax_message').html(t.msg);
                }
            });
        }
    })(jQuery);
</script>

