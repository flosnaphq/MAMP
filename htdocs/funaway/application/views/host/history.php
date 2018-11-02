<?php 
defined('SYSTEM_INIT') or die('Invalid Usage.'); 
$frm->setFormTagAttribute('action',FatUtility::generateUrl('host','historyListing'));
$frm->setFormTagAttribute('onsubmit','submitSearch( this); return false;');
$frm->setFormTagAttribute('id','histrySearchFrm');
$frm->setValidatorJsObjectName('searchFrmValidator');
$frm->setFormTagAttribute('class','form form--vertical form--default');
$submit_btn = $frm->getField('submit_btn');//button button--fill button--green fl--right
$submit_btn->developerTags['noCaptionTag']=true;
$submit_btn->setFieldTagAttribute('class', 'button button--fill button--green fl--right button--fit');

$start_date = $frm->getField('start_date');
$start_date->developerTags['noCaptionTag'] = true;
$start_date->developerTags['col'] = 5;
$start_date->addWrapperAttribute('class','span span--5');
$start_date->setFieldTagAttribute('placeholder',$start_date->getCaption());

$end_date = $frm->getField('end_date');
$end_date->developerTags['noCaptionTag'] = true;
$end_date->developerTags['col'] = 5;
$end_date->addWrapperAttribute('class','span span--5');
$end_date->setFieldTagAttribute('placeholder',$end_date->getCaption());



$submit_btn = $frm->getField('submit_btn');
$submit_btn->developerTags['noCaptionTag'] = true;
$submit_btn->developerTags['col'] = 2;
$submit_btn->addWrapperAttribute('class','span span--2');
?>
<main id="MAIN" class="site-main  with--sidebar">
	<div class="site-main__body">
		 <?php require_once(CONF_THEME_PATH.'host/common/wallet-menu.php')?>
	   <section class="section no--padding-bottom" style="min-height:0">
			<div class="container container--static">
				<header class="section__header section-header--bordered">
					<h6 class="header__heading-text fl--left"><?php echo Info::t_lang('MY_WALLET')?></h6>
				</header>
				<div class="section__body">
					<div class="container container--fluid">
						<div class="span__row">
							<div class="span span--4">
								<div class="box">
									<header class="box__header"><?php echo Info::t_lang('BALANCE')?>
									<a href="#wallet-balance-info" class="js-balance-info">
									<svg class="icon icon--info"><use xlink:href="#icon-info" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg>
									</a>
									</header>
									<div class="box__content">
										<span class="box__heading"><?php echo Currency::displayPrice($wallet['wallet_balance']);?></span>
										
									</div>
								</div>
							</div>
							<div class="span span--4">
								<div class="box">
									<header class="box__header"><?php echo Info::t_lang('CREDIT')?>
									<a href="#wallet-credit-info" class="js-credit-info">
									<svg class="icon icon--info"><use xlink:href="#icon-info" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg>
									</a>
									</header>
									<div class="box__content">
										<span class="box__heading"><?php echo Currency::displayPrice($wallet['credit_amount']);?></span>
										
									</div>
								</div>
							</div>
							<div class="span span--4">
								<div class="box">
									<header class="box__header"><?php echo Info::t_lang('DEBIT')?>
									<a href="#wallet-debit-info" class="js-debit-info">
									<svg class="icon icon--info"><use xlink:href="#icon-info" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg>
									</a>
									</header>
									<div class="box__content">
										<span class="box__heading"><?php echo Currency::displayPrice(abs($wallet['debit_amount']));?></span>
										
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
					<?php echo $frm->getFormHtml($frm);?>
				</header>
				<div class="section__body">
					<div class="container container--fluid">
						<div class="span__row">
							
							<div id="listing"></div>
						</div>
					</div>
				</div>
				</div>
		</section>
	</div>
</main>
<div id="wallet-balance-info" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('BALANCE')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo Info::t_lang('TOTAL_BALANCE_FOR_AVAILABLE_FOR_WITHDRAWAL.');?>
			</div>
		</div>
	</div>
</div>
<div id="wallet-credit-info" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('CREDIT')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo Info::t_lang('TOTAL_AMOUNT_OF_NET_SALES_PROCEEDS.');?>
			</div>
		</div>
	</div>
</div>
<div id="wallet-debit-info" style="display:none">
	<div class="modal">
		<div class="modal__header text--center">
			<h6 class="modal__heading"><?php echo Info::t_lang('DEBIT')?></h6>
		</div>
		
		<div class="modal__footer">
			<div class="regular-text innova-editor">
				<?php echo Info::t_lang('TOTAL_AMOUNT_OF_WITHDRAWALS_TO_YOUR_BANK_ACCOUNT.');?>
			</div>
		</div>
	</div>
</div>