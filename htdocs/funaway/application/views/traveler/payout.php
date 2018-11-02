<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'traveler/common/profile-menu.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text"><?php echo Info::t_lang('PAYOUT') ?></h6>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12" id="form-wrapper"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>