<main id="MAIN" class="site-main ">
    <header class="site-main__header site-main__header--light">
        <div class="site-main__header__content">
            <div class="section section--vcenter">
                <div class="container container--static">

                    <?php if (!empty($service_name)) { ?>
                        <h5 class="special-heading-text"><?php echo $service_name; ?><?php # echo Info::t_lang('CATEGORIES');      ?></h5>
                        <nav role="navigation">
                            <ol class="breadcrumb list list--horizontal" aria-labelledby="bread-crumb-label">
                                <li class="text-heading--label"><a href="<?php echo Route::getRoute('services') ?>"><?php echo Info::t_lang('THEMES'); ?></a></li>
                                <li class="text-heading--label"><a><?php echo $service_name; ?></a></li>
                            </ol>
                        </nav>
                    <?php } else { ?>
                        <h5 class="special-heading-text"><?php echo Info::t_lang('THEMES'); ?></h5>
                    <?php } ?>
                </div>
            </div>
        </div>
    </header>
    <div class="site-main__body">
        <?php if (trim($service_desc) != "") { ?>
            <section class="section no--padding-top" style="background-color:#ffffff;">				
                <div class="container container--static">
                    <div class="section__body">
                        <div class="innova-editor">
                            <?php echo html_entity_decode($service_desc); ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>
        <section class="section">
            <div class="container container--static">
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12" >
                                <div class="category-card__list list--<?php echo empty($service_id) ? 5 : 5; ?>" id="listing"></div>
                                <nav class="pagination text--center" id="load-more" style="display:none;">
                                    <a href="javascript:;" class="button button--fill button--dark" onclick="loadMore();"><?php echo Info::t_lang('SHOW_MORE') ?></a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section category__section section--top-border" id="ACTIVITIES" style="display:none">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12">
                            <hgroup>
                                <h1 class="heading-text "><?php echo Info::t_lang('TOP_ACTIVITES') ?></h1>
                                <h6 class="sub-heading-text "><?php echo $service_name; ?></h6>
                            </hgroup>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="section__body" >
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12">
                            <div class="activity-card__list  grid--style">
                                <div class="activity-media__list" id="theme-activities-list">
                                </div>
                            </div>

                            <nav class="text--center" style="margin-top:1.2em;display:none;" id="see-all-activity"  >
                                <?php
                                $params = '?';
                                if ($service_id) {
                                    $params .= 'activity_type=' . $service_id;
                                }
                                ?>
                                <a href="<?php echo Route::getRoute('search') . $params; ?>" class=""><?php echo Info::t_lang('SEE_ALL_RESULT') ?></a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<script>
    var service_id = <?php echo FatUtility::int($service_id); ?>;
</script>