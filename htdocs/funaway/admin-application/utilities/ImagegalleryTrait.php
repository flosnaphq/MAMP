<?php

trait Imagegallery {

    protected $imageType;

    public function imageForm($recordId) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $images = AttachedFile::getMultipleAttachments($this->imageType, $recordId);

        $this->set('frm', $this->getImageForm($recordId));
        $this->set('images', $images);
        $htm = $this->_template->render(false, false, "_partial/traits/image-form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function imageSetup() {
		
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $record_id = isset($post['record_id']) ? FatUtility::int($post['record_id']) : 0;
        $afile_id = isset($post['afile_id']) ? FatUtility::int($post['afile_id']) : 0;
        $afile_display_order = isset($post['afile_display_order']) ? $post['afile_display_order'] : 0;
        $attachedFile = new AttachedFile();

        if (!$attachedFile->uploadAndSaveFile('photo', $this->imageType, $record_id, 0, $afile_display_order)) {
            FatUtility::dieJsonError('Something Went Wrong');
        }
        FatUtility::dieJsonSuccess('Image Uploaded Successful!');
    }

    public function imageOrderSetup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $afile_id = isset($post['afile_id']) ? FatUtility::int($post['afile_id']) : 0;
        $display_order = isset($post['display_order']) ? $post['display_order'] : 0;
        $update_data['afile_display_order'] = $display_order;
        if (!AttachedFile::updateByAfileId($afile_id, $update_data)) {
            FatUtility::dieJsonError('Something Went Wrong');
        }
        FatUtility::dieJsonSuccess('Display Order Changed!');
    }

    public function imageRemoveSetup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $afile_id = isset($post['afile_id']) ? FatUtility::int($post['afile_id']) : 0;
        if (!AttachedFile::removeFile($afile_id)) {
            FatUtility::dieJsonError('Something Went Wrong');
        }
        FatUtility::dieJsonSuccess('Image Removed!');
    }

    private function getImageForm($record_id = 0) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = new Form('islandImgFrm');
        $frm->addHiddenField("", 'record_id', $record_id);
        $frm->addHiddenField("", 'afile_id');
        $frm->addFileUpload('Upload File', 'photo');
        $frm->addTextBox('Display order', 'afile_display_order');

        $frm->addSubmitButton('', 'submit_btn', 'Upload');
        return $frm;
    }

    public function displayImage($recordId, $w = 200, $h = 200) {
        
        $img = AttachedFile::getAttachmentById($recordId);
        $default_img = 'admin';
        Helper::displayImage($img['afile_physical_path'], $default_img, $w, $h);
        
    }

}
