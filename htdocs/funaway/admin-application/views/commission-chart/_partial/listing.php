<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

?>
<?php	
			$arr_flds = array(
				'commissionchart_min_amount'=>'Minimum Listing Price',
				'commissionchart_max_amount'=>'Maximum Listing Price',
				'commissionchart_rate'=>'Site Fee',
				'action' => 'Action',
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
				
			}
			
			foreach ($arr_listing as $sn=>$row){
				
				$tr = $tbl->appendElement('tr');
					
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'commissionchart_min_amount':
						case 'commissionchart_max_amount':
								$td->appendElement('plaintext', array(), Currency::displayPrice($row[$key]), true);
							break;
						case 'commissionchart_rate':
							$td->appendElement('plaintext', array(), $row[$key].'%', true);
							break;
				
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['commissionchart_id'].")"),'<i class="ion-edit icon"></i>', true);$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Delete',"onclick"=>"deleteCommission(".$row['commissionchart_id'].")"),'<i class="ion-close-circled icon"></i>', true);	
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


