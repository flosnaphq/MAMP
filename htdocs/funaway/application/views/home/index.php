<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('action', Route::getRoute('search'));
$frm->setFormTagAttribute('class', 'form form--horizontal form--default');
$frm->setFormTagAttribute('method', 'get');

$submit_btn = $frm->getField('search');
$submit_btn->developerTags['noCaptionTag'] = true;
$submit_btn->setFieldTagAttribute('class', 'form-element__control__icon');
//$submit_btn->developerTags['col'] = 2;
//$submit_btn->addWrapperAttribute('class', 'span span--2');
?>
<main id="MAIN" class="site-main site-main--light">
    <?php if (!empty($banners)) { ?>
        <header class="site-main__header site-main__header--dark">
            <div class="main-carousel__list js-main-carousel">
                <?php
                $size = count($banners);
                $i = 1;
                foreach ($banners as $banner) {
                    ?>
                    <div class="main-carousel__item">
                        <div class="site-main__header__image">
                            <div class="img"><img class="js-img-parallax" data-speed="-1.5" src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'banner', array($banner['banner_id'], 1600, 590)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $banner['banner_title'] ?>"></div>
                        </div>
                        <div class="site-main__header__content">
                            <div class="section section--vcenter">
                                <div class="section__body">
                                    <div class="container container--static">
                                        <div class="span__row">
                                            <div class="span span--10 span--center text--center">
                                                <hgroup>
                                                    <h5 class="main-carousel__special-heading"><?php echo $banner['banner_title'] ?></h5>
                                                    <h6 class="main-carousel__sub-heading"><?php echo $banner['banner_text'] ?></h6>
                                                </hgroup>
                                                <?php
                                                if (!empty($banner['banner_link'])) {
                                                    ?>
                                                    <a href="<?php echo $banner['banner_link']; ?>" class="button button--fill button--primary" style="margin-top:1rem;"><?php echo Info::t_lang('Get_Started'); ?></a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="section search__section" id="search">
                <?php echo $frm->getFormTag(); ?>
                <div class="container container--fluid">
                    <div class="span__row">
                        <div class="span span--9 span--center">
                            <div class="form-element">
                                <div class="form-element__control">
                                    <?php echo $frm->getFieldHTML('keyword'); ?>                                    
                                    <?php echo $frm->getFieldHTML('search'); ?>
                                </div>
                            </div>
                        </div>                       
                    </div>
                    </form><?php echo $frm->getExternalJS(); ?>
                </div>
        </header>
        <?php
    }
    ?>
    <!-- ADVENTURE_AWAITS Section-->     
    <div class="site-main__body">
        <?php if ($featuredCities): ?>
            <section class="section  island__section" id="islands">
                <div class="section__header">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12" style="position:relative;">
                                <hgroup>
                                    <h5 class="heading-text"><?php echo Info::t_lang('PICK_AN_CITY'); ?></h5>
                                    <?php /* <h6 class="sub-heading-text text--green"><?php echo Info::t_lang('ADVENTURE_AWAITS'); ?></h6> */ ?>
                                </hgroup>
                                <?php if (!(count($featuredCities) < 8)) { ?>
                                    <a href="<?php echo Route::getRoute('search', 'index') ?>" class="hidden-on--mobile">
                                        <?php echo Info::t_lang('SHOW_MORE') ?>
                                    </a>
                                <?php } ?> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12">
                                <div class="island-card__list list--6">
                                    <?php
                                    if (!empty($featuredCities)) {
                                        foreach ($featuredCities as $city) {
                                            if ($city['activities'] < 1) {
                                                continue;
                                            }
                                            ?>
                                            <div class="island-card">
                                                <div class="island-card__image">
                                                    <a href="<?php echo Route::getRoute('city', 'details', array($city['city_id'])); ?>" title="<?php echo $city['city_name'] ?>">
                                                        <img width="579" height="434" class="lazysss" src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'cityRandom', array($city['city_id'], 579, 434)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="" title="<?php echo $city['city_name'] ?>" />
                                                    </a>
                                                </div>
                                                <a href="<?php echo Route::getRoute('city', 'details', array($city['city_id'])); ?>" title="<?php echo $city['city_name'] ?>">
                                                    <div class="island-card__content">
                                                        <h6 class="island-card__heading"><?php echo $city['city_name'] ?></h6>
                                                        <span class="island-card__counter"><?php echo $city['activities']; ?></span>
                                                    </div>
                                                </a>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section__footer visible-on--mobile">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12">
                                <?php if (!(count($featuredCities) < 8)) { ?>
                                    <a href="<?php echo Route::getRoute('search', 'index') ?>" class="button button--non-fill button--dark">
                                        <?php echo Info::t_lang('SHOW_MORE') ?>
                                    </a>
                                <?php } ?> 
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <!-- ESCAPADES Section-->
        <section class="section activity__section no--padding-top" id="activities" style="display:none">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12">
                            <hgroup>
                                <h5 class="heading-text"><?php echo Info::t_lang('TOP_ESCAPADES') ?></h5>
                            </hgroup>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">                                
                        <div class="span span--12">    
                            <div class="activity-card__list grid--style" id="feature-list">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section__footer visible-on--mobile">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12">
                                <a href="<?php echo Route::getRoute('search') ?>" class="button button--non-fill button--dark"><?php echo Info::t_lang('SHOW_MORE') ?></a>
                            </div>
                        </div>
                    </div> 
                </div>
        </section>
        <!--Why Choose section Done-->
        <?php if (!empty($wuc['block_content'])) { ?>
            <section class="section why-choose__section" id="whyChooseUs">
                <div class="section__header">
                    <div class="container container--static">
                        <div class="choose__row">
                            <?php echo html_entity_decode($wuc['block_content']) ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>
        <!--Trending Activities Section Done-->
        <?php if ($services): ?>
            <section class="section category__section" id="categories" >
                <div class="section__header">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--10">
                                <hgroup>
                                    <h5 class="heading-text"><?php echo Info::t_lang('THEMES'); ?></h5>
                                    <?php /* <h6 class="sub-heading-text text--orange"><?php echo Info::t_lang("WHAT'S_YOUR_FLAVOR?"); ?></h6> */ ?>
                                </hgroup>
                                <?php if (!(count($services) < 8)) { ?>
                                    <a href="<?php echo Route::getRoute('services', 'index') ?>" class="hidden-on--mobile">
                                        <?php echo Info::t_lang('SHOW_MORE') ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--10" style="position: relative;">
                                <div class="category-card__list list--carousel js-carousel" data-slides="5,3,2" data-arrows="0" data-next=".category-card__next">
                                    <?php
                                    foreach ($services as $service) {
                                        ?>                               
                                        <div class="category-card">
                                            <a class="category-card__wrap" href="<?php echo Route::getRoute('services', 'index', array($service['service_id'])) ?>" >
                                                <figure class="category-card__image"><img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'service', array($service['service_id'], 200, 200)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $service['service_name']; ?>" width="200" height="200" alt="<?php echo $service['service_name']; ?>" title="<?php echo $service['service_name']; ?>" /></figure>
                                                <div class="category-card__content"><span><?php echo $service['service_name']; ?></span></div>
                                            </a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <a href="javascript:void(0);" class="category-card__next">
                                    <svg class="icon icon--arrow"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow"></use></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section__footer visible-on--mobile">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12">
                                <?php if (!(count($services) < 8)) { ?>
                                    <a href="<?php echo Route::getRoute('services', 'index') ?>" class="hidden-on--mobile">
                                        <?php echo Info::t_lang('SHOW_MORE') ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>			
            </section>
        <?php endif; ?>
        <!--Stats section Done-->        
        <section id="facts" class="section fact__section" style="background-image:url(<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('Image', 'homepageBanner', array(AttachedFile::FILETYPE_HOME_PAGE_BANNER_STATS, 2000, 500)), CONF_DEF_CACHE_TIME, '.jpg'); ?>);">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--8 span--center">

                            <hgroup>
                                <h3 class="special-heading-text text--center"><?php echo Info::t_lang('Digitizing_island_services_for_2020'); ?></h3>
                                <h6 class="sub-heading-text text--center"><?php echo Info::t_lang('Your_Travel_Experiences'); ?></h6>
                            </hgroup>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section__body">
                <div id="mc_embed_signup_scroll" class="container container--static">
                    <div class="span__row">
                        <div class="span span--10 span--center">
                            <div class="fact-card__list list--3">
                                <div class="fact-card">
                                    <div class="fact-card__content">
                                        <div class="fact-card__count"><?php echo ($userCounts['total_host'] > 0 ? $userCounts['total_host'] : 0); ?></div>
                                        <div class="fact-card__text"><?php echo Info::t_lang('Hosts'); ?></div>
                                    </div>
                                </div>
                                <div class="fact-card">
                                    <div class="fact-card__content">
                                        <div class="fact-card__count"><?php echo $activity; ?></div>
                                        <div class="fact-card__text"><?php echo Info::t_lang('Activitie_s'); ?></div>
                                    </div>
                                </div>
                                <div class="fact-card">
                                    <div class="fact-card__content">
                                        <div class="fact-card__count"><?php echo ($userCounts['total_traveler'] > 0 ? $userCounts['total_traveler'] : 0); ?></div>
                                        <div class="fact-card__text"><?php echo Info::t_lang('Travellers'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> 
        <!--End Stats Section-->
        <!--Customer Speaks Done-->
        <?php if (!empty($testimonials)) { ?>
            <section id="asSeenOn" class="section testimonial__section">
                <div class="section__header">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--10 span--center">
                                <hgroup>
                                    <h5 class="heading-text text--center"><?php echo Info::t_lang('AS_SEEN_ON'); ?></h5>
                                </hgroup>
                            </div>
                        </div>
                    </div>   
                </div>
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--11" style="position: relative;">
                                <div class="js-carousel" data-slides="3" data-arrows="0" data-next=".testimonial__next" role="toolbar">
                                    <?php foreach ($testimonials as $testimonial) { ?>                            
                                        <div class="testimonial__item">
                                            <div class="testimonial__image ">
                                                <img alt="<?php echo $testimonial[Testimonial::DB_TBL_PREFIX . 'name'] ?>" title="<?php echo $testimonial[Testimonial::DB_TBL_PREFIX . 'name'] ?>" src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('Image', 'testimonial', array($testimonial[Testimonial::DB_TBL_PREFIX . 'id'], 100, 100)), CONF_DEF_CACHE_TIME, '.jpg'); ?>">
                                            </div>
                                            <div class="testimonial__content">
                                                <h6 class="testimonial__heading"><?php echo $testimonial[Testimonial::DB_TBL_PREFIX . 'name'] ?></h6>
                                                <div class="more-less">
                                                    <div class="more-block">
                                                        <p class="testimonial__text"><?php echo $testimonial[Testimonial::DB_TBL_PREFIX . 'content'] ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>                                    
                                </div>
                                <a href="javascript:void(0);" class="testimonial__next">
                                    <svg class="icon icon--arrow"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow"></use></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>
        <!--Ends Testimonial Section -->
        <!--contact us Done -->
        <section class="section promotion__section" id="islandAdventure" style="background-image:url(<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('Image', 'homepageBanner', array(AttachedFile::FILETYPE_HOME_PAGE_BANNER_CONTACT, 2000, 500)), CONF_DEF_CACHE_TIME, '.jpg'); ?>)">
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--10 span--center text--center">
                            <div class="promotion__content">
                                <h5 class="special-heading-text"><?php echo Info::t_lang('#ISLANDADVENTURES'); ?></h5>
                                <h6 class="sub-heading-text"><?php echo Info::t_lang('DISCOVER_HOW_WE_CREATES_AUTHENTIC_EXPERIENCES_ON_ISLANDS.'); ?></h6>
                            </div>
                            <div class="promotion__action">
                                <a href="mailto:<?php echo FatApp::getConfig('ADMIN_CAREER_EMAIL_ID') ?>" class="button button--fill button--primary"> <?php echo Info::t_lang('VOLUNTEER'); ?></a>
                            </div>                           
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php
        // Shifted this code in Footer page.        
        /*  if (!empty($our_mission)) { ?>
          <section class="section">
          <div class="section__header">
          <div class="container container--static">
          <div class="span__row">
          <div class="span span--10 span--center">
          <hgroup>
          <h5 class="heading-text text--center"><?php echo $our_mission['block_title']; ?></h5>
          </hgroup>
          </div>
          </div>
          </div>
          </div>
          <div class="section__body">
          <div class="container container--static">
          <div class="span__row">
          <div class="span span--8 span--center">
          <div class="f__block">
          <div class="regular-text innova-editor"><?php echo html_entity_decode($our_mission['block_content']); ?></div>
          </div>

          <?php
          $GDPRMailChimpForm = FatApp::getConfig('CONF_MAILCHIMP_NEWS_LETTER_URL', null, '');
          if ($GDPRMailChimpForm != '') {
          ?>
          <div class="f__block_mc">
          <?php #echo Helper::fat_shortcode('[fat_mailchimpnewsletter]'); ?>
          <center><a class="button button--fill button--primary" id="open-MailChimp-popup" href="javascript:void(0);" onClick="showMailChimpPopUp(); return false;"> <?php echo Info::t_lang('Subscribe_to_Newsletter'); ?> </a></center>
          </div>
          <?php
          }
          ?>
          </div>
          </div>
          </div>
          </div>
          </section>
          <?php } */
        ?>
    </div>
</main>