<?php //Info::test($arr_listing);      ?>
<div class="span__row">
    <div class="span span--12">
        <?php if (!empty($arr_listing)) { ?>
            <div class="scrollable--x">
                <table class="table table--bordered table--responsive">
                    <thead>
                    <th><?php echo Info::t_lang('BOOKING_ID'); ?></th>
                    <th><?php echo Info::t_lang('ACTIVITY'); ?></th>
                    <th><?php echo Info::t_lang('Host_Order_list_Traveler'); ?></th>
                    <th><?php echo Info::t_lang('STATUS'); ?></th>
                    <th><?php echo Info::t_lang('ACTIVITY_DATE'); ?></th>
                    <th><?php echo Info::t_lang('AMOUNT'); ?></th>
                    <th><?php echo Info::t_lang('ACTION'); ?></th>
                    </thead>
                    <tbody>
                        <?php foreach ($arr_listing as $order) { ?>
                            <tr class="info">
                                <th class="info__details" data-label="<?php echo Info::t_lang('BOOKING_ID'); ?>"><h6 class="info__heading"><?php echo $order['oactivity_booking_id'] ?></h6></th>
                                <td data-label="<?php echo Info::t_lang('ACTIVITY'); ?>"><?php echo str_replace('[-]', '<br>', $order['ordered']) ?></td>
                                <td data-label="<?php echo Info::t_lang('Host_Order_list_Traveler'); ?>">
									<b><?php echo Info::t_lang('Name'); ?>:</b> <?php echo $order['user_firstname'] . ' ' . $order['user_lastname'] ;?><br />
									<b><?php echo Info::t_lang('Email'); ?>: </b><?php echo $order['user_email'];?><br />
									<?php if(!empty($order['user_phone'])) { ?>
										<b><?php echo Info::t_lang('Phone'); ?>: </b><?php echo $order['user_phone_code'] . $order['user_phone'];?>
									<?php } ?>
								</td>
                                <td data-label="<?php echo Info::t_lang('STATUS'); ?>"><?php echo Info::getPaymentStatus($order['order_payment_status']); ?></td>
                                <td data-label="<?php echo Info::t_lang('STATUS'); ?>"><?php echo FatDate::format($order['oactivity_event_timing'], true); ?></td>
                                <td data-label="<?php echo Info::t_lang('AMOUNT'); ?>"><?php echo $order['oactivity_booking_amount'] ?></td>
                                <td data-label="<?php echo Info::t_lang('ACTION'); ?>">
                                    <a href="<?php echo FatUtility::generateUrl('host', 'detail', array($order['oactivity_booking_id'])) ?>" class="button button--square button--fill button--dark button--small"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-view"></use></svg></a>

                                    <?php if ($order['oactivity_event_timing'] > Info::currentDatetime()) { ?>
                                        <?php if (empty($order['ordercancel_id'])) { ?>
                                            <a class="button button--fill button--dark button--small js-order-cancel" href="<?php echo FatUtility::generateUrl('host', 'order-cancel', array($order['oactivity_booking_id'])); ?>" ><?php echo Info::t_lang('CANCEL') ?></a>
                                        <?php }/* elseif($order['ordercancel_status'] == OrderCancel::STATUS_PENDING){ ?>
                                          <a class="button button--fill button--red" href="javascript:;" onclick="addcancelBookingReminder('<?php echo $order['oactivity_booking_id']?>')"><?php echo Info::t_lang('SEND_REMINDER')?></a>
                                          <?php } */ ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php
            echo FatUtility::createHiddenFormFromData($postedData, array(
                'name' => 'frmUserSearchPaging', 'id' => 'pretend_search_form'
            ));
            if ($totalPage > 1) {
                ?>


                <nav class="pagination text--center">

                    <ul class="list list--horizontal no--margin-bottom">
                        <?php
                        echo FatUtility::getPageString(
                                '<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', $totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, '<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"> << </span></a></li>', '<li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"> >> </span></a></li>', '<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><</a></li>', '<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);">></a></li>');
                        ?>
                    </ul>
                </nav>

                <?php
            }
        } else {
            echo Helper::noRecord(Info::t_lang('NO_ORDERS'));
        }
        ?>
    </div>
</div>