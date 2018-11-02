<script src='https://api.mapbox.com/mapbox.js/v2.4.0/mapbox.js'></script>
<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$cityId = $cityInfo['city_id'];
?>

<!-- Wrapper -->

<main id="MAIN" class="site-main site-main--light">
    <header class="site-main__header site-main__header--dark main-carousel__list js-main-carousel">
        <?php
        $size = count($banners);
        if ($size == 0) {
            $banners[] = array('afile_id' => 0);
            $size = 1;
        }

        $i = 1;
        foreach ($banners as $banner) {
            ?>
            <div class="main-carousel__item">
                <div class="site-main__header__image">
                    <div class="img">
                        <img class="js-img-parallax" data-speed="1" src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'city', array($banner['afile_id'], 2000, 1100)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="">
                    </div>
                </div>
                <div class="site-main__header__content hidden-on--mobile hidden-on--tablet">
                    <div class="section section--vcenter">
                        <div class="section__footer">
                            <div class="container container--static">
                                <div class="span__row">
                                    <div class="span span--12">

                                        <div class="fl--right text--right main-carousel__counter">
                                            <label>
                                                <small><?php echo Info::t_lang('FEATURED_LIST') ?></small>
                                                <span><?php echo $i++; ?></span>/<span><?php echo $size; ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?> 
    </header>
    <div class="site-main__body">
        <div class="menu-bar js-sticky" data-sticky-offset="80" data-sticky-responsive="true">
            <div class="container container--static">
                <nav class="menu" role="navigation">
                    <ul class="list list--horizontal">
                        <li><a href="#CATEGORIES" data-offset="80"><?php echo Info::t_lang('THEMES') ?></a></li>
                        <li><a href="#ACTIVITIES" data-offset="80"><?php echo Info::t_lang('ACTIVITIES') ?></a></li>

                    </ul>
                </nav>
            </div>
        </div>
        <section class="section intro__section" id="intro">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--10 span--center">
                            <hgroup>
                                <h1 class="heading-text text--center"><?php echo Info::t_lang('WELCOME_TO'); ?> <?php echo $cityInfo['city_name'] ?></h1>
                                <h6 class="sub-heading-text text--center"><?php echo Info::t_lang('ADVENTURE_AWAITS'); ?></h6>
                            </hgroup>
                        </div>
                    </div>
                </div>   
            </div>
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--10 span--center">
                            <article class="into text--center">
                                <div class="regular-text innova-editor">
                                    <?php echo html_entity_decode($cityInfo['city_detail']); ?>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php if (!empty($services)) { ?>
            <section class="section section--top-border category__section" id="CATEGORIES" >
                <div class="section__header">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--12">
                                <hgroup>
                                    <h5 class="heading-text "><?php echo Info::t_lang('THEMES'); ?></h5>
                                    <h6 class="sub-heading-text "><?php echo Info::t_lang("WHAT'S_YOUR_FLAVOUR?"); ?></h6>
                                </hgroup>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="section__body">
                    <div class="container container--static">
                        <div class="span__row">
                            <div class="span span--10" style="position: relative;">
                                <div class="category-card__list list--carousel js-carousel" data-slides="5,3,2" data-arrows="0" data-next=".category-card__next">
                                    <!-- 1 for true and 0 for false -->
                                    <?php
                                    foreach ($services as $service_id => $service) {
                                        ?>
                                        <div class="category-card">
                                            <a href="<?php echo Route::getRoute('search') . '?activity_type=' . $service_id . '&city=' . $cityId ?>" class="category-card__wrap" title="<?php echo $service['service_name']; ?>">
                                                <figure class="category-card__image">
                                                    <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'service', array($service_id, 620, 620)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $service['service_name']; ?>">
                                                </figure>
                                                <div class="category-card__content">
                                                    <span><?php echo $service['service_name']; /* ?>( <?php echo $service['tot_activities']; ?> ) */ ?></span>
                                                </div>
                                            </a>
                                        </div>
                                    <?php } ?>                                    
                                    
                                </div>
								<?php if(count($services) > 5) { ?>
									<a href="javascript:void(0);" class="category-card__next">
										<svg class="icon icon--arrow"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-arrow"></use></svg>
									</a>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?> 
        
        <section class="section category__section section--top-border" id="ACTIVITIES" style="display:none">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12">
                            <hgroup>
                                <h5 class="heading-text"><?php echo Info::t_lang('TOP_ACTIVITES_ON') ?> <?php echo $cityInfo['city_name'] ?></h5>
                                <h6 class="sub-heading-text"><?php echo Info::t_lang('ADVENTURE_AWAITS') ?></h6>
                            </hgroup>
                            <a id="see-all-activity" style="display:none"  href="<?php echo Route::getRoute('search') . '?city=' . $cityId ?>" class=""><?php echo Info::t_lang('SEE_ALL_RESULT') ?></a>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12">
                            <div class="activity-card__list grid--style">
                                <div class="activity-media__list" id="island-activities-list">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>


</main>


<script>
    var city_id = <?php echo FatUtility::int($selectedcityId) ?>;
</script>  


