<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				//'photo'=>'Photo',
				'tran_order_id'=>'Order Id',
				'tran_time'=>'Time',
				'debit'=>'Debit',
				'credit'=>'Credit',
				'tran_completed'=>'Status',
				'tran_real_amount'=>'Net Amount',
			
				'tran_payment_mode' => 'Payment Mode',
				'tran_gateway_transaction_id' => 'Gateway Transaction Id',
				'tran_gateway_response_data' => 'Gateway Response',
				'tran_admin_comments' => "Admin's Comment",
				'tran_declined_by_admin' => 'Declined BY Admin',
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
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
						case 'tran_declined_by_admin':
							$declined_status ='--';
							if($row['tran_declined_by_admin'] == 1){
								$declined_status ='Declined';
							}
							$td->appendElement('plaintext', array(), $declined_status);
							break;
						case 'tran_completed':
							$td->appendElement('plaintext', array(), Info::getPaymentStatus($row[$key]));
							break;
						case 'debit':
							$debit_amt ='--';
							if($row['tran_amount'] < 0){
								$debit_amt = Currency::displayPrice(abs($row['tran_amount']));
							}
							$td->appendElement('plaintext', array(), $debit_amt, true);
							break;
						case 'credit':
							$credit_amt ='--';
							if($row['tran_amount'] >= 0){
								$credit_amt = Currency::displayPrice($row['tran_amount']);
							}
							$td->appendElement('plaintext', array(), $credit_amt, true);
							break;
					
						case 'tran_payment_mode':
							$td->appendElement('plaintext', array(), Info::getPaymentmodeKey($row[$key]));
							break;
						case 'tran_real_amount':
							$td->appendElement('plaintext', array(),Currency::displayPrice($row[$key]),true);
							break;
						case 'tran_time':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key],true));
							break;
						case 'tran_gateway_response_data':
							$td->appendElement('span',array('style'=>'word-break:break-word;'), $row[$key],true);
							break;
					
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
				
			}
			
			if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();
