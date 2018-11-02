<?php

class PayuindiaPayController extends PaymentController {

    protected $keyName = "payuindia";

    private function getPaymentForm($orderId) {

        $payment_settings = $this->getPaymentGatewaySettings();

        if ($payment_settings["transaction_mode"] == 1) {
            $action_url = 'https://secure.payu.in/_payment.php';
        } elseif ($payment_settings["transaction_mode"] == 0) {
            $action_url = 'https://test.payu.in/_payment.php';
        }
        $frm = new Form('frmPayuIndia');


        $frm->setFormTagAttribute('action', $action_url);


        $frm->addHiddenField('key', 'key');
        $frm->addHiddenField('txnid', 'txnid');
        $frm->addHiddenField('amount', 'amount');
        $frm->addHiddenField('productinfo', 'productinfo');
        $frm->addHiddenField('firstname', 'firstname');
        $frm->addHiddenField('Lastname', 'Lastname');
        $frm->addHiddenField('email', 'email');
        $frm->addHiddenField('phone', 'phone');
        $frm->addHiddenField('surl', 'surl', FatUtility::generateFullUrl('PayuindiaPay', 'callback'));
        $frm->addHiddenField('Furl', 'Furl', FatUtility::generateFullUrl('order', 'paymentFail', array($orderId)));
        $frm->addHiddenField('curl', 'curl', FatUtility::generateFullUrl('order', 'paymentFail', array($orderId)));

        $frm->addHiddenField('hash', 'hash');
        $frm->addHiddenField('udf1', 'udf1');
        $frm->addHiddenField('Pg', 'Pg', 'CC');


        $frm->setJsErrorDisplay('afterfield');
        $btn = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('Continue'), array('class' => 'button button--fill button--red  button--small'));
        $btn->htmlBeforeField = '<div>' . Info::t_lang('Please_wait_Redirecting_to_Paypal_or_press') . '</div><br>';

        return $frm;
    }

    public function charge() {

        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != ApplicationConstants::USER_TRAVELER_TYPE) {
            FatUtility::dieJsonError(Message::addErrorMessage(Info::t_lang('Invalid_request')));
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

        $orderDetails = $this->getOrderInfo($orderId);

        $frm = $this->getPaymentForm($orderId);

        $payment_settings = $this->getPaymentGatewaySettings();

        $key = $payment_settings["merchant_id"];
        $salt = $payment_settings["salt"];


        $orderDetails = new OrderDetail($orderId);


        $firstname = $orderDetails->getOrderUserFirstName();
        $lastName = $orderDetails->getOrderUserLastName();
        $email = $orderDetails->getOrderUserEmail();
        $phone = $orderDetails->getOrderUserPhone();
        $payment_gateway_charge = $orderDetails->getOrderPayableAmount();
        $txnid = $orderDetails->getOrderInvoice();
        $order_payment_gateway_description = $orderDetails->getOrderProductInfo();


        $udf1 = $orderId;

        $Hash = hash('sha512', $key . '|' . $txnid . '|' . $payment_gateway_charge . '|' . $order_payment_gateway_description . '|' . $firstname . '|' . $email . '|' . $udf1 . '||||||||||' . $salt);


        $frmData = array(
            'key' => $key,
            'txnid' => $txnid,
            'amount' => $payment_gateway_charge,
            'productinfo' => $order_payment_gateway_description,
            'firstname' => $firstname,
            'lastname' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'hash' => $Hash,
            'udf1' => $txnid,
        );

        $frm->fill($frmData);

        return $frm;
    }

    public function callback() {

        $payment_settings = $this->getPaymentGatewaySettings();
        $post = FatApp::getPostedData();

        $request = "";
        foreach ($post as $key => $value) {
            $request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
        }
        $order_id = (isset($post['udf1'])) ? $post['udf1'] : 0;

        $orderPaymentObj = new OrderDetail($order_id);
        $payment_gateway_charge = $orderPaymentObj->getOrderPayableAmount();
        $order_info = $orderPaymentObj->orderDetails();
        if ($order_info) {

            switch ($post['status']) {
                case 'success':
                    $receiver_match = (strtolower($post['key']) == strtolower($payment_settings['merchant_id']));
                    $total_paid_match = ((float) $post['amount'] == (float) $payment_gateway_charge);
                    $hash_string = $payment_settings["salt"] . "|" . $post["status"] . "||||||||||" . $post["udf1"] . "|" . $post["email"] . "|" . $post["firstname"] . "|" . $post["productinfo"] . "|" . $post["amount"] . "|" . $post["txnid"] . "|" . $post["key"];
                    $reverse_hash = strtolower(hash('sha512', $hash_string));
                    $reverse_hash_match = ($post['hash'] == $reverse_hash);
                    if ($receiver_match && $total_paid_match && $reverse_hash_match) {
                        $order_payment_status = 1;
                    }
                    if (!$receiver_match) {
                        $request .= "\n\n PAYUINDIA_NOTE :: RECEIVER MERCHANT MISMATCH! " . strtolower($post['key']) . "\n\n";
                    }
                    if (!$total_paid_match) {
                        $request .= "\n\n PAYUINDIA_NOTE :: TOTAL PAID MISMATCH! " . strtolower($post['amount']) . "\n\n";
                    }
                    if (!$reverse_hash_match) {
                        $request .= "\n\n PAYUINDIA_NOTE :: REVERSE HASH MISMATCH! " . strtolower($post['hash']) . "\n\n";
                    }
                    break;
            }
            if ($order_payment_status == 1) {
                $this->updateResponse($post, $order_id, $order_info['user_id']);

                FatApp::redirectUser(FatUtility::generateFullUrl('order', 'success', array($order_id)));
            } else {
                FatApp::redirectUser(FatUtility::generateFullUrl('order', 'paymentFail', array($order_id)));
            }
        } else {
            FatApp::redirectUser(FatUtility::generateFullUrl('order', 'paymentFail', array($order_id)));
        }
    }

    private function updateResponse($orderResponse, $order_id, $userId) {

        $data['amount'] = floatval($orderResponse['amount']);
        $data['gateway_transaction_id'] = $orderResponse['mihpayid'];
        $data['response_data'] = FatUtility::convertToJson($orderResponse);
        $data['mode'] = $this->keyName;
        $data['transaction_completed'] = 1;

        Sms::orderSmsToHost($order_id);
        Sms::paymentSuccessSmsToTraveler($order_id, $userId);
        Transaction::addNew($order_id, $data);
    }

}
