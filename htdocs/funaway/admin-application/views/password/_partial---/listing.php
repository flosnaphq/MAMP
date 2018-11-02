<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php		
			$arr_flds = array(
				'listserial'=>'S.N.',
				'admin_username'=>'Username',
				'admin_full_name'=>'Admin Full Name',
				'admin_email'=>'Email',
				'action' => 'Action',
			);
			
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);			
			}
			
			foreach ($arr_listing as $sn=>$row){
				$tr = $tbl->appendElement('tr');
				if($row['admin_active']==0) {
					$tr->setAttribute ("class","inactive-tr");
				}	
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sn+1);
							break;
												
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							$li = $ul->appendElement("li");
							$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['admin_id'].")"),'<i class="ion-edit icon"></i>', true);							
							$li = $ul->appendElement("li");
							$li->appendElement('a', array('href'=>FatUtility::generateUrl('admin','permissions',array($row['admin_id'])), 'class'=>'button small green', 'title'=>'Manage Permission'),'<i class="ion-key icon"></i>', true);	
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'Javascript:;', 'onClick'=>'popupView("'.FatUtility::generateUrl('admin','view',array('admin_id'=>$row['admin_id'])).'");', 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
							}
						
						break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
			}
			
			if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();
		
		?>
