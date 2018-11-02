<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('action', FatUtility::generateUrl('host', 'orderListing'));
$frm->setFormTagAttribute('onsubmit', 'submitSearch( ); return false;');
$frm->setFormTagAttribute('id', 'ordSearchFrm');
$frm->setValidatorJsObjectName('searchFrmValidator');
$frm->setFormTagAttribute('class', 'form form--vertical form--default');
$submit_btn = $frm->getField('submit_btn'); //button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag'] = true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green button--fit fl--right');

$activity_id = $frm->getField('activity_id');
$activity_id->developerTags['noCaptionTag'] = true;
$activity_id->developerTags['col'] = 3;
$activity_id->addWrapperAttribute('class', 'span span--3');
$activity_id->setFieldTagAttribute('placeholder', $activity_id->getCaption());

$start_date = $frm->getField('start_date');
$start_date->developerTags['noCaptionTag'] = true;
$start_date->developerTags['col'] = 2;
$start_date->addWrapperAttribute('class', 'span span--2');
$start_date->setFieldTagAttribute('placeholder', $start_date->getCaption());


$end_date = $frm->getField('end_date');
$end_date->developerTags['noCaptionTag'] = true;
$end_date->developerTags['col'] = 2;
$end_date->addWrapperAttribute('class', 'span span--2');
$end_date->setFieldTagAttribute('placeholder', $end_date->getCaption());

$booking_id = $frm->getField('booking_id');
$booking_id->developerTags['noCaptionTag'] = true;
$booking_id->developerTags['col'] = 2;
$booking_id->addWrapperAttribute('class', 'span span--2');
$booking_id->setFieldTagAttribute('placeholder', $booking_id->getCaption());

$payment_status = $frm->getField('payment_status');
$payment_status->setFieldTagAttribute('placeholder', $payment_status->getCaption());
$payment_status->developerTags['noCaptionTag'] = true;
$payment_status->developerTags['col'] = 2;
$payment_status->addWrapperAttribute('class', 'span span--3');

$booking_types = $frm->getField('booking_type');
$booking_types->setFieldTagAttribute('placeholder', $booking_types->getCaption());
$booking_types->developerTags['noCaptionTag'] = true;
$booking_types->developerTags['col'] = 2;
$booking_types->addWrapperAttribute('class', 'span span--3');


$submit_btn = $frm->getField('submit_btn');
$submit_btn->developerTags['noCaptionTag'] = true;
$submit_btn->developerTags['col'] = 2;
$submit_btn->addWrapperAttribute('class', 'span span--2');
?>
<main id="MAIN" class="site-main   with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section  no--padding-bottom" style="min-height:0">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('MY_BOOKINGS') ?></h6>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--4">
                                <div class="box">
                                    <header class="box__header"><?php echo Info::t_lang('UPCOMINGS') ?></header>
                                    <div class="box__content">
                                        <span class="box__heading"><?php echo Currency::displayPrice($report['total_pending_booking_amount']); ?></span>
                                        <span><?php echo Info::t_lang('TOTAL_ACTIVITIES : ') . ' ' . $report['total_pending_booking'] ?></span>
                                        <a href="javascript:;" onclick="getDetails(2); return false;" class="button button--fill button--fit button--red button--small no--margin"><?php echo Info::t_lang('DETAILS') ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="span span--4">
                                <div class="box">
                                    <header class="box__header"><?php echo Info::t_lang('COMPLETED') ?></header>
                                    <div class="box__content">
                                        <span class="box__heading"><?php echo Currency::displayPrice($report['total_complete_booking_amount']); ?></span>
                                        <span><?php echo Info::t_lang('TOTAL_ACTIVITIES : ') . ' ' . $report['total_complete_booking'] ?></span>
                                        <a href="javascript:;" onclick="getDetails(1); return false;"  class="button button--fill button--fit button--blue button--small no--margin"><?php echo Info::t_lang('DETAILS') ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="span span--4">
                                <div class="box">
                                    <header class="box__header"><?php echo Info::t_lang('CANCELLED') ?></header>
                                    <div class="box__content">
                                        <span class="box__heading"><?php echo Currency::displayPrice($report['total_cancelled_amount']); ?></span>
                                        <span><?php echo Info::t_lang('TOTAL_ACTIVITIES : ') . ' ' . $report['total_cancelled'] ?></span>
                                        <a href="javascript:;" onclick="getDetails(3); return false;"  class="button button--fill button--fit button--green button--small no--margin"><?php echo Info::t_lang('DETAILS') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container container--static" >
                <header class="section__header section-header--bordered">
                    <div class="span__row">
                    <?php echo $frm->getFormHtml($frm); ?>
                        </div>
                </header>
                <div class="section__body">
                    <div class="container container--fluid" id="listing" >

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>