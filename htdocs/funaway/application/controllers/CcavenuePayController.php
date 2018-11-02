<?php

require_once(CONF_INSTALLATION_PATH . 'public/includes/payment-plugins/ccavenue/Crypto.php');

class CcavenuePayController extends PaymentController {

    protected $keyName = "ccavenue";

    private function getPaymentForm($order_id) {

        $frm = new Form('frm-ccavenue');
        $frm->setFormTagAttribute('action', FatUtility::generateFullUrl('CcavenuePay', 'iframe'));
        //  $frm->addHiddenField('', 'tid', time());

        $frm->addHiddenField('', 'merchant_id');
        $frm->addHiddenField('', 'order_id', $order_id);
        $frm->addHiddenField('', 'amount');
        $frm->addHiddenField('', 'currency', 'INR');
        $frm->addHiddenField('', 'merchant_param1');
        $frm->addHiddenField('', 'language', "EN");
        $frm->addHiddenField('', 'redirect_url', FatUtility::generateFullUrl('CcavenuePay', 'callback'));
        $frm->addHiddenField('', 'cancel_url', FatUtility::generateFullUrl('order', 'paymentFail', array($order_id))); //$order_payment_gateway_description=sprintf(Utilities::
        $frm->addHiddenField('', 'integration_type', 'iframe_normal');

        $frm->addHiddenField('', 'billing_name');
        $frm->addHiddenField('', 'billing_address');
        $frm->addHiddenField('', 'billing_city');
        $frm->addHiddenField('', 'billing_state');
        $frm->addHiddenField('', 'billing_zip');
        $frm->addHiddenField('', 'billing_country');
        $frm->addHiddenField('', 'billing_tel');
        $frm->addHiddenField('', 'billing_email');
        $frm->addHiddenField('', 'delivery_name');
        $frm->addHiddenField('', 'delivery_address');
        $frm->addHiddenField('', 'delivery_city');
        $frm->addHiddenField('', 'delivery_state');
        $frm->addHiddenField('', 'delivery_zip');
        $frm->addHiddenField('', 'delivery_country');
        $frm->addHiddenField('', 'delivery_tel');



        $btn = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('Continue'), array('class' => 'button button--fill button--red  button--small'));
        $btn->htmlBeforeField = '<div>' . Info::t_lang('Please_wait_Redirecting_to_CCAVENU_or_press') . '</div><br>';
        $frm->setJsErrorDisplay('x');
        return $frm;
    }

    public function charge() {

        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != ApplicationConstants::USER_TRAVELER_TYPE) {
            $error = Info::t_lang('Invalid_request');
            $this->set('error', $error);
            $this->_template->render(false, false);
        }

        $paymentGatewaySettings = $this->getPaymentGatewaySettings();


        if (!$paymentGatewaySettings) {
            $error = Info::t_lang('Invalid_payment_method');
            $this->set('error', $error);
            // $this->_template->render(false, false);
        }


        if (!$this->isEligibleToOrder()) {
            $error = Info::t_lang('Unable_To_Create_Order');
            $this->set('error', $error);
            // $this->_template->render(false, false);
        }

        $this->_template->render(false, false);
    }

    public function process() {
        $ret['error'] = '';
        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != ApplicationConstants::USER_TRAVELER_TYPE) {
            FatUtility::dieJsonError(Message::addErrorMessage(Info::t_lang('Invalid_request')));
        }

        $paymentGatewaySettings = $this->getPaymentGatewaySettings();

        if (!$paymentGatewaySettings) {
            FatUtility::dieJsonError(Message::addErrorMessage(Info::t_lang('Invalid_payment_method')));
        }


        if (!$this->isEligibleToOrder() || !$orderId = $this->createOrder()) {
            $error = Info::t_lang('Unable_To_Create_Order');
            FatUtility::dieJsonError($error);
        }

        $frm = $this->getPaymentProcessForm($orderId);

        $this->set('frm', $frm);
        $ret['msg'] = Info::t_lang('Please_continue');
        $ret['frm'] = $this->_template->render(false, false, 'payuindia-pay/payment-form.php', true);
        FatUtility::dieJsonSuccess($ret);
    }

    private function getPaymentProcessForm($orderId) {


        $frm = $this->getPaymentForm($orderId);

        $payment_settings = $this->getPaymentGatewaySettings();

        $merchantId = $payment_settings["merchant_id"];



        $orderDetails = new OrderDetail($orderId);


        $firstname = $orderDetails->getOrderUserFirstName();
        $lastName = $orderDetails->getOrderUserLastName();
        $email = $orderDetails->getOrderUserEmail();
        $phone = $orderDetails->getOrderUserPhone();
        $payment_gateway_charge = $orderDetails->getOrderPayableAmount();
        $txnid = $orderDetails->getOrderInvoice();
        $order_payment_gateway_description = $orderDetails->getOrderProductInfo();





        $frmData = array(
            'merchant_id' => $merchantId,
            'amount' => $payment_gateway_charge,
            'tid' => $txnid,
            'billing_name' => $firstname,
            'merchant_param1' => User::getLoggedUserId(),
            'billing_address' => 'India',
            'billing_city' => 'India',
            'billing_state' => 'India',
            'billing_zip' => '160045',
            'billing_country' => 'India',
            'billing_tel' => $phone,
            'billing_email' => $email,
        );

        $frm->fill($frmData);

        return $frm;
    }

    public function iframe() {


        $payment_settings = $this->getPaymentGatewaySettings();

        $working_key = $payment_settings['working_key'];
        $access_code = $payment_settings['access_code'];
        $merchant_data = '';
        $post = FatApp::getPostedData();
        unset($post['btn_submit']);
        foreach ($post as $key => $value) {
            $merchant_data.=$key . '=' . $value . '&';
        }

        //  $merchant_data.="currency=INR";	
        $encrypted_data = encrypt($merchant_data, $working_key); // Method for encrypting the data.

        if ($payment_settings["transaction_mode"] == 1) {
            $iframe_url = 'https://secure.ccavenue.com';
        } elseif ($payment_settings["transaction_mode"] == 0) {
            $iframe_url = 'https://test.ccavenue.com';
        }
        $iframe_url.='/transaction/transaction.do?command=initiateTransaction&encRequest=' . $encrypted_data . '&access_code=' . $access_code;

        $this->set('url', $iframe_url);
        $this->_template->render(false, false);
    }

    public function callback() {

        $payment_settings = $this->getPaymentGatewaySettings();

        $post = FatApp::getPostedData();

        $workingKey = $payment_settings['working_key'];
        $encResponse = $post["encResp"];   //This is the response sent by the CCAvenue Server
     $rcvdString = decrypt($encResponse, $workingKey);  //Crypto Decryption used as per the specified working key.
        $request = $rcvdString;
        $order_status = "";

       parse_str($rcvdString, $responseArray);
      
        $order_status = $responseArray['order_status'];
        $order_id = $responseArray['order_id'];
        $paid_amount = $responseArray['amount'];
        $tracking_id = $responseArray['tracking_id'];

        $odrObj = new Order();
        $orderInfo = $odrObj->getOrderDetail($order_id);
       

        $payment_gateway_charge = $orderInfo['order_received_amount'];
        if ($payment_gateway_charge > 0) {
            $total_paid_match = ((float) $paid_amount == $payment_gateway_charge);
            if (!$total_paid_match) {
                $request .= "\n\n CCAvenue :: TOTAL PAID MISMATCH! " . strtolower($paid_amount) . "\n\n";
            }
            if ($order_status == "Success" && $total_paid_match) {

                $this->updateResponse($responseArray, $paid_amount, $tracking_id, $order_id);

                FatApp::redirectUser(FatUtility::generateFullUrl('order', 'success', array($order_id)));
            } else {

                FatApp::redirectUser(FatUtility::generateFullUrl('order', 'paymentFail', array($order_id)));
            }
        }
    }

    private function updateResponse($response, $amount, $transactionId, $order) {




        $data['amount'] = floatval($amount);
        $data['gateway_transaction_id'] = $transactionId;
        $data['response_data'] = json_encode($responseArray);
        $data['mode'] = $this->keyName;
        $data['transaction_completed'] = 1;
        Sms::orderSmsToHost($order);
        Sms::paymentSuccessSmsToTraveler($order, $response['merchant_param1']);
        Transaction::addNew($order, $data);

        return true;
    }

}
