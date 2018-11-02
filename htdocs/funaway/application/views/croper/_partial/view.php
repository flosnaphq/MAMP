<?php 

$maxUploadSize = Helper::file_upload_max_size();

?>
<div class="modal share-card text--center" id="avatar-modal">
    <div class="modal__header">
        <h6 class="modal__heading">Upload Image (Max File Size <?php echo Helper::filesize_formatted($maxUploadSize) ?>)</h6>
    </div>
    <div class="modal__content share-card__image">
        <?php echo $frm->setFormTagAttribute('class', 'avatar-form'); ?>

        <?php echo $frm->getFormTag(); ?>

        <div class="avatar-body">



            <!-- Crop and preview -->

            <div class="span__row">
                <div class="span span--8">
                    <div class="js-avatar-wrapper avatar-wrapper"></div>
                </div>
                <div class="span span--4">
                    <div class="js-avatar-preview avatar-preview preview-lg"></div>
                </div>
            </div>

            <div class="row avatar-btns">
                <div class="col-md-9">
                    <!-- Upload image and data -->


                    <div class="buttons-group">
                        <div class="avatar-upload">
                            <?php echo $frm->getFieldHTML('avatar_src'); ?>
                            <?php echo $frm->getFieldHTML('avatar_data'); ?>


                            <div class="avatar-upload-button">
                                <?php $objImgfld = $frm->getField('avatar_file');
									$objImgfld->addFieldTagAttribute('class', 'js-button-avatar-upload');
								echo $frm->getFieldHTML('avatar_file'); ?>
                                <button type="button" class="button button--fill button--green">Upload Image</button>
                            </div>
                        </div>
                        <button type="button" class="button button--fill button--blue" data-method="rotate" data-option="-90" title="Rotate -90 degrees">
                            <svg class="icon icon--info" style="transform:rotateY(180deg) rotateZ(-90deg); -webkit-transform:rotateY(180deg) rotateZ(-90deg);"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-reload"></use></svg>
                        </button>
                        <button type="button" class="button button--fill button--blue" data-method="rotate" data-option="90" title="Rotate 90 degrees">
                            <svg class="icon icon--info" style="transform:rotateZ(-90deg);-webkit-transform:rotateZ(-90deg);"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-reload"></use></svg>
                        </button>
                        <button type="submit" class="button button--fill button--red avatar-save">Done</button>
                    </div>
                </div>
                <div class="loading"  id="croperLoading" aria-label="Loading" role="img" tabindex="-1"></div>
                </form>
                <?php echo $frm->getExternalJs(); ?>
            </div>
        </div>