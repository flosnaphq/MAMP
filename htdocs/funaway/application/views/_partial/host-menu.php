<button class="menu__toggle h__trigger js-menu-toggle">
    <svg class="icon icon--list"><use xlink:href="#icon-list" /></svg>
</button>
<nav class="menu main-menu js-main-menu" id="MENU">
    <ul class="list list--horizontal">
        <li><a href="<?php echo FatUtility::generateUrl('notification'); ?>" title="<?php echo Info::t_lang('USER_MENU_NOTIFICATIONS');?>">
                <svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-notification"></use></svg>
                <?php if ($unreadNotifications): ?>
                    <span class="notification notification--red"><?php echo $unreadNotifications; ?></span>
                <?php endif; ?>
            </a></li>
        <li><a href="<?php echo FatUtility::generateUrl('message'); ?>" title="<?php echo Info::t_lang('USER_MENU_MESSAGES');?>">
                <svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-message"></use></svg>
                <?php if ($unreadMessages): ?>
                    <span class="notification notification--green"><?php echo $unreadMessages; ?></span>
                <?php endif; ?>      
            </a></li>
        <li><a href="<?php echo FatUtility::generateUrl('host', 'history'); ?>" title="<?php echo Info::t_lang('USER_MENU_MY_WALLET');?>"><svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-wallet"></use></svg></a></li>
        <li class="sub-menu sub-menu--left">
            <a href="javascript:;" ><?php echo Info::t_lang('BOOKINGS'); ?></a>
            <ul class="list list--vertical sub-menu-dropdown">
                <li><a href="<?php echo FatUtility::generateUrl('host', 'request'); ?>"><?php echo Info::t_lang('USER_MENU_MY_CONFIRMATION_REQUESTS'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('host', 'bookings'); ?>"><?php echo Info::t_lang('MY_BOOKINGS'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('host', 'bookingCancelRequests'); ?>"><?php echo Info::t_lang('CANCELLATIONS'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('hostReports'); ?>"><?php echo Info::t_lang('REPORTS') ?></a></li>
            </ul>
        </li>
        <li class="sub-menu sub-menu--left">
            <a href="javascript:;" ><?php echo Info::t_lang('LISTING'); ?></a>
            <ul class="list list--vertical sub-menu-dropdown">
                <li><a href="<?php echo FatUtility::generateUrl('hostactivity', 'update', array(0)); ?>"><?php echo Info::t_lang('ADD_LISTING'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('hostactivity'); ?>"><?php echo Info::t_lang('MANAGE_LISTINGS'); ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('wishlist'); ?>"><?php echo Info::t_lang('Wishlist'); ?></a></li>
            </ul>
        </li>
        <li class="sub-menu sub-menu--right">
            <a href="javascript:;"><?php //echo Info::t_lang('HI')         ?> <?php echo $user_name; ?></a>
            <ul class="list list--vertical sub-menu-dropdown">
                <li><a href="<?php echo FatUtility::generateUrl('host', 'profile'); ?>"><?php echo Info::t_lang('PROFILE') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('host', 'payout'); ?>"><?php echo Info::t_lang('PAYOUT_SETTINGS') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('review'); ?>"><?php echo Info::t_lang('REVIEWS') ?></a></li>
                <li><a href="<?php echo FatUtility::generateUrl('user', 'logout'); ?>"><?php echo Info::t_lang('LOGOUT') ?></a></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo FatUtility::generateUrl('host'); ?>" class="avatar"><img src="<?php echo FatUtility::generateUrl('image', 'user', array($loggedUserId, '219', '219', time())); ?>" alt=""></a>
        </li>
    </ul>
</nav>