<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<a href = "javascript:;" onclick="addCustomLink()">Add Custom Link</a>
<?php	
			$arr_flds = array(
				'navigation_caption'=>'Caption',
				'navigation_link'=>'Link',
				'navigation_open'=>'Open In',
				'navigation_display_order'=>'Display Order',
				'action' => 'Action',
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
			}
		
			foreach ($navigations as $sn=>$row){
				
				$tr = $tbl->appendElement('tr');
				
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'navigation_display_order':
							if($canEdit){
								$td->appendElement('input', array('value'=>$row[$key],'onblur'=>'changeOrder("'.$row['navigation_id'].'",this)','class'=>'text-display-order'));
							}
							else{
								$td->appendElement('plaintext', array(), $row[$key]);
							}
							break;
							
						case 'navigation_link':
							if($row['navigation_type'] == 1){
								$text = $row[$key];
							}
							else{
								$text = "CMS Page";
							}
							$td->appendElement('plaintext', array(), $text);
							break;	
						
						case 'navigation_open':
							if($canEdit){
								$nav = Navigation::windowType();
								$select = $td->appendElement('select', array('name'=>'confirmed','onchange'=>"changeWindowType('Do You Want To Change ?','".$row['navigation_id']."', this.value)"),'');
								foreach($nav as $status=>$status_name){
									$ar = array('value'=>$status);
									if($row[$key] == $status){
										$ar['selected'] = 'selected';
									}
									$select->appendElement('option', $ar, $status_name);
								}
							}
							else{
								$td->appendElement('plaintext', array(), Info::getActivityConfirmStatusByKey($row[$key]));
							}
							
							break;

							
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canEdit){
								/* $li = $ul->appendElement("li");
							$li->appendElement('a', array('class'=>'button small green', 'title'=>'Edit',"href"=>'javascript:;', 'onclick'=>"editNavigation({$row['navigation_id']});"),'<i class="ion-edit icon"></i>', true);	 */
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('class'=>'button small green', 'title'=>'Edit',"href"=>"javascript:;",'onclick'=>"deleteNavigation({$row['navigation_id']});"),'<i class="ion-android-delete icon"></i>', true);	
							}
							
							break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
				
			}
			
			if (count($navigations) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();



?>
