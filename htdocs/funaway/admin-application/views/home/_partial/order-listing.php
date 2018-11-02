<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'No',
				//'photo'=>'Photo',
				'order_id'=>'ID',
				'user_name'=>'Traveler',
				'ordered'=>'Activity',
				'order_user_email'=>'Traveler Email',
				'order_user_phone'=>'Phone',
				'order_date'=>'Date',
			//	'order_payment_method'=>'Gateway',
				'order_payment_status' => 'Status',
			//	'order_process_status' => 'Process Status',
			//	'order_shipping_type' => 'Shipping type',
				//'order_shipping_amount' => 'Shipping Amount',
				
			//	'order_net_amount' => 'Net',
				'order_total_amount' => 'Total',
				'action' => 'Action',
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
			}
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				$tr = $tbl->appendElement('tr');
				
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'order_date':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key],true));
							break;
					
						case 'order_payment_method':
							$td->appendElement('plaintext', array(), Info::getPaymentMethodByKey($row[$key]));
							break;
						
						case 'order_net_amount':
						case 'order_total_amount':
							$td->appendElement('plaintext', array(), Currency::displayPrice($row[$key]),true);
							break;
						case 'order_payment_status':
							$td->appendElement('plaintext', array(), Info::getPaymentStatus($row[$key]));
							break;
						
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							
							if($canViewOrder){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>FatUtility::generateUrl('orders','detail',array('order_id'=>$row['order_id'])), 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
							}
							
							
							if($canViewOrder){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>FatUtility::generateUrl('orders','view-transactions',array($row['order_id'])), 'class'=>'button small green', 'title'=>'Transaction'),'<i class="ion-ios-paper-outline icon"></i>', true);	
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

                                                  

