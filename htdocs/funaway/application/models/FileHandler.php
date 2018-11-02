<?php 
class FileHandler extends MyAppModel {
	
	protected $db;
	public $error;
	
	public function __construct(){
		$this->db = FatApp::getDb();
	}
	
	public static function deleteFile($file = '', $path_suffix = '') {
		if ($file === '' || !is_string($file) || strlen(trim($file)) < 1)
			return false;
		$file_fullpath =  CONF_UPLOADS_PATH . $path_suffix . $file;
		if (file_exists($file_fullpath)) {
			unlink($file_fullpath);
			return true;
		}
		return false;
	}
	
	public function addFile($data) {
        $afileId = 0;
		if(isset($data['afile_id']) && intval($data['afile_id']) > 0){
			$afileId = intval($data['afile_id']);
		}
        
        if ($afileId > 0) {
            $success = $this->db->updateFromArray('tbl_attached_files', $data, array('smt' => 'afile_id = ? and afile_record_id = ?', 'vals' => array($afileId, $data['afile_record_id'])));
        } else {
			$success = $this->db->insertFromArray('tbl_attached_files', $data);
            $afileId = $this->db->getInsertId();
        }
        if ($success) {
            return $afileId;
        } else {
            $this->error = $this->db->getError();
            return false;
        }
    }
	
	public function saveFile($filename, $record_id, $path, $type, $file_id = "") {
        $saveData = array();
		 
        if ($file_id != "") {
             $saveData['afile_id'] = $file_id;
        } 
        $saveData['afile_record_id'] = $record_id;
        $saveData['afile_name'] = $filename;
        $saveData['afile_physical_path'] = trim($path, '.');
        $saveData['afile_type'] = $type;
		$saveData['afile_private'] = 1;
	  
        if (!$this->addFile($saveData)) {
            return false;
        }
        return true;
    }
}
?>