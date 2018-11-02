<?php
class fatRestore
{
/*DB restore [*/

	private $error;
	private $canUse = false;
	private $db;
	public function __construct()
	{
		$this->canUse = CRON_INIT;
		if($this->canUse !== true)
		{
			$this->error = 'Access denied!!';
			return;
		}
		$this->canUse = true;
		$this->db = FatApp::getDb();
	}
	
	function restoreDatabase($backupFile,$concate_path=true)
	{
		if($this->canUse !== true)
		{
			return;
		}
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
		if($rs = $this->db->query($sql))
		{
			while($row = $this->db->fetch($rs))
			{
				$table_name=$row["Tables_in_".$db_databasename];
				$this->db->query("DROP TABLE $db_databasename.$table_name");
			}
		}
		$cmd ="mysql --user=" . $db_user . " --password='" . $db_password . "' " . $db_databasename . " < " . $backupFile;
		system($cmd);
		return true;
	 }
	
	function getMySQLVariable($varname, $scope = "session")
	{
		if($this->canUse !== true)
		{
			return;
		}
		$gv = $this->db->query("show $scope variables");
		$counter = 0;
		$val = false;
		while ($grow = $this->db->fetch($gv))
		{
		if ($grow[0] == $varname)
		{
			   $val = $grow[1];
			   break;
		   }
		}	
		return $val;
	}
	
	function full_copy( $source, $target,$empty_first=true)
	{
		if($this->canUse !== true)
		{
			return;
		}
		if ($empty_first)
		{
			$this->recursiveDelete($target);
		}
		if ( is_dir( $source ) )
		{
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) )
			{
				if ( $entry == '.' || $entry == '..' )
				{
					continue;
				}
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) )
				{
					$this->full_copy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			}

			$d->close();
		}
		else 
		{
			copy( $source, $target );
		}
		return true;
	}
	
	
	function recursiveDelete($str)
	{
		if($this->canUse !== true)
		{
			return;
		}
		if (is_file($str))
		{
				return @unlink($str);
		}
		elseif (is_dir($str))
		{
			$scan = glob(rtrim($str,'/').'/*');
			foreach($scan as $index=>$path)
			{
				$this->recursiveDelete($path);
			}
			return @rmdir($str);
		}
	}
	
	public function getError()
	{
		return $this->error;
	}
}
?>