<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

if (!empty($categories)) {

    foreach ($categories as $category) {
        ?>
        <div class="category-card">
            <?php if ($category['service_parent_id'] == 0) { ?>
                <a href="<?php echo Route::getRoute('services', 'index', array($category['service_id'])); ?>">
                    <figure class="category-card__image">
                        <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'service', array($category['service_id'], 620, 620)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $category['service_name'] ?>">
                    </figure>
                    <div class="category-card__content">
                        <span><?php echo $category['service_name'] ?> <?php /*( <?php echo $category['tot_activities']; ?>)*/?></span>
                    </div>
                </a>
                <?php
            } else {
                $srchParams = '?activity_type=' . $category['service_parent_id'] . '&categories=' . $category['service_id'];
                ?>
                <a href="<?php echo Route::getRoute('search', 'index') . $srchParams; ?>">
                    <figure class="category-card__image">
                        <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('image', 'service', array($category['service_id'], 620, 620)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $category['service_name'] ?>">
                    </figure>
                    <div class="category-card__content" >
                       <span><?php echo $category['service_name'] ?> <?php /*( <?php echo $category['tot_activities']; ?>)*/?></span>
                    </div>
                </a>
            <?php } ?>
        </div>
        <?php
    }
} elseif ($page <= 1) {
    echo Helper::noRecord(Info::t_lang('NO_RECORDS'));
} else {
    echo Info::t_lang('NO_MORE_RECORD');
}
?>
