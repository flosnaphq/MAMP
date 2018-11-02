
<main id="MAIN" class="site-main site-main--dark">
    <span id="cat__val" style="display:none;"><?php echo $categories ?></span>
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
                                    <a href="javascript:;" class="displayFilter listing__filter-button"><?php echo Info::t_lang('SHOW_FILTER') ?></a>
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
                                        <select id = "sortFilter" class = "sortFilter">
                                            <option value = ""> <?php echo Info::t_lang('SORT_BY') ?></option>
                                            <?php foreach ($sortby as $k => $srt) { ?>
                                                <option <?php if ($sort == $k) echo 'selected' ?> value = "<?php echo $k ?>"> <?php echo $srt ?></option>
                                            <?php } ?>
                                        </select>
                                    </label>

                                </li>
                                <li>
                                    <label class="select">
                                        <select class = "searchDuration">
                                            <option value= ""> <?php echo Info::t_lang('DURATION') ?></option>
                                            <?php
                                            $durations = Info::searchDuration();
                                            foreach ($durations as $k => $drs) {
                                                ?>
                                                <option <?php if (@$duration == $k) echo 'selected' ?> value= "<?php echo $k ?>"> <?php echo $drs ?></option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                </li>
                                <li>
                                    <label class="select">
                                        <select class = "searchPrice">
                                            <option value= ""> <?php echo Info::t_lang('PRICE') ?></option>
                                            <?php
                                            $searchPrice = Info::searchPrice();
                                            foreach ($searchPrice as $k => $pr) {
                                                ?>
                                                <option <?php if (@$price == $k) echo 'selected' ?> value= "<?php echo $k ?>"> <?php echo $pr ?></option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                </li>	



                            </ul>
                        </nav>
                    </div>
                </header>

                <div class="listing__filter" style="display:none;">

                    <div class="filter__block theme--block" >
                        <h6 class="filter__heading"><?php echo Info::t_lang('SEARCH_BY_KEYWORD'); ?></h6>
                        <ul class="list list--horizontal">
                            <li>
                                <label class="text">
                                    <input type="text" value=<?php echo $keyword; ?> name="keyword" placeholder="<?php echo Info::t_lang('SEARCH_ACTIVIY'); ?>"/>
                                </label>
                            </li>
                        </ul>
                    </div> 

                    <div class="filter__block theme--block" >
                        <h6 class="filter__heading"><?php echo Info::t_lang('THEMES_/_CATEGORIES') ?></h6>
                        <ul class="list list--horizontal">
                            <li>
                                <label class="select">
                                    <select class = "searchThemes" onchange="getSubService(this)">
                                        <option value= ""> <?php echo Info::t_lang('THEMES') ?></option>

                                        <?php foreach ($services as $k => $serv) { ?>
                                            <option <?php if (@$theme == $k) echo 'selected' ?> value= "<?php echo $k ?>"> <?php echo $serv; ?></option>
                                        <?php } ?>
                                    </select>
                                </label>
                            </li>	
                            <li>
                                <label class="select">
                                    <select class = "searchCategories">
                                        <option value=''><?php echo Info::t_lang('CHOOSE_CATEGORY') ?></option>
                                        <?php if (!empty($scats)) { ?>
                                            <?php foreach ($scats as $k => $serv) { ?>
                                                <option <?php if (@$categories == $k) echo 'selected' ?> value= "<?php echo $k ?>"> <?php echo $serv; ?></option>
                                            <?php } ?>
                                        <?php } ?>

                                    </select>
                                </label>
                            </li>
                        </ul>

                    </div>

                    <div class="filter__block theme--block">
                        <h6 class="filter__heading"><?php echo Info::t_lang('Location') ?></h6>
                        <ul class="list list--horizontal list--5">

                            <li>

                                <label class="select">
                                    <select class = "searchcountry" onchange="getCities(this)">
                                        <option value=''><?php echo Info::t_lang('CHOOSE_COUNTRY') ?></option>
                                        <?php foreach ($country as $k => $c) { ?>
                                            <option <?php if (@$country == $k) echo 'selected' ?> value= "<?php echo $k ?>"> <?php echo $c; ?></option>
                                        <?php } ?> 
                                    </select>
                                </label>
                            </li>
                            <li>
                                <label class="select">
                                    <select class = "searchcity">
                                        <option value=''><?php echo Info::t_lang('CHOOSE_CITY') ?></option>
                                    </select>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <div class="filter__block theme--block"></div>



                </div>

                <div class="section__body listing__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12">

                                <div class="activity-card__list <?php if ($type == "list") { ?> list--style <?php } if ($type == "grid") { ?> grid--style <?php } ?> ">
                                    <div id = "js-activity-list">	
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