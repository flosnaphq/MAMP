<?php
$total_donations = 0;
?>
<style>
    @page { margin: 0; }
</style>
<table style="width:100%; font-family: sans-serif; font-size: 10px;">
    <tr>
        <td>
            <table style="width:100%; margin:0 auto; background-color:#ffffff;">
                <tr>
                    <td style="padding:40px 40px 0">
                        <table style="width:100%; margin:0 auto;">
                            <tr>
                                <td style="">
                                    <h1 style="margin:0; padding:0;"><?php echo Info::t_lang('INVOICE') ?></h1>
                                    <small><?php echo date('l, j F, Y', strtotime($current_datetime)) ?></small>
                                    <br>
                                    <br>
                                    <strong style="margin:0; padding:0; color:#f00"><?php echo Info::t_lang('ORDER_ID') ?>: #<?php echo $order['order_id'] ?></strong>
                                    <br>
                                    <p style="margin:0; padding:0; color:#f00"><?php echo Info::t_lang('Invoice_Booked_By') ?>: <?php echo $order['traveler_name'] ?></p>
                                </td>
                                <td style="padding:10px 0; text-align:right">
                                    <a href="<?php echo FatUtility::generateFullUrl('', '', array(), '/'); ?>"><img src="<?php echo FatUtility::generateFullUrl('image', 'companyLogo'); ?>" alt="" width="100"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 40px">
                        <table class="" style="outline: none; border-collapse: collapse; border-spacing: 0px; width: 100%; margin:0 auto; border: 1px solid rgb(225, 233, 243); color: rgb(0, 21, 59);  line-height: 28px;">
                            <thead style="outline: none;">
                                <tr style="outline: none;">
                                    <th style="outline: none; padding: 0.625em 1.25em; font-weight: bold; white-space: nowrap;  text-transform: uppercase; text-align:left;"><?php echo Info::t_lang('ACTIVITY'); ?></th>
                                    <th style="outline: none; padding: 0.625em 1.25em; font-weight: bold; white-space: nowrap;  text-transform: uppercase; text-align:left;"><?php echo Info::t_lang('PRICE'); ?></th>
                                    <th style="outline: none; padding: 0.625em 1.25em; font-weight: bold; white-space: nowrap;  text-transform: uppercase; text-align:left;"><?php echo Info::t_lang('MEMBER'); ?></th>

                                    <th style="outline: none; padding: 0.625em 1.25em; font-weight: bold; white-space: nowrap;  text-transform: uppercase; text-align:left;"><?php echo Info::t_lang('ADDON'); ?></th>
                                    <th style="outline: none; padding: 0.625em 1.25em; font-weight: bold; white-space: nowrap;  text-transform: uppercase; text-align:right;"><?php echo Info::t_lang('TOTAL'); ?></th>
                                </tr>
                            </thead>
                            <tbody style="outline: none; width: 90%;">
                                <?php foreach ($activities as $activity) {
                                    ?>
                                    <tr class="" style="outline: none;">
                                        <td  class="" style="outline: none; padding: 0.5em 1em;font-weight: inherit; white-space: nowrap; text-align:left; border-top: 1px solid rgb(225, 233, 243);">
                                            <p style="margin:0;"><?php echo Info::t_lang('Invoice_booking_id'); ?>: <?php echo $activity['oactivity_booking_id'] ?>
                                                <?php echo $activity['oactivity_booking_id']; ?>
                                                <span class="cancel-status"> 
                                                    <?php
                                                    if (!empty($activity['cancel_data'])) {
                                                        $orderCancelId = FatUtility::int($activity['cancel_data']['ordercancel_id']);
                                                        if ($orderCancelId > 0) {
                                                            echo sprintf(Info::t_lang('TRAVELER_ORDER_INVOICE_CANCELLATION_STATUS_%s'), Info::getOrderCancelRequestStatusByKey($activity['cancel_data']['ordercancel_status']));
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </p>


                                            <h3 style="margin:0;"><?php echo $activity['oactivity_activity_name'] ?></h3>
                                            <p style="margin:0;"><?php echo Info::t_lang('Invoice_Hosted_By'); ?>: <?php echo $activity['host_name'] ?></p>

                                            <span><?php echo FatDate::format($activity['oactivity_event_timing'], true); ?></span>
                                        </td>
                                        <td  style="outline: none; padding: 0.5em 1em ;font-weight:inherit; white-space: nowrap; text-align:left; border-top: 1px solid rgb(225, 233, 243);"><?php echo Currency::displayPrice($activity['oactivity_unit_price'], false) ?></td>
                                        <td  style="outline: none; padding: 0.5em 1em ;font-weight:inherit; white-space: nowrap; text-align:left; border-top: 1px solid rgb(225, 233, 243);"><?php echo $activity['oactivity_members'] ?></td>

                                        <td style="outline: none; padding: 0.5em 1em ;font-weight: inherit; white-space: nowrap; border-top: 1px solid rgb(225, 233, 243);">
                                            <?php
                                            if (empty($activity['addons'])) {
                                                echo '--';
                                            } else {
                                                ?>
                                                <table class="" style="outline: none; border-collapse: collapse; border-spacing: 0px;  border: 1px solid rgb(225, 233, 243);">
                                                    <thead style="outline: none;">
                                                        <tr style="outline: none;">
                                                            <th style="outline: none; padding: 0.3125em 1.25em; font-weight: normal;  text-transform: uppercase;  text-align:left;"><?php echo Info::t_lang('ADDON_NAME') ?></th>
                                                           <!-- <th style="outline: none; padding: 0.3125em 1.25em; font-weight: normal;  text-transform: uppercase; text-align:left;"><?php echo Info::t_lang('PRICE') ?></th> -->
                                                            <th style="outline: none; padding: 0.3125em 1.25em; font-weight: normal;  text-transform: uppercase; text-align:left;"><?php echo Info::t_lang('NUMBER') ?></th>
                                                            <th style="outline: none; padding: 0.3125em 1.25em; font-weight: normal;  text-transform: uppercase;  text-align:right;"><?php echo Info::t_lang('TOTAL') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="outline: none;">
                                                        <?php
                                                        $addon_totals = 0;
                                                        foreach ($activity['addons'] as $addon) {
                                                            $addon_totals += $addon['oactivityadd_unit_price'] * $addon['oactivityadd_quantity'];
                                                            ?>
                                                            <tr style="outline: none;">
                                                                <td style="outline: none; padding: 0.3125em 1.25em; font-weight: inherit;" ><?php echo Helper::truncateString($addon['oactivityadd_addon_name'], 10, '') ?></td>
                                                                <!--<td data-label="Price" style="outline: none; padding: 0.3125em 1.25em; font-weight: inherit;"><?php echo $addon['oactivityadd_unit_price'] ?></td> -->
                                                                <td  style="outline: none; padding: 0.3125em 1.25em; font-weight: inherit;"><?php echo $addon['oactivityadd_quantity'] ?></td>
                                                                <td data-label="Total" style="outline: none; padding: 0.3125em 1.25em; font-weight: inherit; text-align:right;"><?php echo ($addon['oactivityadd_unit_price'] * $addon['oactivityadd_quantity']) ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <tr style="outline: none;">
                                                            <th colspan="2" style="outline: none; padding: 0.3125em 1.25em; font-weight: inherit; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(225, 233, 243); background-color: rgb(225, 233, 243); text-align:left;"><?php echo Info::t_lang('TOTAL'); ?></th>
                                                            <th style="outline: none; padding: 0.3125em 1.25em; font-weight: inherit; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(225, 233, 243); background-color: rgb(225, 233, 243); text-align:right; text-align:right;"><?php echo $addon_totals; ?></th>
                                                        </tr>
                                                        <tr style="outline: none;"></tr>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                            ?>

                                        </td>
                                        <td style="outline: none; padding: 0.5em 1em ;font-weight: inherit; white-space: nowrap; text-align:right; border-top: 1px solid rgb(225, 233, 243);"><?php echo Currency::displayPrice($activity['oactivity_total_amount'], false) ?> </td>
                                    </tr>
                                <?php } ?>
                                <tr class="" style="outline: none;">
                                    <th colspan="4" style="outline: none; padding: 0.5em 1em ;font-weight: inherit; white-space: nowrap; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(225, 233, 243); background-color: rgb(225, 233, 243); text-align:left;"><?php echo Info::t_lang('ORDER_AMOUNT') ?></th>
                                    <th style="outline: none; padding: 0.5em 1em ;font-weight: inherit; white-space: nowrap; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(225, 233, 243); background-color: rgb(225, 233, 243); text-align:right;"><?php echo Currency::displayDefaultPrice($order['order_net_amount'], false) ?></th>
                                </tr> 

                                </tr> 
                                <tr class="" style="outline: none;">
                                    <th colspan="4" style="outline: none; padding: 0.5em 1em ;font-size:1.5em;font-weight: bold; white-space: nowrap; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(207, 217, 229); background-color: rgb(207, 217, 229); text-align:left;"><?php echo Info::t_lang('TOTAL') ?></th>
                                    <th style="outline: none; padding: 0.5em 1em ;font-size:1.5em;font-weight: bold; white-space: nowrap; border-top-width: 1px; border-top-style: solid; border-top-color: rgb(207, 217, 229); background-color: rgb(207, 217, 229); text-align:right;"><?php echo Currency::displayDefaultPrice($order['order_total_amount'], false) ?></th>
                                </tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 40px 40px">
                        <table style="width:100%; margin:0 auto; ">
                            <tr>
                                <td style="height:30px"></td>
                            </tr>
                            <tr>
                                <td style="text-align:center"><?php echo FatApp::getConfig('conf_copyright_text') ?></td>
                            </tr>
                            <tr>
                                <td style="height:30px"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
