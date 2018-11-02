<?php
if (!empty($activities)) {
    foreach ($activities as $activity) {
        ?>
        <div class="media activity-media">
            <div class="media__figure activity-media__image">
                <img src="<?php echo FatCache::getCachedUrl(FatUtility::generateUrl('Image', 'activity', array($activity['activity_image_id'], 620, 620)), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $activity['activity_name'] ?>">
            </div>
            <div class="media__body media--middle activity-media__content"  >
                <h6 class="activity-media__heading"><a href="<?php echo Route::getActivityUrl($activity['activity_id']); ?>"><?php echo $activity['activity_name'] ?></a></h6>
                <span class="activity-media__cat"><a href="<?php echo Route::getServiceUrl($activity['parentservice_id']) ?>"><?php
                        echo $activity['parentservice_name'];
                        if (!empty($activity['childservice_name']))
                            echo '/' . $activity['childservice_name']
                            ?></a></span>
                <span class="activity-media__price"><?php echo Currency::displayPrice($activity['activity_price']) . '/' . Info::activityTypeByKey($activity['activity_price_type']); ?></span>
            </div>
        </div>
        <?php
    }
}
?>