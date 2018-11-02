<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php if (!empty($header_msg)) { ?>
    <div class="site-header-nofication" style="display:none">
        <div class="container container--static text--center">
            <p>
                <?php echo $header_msg; ?>
                <a class="fl--right close-js" href="javascript:clearSystemMessage();">
                    <svg class="icon icon--cross">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                    </svg>
                </a>
            </p>

        </div>
    </div>
<?php } ?>
<!-- Header -->

<header id="HEADER" class="site-header site-header--fixed">
    <div class="container container--static clearfix">
        <div class="h__brand fl--left">
            <a href="<?php echo FatUtility::generateUrl() ?>" class="h__logo">
                <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'companyLogo', array('conf_website_logo')), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="">
            </a>
        </div>

        <div class="h__navigation fl--left">
            <?php require_once("header-mega-menu.php") ?>
        </div>

        <div  class="h__navigation fl--right">
            <button class="menu__toggle h__trigger js-menu-toggle">
                <svg class="icon icon--list" style="width:1.25em"><use xlink:href="#icon-list" /></svg>
            </button>
            <nav  class="menu main-menu js-main-menu" id="MENU">
                <ul  class="list list--horizontal">
                    <li><a href="<?php echo FatUtility::generateUrl('guest-user', 'login-form'); ?>"><?php echo Info::t_lang('LOGIN'); ?></a></li>
                    <li><a href="<?php echo FatUtility::generateUrl('guest-user', 'social'); ?>"><?php echo Info::t_lang('SIGNUP'); ?></a></li>
                    <?php /*<li><a href="<?php echo $become_a_host_link; ?>" class="button button--fill button--red"><?php echo Info::t_lang('BECOME_A_HOST'); ?></a></li>*/?>
                </ul>
            </nav>
            <a href="<?php echo FatUtility::generateUrl('cart') ?>" class="h__cart">
                <span><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cart"></use></svg></span>
                <span> <span class="hidden-on--mobile"><?php echo Info::t_lang('BAG') ?></span> [ <span class="text--red cartCount"><?php
                        $crt = new Cart();
                        echo $crt->getCartCount();
                        ?></span> ]</span>
            </a>
        </div>

        <div class="h__search fl--left">
            <span  class="search__icon">
                <svg  class="icon icon--search">
                <use  xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-search"></use>
                </svg>
            </span>
            <input type="text" id="search-autocomplete" value=""  placeholder="<?php echo Info::t_lang('ACTIVITY_AND_WELLNESS_TRAVELS_ON_ISLANDS_IN_ASIA') ?>">
        </div>
        
    </div>
</header>

<!-- Wrapper -->