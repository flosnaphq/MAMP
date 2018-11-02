<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$imageUrl = FatUtility::generateUrl('image', 'user', array($userId, 200, 200, time()));
$imageLabel = Info::t_lang('UPLOAD_IMAGE');
?>

<div class="row">
    <div class="col-12">
        <div class='img-uploader'>
            <img id='profile_photo' src='<?php echo $imageUrl; ?>'/>
            <a href="<?php echo FatUtility::generateUrl('croper', 'load'); ?>" class="modaal-ajax">
                <label for='img-uploader' class='upload-label'>
                    <?php echo $imageLabel; ?>
                </label>
            </a>
        </div>
    </div>
</div>
<?php if ($imageUploaded): ?>
    <div class="row">
        <div class="col-12">
            <a href="javascript:;" onclick="removeImage()" class="button button--small button--fill button--red">
                <?php echo Info::t_lang('REMOVE_IMAGE'); ?>
            </a>
        </div>
    </div>
<?php endif; ?>

