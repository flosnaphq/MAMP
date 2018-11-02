<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/sales-reports-menu.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text"><?php echo Info::t_lang('SALES_REPORT') ?></h6>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--3">
                                <?php require_once(CONF_THEME_PATH . 'host/common/sales-reports-left.php') ?>
                            </div>
                            <div class="span span--9 span-offset--1" id="form-wrapper">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>