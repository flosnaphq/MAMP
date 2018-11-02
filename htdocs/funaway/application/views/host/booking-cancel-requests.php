<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('CANCELLATIONS') ?></h6>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row" id="listing"></div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section" style="min-height:0;">
            <div class="container container--static" ></div>
        </section>
    </div>
</main>