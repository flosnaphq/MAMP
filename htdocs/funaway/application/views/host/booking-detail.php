<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
//Info::test($activities);
//exit;
?>
<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section order__section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text"><?php echo Info::t_lang('BOOKING_DETAILS') ?></h6>
                        <p class="regular-text text--uppercase no--margin"><?php echo Info::t_lang('BOOKING_ID'); ?>: <span class="text--red"><?php echo $activities['oactivity_booking_id'] ?></span></p>
                        <a target="_blank" href="<?php echo FatUtility::generateUrl('host', 'printInvoice', array($activities['oactivity_booking_id'])) ?>" class="button button--fill button--red button--small fl--right"><?php echo Info::t_lang('PRINT') ?></a>
                    </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">

                            <div class="span span--12">
                                <table class="table table--bordered table--responsive info-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo Info::t_lang('BOOKING_ID'); ?></th>
                                            <th><?php echo Info::t_lang('ACTIVITY'); ?></th>
                                            <th><?php echo Info::t_lang('PRICE'); ?></th>
                                            <th><?php echo Info::t_lang('NUMBER'); ?></th>
                                            <th><?php echo Info::t_lang('ADDON'); ?></th>
                                            <th><?php echo Info::t_lang('TOTAL'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="info">
                                            <th data-label="<?php echo Info::t_lang('BOOKING_ID'); ?>" class="info__details"><h6 class="info__heading"><?php echo $activities['oactivity_booking_id'] ?></h6></th>
                                            <td data-label="<?php echo Info::t_lang('ACTIVITY'); ?>"><?php echo Helper::addBrString($activities['oactivity_activity_name'], 21) ?></td>
                                            <td data-label="<?php echo Info::t_lang('PRICE'); ?>"><?php echo Currency::displayPrice($activities['oactivity_unit_price']) ?></td>
                                            <td data-label="<?php echo Info::t_lang('NUMBER'); ?>"><?php echo $activities['oactivity_members'] ?></td>
                                            <td data-label="<?php echo Info::t_lang('ADDON'); ?>">
                                                <?php
                                                if (!empty($activities['addons'])) {
                                                    ?>
                                                    <table class="table table-responsive table-select" style="border:1px solid #ddd">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo Info::t_lang('ADDON_NAME') ?></th>
                                                                <th><?php echo Info::t_lang('PRICE') ?></th>
                                                                <th><?php echo Info::t_lang('NUMBER') ?></th>
                                                                <th><?php echo Info::t_lang('TOTAL') ?></th>
                                                            </tr>
                                                        </thead>
                                                        <?php
                                                        $addon_totals = 0;
                                                        foreach ($activities['addons'] as $addon) {
                                                            $addon_totals += $addon['oactivityadd_unit_price'] * $addon['oactivityadd_quantity'];
                                                            ?>
                                                            <tr>
                                                                <td data-label="<?php echo Info::t_lang('ADDON_NAME'); ?>"><?php echo Helper::addBrString($addon['oactivityadd_addon_name'], 10) ?></td>
                                                                <td data-label="<?php echo Info::t_lang('PRICE'); ?>"><?php echo $addon['oactivityadd_unit_price'] ?></td>
                                                                <td data-label="<?php echo Info::t_lang('NUMBER'); ?>"><?php echo $addon['oactivityadd_quantity'] ?></td>
                                                                <td data-label="<?php echo Info::t_lang('TOTAL'); ?>"><?php echo ($addon['oactivityadd_unit_price'] * $addon['oactivityadd_quantity']) ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <tr>
                                                            <th style="background-color:#e8eef6" colspan="3">
                                                                <?php echo Info::t_lang('TOTAL'); ?>
                                                            </th>
                                                            <th style="background-color:#e8eef6"><?php echo $addon_totals; ?>
                                                            </th>
                                                        </tr> 
                                                    </table>
                                                    <?php
                                                } else {
                                                    echo '--';
                                                }
                                                ?>
                                            </td>
                                            <td data-label="<?php echo Info::t_lang('TOTAL'); ?>"><?php echo Currency::displayPrice($activities['oactivity_total_amount']) ?> </td>
                                        </tr>
                                        <tr class="info">
                                            <th colspan="5" style="background-color:#e8eef6"><?php echo Info::t_lang('BOOKING_AMOUNT') ?></th>
                                            <th style="background-color:#e8eef6"><?php echo Currency::displayPrice($activities['oactivity_booking_amount']) ?></th>
                                        </tr>

                                        <tr class="info">
                                            <th colspan="5" ><?php echo Info::t_lang('TOTAL_PAID_BY_CUSTOMER') ?></th>
                                            <th><?php echo Currency::displayPrice($activities['oactivity_total_amount']) ?></th>
                                        </tr>


                                        <tr class="info">
                                            <th colspan="5" ><?php echo Info::t_lang('SITE_FEE') ?></th>
                                            <th><?php echo Currency::displayPrice($activities['oactivity_admin_commission']) ?></th>
                                        </tr>
                                        <tr class="info">
                                            <th colspan="5" style="background-color:#e8eef6"><?php echo Info::t_lang('CREDIT_TO_HOST_WALLET') ?></th>
                                            <th style="background-color:#e8eef6"><?php echo Currency::displayPrice($activities['oactivity_host_commission']) ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<div id="send-msg" style="display:none;"></div>
<div id="transaction-info" style="display:none">
    <div class="modal">
        <div class="modal__header text--center">
            <h6 class="modal__heading"><?php echo $transaction_info['block_title'] ?></h6>
        </div>

        <div class="modal__footer">
            <div class="regular-text innova-editor">
                <?php echo htmlspecialchars($transaction_info['block_content']); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.js-transaction-info').modaal();
    });
</script>