<?php

class ImageController extends FatController {

    public function crop($img_name) {
        Helper::showImage($img_name);
    }

    public function user($user_id, $w = 0, $h = 0) {
        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PHOTO, $user_id);
        $default_img = 'user';
        if ($user_id == 0) {
            $default_img = 'admin';
        }
        Helper::displayImage($img['afile_physical_path'], $default_img, $w, $h);
    }

    public function adminActivity($afile_id, $w = 0, $h = 0) {
        $img = AttachedFile::getAttachmentById($afile_id);
        $image_name = @$img['afile_physical_path'];
        if (isset($img[AttachedFile::DB_TBL_PREFIX . 'approved']) && $img[AttachedFile::DB_TBL_PREFIX . 'approved'] != 1) {
            if (User::isUserLogged()) {
                $user_type = User::getLoggedUserAttribute('user_type');
                if ($user_type == 1) {
                    $activity_id = @$img[AttachedFile::DB_TBL_PREFIX . 'record_id'];

                    $act = new Activity($activity_id);
                    $activity_data = $act->getAttributesById($activity_id);
                    if (empty($activity_data)) {
                        $image_name = '';
                    } elseif ($activity_data[Activity::DB_TBL_PREFIX . 'user_id'] != User::getLoggedUserId()) {
                        $image_name = '';
                    }
                } else {
                    $image_name = '';
                }
            } else {
                $image_name = '';
            }
        }

        if ($w != 0)
            Helper::cropImage($image_name, "activity", $w, $h);
        else
            Helper::showImage($image_name);
    }

}

?>