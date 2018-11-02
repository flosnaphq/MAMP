<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text"><?php echo Info::t_lang('REQUEST_WITHDRAWAL') ?></h6>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">

                            <div class="span span--9 span--center" >
                                <table class="table table--fixed table--bordered table--responsive withdrawal-req-tbl">
                                    <tbody>
                                        <tr>
                                            <td><?php echo Info::t_lang('REQUEST_AMOUNT') ?></td>
                                            <td><?php echo $data['withdrawalrequest_amount'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo Info::t_lang('COMMENT') ?></td>
                                            <td><?php echo nl2br($data['withdrawalrequest_comment']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo Info::t_lang('ADMIN_COMMENT') ?></td>
                                            <td><?php echo nl2br($data['withdrawalrequest_admin_comment']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo Info::t_lang('REQUEST_TIME') ?></td>
                                            <td class="text--red"><?php echo FatDate::format($data['withdrawalrequest_datetime'], true); ?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo Info::t_lang('STATUS') ?></td>
                                            <td><?php echo Info::getWithdrawalRequestStatusByKey($data['withdrawalrequest_status']); ?></td>
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