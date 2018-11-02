<?php

class AttachedFile extends MyAppModel {

    const DB_TBL = 'tbl_attached_files';
    const DB_TBL_PREFIX = 'afile_';
    const FILETYPE_USER_PHOTO = 1;
    const FILETYPE_SERVICE_PHOTO = 2;
    const FILETYPE_ACTIVITY_PHOTO = 3;
    const FILETYPE_ICELAND_PHOTO = 4;
    const FILETYPE_BANNER_PHOTO = 5;
    const FILETYPE_FOUNDER_PHOTO = 6;
    const FILETYPE_INVESTOR_PHOTO = 7;
    const FILETYPE_LANGUAGE_PHOTO = 8;
    const FILETYPE_OFFICE_PHOTO = 9;
    const FILETYPE_CMS_PHOTO = 10;
    const FILETYPE_ACTIVITY_ATTRIBUTE = 11;
    const FILETYPE_ACTIVITY_ADDON = 12;
    const FILETYPE_TESTIMONIAL = 13;
    const FILETYPE_DUMMY_REVIEW_USER = 14;
    const FILETYPE_PMETHOD_IMAGE = 91;
    const FILETYPE_COUNTRY_IMAGE = 15;
    const FILETYPE_CITY_IMAGE = 16;
    const FILETYPE_HOME_PAGE_BANNER_STATS = 17;
    const FILETYPE_HOME_PAGE_BANNER_CONTACT = 18;
    const POST_IMG_FOLDER = 'post-images/';
    const BLOG_POST_CONTRIBUTE_FOLDER = 'contributions/';
    const CONTRIBUTION_IMG_FOLDER = 'contributions/';
    const PMETHOD_IMGS_FOLDER = 'payment-methods/';
    const DEFAULT_IMGS_FOLDER = 'default-images/';
    const LOGOS_IMGS_FOLDER = 'site-logos/';
    const COMMON_IMGS_FOLDER = "";

    public function __construct($userId = 0) {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $userId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getMultipleAttachments($fileType, $recordId, $recordSubid = 0, $approved = 1) {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::DB_TBL_PREFIX . 'type', '=', $fileType);
        $srch->addCondition(static::DB_TBL_PREFIX . 'record_id', '=', $recordId);
        $srch->addCondition(static::DB_TBL_PREFIX . 'record_subid', '=', $recordSubid);

        if ($approved != -1) {
            $srch->addCondition(static::DB_TBL_PREFIX . 'approved', '=', $approved);
        }
        $srch->addOrder('afile_display_order');

        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetchAll($rs, 'afile_id');
    }

    public static function getAllAttachmentsByType($fileType) {
        $srch = new SearchBase(static::DB_TBL);
        if (is_array($fileType)) {
            $srch->addCondition(static::DB_TBL_PREFIX . 'type', 'IN', $fileType);
        }else{
              $srch->addCondition(static::DB_TBL_PREFIX . 'type', '=', $fileType);
        }
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs, 'afile_id');
    }

    public static function getAttachment($fileType, $recordId, $recordSubid = 0, $approved = 1) {
        $data = static::getMultipleAttachments($fileType, $recordId, $recordSubid, $approved);
        if (count($data > 0)) {
            reset($data);
            return current($data);
        }
        return null;
    }

    public static function getAttachmentById($afile_id, $approved = -1) {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::DB_TBL_PREFIX . 'id', '=', $afile_id);
        if ($approved > -1) {
            $srch->addCondition(static::DB_TBL_PREFIX . 'approved', '=', $approved);
        }
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs, 'afile_id');
    }

    public function saveCropAttachment($saveName, $fileType, $recordId, $recordSubid, $name, $displayOrder = 0, $uniqueRecord = false, $approved = 1) {


        $this->assignValues(array(
            static::DB_TBL_PREFIX . 'type' => $fileType,
            static::DB_TBL_PREFIX . 'record_id' => $recordId,
            static::DB_TBL_PREFIX . 'record_subid' => $recordSubid,
            static::DB_TBL_PREFIX . 'physical_path' => $saveName,
            static::DB_TBL_PREFIX . 'approved' => $approved,
            static::DB_TBL_PREFIX . 'name' => $name
        ));

        $db = FatApp::getDb();

        if ($displayOrder == -1) {
            //@todo display order thing needs to be checked. 
            $smt = $db->prepareStatement('SELECT MAX(afile_display_order) AS max_order FROM ' . static::DB_TBL . '
					WHERE afile_type = ? AND afile_record_id = ? AND afile_record_subid = ?');
            $smt->bindParameters('iii', $fileType, $recordId, $recordSubid);

            $smt->execute();
            $row = $smt->fetchAssoc();

            $displayOrder = FatUtility::int($row['max_order']) + 1;
        }

        $this->setFldValue('afile_display_order', $displayOrder);
        if (!$this->save()) {
            // $this->error = 'Something went Wrong. Please try again';
            return false;
        }
        if ($uniqueRecord) {

            $db->deleteRecords(static::DB_TBL, array(
                'smt' => 'afile_type = ? AND afile_record_id = ? AND afile_record_subid = ? AND afile_id != ?',
                'vals' => array($fileType, $recordId, $recordSubid, $this->mainTableRecordId)
            ));
        }

        return true;
    }

    public function saveAttachment($fl, $fileType, $recordId, $recordSubid, $name, $displayOrder = 0, $uniqueRecord = false, $approved = 1) {
        $pos = strrpos($name, '.');
        $extension = substr($name, $pos + 1);
        $real_name = substr($name, 0, $pos);
        $real_name = preg_replace('/[^A-Za-z0-9\-]/', '', $real_name);
        $name = $real_name . '.' . $extension;
        $saveName = time() . '-' . $name;
        while (file_exists(CONF_UPLOADS_PATH . $saveName)) {
            $saveName = rand(10, 99) . '-' . $saveName;
        }
        if (!move_uploaded_file($fl, CONF_UPLOADS_PATH . $saveName)) {
            $this->error = 'Could not move file.';
            return false;
        }
        //@todo should divide files in directories.

        $this->assignValues(array(
            static::DB_TBL_PREFIX . 'type' => $fileType,
            static::DB_TBL_PREFIX . 'record_id' => $recordId,
            static::DB_TBL_PREFIX . 'record_subid' => $recordSubid,
            static::DB_TBL_PREFIX . 'physical_path' => $saveName,
            static::DB_TBL_PREFIX . 'approved' => $approved,
            static::DB_TBL_PREFIX . 'name' => $name
        ));

        $db = FatApp::getDb();

        if ($displayOrder == -1) {
            //@todo display order thing needs to be checked. 
            $smt = $db->prepareStatement('SELECT MAX(afile_display_order) AS max_order FROM ' . static::DB_TBL . '
					WHERE afile_type = ? AND afile_record_id = ? AND afile_record_subid = ?');
            $smt->bindParameters('iii', $fileType, $recordId, $recordSubid);

            $smt->execute();
            $row = $smt->fetchAssoc();

            $displayOrder = FatUtility::int($row['max_order']) + 1;
        }

        $this->setFldValue('afile_display_order', $displayOrder);

        if (!$this->save()) {
            $this->error = 'Something went Wrong. Please try again';
            return false;
        }
        if ($uniqueRecord) {

            $db->deleteRecords(static::DB_TBL, array(
                'smt' => 'afile_type = ? AND afile_record_id = ? AND afile_record_subid = ? AND afile_id != ?',
                'vals' => array($fileType, $recordId, $recordSubid, $this->mainTableRecordId)
            ));
        }

        return true;
    }

    public function saveImage($fl, $fileType, $recordId, $recordSubid, $name, $displayOrder = 0, $uniqueRecord = false, $approved = 1) {
        if (self::FILETYPE_ACTIVITY_ATTRIBUTE != $fileType) {
            if (getimagesize($fl) === false) {
                $this->error = 'Unrecognised Image file.';
                return false;
            }
        }

        return $this->saveAttachment($fl, $fileType, $recordId, $recordSubid, $name, $displayOrder, $uniqueRecord, $approved);
    }

    public static function uploadImage($fl, $name, &$response) {
        $size = getimagesize($fl);
        if ($size === false) {
            $response = 'Invalid Image';
            return false;
        }

        $fname = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        while (file_exists(CONF_UPLOADS_PATH . $fname)) {
            $fname .= '_' . rand(10, 99);
        }
        if (!move_uploaded_file($fl, CONF_UPLOADS_PATH . $fname)) {
            $response = 'could Not Save File';
            return false;
        }
        $response = $fname;
        return true;
    }

    public function uploadAndSaveFile($file_field_name, $type, $recordId = 0, $recordSubid = 0, $displayOrder = 0, $uniqueRecord = false, $approved = 1) {
        if (isset($_FILES[$file_field_name]['tmp_name'])) {
            if (is_uploaded_file($_FILES[$file_field_name]['tmp_name'])) {
                if (!$afile_id = $this->saveImage($_FILES[$file_field_name]['tmp_name'], $type, $recordId, $recordSubid, $_FILES[$file_field_name]['name'], $displayOrder, $uniqueRecord, $approved)) {
                    return false;
                }
                return true;
            }
        }
        $this->error = 'File Was Empty';
        return false;
    }

    public function saveExistAttachment($fl, $fileType, $recordId, $recordSubid, $name, $displayOrder = 0, $uniqueRecord = false, $approved = 1) {
        $this->assignValues(array(
            static::DB_TBL_PREFIX . 'type' => $fileType,
            static::DB_TBL_PREFIX . 'record_id' => $recordId,
            static::DB_TBL_PREFIX . 'record_subid' => $recordSubid,
            static::DB_TBL_PREFIX . 'physical_path' => $name,
            static::DB_TBL_PREFIX . 'approved' => $approved,
            static::DB_TBL_PREFIX . 'name' => $name
        ));

        $db = FatApp::getDb();

        if ($displayOrder == -1) {
            $smt = $db->prepareStatement('SELECT MAX(afile_display_order) AS max_order FROM ' . static::DB_TBL . '
					WHERE afile_type = ? AND afile_record_id = ? AND afile_record_subid = ?');
            $smt->bindParameters('iii', $fileType, $recordId, $recordSubid);

            $smt->execute();
            $row = $smt->fetchAssoc();

            $displayOrder = FatUtility::int($row['max_order']) + 1;
        }

        $this->setFldValue('afile_display_order', $displayOrder);

        if (!$this->save()) {
            return false;
        }

        if ($uniqueRecord) {
            $db->deleteRecords(static::DB_TBL, array(
                'smt' => 'afile_type = ? AND afile_record_id = ? AND afile_record_subid = ? AND afile_id != ?',
                'vals' => array($fileType, $recordId, $recordSubid, $this->mainTableRecordId)
            ));
        }

        return true;
    }

    static function removeFile($afile_id) {
        $afile_id = intval($afile_id);
        $filesArr = AttachedFile::getAttachmentById($afile_id);
        $pth = CONF_UPLOADS_PATH . $filesArr[static::DB_TBL_PREFIX . "physical_path"];
        if (file_exists($pth)) {
            unlink($pth);
        }
        $db = FatApp::getDb();
        $success = $db->deleteRecords(static::DB_TBL, array("smt" => static::DB_TBL_PREFIX . "id=?", "vals" => array($afile_id)));
        if ($success) {
            return true;
        }
        return false;
    }

    public static function removeFiles($fileType, $recordId = 0, $recordSubid = 0, $folder = '') {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(static::DB_TBL_PREFIX . 'type', '=', $fileType);
        $srch->addCondition(static::DB_TBL_PREFIX . 'record_id', '=', $recordId);
        $srch->addCondition(static::DB_TBL_PREFIX . 'record_subid', '=', $recordSubid);

        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs, static::DB_TBL_PREFIX . 'id');
        $directory_path = CONF_UPLOADS_PATH;
        if (!empty($folder)) {
            $directory_path = $folder . '/';
        }
        foreach ($rows as $row) {
            $file_path = $directory_path . $row[static::DB_TBL_PREFIX . "physical_path"];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $db = FatApp::getDb();
        $success = $db->deleteRecords(static::DB_TBL, array(
            "smt" => static::DB_TBL_PREFIX . 'type =? and ' . static::DB_TBL_PREFIX . 'record_id=? and ' . static::DB_TBL_PREFIX . 'record_subid = ? ',
            "vals" => array(
                $fileType,
                $recordId,
                $recordSubid
            )
                )
        );
        if ($success) {
            return true;
        }
        return false;
    }

    public static function updateByAfileId($afile_id, $update_data = array()) {
        $afile_id = FatUtility::int($afile_id);
        $tbl = new TableRecord(static::DB_TBL);
        $tbl->assignValues($update_data);
        $condition = array('smt' => static::DB_TBL_PREFIX . 'id = ?', 'vals' => array($afile_id));
        if (!$tbl->update($condition)) {
            return false;
        }
        return true;
    }

    public static function showImage($img_name) {
        if (ob_get_contents())
            ob_end_clean();
        if ($img_name != "" and file_exists(CONF_UPLOADS_PATH . $img_name)) {
            $path = CONF_UPLOADS_PATH . $img_name;
        }

        header('Cache-Control: public');
        header("Pragma: public");
        header('Content-type: image/jpeg');
        header("Expires: " . date('D, d M Y H:i:s', strtotime("+30 days")));

        $headers = FatApp::getApacheRequestHeaders();
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($path))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 304);
            exit;
        }

        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200);

        /* header('Cache-Control: public, must-revalidate');
          header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT', true, 200); */
        readfile($path);
    }

}
