<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>




<div class="no-print fixed-demo-btn" id="demo-btn">
    <a href="<?php echo FatUtility::generateUrl('custom', 'requestDemo'); ?>" class="modaal-ajax">
        <!--a href="javascript:void(0);" class="request-demo" id="btn-demo"-->
        Request a Demo
    </a>
</div>


<!-- Footer -->
<?php if (isset($controller) && $controller == "guest") { ?>
    <?php /* <footer id="FOOTER" class="site-footer site-footer--light" style="background-image:url(<?php echo FatUtility::generateUrl('Image', 'fatImages', array('footer-bg.jpg', 'fullwidthbanner', 2000, 500)); ?>);"> */ ?>
    <footer id="FOOTER" class="site-footer site-footer--light">
        <?php
    } elseif (isset($class) && $class == "is--dashboard") {
        $usertype = User::getLoggedUserAttribute("user_type");
        ?>

        <footer id="FOOTER" class="site-footer site-footer--light">
        <?php } else {
            ?>
            <?php /*
              <script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script>
              <script type="text/javascript">
              require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us12.list-manage.com","uuid":"24c8dcd85be85d18ebbb9716d","lid":"062b62f477"}) });
              </script>

              <script type="text/javascript" src="//downloads.mailchimp.com/js/signup-forms/popup/embed.js" data-dojo-config="usePlainJson: true, isDebug: false"></script><script type="text/javascript">require(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us12.list-manage.com","uuid":"24c8dcd85be85d18ebbb9716d","lid":"0f80b99cbd"}) })</script>

              <a href="http://eepurl.com/dxb35j" class="iframe">Show</a>
              <script>
              $('.iframe').modaal({
              type: 'iframe'
              });
              </script> */ ?>
            <footer id="FOOTER" class="site-footer site-footer--light">
            <?php } ?>

            <?php ///*******New design Addedby 0142********* ?>

            <section class="site-footer__upper">

                <div class="container container--static">
                    <div class="span__row">
                        <?php if (!empty($our_mission)) { ?>
                            <div class="span span--8">
                                <div class="f__block">
                                    <h6 class="f__block__heading"><?php echo $our_mission['block_title']; ?></h6>
                                    <div class="regular-text innova-editor"><?php echo html_entity_decode($our_mission['block_content']); ?><br></div>
                                </div>
                                <?php
                                $GDPRMailChimpForm = FatApp::getConfig('CONF_MAILCHIMP_NEWS_LETTER_URL', null, '');
                                if ($GDPRMailChimpForm != '') {
                                    ?>
                                    <div class="f__block_mc">
                                        <?php #echo Helper::fat_shortcode('[fat_mailchimpnewsletter]'); ?>
                                        <a class="button button--fill button--primary" id="open-MailChimp-popup" href="javascript:void(0);" onClick="showMailChimpPopUp(); return false;"> <?php echo Info::t_lang('Subscribe_to_Newsletter'); ?> </a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>

                        <?php } ?>

                        <div class="span span--2 span--last">
                            <div class="f__block f__currency">
                                <h6 class="f__block__heading"><?php echo Info::t_lang('CURRENCY'); ?></h6>
                                <form class="form form--theme form--vertical">
                                    <div class="form-element no--margin">
                                        <div class="form-element__control">
                                            <select class='js-currency-class'>
                                                <?php foreach ($currencyopt as $k => $v) { ?>
                                                    <option value="<?php echo $k ?>" <?php if ($k == Info::getCurrentCurrency()) { ?> selected="selected" <?php } ?>><?php echo $v ?></option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                    </div>
                                </form>

                            </div>
                            <a href="<?php echo Route::getRoute(); ?>" class="f__logo">
                                <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'companyLogo', array('conf_website_footer_logo')), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="">
                            </a>
                        </div>                                               
                    </div>


                    <!--  Second dfdf -->

                    <div class="span__row">


                        <div class="span span--3">
                            <?php
                            $browse_cms = Navigation::getNavigations(1);
                            ?>
                            <div class="f__block">
                                <h6 class="f__block__heading"><?php echo Info::t_lang('BROWSE') ?></h6>
                                <ul class="list list--vertical">
                                    <ul class="list list--vertical">
                                        <?php if (!empty($browse_cms)) { ?>
                                            <?php foreach ($browse_cms as $cms) { ?>
                                                <li><a href="<?php echo $cms['link']; ?>" target = "<?php echo $cms['target'] ?>"><?php echo $cms['caption'] ?></a></li>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>
                            </div>
                        </div>



                        <div class="span span--3">
                            <div class="f__block">
                                <?php
                                $about_cms = Navigation::getNavigations(2);
                                ?>
                                <h6 class="f__block__heading"><?php echo Info::t_lang('ABOUT') ?></h6>
                                <ul class="list list--vertical">
                                    <?php if (!empty($about_cms)) { ?>
                                        <?php foreach ($about_cms as $cms) { ?>
                                            <li><a href="<?php echo $cms['link']; ?>" target = "<?php echo $cms['target'] ?>"><?php echo $cms['caption'] ?></a></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>



                        <div class="span span--5 span--last">
                            <div class="f__block">
                                <?php echo Helper::fat_shortcode("[fat_sociallinks]"); ?>
                            </div>
                        </div>

                    </div>                    
                </div> 
            </section>

            <section class="site-footer__upper">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--2 f__certified"></div>
                        <div class="span span--5 span--last f__payment">
                            <ul class="list list--horizontal">
                                <li><img src="<?php echo CONF_WEBROOT_URL; ?>images/payment/payments-logo-small.png" alt="jcb"></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="site-footer__lower">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12 text--center">
                            <p class="regular-text">
                                <?php
                                echo FatApp::getConfig('conf_copyright_text');
                                //echo FatDate::nowInTimezone(FatApp::getConfig('conf_timezone'), 'Y-m-d H:i:s');
                                ?></p>


                            <a href="#disc-content" class="js-disc-inline" style="color:red;">
                                Disclaimer
                            </a>

                            <div id="disc-content" class="disc-content-hidden">
                                <div>
                                    <h6 class="block__heading">Disclaimer</h6>
                                    <p>The content and images shown here are solely for illustration. We do not own, monitor, review or update, and do not have any control over, any third party content/images on our website. None of the content is to be relied upon for, nor to form part of, any contract unless specifically incorporated into a contract in writing. We do not hold any responsibility for how these images are used anywhere else. You should not construe any of the content/images. None of the images will be deployed or distributed in the final copy. We neither endorse nor make warranty or guarantee as to the accuracy, completeness or reliability of these images. If you use these images, you do so entirely at your own risk. Also, any reference to the products, services, process, information or trade names on the images used for illustration here does not constitute or imply any endorsement, sponsorship or recommendation.</p>
                                </div>
                            </div>
                            <style>
                                .disc-content-hidden {
                                    display: none;
                                }
                            </style>
                            <script>
                                $(document).ready(function () {
                                    $(".js-disc-inline").modaal();
                                });
                            </script>

                        </div>
                    </div>
                </div>
            </section>

        </footer>


        <?php ///*************End New Design********  ?>


        <?php
        if (!empty($_SESSION[User::SESSION_ELEMENT_NAME]['email_verify_msg'])) {
            ?>
            <aside  class="alert alert_info fixed"> 
                <div> 		
                    <div class="content"><?php echo $_SESSION[User::SESSION_ELEMENT_NAME]['email_verify_msg']; ?></div> 		

                </div>
            </aside>
            <?php
        }

        if ((Message::getErrorCount() + Message::getMessageCount()) > 0) {
            $messages = Message::getData();

            if (!empty($messages['errs'])) {
                ?>
                <aside id="mbsmessage" class="alert alert_danger"> 	
                    <div> 				
                        <div class="content">
                            <div class="div_error">
                                <ul>
                                    <?php
                                    foreach ($messages['errs'] as $message) {
                                        echo '<li>' . $message . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div> 		
                        <a class="close" onclick="$(document).trigger('close.mbsmessage');"><svg class="icon icon--cross"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></a> 				
                    </div> 				
                </aside>
                <?php
            }
            if (!empty($messages['msgs'])) {
                ?>
                <aside id="mbsmessage" class="alert alert_success"> 	
                    <div> 				
                        <div class="content">
                            <div class="div_msg">
                                <ul>
                                    <?php
                                    foreach ($messages['msgs'] as $message) {
                                        echo '<li>' . $message . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div> 		
                        <a class="close" onclick="$(document).trigger('close.mbsmessage');"><svg class="icon icon--cross"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg></a> 				
                    </div> 				
                </aside>
                <?php
            }
        }
        ?>


        <div  style="display:none;" id="main-search">
            <div class="search-card"  >
                <div class="search-card__action">
                    <div class="container container--static">
                        <span class="search-card__action__label">
                            <svg class="icon icon--search"><use xlink:href="#icon-search" /></svg>
                        </span>
                        <label class="search-card__action__input">
                            <input type="text" id="search-autocomplete" value=""  placeholder="<?php echo Info::t_lang('ACTIVITY_AND_WELLNESS_TRAVELS_ON_ISLANDS_IN_ASIA') ?>">
                        </label>
                        <a href="javascript:;" class="search-card__action__close" onclick="closeMainSearch()">
                            <svg class="icon icon--cross"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg>
                        </a>
                    </div>
                </div>
                <div class="search-card__result" id="search-card-result-wrapper" style="display:none;" >
                    <section class="section">
                        <div class="section__body">
                            <div class="container container--static">
                                <div class="span__row">
                                    <div class="span span--12">

                                        <div class="activity-media__list" id="search-card__result">
                                        </div>
                                        <nav class="text--center" style="margin-top:1.2em;display:none" id="more-result" >
                                            <a href="javascript:;" onclick="loadMoreMainSearch()" class="button button--fill button--dark"><?php echo Info::t_lang('LOAD_MORE') ?></a>
                                        </nav>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>  
        </div> 
        <!-- mobile menu-->
        <nav class="menu mobile-menu js-mobile-menu">
            <button class="block-heading-text block-heading-text--small js-menu-close has--opened">
                <svg class="icon icon--cross"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg> <span><?php echo Info::t_lang('CLOSE') ?></span>
            </button>
        </nav> 

        <div class="overlay js-overlay"></div>
        <!-- mobile menu end -->

        <script>

            !function (f, b, e, v, n, t, s) {
                if (f.fbq)
                    return;
                n = f.fbq = function () {
                    n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq)
                    f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window,
                    document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

            fbq('init', '<?php echo FatApp::getConfig('CONF_FACEBOOK_TRACKING_ID') ?>');
            fbq('track', "PageView");

        </script>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo FatApp::getConfig('CONF_FACEBOOK_TRACKING_ID') ?>&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->

        <a style="display:none;" title="Google Analytics Alternative" href="http://clicky.com/100963909">
            <img alt="Google Analytics Alternative" src="//static.getclicky.com/media/links/badge.gif" border="0" /></a>
        <script src="//static.getclicky.com/js" type="text/javascript"></script>
        <script type="text/javascript">try {
                clicky.init(100963909);
            } catch (e) {
            }</script>
        <noscript><p><img alt="Clicky" width="1" height="1" src="//in.getclicky.com/100963909ns.gif" /></p></noscript>
        <?php echo FatApp::getConfig('CONF_WEBSITE_TRACKING_CODE') ?>
        </body>
        </html>