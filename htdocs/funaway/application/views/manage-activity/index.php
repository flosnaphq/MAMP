<script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
<script language="javascript" type="text/javascript" src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>

<script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js" type="text/javascript"></script>	
<main id="MAIN" class="site-main  with--sidebar" ng-app="activityApp">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'hostactivity/common/right-menu.php'); ?> 
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <div class="container container--fluid container--flex">
                        <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('ADD_LISTING') ?></h6>
                    </div>
                </header>
                <div class="section__body" ng-controller="MainController">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--3">
                                <style>
                                    /*.js-menu-tab .list.list--flex{
                                        -webkit-flex-wrap: nowrap;
                                        -ms-flex-wrap: nowrap;
                                        flex-wrap: nowrap;
                                    }*/
                                </style>
                                <nav class="menu menu--large menu--bordered menu--icon js-menu-tab">
                                    <!--
									<div class="scrollable--x">
										<svg class="icon icon--check">
											<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-check"></use>
										</svg>
									-->
									<ul class="list list--vertical" auto-active>
										<li><a id='first-tb'  class="active" href="#!/">
												<span>
													<strong>1</strong>
												</span> 
												<?php echo Info::t_lang('BASIC_INFORMATION'); ?></a></li>
										<li><a id='fourth-tb' href="#!/activity-brief" >
												<span>
													<strong>2</strong>
												</span> 
												<?php echo Info::t_lang('ACTIVITY_BRIEF') ?></a></li>
										<li><a id='sixth-tb' href="#!/availablity" >
												<span>
													<strong>3</strong>
												</span> 
												<?php echo Info::t_lang('AVAILABILITY'); ?></a></li>
										<li><a id='second-tb' href="#!/photos">
												<span>
													<strong>4</strong>
												</span>  
												<?php echo Info::t_lang('PHOTOS'); ?></a></li>
										<li><a id='third-tb' href="#!/videos" >
												<span>
													<strong>5</strong>
												</span>  
												<?php echo Info::t_lang('VIDEOS'); ?></a></li>
										<li><a id='fifth-tb' href="#!/map" >
												<span>
													<strong>6</strong>
												</span> 
												<?php echo Info::t_lang('MAP'); ?></a></li>
										<li><a id='seventh-tb' href="#!/addons">
												<span>
													<strong>7</strong>
												</span> 
												<?php echo Info::t_lang('ADDONS'); ?></a></li>
									</ul>
                                    <!--</div>-->
                                </nav>
                            </div>
                            <div class="span span--9 span-offset--1">
                                <div style="max-width:900px;margin:auto;">
                                    <div class='form-section' ng-view></div>
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
    var activityState = '<?php echo $activityState ?>';
</script>