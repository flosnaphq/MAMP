<script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>

<script language="javascript" type="text/javascript" src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>

<script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js" type="text/javascript"></script>	
<main id="MAIN" class="site-main site-main--dark with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'hostactivity/common/right-menu.php'); ?> 
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('ADD_LISTING') ?></h6>
                    </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--3 js-sticky" data-sticky-offset="105" data-sticky-responsive="true">
                                <nav class="menu menu--large menu--bordered js-menu-tab">
                                    <ul  class="list list--vertical ">
                                        <li><a id='first-tb' href="javascript:;"  class="active"  onclick = "step1(this);
                                                return false;"><?php echo Info::t_lang('BASIC_INFORMATION'); ?></a></li>
                                        <li><a id='second-tb' href="javascript:;"   onclick = "step2(this);
                                                return false;"><?php echo Info::t_lang('PHOTOS'); ?></a></li>
                                        <li><a id='third-tb' href="javascript:;"   onclick = "step3(this);
                                                return false;"><?php echo Info::t_lang('VIDEOS'); ?></a></li>
                                        <li><a id='fourth-tb' href="javascript:;"   onclick = "step4(this);
                                                return false;"><?php echo Info::t_lang('ACTIVITY_BRIEF') ?></a></li>
                                        <li><a id='fifth-tb' href="javascript:;"   onclick = "step5(this);
                                                return false;"><?php echo Info::t_lang('MAP'); ?></a></li>
                                        <li><a id='sixth-tb' href="javascript:;"   onclick = "step6(this);
                                                return false;"><?php echo Info::t_lang('AVAILABILITY'); ?></a></li>
                                        <li><a id='seventh-tb' href="javascript:;"   onclick = "step7(this);
                                                return false;"><?php echo Info::t_lang('ADDONS'); ?></a></li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="span span--9 span-offset--1">
                                <div class='form-section'>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

</main>
<a href="javascript:void(0);" class="confirm" style="display:none;">Show</a>
<script>
    var mapbox_access_token = '<?php echo FatApp::getConfig('mapbox_access_token') ?>';
</script>  
