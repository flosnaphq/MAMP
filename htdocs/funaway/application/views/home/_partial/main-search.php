<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

if (!empty($records)) {
    foreach ($records as $record) {
        ?>
        <div class="media activity-media">
            <div class="media__figure activity-media__image">
                <a href="<?php echo FatUtility::generateUrl('activity', 'detail', array($record['activity_id'])) ?>">
                    <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('Image', 'activity', array($record['activity_image_id'], 620, 620)), CONF_DEF_CACHE_TIME, '.jpg'); ?>">
                </a>
            </div>
            <div class="media__body media--middle activity-media__content">
                <h6 class="activity-media__heading"><a href="<?php echo FatUtility::generateUrl('activity', 'detail', array($record['activity_id'])) ?>"><?php echo $record['activity_name']; ?></a></h6>
                <span class="activity-media__cat">
                    <a href="<?php echo FatUtility::generateUrl('services') ?>">
                        <?php
                        echo $record['parentservice_name'];
                        echo (!empty($record['childservice_name']) ? ' / ' . $record['childservice_name'] : '');
                        ?>
                    </a>
                </span>
                <span class="activity-media__price fl--right">
                    <?php echo Currency::displayPrice($record['activity_price']) . '/' . Info::activityTypeByKey($record['activity_price_type']); ?>
                </span>
            </div>
        </div>
        <?php
    }
} else {
    echo Helper::noRecord(Info::t_lang('NO_RECORD_FOUND'));
}
?>
			