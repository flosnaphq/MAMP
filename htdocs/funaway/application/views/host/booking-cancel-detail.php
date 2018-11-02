<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

// echo '<pre>'. print_r($activities, true); exit;
?>
<main id="MAIN" class="site-main   with--sidebar">
	<div class="site-main__body">
		<?php require_once(CONF_THEME_PATH.'host/common/report-menu.php')?>
	   <section class="section order__section no--padding" style="min-height:0;">
			<div class="container container--static">
				<header class="section__header section-header--bordered">
					<div class="container container--fluid container--flex">
						<h6 class="header__heading-text"><?php echo Info::t_lang('BOOKING_CANCELLATIONS')?></h6>
						<p class="regular-text text--uppercase no--margin"><?php echo Info::t_lang('BOOKING_ID');?>: <span class="text--red"><?php echo $activities['oactivity_booking_id']?></span></p>
					</div>
				</header>
				<div class="section__body">
					<div class="container container--fluid">
						<div class="span__row">
							<?php /*
							<div class="span span--3">
								<div class="block summary__block clearfix">
									 <h6 class="block__heading-text text--blue"><small><?php echo Info::t_lang('BOOKING_SUMMARY');?></small></h6>
									 <div class="clearfix summary__sub">
										 <span class="fl--left"><?php echo Info::t_lang('TOTAL')?></span>
										 <span class="fl--right"><?php echo Currency::displayPrice($order['order_total_amount']);?></span>
									 </div>

									 <div class="clearfix summary__total">
										 <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYBLE');?></span>
										 <span class="fl--right"><?php echo Currency::displayPrice($order['order_net_amount'])?></span>
									 </div>
								</div>
							</div>
							*/ ?>
							<div class="span span--12">
								<div class="block booking__block clearfix">
									<div class="booking-media__list">
										<div class="media booking-media">
											<div class="media__figure booking-media__image">
												<img src="<?php echo FatUtility::generateUrl('image','dactivity',array($activities['oactivity_activity_id'],100,75));?>" alt="">
											</div>
											<div class="media__body booking-media__content">
												<h6 class="booking-media__heading"><a href="/"><?php echo $activities['oactivity_activity_name']?></a></h6>
												<p class="booking-media__text">
													<?php echo Info::t_lang('ON')?> 
													<?php 
													$activityTime = FatDate::format($activities['oactivity_event_timing'], true);
													if(1 == $activities['oactivity_activityevent_anytime']) {
														
														$date = FatDate::format($activities['oactivity_event_timing'], false);
														
														$activityTime = sprintf(Info::t_lang('Lbl_Cart_Full_Day_%s'), $date);
													}
													echo $activityTime;
													?> 
													
													<?php echo Info::t_lang('FOR')?> <?php echo $activities['oactivity_members']?> <?php echo Info::activityTypeLabelByKey($activities['activity_price_type'])?>
												</p>
												<small class="text--red"><?php echo Info::t_lang('BOOKING_ID')?>: <?php echo $activities['oactivity_booking_id']?></small>
												<?php
												if(!empty($activities['addons'])){?>
													<h6 class="booking-media__heading no--margin"><small><?php echo Info::t_lang('ADDONS');?></small></h6>
													<ul class="list list--horizontal booking-media__addon">
														<?php
														foreach($activities['addons'] as $ac){?>
															<li><span href="/" class="tag">
																<span class="tag__label"><?php echo $ac['oactivityadd_addon_name']?></span>
																<span class="tag__remove"><?php echo $ac['oactivityadd_quantity']?></span>
																</span>
															</li>
														<?php } ?>
													</ul>
												<?php } ?>
											</div>
											<div class="media__body booking-media__content">
												<h6 class="booking-media__heading no--margin">
													<small>
														<?php echo Info::t_lang('CANCELLATION_REQUEST');?>
													</small>
												</h6>
												<p class="text--red no--margin">
													<?php echo Info::t_lang('REQUEST_TIME')?>: <?php echo FatDate::format($cancel_data['ordercancel_datetime'], true)?>
												</p>
												<p class="text--red  no--margin">
													<?php echo Info::t_lang('STATUS')?>: <?php echo Info::getOrderCancelRequestStatusByKey($cancel_data['ordercancel_status'],true)?>
												</p><hr>
												<a href="<?php echo FatUtility::generateUrl('host','add-order-cancel-comment',array($booking_id));?>" class="button button--fill button--blue button--small  js-add-comment">
													<?php echo Info::t_lang('ADD_COMMENT');?>
												</a>
											</div>
										</div>
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
					<div class="container container--fluid container--flex">
						<h6 class="header__heading-text"><?php echo Info::t_lang('BOOKING_CANCELLATION')?></h6>

					</div>
				</header>
				<div class="section__body">
					<div id="listing" class="container container--fluid">
						<div class="span__row">
							<div class="span span--12">
								<?php
								if(!empty($comments)){
								?>
									<table class="table table--bordered table--responsive">
										<thead>
											<tr>
												<th><?php echo Info::t_lang('USER_NAME');?></th>
												<th><?php echo Info::t_lang('DESIGNATION');?></th>
												<th><?php echo Info::t_lang('COMMENT');?></th>
												<th><?php echo Info::t_lang('REQUEST_TIME');?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach($comments as $comment){
												if(empty($comment['user_id'])){
													$comment['user_firstname'] = 'Admin';
													$comment['user_lastname'] = '';
													$comment['user_email'] = '';
													$user_type = 'Admin';
												}
												else{
													$user_type = Info::getUserTypeByKey($comment['user_type']);
												}
												if($loggedUserId == $comment['user_id']){
													$comment['user_firstname'] = 'Me';
													$comment['user_lastname'] = '';
												}
												?>
												<tr class="info">
													<td data-label="<?php echo Info::t_lang('USER_NAME');?>" class="info__details">
														<h6 class="info__heading">
															<?php echo ucwords($comment['user_firstname']).' '.ucwords($comment['user_lastname']);?>
														</h6>
													</td>
													<td data-label="<?php echo Info::t_lang('DESIGNATION');?>">
														<?php echo $user_type; ?>
													</td>
													<td data-label="<?php echo Info::t_lang('COMMENT');?>" class="info__wrap">
														<?php echo nl2br($comment['comment_comment']); ?>
													</td>
													<td data-label="<?php echo Info::t_lang('REQUEST_TIME');?>">
														<?php echo FatDate::format($comment['comment_datetime'],true);?>
													</td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</main>

<script>
$('.js-add-comment').modaal({
		type: 'ajax'
});
</script>
