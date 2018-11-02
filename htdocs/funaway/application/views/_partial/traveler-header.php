<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php if (!empty($header_msg)) { ?>
    <div class="site-header-nofication" style="display:none">
        <div class="container container--static text--center"> <p> 
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
<header id="HEADER" class="site-header site-header--fixed" >

    <div class="container container--static clearfix">

        <div class="h__brand fl--left">
            <a href="<?php echo FatUtility::generateUrl(); ?>" class="h__logo">
                <img src="<?php echo FatUtility::generateFullUrl('image', 'companyLogo', array('conf_website_logo')); ?>" alt="">
            </a>
        </div>
        <div class="h__navigation fl--left">
            <?php require_once("header-mega-menu.php") ?>
        </div>

        <div class="h__navigation fl--right">
            <?php require_once("traveler-menu.php") ?>
            <!-- Cart is disabled For Host -->
            <?php if (!$isUserLogged || $user_type == 0) { ?>

                <a href="<?php echo Route::getRoute('cart') ?>" class="h__cart">
                    <span class=""><?php echo Info::t_lang('Cart') ?> </span><span class="h__cart__count cartCount"> <?php
                        $crt = new Cart();
                        echo $crt->getCartCount();
                        ?></span>
                </a>


            <?php } ?>
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