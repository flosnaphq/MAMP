<main id="MAIN" class="site-main site-main--dark">
    <header class="site-main__header site-main__header--light">
        <div class="site-main__header__content">
            <div class="section section--vcenter">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--8">
                            <nav class="" role="navigation">
                                <p id="bread-crumb-label" class="assistive__text">You are here:</p>
                                <ol class="breadcrumb list list--horizontal" aria-labelledby="bread-crumb-label">
                                    <li class="text-heading--label"><a href="<?php echo Route::getRoute(); ?>"><?php echo Info::t_lang('Home') ?></a></li>
                                    <li class="text-heading--label"><a  class=""><?php echo Info::t_lang('HOST') ?></a></li>
                                    <li class="text-heading--label"><a  class=""><?php echo $user_data['user_firstname'] . ' ' . $user_data['user_lastname'] ?></a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="site-main__body">
        <section class="section section--lightest no--padding-top section__host-profile">
            <div class="container container--static">
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--12">
                                <div class="media host">
                                    <div class="media__figure media--right">
                                        <a href="/" class="host__avatar">
                                            <img src="<?php echo FatUtility::generateUrl('image', 'user', array($user_data[User::DB_TBL_PREFIX . 'id'], 220, 220)) ?>" alt="<?php echo $user_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $user_data[User::DB_TBL_PREFIX . 'lastname'] ?>">
                                        </a>
                                    </div>
                                    <div class="media__body">
                                        <hgroup>
                                            <h4 class="host__name"><?php echo $user_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $user_data[User::DB_TBL_PREFIX . 'lastname'] ?></h4>
                                            <h6 class="host__label"><small><?php echo Info::t_lang('MEMBER_SINCE_');
echo date('M Y', strtotime($user_data[User::DB_TBL_PREFIX . 'regdate'])) ?></small></h6>
                                        </hgroup>
                                        <?php echo Info::rating($total_review['rating']) ?>
                                        <?php echo @$total_review['total_count'] . Info::t_lang('_REVIEWS') ?>
                                        <?php if (!empty($user_data[User::DB_TBL_PREFIX . 'description'])) { ?>
                                            <p class="host__desc"><?php echo $user_data[User::DB_TBL_PREFIX . 'description'] ?></p>
<?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section section__listing">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12" style="position:relative;">
                            <hgroup>
                                <h5 class="heading-text"><?php echo Info::t_lang('Host_Activites'); ?></h5>
                      
                            </hgroup>
                               
                        </div>
                    </div>
                </div> 
            </div>
            <div class="section__body listing__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--12">
                            <div class="activity-card__list grid--style" id = "js-activity-list">


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
</main>
<script>
    var host_id = '<?php echo $host_id ?>';
</script>