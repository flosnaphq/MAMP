
<div class='image-section'>
    <h6 class="header__heading-tex">
        <?php echo $addon['activityaddon_text'] ?>
    </h6><hr>
    <?php
    if (!empty($images)) {
        $delete = '<svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg>';
        ?>
        <ul class="gallery gallery--4 thumb__list">
            <?php foreach ($images as $img) { ?> 
                <li class="gallery__item thumb__item">
                    <div class="thumb__image">
                        <img src='<?php echo FatUtility::generateUrl('image', 'addon', array($img['afile_id'], 165, 165)) ?>'>

                    </div>
                    <div class="buttons__group">
                        <a href='javascript:;' onclick='removeAddonImage(<?php echo $img['afile_id'] ?>)' class="button button--small button--fill button--red thumb__delete"><?php echo $delete ?></a></div>

                </li>
            <?php } ?>
        </ul>
        <?php
    } else {
        echo Info::t_lang('NO_IMAGES_YET');
    }
    ?>
</div>
<hr>
<div class="col-6">
    <div class="field-set">
        <div class="caption-wraper">
            <label class="field_label">

            </label>
        </div>
        <div class="field-wraper">
            <div class="field_cover">
                <a class="button button--fill button--red addon-modaal-ajax" href="<?php echo FatUtility::generateUrl('croper', 'load'); ?>" >Upload</a>
                <input class="button button--fill button--dark " onclick="step7()" title=""  name="button" value="Back" type="button">
            </div>
        </div>
    </div>
</div>

