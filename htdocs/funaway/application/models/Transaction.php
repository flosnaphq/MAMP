<?php
class Transaction extends FatModel {
    private $db;
	function __construct() {
        parent::__construct();
		
    }
	
	public static function totalAmountOnOrder($order_id){
		$record = array();
		$srch = new SearchBase('tbl_order_transactions');
		$srch->addCondition("tran_order_id","=",$order_id);
		$srch->addCondition("tran_completed","=","1");
		$srch->addFld("sum(tran_amount) as total_amount");
		$rs = $srch->getResultSet();
		$record  = FatApp::getDb()->fetch($rs);
		return $record['total_amount'];
	}
	
	public static function add($data=array()){
		if(empty($data)){
			return false;
		}
		$success = FatApp::getDb()->insertFromArray('tbl_order_transactions', $data);
	}
	
	public static function addNew($order_id = "",$data = array()){
		$crt = new Cart();
		$ord = new Order();
		$trans = array();
		$order = $ord->getOrderDetail($order_id);	
		$trans['tran_order_id'] = $order['order_id'];
		$trans['tran_time'] = Info::currentDatetime();
		$trans['tran_amount'] =  $data['amount'];
		$trans['tran_real_amount'] =  $order['order_net_amount'];
		$price = $order['order_net_amount'];
		$received_amount = $order['order_received_amount'];
		if(!empty($data)){
			$trans['tran_gateway_transaction_id'] = $data['gateway_transaction_id'];
			$trans['tran_gateway_response_data'] = $data['response_data'];
			$trans['tran_completed'] = $data['transaction_completed'];
			$trans['tran_payment_mode'] = Info::getPaymentMode($data['mode']);
			if($data['mode'] =="admin"){
				$trans['tran_admin_comments'] = $data['tran_admin_comments'];
			}
			if(isset($data['tran_declined_by_admin']) && $data['tran_declined_by_admin']){
				$trans['tran_declined_by_admin'] = $data['tran_declined_by_admin'];
			}
		}
		
		$success = FatApp::getDb()->insertFromArray('tbl_order_transactions', $trans);
          
		if($success){
			if(isset($data['transaction_completed']) && $data['transaction_completed'] == 1){
				$total_amount = Transaction::totalAmountOnOrder($order_id);
				$usr = new User();
				$user = $usr->getUserByUserId($order["order_user_id"]);
				$email = (trim($order["order_user_email"])!="")?$order["order_user_email"]:$user["user_email"];
				$temp = 0;
				$type = "";
				$array = array();
				if($total_amount>=$received_amount){
                                    
					$ord->updateOrder($order_id);
		//			sendMailNew(CONF_ADMIN_EMAIL_ID,18,array("{order}"=>$order_id,"{type}"=>$type));	
					
					$activities = $ord->getOrderActivity($order_id);
						
					foreach($activities as $acts){
						$wallet = array(); // for host
						 $chargeable_amt = $acts['oactivity_received_amount'];
                                                
						$host_commission = $ord->getCommission($chargeable_amt, $acts['activity_user_id']);
						$admin_commission = $ord->getCommission($chargeable_amt, $acts['activity_user_id'],true);
						$order_commissions['oactivity_admin_commission']= $admin_commission;
						$order_commissions['oactivity_host_commission']= $host_commission;
						
						$wallet['wtran_activity_id'] = $acts['oactivity_activity_id']; 
						$wallet['wtran_user_id'] = $acts['activity_user_id']; 
						$wallet['wtran_type'] = 1; 
						$wallet['wtran_user_type'] = 1; 
						$wallet['wtran_date'] = Info::currentDatetime(); 
						
						$wallet['wtran_amount'] = $host_commission; 
						$wallet['wtran_desc'] = Info::t_lang("NEW_BOOKING_ADDED_: ").$acts['oactivity_booking_id']; 
						$wallet['wtran_status'] = 1;
						$wallet['wtran_referal'] = ''; 
						$wallet['wtran_ref_tran_id'] = ''; 
						$wallet['wtran_ref_tran_data'] = ''; 
						$wallet['wtran_booking_id'] = $acts['oactivity_booking_id'];
						$wallet['wtran_order_id'] = $order_id;
						
                                       
						
						Wallet::addToWallet($wallet);
						
						$wallet = array(); // for admin
						$wallet['wtran_activity_id'] = $acts['oactivity_activity_id']; 
						$wallet['wtran_user_id'] = 0; 
						$wallet['wtran_type'] = 1; 
						$wallet['wtran_user_type'] = 0; 
						$wallet['wtran_date'] = Info::currentDatetime(); 
						$wallet['wtran_amount'] =  $admin_commission;
						$wallet['wtran_desc'] = "Commission From ".$acts['oactivity_booking_id']; 
						$wallet['wtran_status'] = 1;
						$wallet['wtran_referal'] = ''; 
						$wallet['wtran_ref_tran_id'] = ''; 
						$wallet['wtran_ref_tran_data'] = ''; 
						$wallet['wtran_booking_id'] = $acts['oactivity_booking_id'];
						$wallet['wtran_order_id'] = $order_id;
							
						Wallet::addToWallet($wallet);
						$ord->updateOrderActivity($acts['oactivity_booking_id'], $order_commissions);
					}
							
					
					
				}else{
					$ord->updateOrder($order_id,"declined");
		//			sendMailNew($email,$temp,$array);
				}		
			}
                              
		}	
		return $success;
	}
	
}	
?>