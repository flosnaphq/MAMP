<?php

Class DummyController extends FatController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function deleteGarbageImages()
    {
        $srch = new SearchBase(AttachedFile::DB_TBL);
        $srch->addFld('afile_physical_path');
        // $srch->setPageSize(200);
        $rs = $srch->getResultSet();
        $images = FatApp::getDb()->fetchAll($rs);
        // echo '<pre>' . print_r($images, true); exit;

        $originalPath = CONF_UPLOADS_PATH;
        $backupPath = CONF_UPLOADS_PATH . 'backup/';
        foreach ($images as $keys => $image) {
            $originalImagePath = $originalPath . $image['afile_physical_path'];
            if (file_exists($originalPath . $image['afile_physical_path'])) {
                echo 'File Exist: ' . $originalPath . $image['afile_physical_path'];
                $fileFullPath = $backupPath . $image['afile_physical_path'];
                if (!copy($originalImagePath, $fileFullPath)) {
                    continue;
                }
                unlink($originalImagePath);
                /* $backupFileFullPath = $backupPath . 'backup-' . $image['afile_physical_path'];
                  rename($fileFullPath,$backupFileFullPath); */
            }
        }
    }

    public function akcode()
    {
        $user_id = 0;
        if (User::isUserLogged()) {
            $user_id = User::getLoggedUserId();
        }
        $rowUser = User::getAttributesById($user_id);
        echo '<strong>Logged user details</strong> <pre>';
        print_r($rowUser);
        echo '</pre>';
        die('stoped');

        $post['request_id'] = 4;
        Sms::requestUpdateSmsToTraveler($post['request_id']);
    }

}
