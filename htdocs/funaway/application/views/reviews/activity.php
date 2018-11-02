<?php defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<main id="MAIN" class="site-main ">
    <header class="site-main__header site-main__header--light">
        <div class="site-main__header__content">
            <div class="section section--vcenter">
                <div class="container container--static">
                    <h5 class="special-heading-text text--center"><?php echo Info::t_lang('REVIEWS') ?></h5>
                    <h6 class="sub-heading-text text--center text--primary"><?php echo Info::t_lang('WHAT_OUR_TRAVELLERS_SAY') ?></h6>
                </div>
            </div>
        </div>
    </header>
    <div class="site-main__body">
        <section class="section faq__section" id="founder">
            <div class="section__header">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--10 span--center">
                            <hgroup>
                                <h5 class="heading-text"><?php
                                    echo Info::t_lang('REVIEWS_OF_');
                                    echo ' ' . @$activity_data['activity_name']
                                    ?></h5>
                                <h6 class="sub-heading-text text--green"><?php echo Info::t_lang('WHAT_TRAVELLERS_SAY') ?></h6>
                            </hgroup>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section__body">
                <div class="container container--static">
                    <div class="span__row">
                        <div class="span span--10 span--center">
                            <div class="block rating__block overall__rating clearfix">
                                <div class="fl--left">
                                    <h6 class="rating__numbers"><?php echo @$total_reviews['rating'] ?></h6>
                                    <?php echo Info::rating($total_reviews['rating'], false, 'rating--light rating--large') ?>
                                    <span><?php echo $total_reviews['total_count'] ?> <?php echo Info::t_lang('REVIEWS') ?></span>		
                                </div>
                                <?php if ($canUserAddReviews) { ?>
                                    <div class="fl--right">
                                        <a href="#write-review" onclick = 'writeReview(<?php echo $activity_id ?>)' class="button button--large button--fill button--green hidden-on--mobile write-review"><?php echo Info::t_lang('WRITE_A_REVIEW') ?></a>
                                    </div>
                                <?php } ?>
                                <div style="display:none;" id='write-review'></div>
                            </div>
                            <hr>
                            <ul class="list list--vertical review__list" id="listing">
                                <span><?php echo Info::t_lang('LOADING...') ?></span>
                            </ul>
                            <nav class="text--center" style="margin-top: 1.2em; display: none;" id="more-review-result">
                                <a href="javascript:;" onclick="loadMoreReviews()" class="button button--fill button--dark"><?php echo Info::t_lang('LOAD_MORE') ?></a>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<script>
    var activity_id = '<?php echo $activity_id ?>';
</script>