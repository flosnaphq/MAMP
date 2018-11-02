<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
?>
<div class="menu-bar">
    <nav class="fl--left" role="navigation">
        <p id="bread-crumb-label" class="assistive__text">You are here:</p>
        <?php echo html_entity_decode($breadcrumb); ?>
    </nav>
    <nav class="menu fl--right" role="navigation">
        <?php
        $activeAddCls = '';
        $activeMangCls = '';        
        if ($_GET['url'] == 'hostactivity') {           
             $activeMangCls = 'active';
        } else if ($_GET['url'] == 'manage-activity') {
            $activeAddCls = 'active';
        }        
        ?>

        <ul class="list list--horizontal">
            
            <?php /*
             * <li><a href="<?php echo FatUtility::generateUrl('hostactivity', 'update', array(0)) ?>" <?php if (isset($action) && $action == 'action' && isset($controller) && $controller == 'hostactivity') { ?> class="active add_listing" <?php } else { ?> class="add_listing" <?php } ?> ><?php echo Info::t_lang('ADD_LISTING') ?></a></li>
              <li><a href="<?php echo FatUtility::generateUrl('hostactivity') ?>" <?php if (isset($action) && $action == 'index' && isset($controller) && $controller == 'hostactivity') { ?> class="active manage_listing" <?php } else { ?> class="manage_listing" <?php } ?> ><?php echo Info::t_lang('MANAGE_LISTING') ?></a></li>
              <?php */ ?>

            <li><a class="<?= $activeAddCls; ?>"  href="<?php echo FatUtility::generateUrl('hostactivity', 'update', array(0)) ?>"  ><?php echo Info::t_lang('ADD_LISTING') ?></a></li>
            <li><a class="<?= $activeMangCls; ?>" href="<?php echo FatUtility::generateUrl('hostactivity') ?>" ><?php echo Info::t_lang('MANAGE_LISTING') ?></a></li>


        </ul>
    </nav>
</div>