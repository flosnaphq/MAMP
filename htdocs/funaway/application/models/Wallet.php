<?php 
class Wallet extends FatModel {
	const DB_TBL = 'tbl_wallet_transactions';
	const DB_TBL_PREFIX = 'wtran_';

	public static function addToWallet($wallet) {
		return FatApp::getDb()->insertFromArray(static::DB_TBL,$wallet);
	}
	
	public static function getBlockedAmount($user_id){
		$srch = new SearchBase('tbl_order_activities');
		$srch->joinTable('tbl_orders','inner join','oactivity_order_id = order_id and order_payment_status = 1');
		$srch->addDirectCondition('date(oactivity_event_timing) > '.Info::currentDate());
		$srch->addFld('sum(oactivity_booking_amount) as blocked_amount');
		$rs = $srch->getResultSet();
		$record = FatApp::getDb()->fetch($rs);
		return $record['blocked_amount'];
	}
	
	
	
	public static function getWalletBalance($userId, $type=1){
		$srch = new SearchBase(static::DB_TBL);
		$srch->addCondition("wtran_user_id","=",$userId);
		//$srch->addCondition("wtran_type","=",$type);
		$srch->addFld('sum(wtran_amount) as balance');
		$rs = $srch->getResultSet();
		$record = FatApp::getDb()->fetch($rs);
		return $record['balance'];
	}
	
	public static function getWalletList($userId){
		$srch = new SearchBase(static::DB_TBL);
		$srch->addCondition("wtran_user_id","=",$userId);
		return $srch;
	}
	
	public static function isPossibleWalletTransaction($user_id,$amount){
		$wallet = Wallet::getWalletBalanceByUser($user_id);
		$db = FatApp::getDb();
	}
	
	public static function updateWallet($data){
		$db = FatApp::getDb();
		$db->updateFromArray('tbl_wallet_transactions', $data, array('smt' => 'wtran_id = ?', 'vals' => array($data['wtran_id'])));
		
		return true;
	}
	

        
	
	public static function getWalletBalanceByUser($user_id, $type=1){
		
		$srch = new SearchBase("tbl_wallet_transactions");
		$srch->addCondition("wtran_user_id","=",$user_id);
		$srch->addFld("SUM(IF(wtran_amount < 0,wtran_amount,0)) as debit_amount");
		$srch->addFld("SUM(IF(wtran_amount > 0,wtran_amount,0)) as credit_amount");
		$srch->addFld("SUM(wtran_amount) as wallet_balance");
		$srch->addCondition("wtran_user_type","=",$type);
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$db = FatApp::getDb();
		$rs = $srch->getResultSet();
		
		return $record  =  $db->fetch($rs);
	}
	
	
	
	public static function getWalletByUser($user_id){
		$srch = new SearchBase("tbl_wallet_transactions");
		$srch->addCondition("wtran_user_id","=",$user_id);
		$srch->addMultipleFields(array("wtran_id","wtran_desc","IF(wtran_amount<0,wtran_amount,0) as debit_amount","IF(wtran_amount>0,wtran_amount,0) as credit_amount","wtran_date"));
		return $srch;
	}
	
	
	
	public function getBookingTotalAmount($user_id, $booking_id){
		$srch = new SearchBase("tbl_wallet_transactions");
		$srch->addCondition("wtran_user_id","=",$user_id);
		$srch->addCondition("wtran_booking_id","=",$booking_id);
		$srch->addCondition("wtran_status","=",1);
		$srch->addCondition("wtran_type","=",1);
		$srch->addCondition("wtran_user_type","=",1);
		$srch->addFld('sum(wtran_amount) as total_amount');
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		
		$row = FatApp::getDb()->fetch($rs);
		return $row['total_amount'];
	}
	
	/* public function getWalletByMerchant($merchant_id){
		$srch = new SearchBase("tbl_wallet_transactions");
		$srch->joinTable('tbl_restaurants','INNER JOIN','wtran_restaurant_id = restaurant_id and restaurant_user_id = '.$merchant_id);
		$srch->joinTable('tbl_restaurants_lang','INNER JOIN','restaurantlang_restaurant_id = wtran_restaurant_id and restaurantlang_lang_id = '.Info::defaultLang());
		$srch->addCondition("restaurant_user_id","=",$merchant_id);
		$srch->addMultipleFields(array("wtran_id","wtran_desc","IF(wtran_amount<0,wtran_amount,0) as debit_amount","IF(wtran_amount>0,wtran_amount,0) as credit_amount","wtran_date",'restaurant_name'));
		return $srch;
	} */
	
	/* private static function isTransactionKeyExist($transact_id){
		$srch = new SearchBase("tbl_transaction_record");
		$srch->addCondition("trecord_id","=",$transact_id);
		$db = FatApp::getDb();
		$rs = $srch->getResultSet();
		$record  =  $db->fetch($rs);
		if(isset($record) && !empty($record)){
			return true;
		}
		return false;
		
	} */
	
	/* public static function transactionKey($user_id,$user_type=0){
		$user_id = intval($user_id);
		$transact_id = Info::createWLTransacatKey($user_id, $user_type);
		while(Wallet::isTransactionKeyExist($transact_id)){
			$transact_id = Info::createWLTransacatKey($user_id, $user_type);
		}
		$db = FatApp::getDb();
		$db->insertFromArray("tbl_transaction_record",array("trecord_id"=>$transact_id));	
		return $transact_id;
	}
	 */
	
}