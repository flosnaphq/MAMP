<?php
class Configurations{
	const CONF_TBL ='tbl_configurations';
	private $error;
	private $db;
	public function __construct(){
		$this->db = FatApp::getDb();
	}
	public function getConfigurations($city_id=0){
		$search = new SearchBase(static::CONF_TBL);
		$rs = $search->getResultSet();
		return FatApp::getDb()->fetchAll($rs);
	}
		
	public function update($conf_name,$conf_value){
		$tbl = new TableRecord(static::CONF_TBL);
		$data = array('conf_val'=>$conf_value);
		$where = array('smt'=>'conf_name = ?', 'vals'=>array($conf_name));
		$tbl->assignValues($data);
		if(!$tbl->update($where)){
			$this->error = FatApp::getDb()->getError();
			return false;
		}
		return true;
	}
	
	public function getError(){
		return $this->error;
	}
	
	/*DB restore [*/
	function restoreDatabase($backupFile,$concate_path=true) {
			$db_server = CONF_DB_SERVER;
			$db_user = CONF_DB_USER;
			$db_password = CONF_DB_PASS;
			$db_databasename = CONF_DB_NAME;
			$conf_db_path = CONF_DB_BACKUP_DIRECTORY_FULL_PATH;
			$varbsedir = $this->getMySQLVariable("basedir");
			if ($varbsedir == "/")
				$varbsedir = $varbsedir . "usr/";
			else
				$varbsedir = $varbsedir;
			if ($concate_path==true)	
				$backupFile = $conf_db_path . $backupFile;
				
			$sql = "SHOW TABLES FROM $db_databasename";
			if($rs = $this->db->query($sql)){
				  while($row = $this->db->fetch($rs)){
						$table_name=$row["Tables_in_".$db_databasename];
						$this->db->query("DROP TABLE $db_databasename.$table_name");
				  }
			}
			$cmd ="mysql --user=" . $db_user . " --password='" . $db_password . "' " . $db_databasename . " < " . $backupFile;
			system($cmd);
			echo $restore_backup . "<hr>" . $data_str;
	 }
	
	function getMySQLVariable($varname, $scope = "session") {
		   $gv = $this->db->query("show $scope variables");
		   $counter = 0;
		   $val = false;
		   while ($grow = $this->db->fetch($gv)) {
	       if ($grow[0] == $varname) {
    	    	   $val = $grow[1];
	        	   break;
	    	   }
		   }	
		   return $val;
	}
	
	function getDatabaseDirectoryFiles(){
		$dir = dir(CONF_DB_BACKUP_DIRECTORY_FULL_PATH);
		$count = 0;
		while (($file = $dir->read()) !== false  ){ 
			if (!($file=="." || $file==".." ||  $file==".htaccess")){
				$files_arr[]=$file;
			}
		}
		return $files_arr;
	}
	
	function backupDatabase($name, $attachtime = true, $download = false,$backup_path="") {
	   $db_server = CONF_DB_SERVER;
	   $db_user = CONF_DB_USER;
	   $db_password = CONF_DB_PASS;
	   $db_databasename = CONF_DB_NAME;
	   $conf_db_path = $backup_path!=""?$backup_path:CONF_DB_BACKUP_DIRECTORY_FULL_PATH;
	   if ($attachtime) {
		   $backupFile = $conf_db_path . "/" . $name . "_" . date("Y-m-d-H-i-s") . '.sql';
		   $fileToDownload = $name . "_" . date("Y-m-d-H-i-s") . '.sql';
	   } else {
		   $backupFile = $conf_db_path . "/" . $name . '.sql';
		   $fileToDownload = $name . '.sql';
	   }
	   $data_str = "mysqldump --opt --host=" . $db_server . " --user=" . $db_user . " --password='" . $db_password . "' " . $db_databasename . " > " . $backupFile;
	   $create_backup = system($data_str);
	   if ($download)
		   $this->download_file($fileToDownload);
		   return true;
	}
	
	function download_file($file) {
	   $download_dir = CONF_DB_BACKUP_DIRECTORY_FULL_PATH; // the folder where the files are stored ('.' if this script is in the same folder)
	   $path = $download_dir  . $file;
	   if (file_exists($path)) {
		   $filename = $download_dir . "/" . $file;
		   header('Content-Description: File Transfer');
		   header("Content-Type: application/force-download");
		   header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\";");
		   header('Content-Length: ' . filesize($filename));
		   readfile("$filename");
	   } else {
		   echo "<center>The file [$file] is not available for download.</center>";
	   }
	}
			
	function recurse_zip($src,&$zip,$path_length) {
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				$this->recurse_zip($src . '/' . $file,$zip,$path_length);
			}else {
				$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path_length));
				}
			}
		}
		closedir($dir);
	}
	
	//Call this function with argument = absolute path of file or directory name.
	function compress($src, $destination){
		
			if(substr($src,-1)==='/'){$src=substr($src,0,-1);}
			$arr_src=explode('/',$src);
			$filename = end($src);
			
			unset($arr_src[count($arr_src)-1]);
			$path_length=strlen(implode('/',$arr_src).'/');
			$f=explode('.',$filename);
			$filename=$f[0];
			$filename=(($filename=='')? $destination.date("d-m-y H-i-s").'.zip' : $filename.'.zip');
			$zip = new ZipArchive;
			$res = $zip->open($filename, ZipArchive::CREATE);
			if($res !== TRUE){
				echo 'Error: Unable to create zip file';
				exit;
			}
			if(is_file($src)){
					$zip->addFile($src,substr($src,$path_length));}
			else{
				if(!is_dir($src)){
					$zip->close();
					@unlink($filename);
					echo 'Error: File not found';
				exit;
			}
			$this->recurse_zip($src,$zip,$path_length);}
			$zip->close();
			return true;
	}
	
	function full_copy( $source, $target,$empty_first=true)
	{
		if ($empty_first){
			$this->recursiveDelete($target);
		}
		if ( is_dir( $source ) ) {
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) ) {
					$this->full_copy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			}

			$d->close();
		}else {
			copy( $source, $target );
		}
	}
	
	
	function recursiveDelete($str) {
		if (is_file($str)) {
				return @unlink($str);
		}
		elseif (is_dir($str)) {
			$scan = glob(rtrim($str,'/').'/*');
			foreach($scan as $index=>$path) {
				$this->recursiveDelete($path);
			}
			return @rmdir($str);
		}
	}
	
	function reloadPage() {
		header('Location: '.$_SERVER['REQUEST_URI']);
		exit;
	}

	/*]*/
}
?>