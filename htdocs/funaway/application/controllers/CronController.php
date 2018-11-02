<?php

class CronController extends MyAppController {

    function index() {
        define('STITCH_RESTORE_SOURCE_PATH', CONF_INSTALLATION_PATH . 'restore-files/images/');
        define('STITCH_RESTORE_DB_SOURCE_PATH', CONF_INSTALLATION_PATH . 'restore-files/database.bak/');
        $this->images_restore();
        $this->db_restore();
    }

    private function db_restore() {
        $bkfile = dir(STITCH_RESTORE_DB_SOURCE_PATH);
        while (($file = $bkfile->read()) !== false) {
            if (!($file == "." || $file == ".." || $file == ".htaccess")) {
                $source = $bkfile->path . $file;
                $cmd = "mysql --user=" . CONF_DB_USER . " --password='" . CONF_DB_PASS . "' --default-character-set=utf8 " . CONF_DB_NAME . " < " . $source;
                exec($cmd, $res, $ret);
            }
        }
    }

    private function images_restore() {
        $imagesFolderArray = array('backend', 'contributions', 'database-backups', 'default-images', 'payment-methods', 'post-images');
        foreach ($imagesFolderArray as $folder_name) {
            echo $this->tableHeaderHtml($folder_name);
            $this->folder_restore($folder_name);
        }
    }

    private function folder_restore($folder_name) {
        if ($folder_name != "") {
            $dir = dir(CONF_INSTALLATION_PATH . "user-uploads/" . $folder_name . "/");
            $conf_db_path = CONF_INSTALLATION_PATH . "user-uploads/" . $folder_name . "/";
            while (($file = $dir->read()) !== false) {
                if ($file == "." || $file == ".." || $file == ".htaccess") {
                    
                } else {
                    foreach (glob($conf_db_path . '*') as $v) {
                        unlink($v);
                    }
                }
            }
            $bkfile = dir(STITCH_RESTORE_SOURCE_PATH . $folder_name . "/");
            while (($file = $bkfile->read()) !== false) {
                if (!($file == "." || $file == ".." || $file == ".htaccess")) {
                    $newfile = $conf_db_path . $file;
                    $source = $bkfile->path . $file;
                    if (!copy($source, $newfile)) {
                        echo "Failed to copy $file...\n" . $newfile;
                    } else {
                        #echo "copy $file...\n";
                    }
                }

                echo $this->tablebodyHtml($newfile);
            }
            echo "</table>";
        }
    }

    private function files_restore($folder_name = 'user-uploads') {
        if ($folder_name != "") {
            $dir = dir(CONF_INSTALLATION_PATH . "user-uploads/" . $folder_name . "/");
            $conf_db_path = CONF_INSTALLATION_PATH . "user-uploads/" . $folder_name . "/";
            while (($file = $dir->read()) !== false) {
                if ($file == "." || $file == ".." || $file == ".htaccess") {
                    
                } else {
                    foreach (glob($conf_db_path . '*') as $v) {
                        unlink($v);
                    }
                }
            }
            $bkfile = dir(STITCH_RESTORE_SOURCE_PATH . $folder_name . "/");
            while (($file = $bkfile->read()) !== false) {
                if (!($file == "." || $file == ".." || $file == ".htaccess")) {
                    $newfile = $conf_db_path . $file;
                    $source = $bkfile->path . $file;
                    if (!copy($source, $newfile)) {
                        echo "Failed to copy $file...\n" . $newfile;
                    } else {
                        
                    }
                }

                echo $this->tablebodyHtml($newfile);
            }
            echo "</table>";
        }
    }

    private function tableHeaderHtml($foldername) {
        $str = '<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="tbl_listing">
				<thead><tr>
				<th width="285">' . $foldername . '</th>
				<th width="323" >&nbsp;</th>
				<th width="144" height="25" ></th>
				</tr></thead>';
        return $str;
    }

    private function tablebodyHtml($filepath) {
        $str = '<tr>
				<td width="285" height="25" style="color:#1a91f7" >
					' . basename($filepath) . '
				</td>
				<td width="323" height="25" >' . date("d/m/Y H:i:s.", filectime($filepath)) . '</td>
				<td height="25" align="center" nowrap ><ul class="listing_option"></ul></td>
			</tr>';
        return $str;
    }
	
	public function execute($id = 0) {
		$db = FatApp::getDb();
		
		$allCrons = Cron::getAllRecords(true, $id);
		
		foreach ( $allCrons as $row ) {
			$cron = new Cron ( $row ['cron_id'] );
			$cron->loadFromDb ();
			
			$logId = $cron->markStarted ();
			if (! $logId) {
				continue;
			}
			
			$arr = explode('/', $row['cron_command']);
			
			$class = $arr[0];
			//$obj = new $class();
			array_shift($arr);
			$action = $arr[0];
			$obj = new $class($action);
			array_shift($arr);
			
			$success = call_user_func_array(array($obj, $action), $arr);
			
			if ($success !== false) {
				$cron->markFinished($logId, 'Response Got: ' . $success);
			}
			else {
				$cron->markFinished($logId, 'Marked finished with error ');
			}
			echo 'Ended';
		}
		Cron::clearOldLog();
	}

}
