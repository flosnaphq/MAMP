<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<!Doctype html>
<html>
    <!-- Basic Page Needs ================================================== -->
    <head>
        <meta charset="UTF-8">
        <title><?php echo FatApp::getConfig('conf_website_name') ?>-Admin</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"> 
		
		<style>
			<?php echo AppUtilities::includeFonts();?>
        </style>
		
        <?php
        echo $this->getJsCssIncludeHtml(!CONF_DEVELOPMENT_MODE);
        ?>
        <script language="javascript" type="text/javascript" src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
        <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js" type="text/javascript"></script>
    </head>
    <?php
    $dashboard_layout = $admin_layout;
    $body_class = 'class="default"';
    if ($dashboard_layout == 1) {
        $body_class = 'class="switch_layout"';
    }
    ?>
    <body <?php echo $body_class; ?>>

        <main id="wrapper"><!--wrapper start here-->
            <header id="header" > <!--header start here-->
                <div class="headerwrap">
                    <div class="one_third_grid"><a href="javascript:void(0);" class="menutrigger"></a></div>
                    <div class="one_third_grid logo"><a href="<?php echo FatUtility::generateUrl('home', '', array()) ?>">
                            <img alt="" src="<?php echo FatUtility::generateFullUrl('image', 'companyLogo', array('conf_website_admin_logo'), '/'); ?>">
                        </a></div>
                    <div class="one_third_grid">
                        <a href="<?php echo FatUtility::generateUrl('admin', 'logout'); ?>" title="Logout" class="logout"></a>
                        <ul class="iconmenus">
                            <li class="switchtoggle">
                                <label class="switch <?php echo ($dashboard_layout == 1) ? 'active' : '' ?>">
                                    <span class="switch-label" data-on="Fluid" data-off="Fixed"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </li>
                            <li class="droplink" >
                                <a target="_blank" href="<?php echo FatUtility::generateUrl('', '', array(), "/") ?>" title="View Website"><i class="icon ion-android-globe"></i></a>
                            </li>
							<?php if(true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {?>
							<li class="erase">
								<a title="Clear Cache" href="javascript:void(0);" onClick="clearFatCache();return false;"><img src="<?php echo CONF_WEBROOT_URL;?>images/erase.svg" alt="" class="iconerase"></a>
							</li>
							<?php } ?>
                        </ul>
                    </div>
                </div>  
                <div class="searchwrap">
                    <div class="searchform"><input type="text"></div><a href="javascript:void(0)" class="searchclose searchtoggle"></a>
                </div>
            </header>    
            <div id="body">
                <!--left panel start here-->
                <span class="leftoverlay"></span>
                <aside class="leftside">
                    <div class="sidebar_inner">
                        <div class="profilewrap">
                            <div class="profilecover">
                                <figure class="profilepic"><img id="leftmenuimgtag" src="<?php echo FatUtility::generateFullUrl('image', 'companyLogo', array('conf_website_admin_logo'), '/'); ?>" alt=""></figure>
                                <span class="profileinfo"><?php echo "Welcome" . ' ' . $adminName; ?></span>
                            </div>    
                            <div class="profilelinkswrap">
                                <ul class="leftlinks">
                                    <li class=""><a href="<?php echo FatUtility::generateUrl('profile'); ?>">Edit Profile</a></li>
                                    <li class=""><a href="<?php echo FatUtility::generateUrl('profile', 'changePassword'); ?>">Change Password</a></li>
                                    <li class=""><a href="<?php echo FatUtility::generateUrl('admin', 'logout'); ?>">Logout</a></li>
                                </ul>   
                            </div>    
                        </div>
                       
						<?php
							echo FatUtility::decodeHtmlEntities($menus);
						?>
                    
                    </div>
                </aside>
                <!--left panel end here-->
                <?php if (Message::getErrorCount() + Message::getMessageCount() > 0) { ?>
                    <div class="system_message">
                        <a class="closeMsg" href="javascript:void(0);"></a>
                        <?php echo Message::getHtml(); ?>
                    </div>
                <?php } ?>
                
                <div class='page'>
                <?php
                if (isset($breadcrumb))
                    echo html_entity_decode($breadcrumb);
                ?>
