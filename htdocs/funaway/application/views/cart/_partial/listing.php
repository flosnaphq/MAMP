
<?php if (!empty($carts)) { ?>	
    <div class="section__header">
        <div class="container container--static">
            <div class="span__row">
                <div class="span span--10 span--center">
                    <div class="container container--fluid">
                        <div class="span__row">
                            <div class="span span--7">
                                <?php echo Info::t_lang('ITEM_DESCRIPTION') ?>
                            </div>
                            <div class="span span--5 clearfix">
                                <span class="fl--left"><?php echo Info::t_lang('OPTION'); ?></span>
                                <span class="fl--right"><?php echo Info::t_lang('TOTAL'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section__body">
        <div class="container container--static">
            <div class="span__row">
                <div class="span span--10 span--center">

                    <?php #Info::test($carts); exit;?> 

                    <div class="container container--fluid cart__list">
                        <?php foreach ($carts as $cart) { ?>
                            <?php Info::test($cart['cart_id']); ?>
                            <div class="cart__item">
                                <div class="span__row">
                                    <div class="span span--7">
                                        <div class="media">
                                            <div class="media__figure media--left item__image">
                                                <img src="<?php echo FatUtility::generateUrl('image', 'activity', array($cart['events']['activity_image_id'], 120, 90)) ?>" alt="">
                                            </div>
                                            <div class="media__body item__content">
                                                <?php /*    <span class="item__cat"><a href="#">DIVING / Discover Scuba</a></span> */ ?> 
                                                <h6 class="item__heading"><a href="<?php echo Route::getRoute('activity', 'detail', array($cart['events']['activity_id'])) ?>"><?php echo $cart['events']['activity_name'] ?></a></h6>
												<?php 
												$displayDate = date('Y-m-d H:i:s', strtotime($cart['events']['activityevent_time']));
												if ($cart['events']['activityevent_anytime'] == 1) {
													$date = date('Y-m-d', strtotime($cart['events']['activityevent_time']));
													$displayDate = sprintf(Info::t_lang('Lbl_Cart_Full_Day_%s'), $date);
												}
												?>
												<h6 class="item__heading"><?php echo $displayDate;?></h6>
                                                <?php if (isset($cart['addons']) && !empty($cart['addons'])) {
                                                    ?>
                                                    <ul class="list list--horizontal item__addon">
                                                        <?php foreach ($cart['addons'] as $addon) { ?>
                                                            <li><span href="javascript:void()" class="tag">
                                                                    <span class="tag__label"><?php echo $addon['activityaddon_text'] ?></span>
                                                                    
                                                                    <span class="tag__remove"><?php echo $addon['size'] ?></span>
                                                                </span>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                                <div class="buttons__group item__action">
                                                    <?php
                                                      $delete = '<svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg>';
                                                    ?>
                                                    <a href="javascript:;" onclick = "deleteFromCart('<?php echo Info::t_lang('DO_YOU_WANT_TO_REMOVE?') ?>',<?php echo $cart['cart_id']; ?>, '<?php echo $cart['events']['activity_id'] ?>', '<?php echo $cart['events']['activityevent_id'] ?>');"  class="button button--small button--fill button--red button--square"><?php echo $delete; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span span--5 clearfix">
                                        <div class="fl--left">
                                            <span class="option__heading"><?php echo Info::t_lang('PARTICIPANTS'); ?></span>
                                            <span class="option__action">
                                                <select onchange = "updateParticpant(<?php echo $cart['cart_id']; ?>,<?php echo $cart['events']['activity_id'] ?>,<?php echo $cart['events']['activityevent_id'] ?>, this, <?php echo $cart['member']; ?>)">
                                                    <?php   
                                                    $activityMemberCount = $cart['events']['activity_members_count'];
                                                    for ($i = 1; $i <= $activityMemberCount; $i++) { ?>
                                                        <option value="<?php echo $i ?>" <?php if ($i == $cart['member']) echo "selected"; ?>>
                                                            <?php echo $i; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="fl--right text--right">
                                            <span class="total__cal"><?php echo Currency::displayPrice($cart['events']['activity_price']) ?> x <?php echo $cart['member'] ?></span>
                                            <br>
                                            <?php if(isset($cart['addons'] )){
                                                foreach ($cart['addons'] as $addon) { ?>
                                            <span class="total__cal"><?php echo Info::t_lang('Lbl_Cart_Add_On') ?><?php echo Currency::displayPrice($addon['activityaddon_price']) ?> x <?php echo $addon['size'] ?></span><br>
                                            <?php }} ?>
                                            <span class="total__amt"><?php echo Currency::displayPrice($cart['price']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="container container--fluid">
                        <div class="span__row">
							<div class="span span--6 block continue__block">
								<nav class="buttons__group clearfix">
									<a href="<?php echo Route::getRoute("search"); ?>" class="button button--fill button--primary"><?php echo Info::t_lang('CONTINUE_SHOPPING'); ?></a>
								</nav>
							</div>
                            <div class="span span--6 span--last block summary__block">
                                <h6 class="block__heading-text"><?php echo Info::t_lang('ORDER_SUMMARY') ?></h6>
                                <div class="clearfix summary__sub">
                                    <span class="fl--left"><?php echo Info::t_lang('SUB_TOTAL') ?></span>
                                    <span class="fl--right"><?php echo Currency::displayPrice($sub_total) ?></span>
                                </div>

                                <h6 class="block__heading-text summary__total">
                                    <span class="fl--left"><?php echo Info::t_lang('AMOUNT_PAYABLE') ?></span>
                                    <span class="fl--right"><?php echo Currency::displayPrice($total) ?></span>
                                </h6>
                                <a href="<?php echo Route::getRoute("order", "payment") ?>" class="button button--large button--fill button--red"><?php echo Info::t_lang('PLACE_ORDER'); ?></a>
                            </div>

                        </div>
                    </div>		
                </div>
            </div>
        </div>
    </div>	 
<?php }else { ?>
    <?php echo Helper::noRecord(Info::t_lang("EMPTY_CART!")); ?>
<?php } ?>									 