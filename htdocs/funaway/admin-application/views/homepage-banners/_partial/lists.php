<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				'afile_id'=>'Image',
				'afile_type'=>'Banner Type',
				'action' => 'Action',
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			$status = array(0=>'Inactive',1=>'Active');
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
			}
			$sr_no = 0;
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				$tr = $tbl->appendElement('tr');
		
				foreach ($arr_flds as $key=>$val){
					
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'afile_id':
							
							$td->appendElement('img', array('src'=>FatUtility::generateUrl('image','homepageBanner',array($row['afile_type'],100,100,time()),CONF_BASE_DIR)), $sr_no);
							break;
						case 'afile_type':
                                                    $td->appendElement('plaintext', array(), $row[$key]=="17"?"Home page Stats Banner":"Home page Contact Banner");
							break;						
						
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['afile_id'].",".$row['afile_type'].")"),'<i class="ion-edit icon"></i>', true);
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


