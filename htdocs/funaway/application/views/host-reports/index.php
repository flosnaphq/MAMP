<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('hostReports', 'reportsListing'));
$frm->setFormTagAttribute('onsubmit', 'submitSearch(searchFrmValidator, this); return false;');
$frm->setValidatorJsObjectName('searchFrmValidator');
$frm->setFormTagAttribute('class', 'form form--vertical form--default');
$submit_btn = $frm->getField('submit_btn'); //button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag'] = true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--fit button--green fl--right');

$activity_id = $frm->getField('activity_id');
$activity_id->developerTags['noCaptionTag'] = true;
$activity_id->developerTags['col'] = 3;
$activity_id->addWrapperAttribute('class', 'span span--3');

$start_date = $frm->getField('start_date');
$start_date->setFieldTagAttribute('placeholder', $start_date->getCaption());
$start_date->developerTags['noCaptionTag'] = true;
$start_date->developerTags['col'] = 2;
$start_date->addWrapperAttribute('class', 'span span--2');

$end_date = $frm->getField('end_date');
$end_date->setFieldTagAttribute('placeholder', $end_date->getCaption());
$end_date->developerTags['noCaptionTag'] = true;
$end_date->developerTags['col'] = 2;
$end_date->addWrapperAttribute('class', 'span span--2');

$report_type = $frm->getField('report_type');
$report_type->developerTags['noCaptionTag'] = true;
$report_type->developerTags['col'] = 2;
$report_type->addWrapperAttribute('class', 'span span--2');

$submit_btn = $frm->getField('submit_btn');
$submit_btn->developerTags['noCaptionTag'] = true;
$submit_btn->developerTags['col'] = 3;
$submit_btn->addWrapperAttribute('class', 'span span--3');
?>
<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section no--padding-bottom" style="min-height:0">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('REPORT') ?></h6>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <?php /* ?><div class="span span--4">
                              <div class="box">
                              <header class="box__header"><?php echo Info::t_lang('NEW_BOOKINGS')?>
                              <br>
                              <small>(<?php echo Info::t_lang('FROM_LAST_LOGIN').' : '.date('j M Y H:i',strtotime($last_login))?>)</small></header>
                              <div class="box__content">
                              <span class="box__heading"><?php echo Currency::displayPrice($new_records['total_net_amount']);?></span>
                              <span><?php echo Info::t_lang('TOTAL_BOOKING')?> : <?php echo $new_records['total_net_records']; ?></span>

                              </div>
                              </div>
                              </div> <?php */ ?>
                            <div class="span span--6">
                                <div class="box">
                                    <header class="box__header"><?php echo Info::t_lang('TODAY_BOOKINGS') ?></header>
                                    <div class="box__content">
                                        <span class="box__heading"><?php echo Currency::displayPrice($today_records['total_net_amount']); ?></span>
                                        <span><?php echo Info::t_lang('TOTAL_BOOKING') ?> : <?php echo $today_records['total_net_records']; ?></span>

                                    </div>
                                </div>
                            </div>
                            <div class="span span--6">
                                <div class="box">
                                    <header class="box__header"><?php echo Info::t_lang('LAST_7_DAYS') ?></header>
                                    <div class="box__content">
                                        <span class="box__heading"><?php echo Currency::displayPrice($last_7_records['total_net_amount']); ?></span>
                                        <span><?php echo Info::t_lang('TOTAL_BOOKING') ?> : <?php echo $last_7_records['total_net_records']; ?></span>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </section>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text "><?php echo Info::t_lang('GENERATE_REPORT') ?></h6>
                    
                </header>
                <div class="section__body">
                    
                    <?php echo $frm->getFormHtml($frm); ?>                    

                    <div class="container container--static" id="listing"></div>

                </div>


            </div>
        </section>
    </div>
</main>