<?php

class PaypalstandardPayController extends PaymentController {

    protected $keyName = "paypalstandard";

    private function getPaymentForm($order_id) {

        $pmObj = new PaymentSettings($this->keyName);

        $paymethodSettings = $pmObj->getPaymentSettings();

        if ($paymethodSettings["transaction_mode"] == 1) {
            $actionUrl = 'https://www.paypal.com/cgi-bin/webscr';
        } elseif ($paymethodSettings["transaction_mode"] == 0) {
            $actionUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

        // $currency = Currency::getAttributesById(FatApp::getConfig('conf_default_currency'));
		
		$currency = Currency::getAttributesById(Currency::getDefaultId());

        $frm = new Form('frmPayPalStandard');
        $frm->setFormTagAttribute('id', 'frmPayPalStandard');
        $frm->setFormTagAttribute('action', $actionUrl);
        $frm->addHiddenField('&nbsp;', 'cmd', '_xclick');
        $frm->addHiddenField('', 'upload', "1");
        $frm->addHiddenField('&nbsp;', 'business', $paymethodSettings['merchant_email']);
        $frm->addHiddenField('&nbsp;', 'charset', 'utf-8');

        $order_payment_gateway_description = sprintf(Info::t_lang('M_Order_Payment_Gateway_Description'), FatApp::getConfig("conf_website_name"), $order_id);

        $frm->addHiddenField('', 'item_name', $order_payment_gateway_description);

        $frm->addHiddenField('', 'item_number', $order_id);
        $frm->addHiddenField('', 'amount');
        $frm->addHiddenField('', 'quantity', 1);
        $frm->addHiddenField('', 'first_name');
        $frm->addHiddenField('', 'currency_code', $currency['currency_code']);
        // $frm->addHiddenField('', 'currency_code', 'USD');

        $frm->addHiddenField('', 'address_override', 0);
        $frm->addHiddenField('', 'email');

        $frm->addHiddenField('&nbsp;', 'custom', $order_id . '|' . User::getLoggedUserId());
        $frm->addHiddenField('', 'rm', "2"); // return method

        $frm->addHiddenField('', 'no_note', 1);
        $frm->addHiddenField('', 'no_shipping', 1);

        $frm->addHiddenField('', 'return', FatUtility::generateFullUrl('order', 'success', array($order_id)));
        $frm->addHiddenField('', 'notify_url', FatUtility::generateFullUrl('PaypalstandardPay', 'callback', array($order_id)));
        $frm->addHiddenField('', 'cancel_return', FatUtility::generateFullUrl('order', 'paymentFail', array($order_id)));

        $frm->addHiddenField('', 'paymentaction', 'sale');  // authorization or sale

        $btn = $frm->addSubmitButton('', 'btn_submit', Info::t_lang('Continue'), array('class' => 'button button--fill button--red  button--small'));
        $btn->htmlBeforeField = '<div>' . Info::t_lang('Please_wait_Redirecting_to_Paypal_or_press') . '</div><br>';
        $frm->setJsErrorDisplay('x');
        return $frm;
    }

    public function charge() {
        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != ApplicationConstants::USER_TRAVELER_TYPE) {
            FatUtility::dieJsonError(Message::addErrorMessage(Info::t_lang('Invalid_request')));
        }
        $pmObj = new PaymentSettings($this->keyName);
        if (!$paymethodSettings = $pmObj->getPaymentSettings()) {
            $this->set('error', Info::t_lang('Invalid_payment_method'));
        } else {
            if ($paymethodSettings['pmethod_active'] != 1) {
                $this->set('error', Info::t_lang('Invalid_payment_method'));
            } else {
                $ct = new Cart();
                $detail = $ct->getCartDetail();
                if (!isset($detail['total']) || $detail["total"] <= 0) {
                    $this->set('error', Info::t_lang('Unable_To_Create_Order'));
                }
            }

            echo $this->_template->render(false, false, null, true);
        }
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

		$orderId = $this->createOrder();
		
        if ((!$this->isEligibleToOrder()) || (false == $orderId)) {
            $error = Info::t_lang('Unable_To_Create_Order');
            FatUtility::dieJsonError($error);
        }

        $frm = $this->getPaymentProcessForm($orderId);
        $this->set('frm', $frm);
        $ret['msg'] = Info::t_lang('Please_continue');
        $ret['frm'] = $this->_template->render(false, false, 'paypalstandard-pay/payment-form.php', true);
        FatUtility::dieJsonSuccess($ret);
    }

    private function getPaymentProcessForm($orderId) {

  

        $frm = $this->getPaymentForm($orderId);
        $orderDetails = new OrderDetail($orderId);

        $firstname = $orderDetails->getOrderUserFirstName();
        $lastName = $orderDetails->getOrderUserLastName();
        $email = $orderDetails->getOrderUserEmail();
        $payment_gateway_charge = $orderDetails->getOrderPayableAmount();
 

        $frmData = array();

        $frmData['first_name'] = $firstname . ' ' . $lastName;
        $frmData['email'] = $email;
        $frmData['amount'] = $payment_gateway_charge;


        $frm->fill($frmData);

        return $frm;
    }

    public function callback($order_id = 0) {
        $pmObj = new PaymentSettings($this->keyName);
        $paymethodSettings = $pmObj->getPaymentSettings();

        $post = FatApp::getPostedData();

        $ppret = $post;

      

        $odrObj = new Order();
        $orderInfo = $odrObj->getOrderDetail($order_id);

        $payment_gateway_charge = $orderInfo['order_received_amount'];

        if ($payment_gateway_charge > 0) {

            $response = $this->verifyIPN($post, $paymethodSettings["transaction_mode"]);
            
            if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($post['payment_status'])) {
                $order_payment_status = 0;
                switch ($post['payment_status']) {
                    case 'Pending':
                        $order_payment_status = $order_payment_status;
                        break;
                    case 'Processed':
                        $order_payment_status = $order_payment_status;
                        break;
                    case 'Completed':
                        $order_payment_status = 1;
                        break;
                    default:
                        $order_payment_status = $order_payment_status;
                        break;
                }

                $receiver_match = (strtolower($post['receiver_email']) == strtolower($paymethodSettings['merchant_email']));
                $total_paid_match = ((float) $post['mc_gross'] == $payment_gateway_charge);

                if ($order_payment_status == 1 && $receiver_match && $total_paid_match) {
                    $this->updateResponse($post, $order_id);
                } else {
                    $re = '<order_payment_status>' . $order_payment_status . '<receiver_match>' . $receiver_match . '<total_paid_match>' . $total_paid_match;
                    FatApp::redirectUser(FatUtility::generateFullUrl('order', 'paymentFail', array($order_id)));
                }
            } else {
                FatApp::redirectUser(FatUtility::generateFullUrl('order', 'paymentFail', array($order_id)));
            }
        }
    }

    private function verifyIPN($post, $transactionMode = 0) {
        $request = 'cmd=_notify-validate';

        foreach ($post as $key => $value) {
            $request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
        }

        $curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');

        if ($transactionMode == 1) {
            $curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
        }

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $retData = curl_exec($curl);

        curl_close($curl);

        return $retData;
    }

    // public function updateResponse($orderResponse, $order_id)
    private function updateResponse($orderResponse, $order_id) {
        $data['amount'] = floatval($orderResponse['mc_gross']);
        $data['gateway_transaction_id'] = $orderResponse['txn_id'];
        $data['response_data'] = FatUtility::convertToJson($orderResponse);
        $data['mode'] = "paypal";
        $data['transaction_completed'] = 1;

        $custom = $orderResponse['custom'];
        $custom = explode('|', $custom);

        Sms::orderSmsToHost($order_id);
        Sms::paymentSuccessSmsToTraveler($order_id, $custom[1]);
        Transaction::addNew($order_id, $data);
    }

}
