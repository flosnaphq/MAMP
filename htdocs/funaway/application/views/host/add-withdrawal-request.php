<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$walletAmount = $walletAmount['wallet_balance'];
?>
<main id="MAIN" class="site-main  with--sidebar">
    <div class="site-main__body">
        <?php require_once(CONF_THEME_PATH . 'host/common/report-menu.php') ?>
        <section class="section">
            <div class="container container--static">
                <header class="section__header section-header--bordered">
                    <h6 class="header__heading-text fl--left"><?php echo Info::t_lang('ADD_WITHDRAWAL_REQUEST') ?></h6>
                    <span class=" fl--right" href="/host/add-withdrawal-request"><?php echo Info::t_lang('AVAILABLE_WITHDRWAL_AMOUNT')." : ".Currency::displayDefaultPrice($walletAmount); ?></span>
                </header>
                <div class="section__body">
                    <div class="container container--fluid">
                        <div class="span__row">

                            <div class="span span--9 span-offset--1" id="form-wrapper">
                                <?php
                                $frm->setFormTagAttribute('action', FatUtility::generateUrl('host', 'setupWithdrawalRequest'));
                                $frm->setFormTagAttribute('id', 'updatePasswordForm');
                                $frm->setFormTagAttribute('onsubmit', 'submitForm(updatePasswordFrmValidator, this); return false;');
                                $frm->setValidatorJsObjectName('updatePasswordFrmValidator');
                                $frm->setFormTagAttribute('class', 'form form--default form--horizontal');
                                $submit_btn = $frm->getField('submit_btn'); //button button--fill button--green fl--right
                                $submit_btn->developerTags['noCaptionTag'] = true;
                                $frm->getField('submit_btn');
                                
                                $withdrawalText = $frm->getField('withdrawalrequest_amount');
                                $withdrawalText->requirements()->setRange(1,$walletAmount);
                                $withdrawalText->requirements()->setInt();
								if(1 < $walletAmount) {
									$withdrawalText->requirements()->setCustomErrorMessage("Withdrawal Amount must be  integer and between 1 ,$walletAmount ");
								} else {
									$withdrawalText->requirements()->setCustomErrorMessage("Not sufficient amount in your wallet");
								}

                                $submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right');
                                echo $frm->getFormHtml();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>