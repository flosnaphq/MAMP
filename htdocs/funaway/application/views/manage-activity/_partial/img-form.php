<div class='image-section'>
    <?php if (!empty($images)) { ?>
        <ul class="gallery gallery--4 thumb__list">

            <?php
            $default = '<svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-cross"></use></svg>';
            $setdefault = '<svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-check"></use></svg>';
            $delete = '<svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg>';
            $icontitle = "Set Default";
            $imageSelected = "";
            foreach ($images as $img) {
                  $activeClass =  "";
                 if ($img['afile_id'] == $activity_detail['activity_image_id']){
                     $activeClass = "active";
                 }
                
                ?> 
                <li class="gallery__item thumb__item <?php echo $activeClass;?>">
                    <div class="thumb__image">
                        <img src='<?php echo FatUtility::generateUrl('image', 'activity', array($img['afile_id'], 165, 165)) ?>'>
                    </div>
                    <div class="buttons__group">
                        <a href='javascript:;' ng-click='removeImage(<?php echo $img['afile_id'] ?>)' class="button button--small button--fill button--red thumb__delete"><?php echo $delete; ?></a>

                        <?php
                        $imageSelected = "";
                        if ($img['afile_id'] != $activity_detail['activity_image_id']):?>
                        <a href='javascript:;'  title="Set Default" ng-click='setDefault(<?php echo $img['afile_id'] ?>)'  class="button button--small button--fill button--green  default-image-set ">
                           

                            <?php echo (($img['afile_id'] == $activity_detail['activity_image_id']) ? $default : $setdefault); ?>
                             <?php endif;?>
                        </a>
                    </div>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <?php echo Info::t_lang('NO_IMAGES_YET'); ?>
    <?php } ?>
</div>
<hr>
	

<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal c-file-upload');
$frm->setFormTagAttribute('style', 'margin-top:1.25em;');
$frm->setFormTagAttribute('id', 'frmPhoto');
$frm->setFormTagAttribute('action', 'setup2');
$frm->developerTags['fld_default_col'] =6;
$frm->setValidatorJsObjectName('setup2Validator');
$frm->setFormTagAttribute('onsubmit', 'actionStep2(setup2Validator); return(false);');
echo $frm->getFormHtml();
?>