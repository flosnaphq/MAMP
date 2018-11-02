<?php

class CroperController extends MyAppController {

    public function load() {
        $this->set('frm', $this->imageUploadForm());
        $this->_template->render(false, false, 'croper/_partial/view.php');
    }

    public function imageUploadForm() {

        $frm = new Form('imageUploadFrm');
        $frm->addHiddenField('', 'avatar_src', '', array('class' => 'avatar-src'));
        $frm->addHiddenField('', 'avatar_data', '', array('class' => 'avatar-data'));
        $frm->addFileUpload('Image', 'avatar_file', array('id' => 'avatarInput', 'class' => 'avatar-input'))->requirements()->setRequired();
        return $frm;
    }

    public function userProfileImage() {
        
        $crop = new Cropper(
                isset($_POST['avatar_src']) ? $_POST['avatar_src'] : null, isset($_POST['avatar_data']) ? $_POST['avatar_data'] : null, isset($_FILES['avatar_file']) ? $_FILES['avatar_file'] : null
        );

        if ($crop->getMsg()) {
            FatUtility::dieJsonError($crop->getMsg());
        }

        $fileName = $crop->getResult();
        $physicalName = $fileName;
        $orignalName = $_FILES['avatar_file']['name'];
        $fileType = AttachedFile::FILETYPE_USER_PHOTO;
        $recordId = User::getLoggedUserId();

        $atachedFile = new AttachedFile();
        $result = $atachedFile->saveCropAttachment($physicalName, $fileType, $recordId, 0, $orignalName, $displayOrder = 0, $uniqueRecord = true, $approved = 1);
        if (!$result) {
            FatUtility::dieJsonError("Error While Croping The Image");
        }

        FatUtility::dieJsonSuccess(array('msg' => "File Uploaded"));
    }
    public function activityImage() {
        $activityId = isset($_SESSION['activity_id'])?$_SESSION['activity_id']:0;
        if(intval($activityId)<1){
            FatUtility::dieJsonError("Invalid Access");
        }
        
        $crop = new Cropper(
                isset($_POST['avatar_src']) ? $_POST['avatar_src'] : null, isset($_POST['avatar_data']) ? $_POST['avatar_data'] : null, isset($_FILES['avatar_file']) ? $_FILES['avatar_file'] : null
        );

        if ($crop->getMsg()) {
            FatUtility::dieJsonError($crop->getMsg());
        }

        $fileName = $crop->getResult();
        $physicalName = $fileName;
        $orignalName = $_FILES['avatar_file']['name'];
        $fileType = AttachedFile::FILETYPE_ACTIVITY_PHOTO;
        $recordId = $activityId;

        $atachedFile = new AttachedFile();
        $result = $atachedFile->saveCropAttachment($physicalName, $fileType, $recordId, 0, $orignalName, $displayOrder = 0, $uniqueRecord = false, $approved = 1);
		
        if (!$result) {
            FatUtility::dieJsonError("Error While Croping The Image");
        }

        FatUtility::dieJsonSuccess(array('msg' => "File Uploaded"));
    }
    public function activityAddonImage($addonId) {
        $activityId = isset($_SESSION['activity_id'])?$_SESSION['activity_id']:0;
        if(intval($activityId)<1){
            FatUtility::dieJsonError("Invalid Access");
        }
        
        $crop = new Cropper(
                isset($_POST['avatar_src']) ? $_POST['avatar_src'] : null, isset($_POST['avatar_data']) ? $_POST['avatar_data'] : null, isset($_FILES['avatar_file']) ? $_FILES['avatar_file'] : null
        );

        if ($crop->getMsg()) {
            FatUtility::dieJsonError($crop->getMsg());
        }

        $fileName = $crop->getResult();
        $physicalName = $fileName;
        $orignalName = $_FILES['avatar_file']['name'];
        $fileType = AttachedFile::FILETYPE_ACTIVITY_ADDON;
        $recordId = $addonId;

        $atachedFile = new AttachedFile();
        $result = $atachedFile->saveCropAttachment($physicalName, $fileType, $recordId, $activityId, $orignalName, $displayOrder = 0, $uniqueRecord = false, $approved = 1);
        if (!$result) {
            FatUtility::dieJsonError("Error While Croping The Image");
        }

        FatUtility::dieJsonSuccess(array('msg' => "File Uploaded"));
    }
}

?>