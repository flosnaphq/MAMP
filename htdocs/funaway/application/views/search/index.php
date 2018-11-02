
<main id="MAIN" class="site-main">

    <header class="site-main__header site-main__header--light">
        <div class="site-main__header__content">
            <div class="section section--vcenter">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--8">
                            <h5 class="special-heading special-heading-text"><?php echo Info::t_lang('ACTIVITIES') ?> </h5>

                        </div>
                        <div class="span span--4">
                            <nav class="filters text--right" role="navigation">
                                <ul class="list list--horizontal">

                                    <li>
                                        <a href="javascript:;" onclick="changeListView('list', this)" id="listView" class="f-button hidden-on--mobile hidden-on--tablet displayButton <?php if ($type == "list") echo "active"; ?> ">
                                            <svg class="icon icon--list"><use xlink:href="#icon-list" /></svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;" onclick="changeListView('grid', this)" id="gridView" class="f-button hidden-on--mobile hidden-on--tablet displayButton <?php if ($type == "grid") echo "active"; ?>">
                                            <svg class="icon icon--grid"><use xlink:href="#icon-grid" /></svg>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="site-main__body">
        <div class="container container--static">
            <section class="section section__listing">
                <header class="section__header listing__header no--padding-top">
                    <div class="container container--fluid">
                        <nav class="filters fl--left" role="navigation">
                            <ul class="list list--horizontal">
                                <li>
                                    <?php
                                    $isPreSearch = false;
                                    if ($isPreSearch):
                                        $Filtertext = "Hide Filter";
                                    else:
                                        $Filtertext = "Show Filter";
                                    endif;
                                    ?>
                                    <a href="javascript:;" class="displayFilter listing__filter-button"><?php echo $Filtertext; ?></a>
                                </li>
                                <li>
                                    <a href="javascript:;"   onclick = 'clearSearch();' class="link"><?php echo Info::t_lang('RESET') ?> </a>
                                </li>
                            </ul>
                        </nav>
                        <nav class="filters fl--right" role="navigation">
                            <ul class="list list--horizontal">
                                <li>
                                    <label class="select">
                                        <?php echo $searchFrm->getFieldHtml('sort'); ?>
                                    </label>

                                </li>
                                <li>
                                    <label class="select">
                                        <?php echo $searchFrm->getFieldHtml('duration'); ?>
                                    </label>
                                </li>
                                <li>
                                    <label class="select">
                                        <?php echo $searchFrm->getFieldHtml('price'); ?>
                                    </label>
                                </li>	
                            </ul>
                        </nav>
                    </div>
                </header>
                <?php
                if ($isPreSearch):
                    $style = "style='display:block'";
                else:
                    $style = "style='display:none'";
                endif;
                $style = "";
                //TODO
                ?>
                <div class="listing__filter js-sticky" data-sticky-offset="110" data-sticky-responsive="true" data-sticky-parent=".site-main" <?php echo $style; ?>>
                    <div>
                        <div>
                            <div class="filter__block theme--block" >
                                <h6 class="filter__heading"><?php echo Info::t_lang('SEARCH_BY') ?></h6>
                                <nav class="filters" role="navigation">
                                    <ul class="list list--vertical">
                                        <li>
                                            <label class="select">
                                                <?php echo $searchFrm->getFieldHtml('keyword'); ?>
                                            </label>
                                        </li>	

                                    </ul>
                                </nav>
                            </div>

                            <div class="filter__block theme--block" >

                                <h6 class="filter__heading"><?php echo Info::t_lang('THEMES_/_CATEGORIES') ?></h6>
                                <nav class="filters" role="navigation">
                                    <ul class="list list--vertical">
                                        <li>
                                            <label class="select">
                                                <?php echo $searchFrm->getFieldHtml('activity_type'); ?>
                                            </label>
                                        </li>	
                                        <li>
                                            <label class="select">
                                                <?php echo $searchFrm->getFieldHtml('categories'); ?>
                                            </label>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="filter__block theme--block" >
                                <h6 class="filter__heading"><?php echo Info::t_lang('Location') ?></h6>
                                <nav class="filters" role="navigation">
                                    <ul class="list list--vertical">
                                        <li>
                                            <label class="select">
                                                <?php echo $searchFrm->getFieldHtml('country'); ?>
                                            </label>
                                        </li>	
                                        <li>
                                            <label class="select">
                                                <?php echo $searchFrm->getFieldHtml('city'); ?>
                                            </label>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                </div>


                <div class="section__body listing__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12">

                                <div class="activity-card__list <?php if ($type == "list") { ?> list--style <?php } if ($type == "grid") { ?> grid--style <?php } ?> ">
                                    <div id="js-activity-list">	
                                    </div>
                                </div>
                                <nav  class=" showMoreButton pagination text--center" style="display:none">
                                    <a href="javascript:;" onclick='showMoreActivity()' class="button button--fill button--dark" > <?php echo Info::t_lang('SHOW_MORE'); ?></a>
                                </nav>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
<script>

<?php echo TrackingCode::getTrackingCode(2); ?>
    function facebookWishListTrack() {
<?php echo TrackingCode::getTrackingCode(3); ?>
    }

</script>