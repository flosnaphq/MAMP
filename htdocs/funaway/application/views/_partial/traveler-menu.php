<button class="menu__toggle h__trigger js-menu-toggle">
    <svg class="icon icon--list" style="width:1.25em"><use xlink:href="#icon-list" /></svg>
</button>
<nav class="menu main-menu js-main-menu" id="MENU">
    <ul class="list list--horizontal"> 
        <li><a href="<?php echo FatUtility::generateUrl('notification'); ?>">
                <svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-notification"></use></svg>
                <?php if ($unreadNotifications): ?>
                    <span class="notification notification--red"><?php echo $unreadNotifications; ?></span>
                <?php endif; ?>
            </a></li>
        <li><a href="<?php echo FatUtility::generateUrl('message'); ?>">

                <svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-message"></use></svg>
                <?php if ($unreadMessages): ?>
                    <span class="notification notification--green"><?php echo $unreadMessages; ?></span>
                <?php endif; ?>      
            </a></li>
        <li class="sub-menu sub-menu--left">
            <a href="javascript:;"><?php echo Info::t_lang('BOOKINGS') ?></a>
            <ul class="list list--vertical sub-menu-dropdown">
				<li><a href="<?php echo FatUtility::generateUrl('traveler', 'request'); ?>"><?php echo Info::t_lang('USER_MENU_MY_CONFIRMATION_REQUESTS'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('traveler', 'order'); ?>"><?php echo Info::t_lang('MY_BOOKINGS') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('traveler', 'booking-cancel-requests'); ?>"><?php echo Info::t_lang('CANCELLATIONS') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('traveler', 'request'); ?>"><?php echo Info::t_lang('BOOKING_REQUESTS') ?></a></li>
            </ul>
        </li>
        <li class="sub-menu sub-menu--right">
            <a href="javascript:;"><?php echo Info::t_lang('HI') ?> <?php echo $user_name ?></a>
            <ul class="list list--vertical sub-menu-dropdown">
                <li><a href="<?php echo FatUtility::generateUrl('traveler', 'profile'); ?>"><?php echo Info::t_lang('PROFILE') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('wishlist'); ?>"><?php echo Info::t_lang('Wishlist'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('traveler', 'payout'); ?>"><?php echo Info::t_lang('PAYOUT_SETTINGS') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('review'); ?>"><?php echo Info::t_lang('REVIEWS') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('user', 'logout'); ?>"><?php echo Info::t_lang('LOGOUT') ?></a></li>
            </ul>
        </li>
        <li>
            <a  href="<?php echo FatUtility::generateUrl('traveler'); ?>" class="avatar"><img src="<?php echo FatUtility::generateUrl('image', 'user', array($loggedUserId, '219', '219'))."?".Info::timestamp()?>" alt=""></a>
        </li>
    </ul>
</nav>