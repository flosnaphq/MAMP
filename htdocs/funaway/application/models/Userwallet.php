<?php

class Userwallet extends FatModel {

    const WITHDRAWAL_REQUEST = 0;
    
    protected $user_id;
    protected $user_type;
    protected $user_wallet_amount = 0;

    public function __construct($user_id, $user_type) {
        $this->user_id = $user_id;
        $this->user_type = $user_type;
        $this->user_wallet_amount = Wallet::getWalletBalance($this->user_id);
    }

    private function creditAmount($amount, $transType, $description, $status) {

        $saveData = array(
            'wtran_user_id' => $this->user_id,
            'wtran_user_type' => $this->user_type,
            'wtran_amount' => $amount,
            'wtran_type' => $transType,
            'wtran_status' => $status,
            'wtran_desc' => $description,
        );

        return Wallet::addToWallet($saveData);
    }
   private function debitAmount($amount, $transType, $description, $status) {

        $saveData = array(
            'wtran_user_id' => $this->user_id,
            'wtran_user_type' => $this->user_type,
            'wtran_amount' => -$amount,
            'wtran_type' => $transType,
            'wtran_status' => $status,
            'wtran_desc' => $description,
        );

        return Wallet::addToWallet($saveData);
    }
    public function addWithdrawalRequest($amount, $description) {
	
        if($this->user_wallet_amount<$amount){
            $this->error = "Invalid Amount";
            return false;
        }
        
       if($this->debitAmount($amount,0,$description,0)){
           return true;
       }
       
       $this->error = "Error While Updating";
       return false;
    }
    public function addCreditToUser($amount, $description) {
 
       if($this->creditAmount($amount,0,$description,1)){
           return true;
       }
       
       $this->error = "Error While Updating";
       return false;
    }
}
