<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/wallet-menu.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('REQUEST_WITHDRAWAL') ?> <a class="js-withdrawal-info" href="#withdrawal-info" data-modaal-scope="modaal_1469778955606b948cdc1a910c8"><svg class="icon icon--info"><use xlink:href="#icon-info" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a></h6>
                    <a class="button button--fill button--red button--small fl--right" href="<?php echo FatUtility::generateUrl('host', 'addWithdrawalRequest') ?>">
                        <?php echo Info::t_lang('ADD_NEW'); ?></a>
                </header>

                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            
                            <div id="listing"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<?php if (!empty($withdrawal_info)) { ?>
    <div id="withdrawal-info" style="display:none">
        <div class="modal">
            <div class="modal__header text--center">
                <h6 class="modal__heading"><?php echo Info::t_lang('REQUEST_WITHDRAWAL') ?></h6>
            </div>

            <div class="modal__footer">
                <div class="regular-text innova-editor">
                    <?php echo html_entity_decode($withdrawal_info[Block::DB_TBL_PREFIX . 'content']); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>