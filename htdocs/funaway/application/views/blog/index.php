<?php require_once(CONF_THEME_PATH.'blog/common/social_js.php');?>
    <!-- Wrapper -->

        <main id="MAIN" class="site-main site-main--light">
            <header class="site-main__header">
               <div class="site-main__header__content">
                    <div class="section section--vcenter">
                        <div class="container container--static">
                            <h5 class="special-heading-text text--center"><?php echo Info::t_lang('STAY_UPDATE')?></h5>
                            <h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('FOOTOOL_BLOG')?></h6>
                        </div>
                    </div>
                </div>
            </header>
            <div class="site-main__body">
                <section class="section no--padding-top" id="">
                   <?php require_once(CONF_THEME_PATH.'blog/common/top-panel.php');?>
                    <div class="section__body">
                        <div class="container container--static">
                            <div class="span__row">
                                <div class="span span--10 span--center" id="post-list">
                                 
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
       