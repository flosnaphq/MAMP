<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
    <div class="fixed_container">
        <div class="row">
            <ul class="cellgrid">
                <li>
                    <div class="flipbox green">
                        <div class="flipper">
                            <div class="front">
                                <div class="iconbox">
                                    <figure class="icon"><img src="<?php echo CONF_WEBROOT_URL ?>images/traveler.png" alt=""></figure>
                                    <span class="value"><span>Travelers</span><?php echo $user_totals['total_traveler'] ?></span>

                                </div>
                            </div>
                            <div class="back">
                                <div class="cell">
                                    <div class="group">
                                        <div class="col-sm-6"><span>Total Users</span><?php echo $user_totals['total_traveler'] ?></div>
                                        <div class="col-sm-6"><span>This Month</span><?php echo $user_totals['current_month_total_traveler'] ?></div>
                                    </div>
                                    <a href="<?php echo FatUtility::generateUrl('traveler') ?>" class="themebtn btn-default btn-sm">View Summary</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="flipbox orange">
                        <div class="flipper">
                            <div class="front">
                                <div class="iconbox">
                                    <figure class="icon"><img src="<?php echo CONF_WEBROOT_URL ?>images/hosts.png" alt=""></figure>
                                    <span class="value"><span>Hosts</span><?php echo $user_totals['total_host'] ?></span>

                                </div>
                            </div>
                            <div class="back">
                                <div class="cell">
                                    <div class="group">
                                        <div class="col-sm-6"><span>Total Hosts</span><?php echo $user_totals['total_host'] ?></div>
                                        <div class="col-sm-6"><span>This month</span><?php echo $user_totals['current_month_total_host'] ?></div>
                                    </div>
                                    <a href="<?php echo FatUtility::generateUrl('host') ?>" class="themebtn btn-default btn-sm">View Summary</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="flipbox purple">
                        <div class="flipper">
                            <div class="front">
                                <div class="iconbox">
                                    <figure class="icon"><img src="<?php echo CONF_WEBROOT_URL ?>images/listing.png" alt=""></figure>
                                    <span class="value"><span>LISTINGS</span><?php echo $activity_totals['total'] ?></span>

                                </div>
                            </div>
                            <div class="back">
                                <div class="cell">
                                    <div class="group">
                                        <div class="col-sm-6"><span>Total Listings</span><?php echo $activity_totals['total'] ?></div>
                                        <div class="col-sm-6"><span>Active Listings</span><?php echo $activity_totals['total_active'] ?></div>
                                    </div>
                                    <a href="<?php echo FatUtility::generateUrl('activities') ?>" class="themebtn btn-default btn-sm">View Summary</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="cellgrid">
                <li>
                    <div class="flipbox purple">
                        <div class="flipper">
                            <div class="front">
                                <div class="iconbox">
                                    <figure class="icon"><img src="<?php echo CONF_WEBROOT_URL ?>images/booking.png" alt=""></figure>
                                    <span class="value"><span>BOOKINGS</span><?php echo $order_totals['total_orders'] ?></span>

                                </div>
                            </div>
                            <div class="back">
                                <div class="cell">
                                    <div class="group">
                                        <div class="col-sm-6"><span>Total Bookings</span><?php echo $order_totals['total_orders'] ?></div>
                                        <div class="col-sm-6"><span>This Month</span><?php echo $order_totals['current_month_total'] ?></div>
                                    </div>
                                    <a href="<?php echo FatUtility::generateUrl('orders') ?>" class="themebtn btn-default btn-sm">View Summary</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="flipbox darkgreen">
                        <div class="flipper">
                            <div class="front">
                                <div class="iconbox">
                                    <figure class="icon"><img src="<?php echo CONF_WEBROOT_URL ?>images/sales.png" alt=""></figure>
                                    <span class="value"><span>Sales</span><?php echo Currency::displayPrice($order_totals['total_sales']); ?></span>

                                </div>
                            </div>
                            <div class="back">
                                <div class="cell">
                                    <div class="group">
                                        <div class="col-sm-6"><span>Total Sales</span><?php echo Currency::displayPrice($order_totals['total_sales']); ?></div>
                                        <div class="col-sm-6"><span>This Month</span><?php echo Currency::displayPrice($order_totals['current_month_sales']); ?></div>
                                    </div>
  <a href="<?php echo FatUtility::generateUrl('reports') ?>" class="themebtn btn-default btn-sm">View Summary</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li >
                    <div class="flipbox purple">
                        <div class="flipper">
                            <div class="front">
                                <div class="iconbox">
                                    <figure class="icon"><img src="<?php echo CONF_WEBROOT_URL ?>images/earning.png" alt=""></figure>
                                    <span class="value"><span>Earnings</span><?php echo Currency::displayPrice($order_totals['admin_commision']); ?></span>
                                </div>
                                
                            </div>
                            <div class="back">
                                <div class="cell">
                                    <div class="group">
                                        <div class="col-sm-6"><span>Total Earnings</span><?php echo Currency::displayPrice($order_totals['admin_commision']); ?></div>
                                        <div class="col-sm-6"><span>This Month</span><?php echo Currency::displayPrice($order_totals['current_month_admin_commission']); ?></div>
                                    </div>
  <a href="<?php echo FatUtility::generateUrl('wallet','admin') ?>" class="themebtn btn-default btn-sm">View Summary</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>

            <!--
      <div class="col-sm-12">  
            <section class="section">
                    <div class="sectionhead"><h4>Sales Statistics </h4></div>
                    <div class="sectionbody">
                            <div class='chartwrap'>
                              <img src="images/graph.png" alt="">
                            </div>
                    </div>
            </section>
      </div> -->


            <div class="fourcols"> 


                <div class="col-sm-6">
                    <div class="coloredbox whitewrap">
                        <div class="top">
                            <span class="txtsmall">Reviews This Month</span>
                            <i class="icon ion-chatboxes"></i>
                        </div>
                        <div class="body">
                            <h3><?php echo FatUtility::int($review_totals['current_month_total']) ?> </h3>
                            <a class="themebtn btn-sm" href="<?php echo FatUtility::generateUrl('reviews') ?>">View Summary</a>
                        </div>
                    </div>
                </div> 

                <div class="col-sm-6">
                    <div class="coloredbox blue whitewrap">
                        <div class="top">
                            <span class="txtsmall">Unread Notifications</span>
                            <i class="icon ion-android-mail"></i>
                        </div>
                        <div class="body">
                            <h3><?php echo $unread_notifications; ?></h3>
                            <a class="themebtn btn-sm" href="<?php echo FatUtility::generateUrl('notifications') ?>">View Summary</a>
                        </div>
                    </div>
                </div> 
               


            </div>    





            <?php if ($canViewOrder) { ?>
                <div class="col-sm-12">  
                    <section class="section">
                        <div class="sectionhead">
                            <h4><?php echo Info::t_lang('RECENT_BOOKINGS'); ?> </h4>
                            <!--<a href="" class="themebtn btn-default btn-sm">View All</a>-->
                            <ul class="actions">
                                <li class="droplink">
                                    <a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
                                    <div class="dropwrap">
                                        <ul class="linksvertical">
                                            <li><a href="<?php echo FatUtility::generateUrl('orders', 'listing') ?>">View All</a></li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="sectionbody">
                            <div class="tablewrap" id="listing">

                            </div>    
                        </div>
                    </section>
                </div> 
            <?php } ?>



        </div>
    </div>

