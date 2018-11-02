<?php

class Admin {
    const DB_TBL = 'tbl_admin';
    const DB_TBL_PREFIX = 'admin_';
    public function getAdmins() {
        $srch = new SearchBase("tbl_admin");
        return $srch;
    }

    function addUpdate($data) {
        $db = FatApp::getDb();
        $admin_id = intval($data['admin_id']);
        if (!($admin_id > 0))
            $admin_id = 0;
        unset($data['admin_id']);
        $arr_fields = $data;
        if (isset($data['admin_password']) && $data['admin_password'] != '')
            $arr_fields['admin_password'] = UserAuthentication::encryptPassword($data['admin_password']);
        if ($admin_id > 0) {
            $success = $db->updateFromArray('tbl_admin', $arr_fields, array('smt' => 'admin_id = ?', 'vals' => array($admin_id)));
        } else {
            $success = $db->insertFromArray('tbl_admin', $arr_fields);
            echo $db->getError();
            $admin_id = $db->getInsertId();
        }
        if ($success) {
            
        } else {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public static function getAdminById($admin_id) {
        $admin_id = intval($admin_id);
        $srch = new SearchBase("tbl_admin");
        $srch->addCondition("admin_id", "=", $admin_id);
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $record = $db->fetch($rs);
        return $record;
    }

    /////////////////////////////////////////////////////////////////////////// PERMISSION FUNCITONS /////////////////////////////////////////////////////

    function getPermissionOption($admin_id = 0) {
        $admin_id = intval($admin_id);
        $db = FatApp::getDb();
        $srch = new SearchBase("tbl_admin_permission_names");
        $srch->joinTable('tbl_admin_permissions', 'left join', 'permission_permname_id = permname_id and permission_admin_id =' . $admin_id);
        $srch->addMultipleFields(array('permname_id', 'permname_name'));
        $srch->addCondition("permname_manageable",'=',1);
        $srch->addFld("IFNULL(permission_level,0) as permisson_level");
        $srch->addOrder("permname_display_order", "asc");
        $rs = $srch->getResultSet();
        $row = $db->fetchAll($rs);
        return $row;
        exit;
    }

    function updatePermissions($data) {
        $db = FatApp::getDb();
        $admin_id = 0;
        if (isset($data['admin_id']) && trim($data['admin_id']) != "") {
            $admin_id = $data['admin_id'];
        }
        unset($data['admin_id']);
        $this->removePermission($admin_id);
        foreach ($data['permission'] as $key => $value) {
            $permission = array();
            $permission['permission_admin_id'] = $admin_id;
            $permission['permission_permname_id'] = $key;
            $permission['permission_level'] = $value;
            $db->insertFromArray("tbl_admin_permissions", $permission);
        }
        return true;
    }

    function removePermission($admin_id) {
        $db = FatApp::getDb();
        $db->deleteRecords("tbl_admin_permissions", array("smt" => "permission_admin_id = ?", "vals" => array($admin_id)));
    }

    function logout() {
        session_destroy();
    }

    public function updatePassword($admin_id, $new_password) {
        $db = FatApp::getDb();
        $tbl = new TableRecord('tbl_admin');
        $new_password = UserAuthentication::encryptPassword($new_password);
        $update_data = array('admin_password' => $new_password);
        $where = array('smt' => 'admin_id = ?', 'vals' => array($admin_id));
        $tbl->assignValues($update_data);
        if (!$tbl->update($where)) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    public function isEmailExist($email) {
        $search = new SearchBase('tbl_admin');
        $search->addCondition('admin_email', '=', $email);
        $rs = $search->getResultSet($search);
        $records = FatApp::getDb()->fetch($rs);
        if (empty($records))
            return false;
        return $records;
    }

    function deleteOldPasswordResetRequest() {
        $db = FatApp::getDb();
        if (!$db->deleteRecords('tbl_admin_password_reset_requests', array('smt' => 'aprr_expiry < ?', 'vals' => array(date('Y-m-d H:i:s'))))) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    function deletePasswordResetRequest($admin_id) {
        $db = FatApp::getDb();
        if (!$db->deleteRecords('tbl_admin_password_reset_requests', array('smt' => 'appr_admin_id = ?', 'vals' => array($admin_id)))) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    function getPasswordResetRequest($admin_id) {
        $search = new SearchBase('tbl_admin_password_reset_requests');
        $search->addCondition('appr_admin_id', '=', $admin_id);
        $rs = $search->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    function addPasswordResetRequest($array) {
        $tbl = new TableRecord('tbl_admin_password_reset_requests');
        $tbl->assignValues($array);
        if (!$tbl->addNew()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

    function updateAdminLayoutPrefrence($adminId, $layout) {

        $db = FatApp::getDb();
        $tbl = new TableRecord('tbl_admin');

        $update_data = array('admin_layout' => $layout);
        $where = array('smt' => 'admin_id = ?', 'vals' => array($adminId));
        $tbl->assignValues($update_data);
        if (!$tbl->update($where)) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }

}

?>