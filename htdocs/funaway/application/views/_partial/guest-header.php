<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php if (!empty($header_msg)) { ?>
    <div class="site-header-nofication" style="display:none">
        <div class="container container--static text--center">
            <p><?php echo $header_msg; ?><a class="fl--right close-js" href="javascript:clearSystemMessage();">
                    <svg class="icon icon--cross">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use>
                    </svg>
                </a></p>
        </div>
    </div>

<?php } ?>

<!-- Header -->
<header id="HEADER" class="site-header site-header--fixed" >

     <div class="container container--static clearfix">

        <div class="h__brand fl--left">
            <a href="<?php echo FatUtility::generateUrl(); ?>" class="h__logo">                
                <!--<img src="<?php echo FatUtility::generateFullUrl('image', 'companyLogo', array('conf_website_footer_logo')); ?>" alt="">-->                
                <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'companyLogo', array('conf_website_logo')), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="">
            </a>
        </div>

        <div class="h__navigation fl--right">
            <button class="menu__toggle h__trigger js-menu-toggle">
                <svg class="icon icon--list" style="width:1.25em"><use xlink:href="#icon-list" /></svg>
            </button>
            <nav class="menu main-menu js-main-menu" id="MENU">

                <ul class="list list--horizontal">
                    <?php if ($action != 'loginForm') { ?>
                        <?php if (isset($_SESSION['login_as']) && $_SESSION['login_as'] == 'traveler') { ?>
                            <li><a href="<?php echo Route::getRoute('guest-user', 'login-form') ?>"><?php echo Info::t_lang('ALREADY_A_MEMBER') ?></a></li>
                        <?php } else { ?>
                            <li><a href="<?php echo Route::getRoute('cms', 'become-a-host'); ?>"><?php echo Info::t_lang('BECOME_A_HOST') ?></a></li>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if (isset($_SESSION['login_as']) && $_SESSION['login_as'] == 'traveler') { ?>
                            <li><a href="<?php echo Route::getRoute('guest-user', 'social') ?>"><?php echo Info::t_lang('NOT_MEMBER_YET') ?></a></li>
                        <?php } else { ?>
                            <li><a href="<?php echo $become_a_host_link; ?>"><?php echo Info::t_lang('BECOME_A_HOST') ?></a></li>
                        <?php } ?>
                    <?php } ?>	
                </ul>
            </nav>

        </div>

        <?php /* ?>
        <div class="h__search fl--left">
            <span class="search__icon">
                <svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-search"></use></svg>
            </span>
            <span class="search__text"><input type="text" id="search-autocomplete" value="" placeholder="<?php echo Info::t_lang('Activities') ?>" class="tt-input empty" autocomplete="off" spellcheck="false" dir="auto" style="position: relative; vertical-align: middle;"></span>
        </div>
        <?php */ ?>

    </div>
</header>