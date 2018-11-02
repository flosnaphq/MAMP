<?php

/**
 * Sage pay server Integration
 */
class SagepayPayController extends PaymentController {

    protected $keyName = "Sagepay";

    private function getPaymentForm($order_id) {

        $actionUrl = FatUtility::generateFullUrl("sagepay-pay", "send");
        $frm = new Form('frmSagepay');
        $frm->setFormTagAttribute('id', 'frmSagepay');
        $frm->setFormTagAttribute('action', $actionUrl);
        $frm->addRequiredField(Info::t_lang('Lblcheckout_Address_1'), 'address_1');
        $frm->addTextBox(Info::t_lang('Lblcheckout_Address_2'), 'address_2');
        $frm->addRequiredField(Info::t_lang('Lblcheckout_City'), 'city');
        $frm->addRequiredField(Info::t_lang('Lblcheckout_Post_Code'), 'postcode');
        $frm->addRequiredField(Info::t_lang('Lblcheckout_Country'), 'country_id');
        $frm->addRequiredField(Info::t_lang('Lblcheckout_Region/State'), 'zone_id');
        $frm->addHiddenField('order id', 'order_id', $order_id);

        //$frm->addSelectBox(Info::t_lang('COUNTRY'), 'activity_country_id', Countries::getCountries(), '', array('onChange' => 'getCities(this.value)') );
        //$frm->addSelectBox(Info::t_lang('CITY'), 'activity_city_id', '', '', array('id' => 'cities'));

        $btn = $frm->addButton('', 'btn_submit', Info::t_lang('Continue'), array('class' => 'button button--fill button--red  button--small', 'id' => 'button-confirm'));
        $btn->htmlBeforeField = '<div id="msg" style="display:none">' . Info::t_lang('Please_wait_Redirecting_to_Sage_Pay') . '</div><br>';
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


        if (!$this->isEligibleToOrder() || !$orderId = $this->createOrder()) {
            $error = Info::t_lang('Unable_To_Create_Order');
            FatUtility::dieJsonError($error);
        }

        $frm = $this->getPaymentProcessForm($orderId);

        $this->set('frm', $frm);
        $ret['msg'] = Info::t_lang('Please_continue');
        $ret['frm'] = $this->_template->render(false, false, 'sagepay-pay/payment-form.php', true);
        FatUtility::dieJsonSuccess($ret);
    }

    private function getPaymentProcessForm($orderId) {

        $frm = $this->getPaymentForm($orderId);
        $orderDetails = new OrderDetail($orderId);

        $firstname = $orderDetails->getOrderUserFirstName();
        $lastName = $orderDetails->getOrderUserLastName();
        $email = $orderDetails->getOrderUserEmail();
        $payment_gateway_charge = $orderDetails->getOrderPayableAmount();

        //Info::test($orderDetails);die;

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
            Email::simpleMail('atul.sharma@ablysoft.com', 'PP verifyIPN Response', $response);
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

    //*************************NEW code *********************************/

    /**
     * 
     * @param type $url
     * @param type $payment_data
     * @param type $i
     * @return type
     */
    public function sendCurl($url, $payment_data, $i = null) {

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

        $response = curl_exec($curl);

        curl_close($curl);

        $response_info = explode(chr(10), $response);

        foreach ($response_info as $string) {
            if (strpos($string, '=') && isset($i)) {
                $parts = explode('=', $string, 2);
                $data['RepeatResponseData_' . $i][trim($parts[0])] = trim($parts[1]);
            } elseif (strpos($string, '=')) {
                $parts = explode('=', $string, 2);
                $data[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $data;
    }

    public function send() {

        $post = FatApp::getPostedData();
        $order_id = $post['order_id'];

        $odrObj = new Order();
        $orderDetails = new OrderDetail($order_id);
        $orderInfo = $odrObj->getOrderDetail($order_id);
        $orderActivityInfo = $odrObj->getOrderActivity($order_id);

        $order_payment_gateway_description = sprintf(Info::t_lang('M_Order_Payment_Gateway_Description'), FatApp::getConfig("conf_website_name"), $order_id);
        $order_info = array();
        if (!empty($orderInfo)) {
            $order_info = array_merge($orderInfo, $post);
        } else {
            $order_info = $post;
        }
        $orderDescription = '';
        foreach ($orderActivityInfo as $k => $orderActivity) {
            $orderDescription .= Info::t_lang('Activity_booked') . '(' . $orderActivity['oactivity_activity_name'] . ')-Booking-' . $orderActivity['oactivity_booking_id'] . '-Order-' . $orderActivity['oactivity_order_id'];
        }


        $pmObj = new PaymentSettings($this->keyName);
        $paymethodSettings = $pmObj->getPaymentSettings();
        $currency = Currency::getAttributesById(FatApp::getConfig('conf_default_currency'));

        if ($paymethodSettings['transaction_mode'] == 0) {
            $url = 'https://test.sagepay.com/gateway/service/vspserver-register.vsp';
        } else {
            $url = 'https://live.sagepay.com/gateway/service/vspserver-register.vsp';
        }


        $firstname = $orderDetails->getOrderUserFirstName();
        $lastName = $orderDetails->getOrderUserLastName();
        $email = $orderDetails->getOrderUserEmail();
        $payment_gateway_charge = $orderDetails->getOrderPayableAmount();



        //Info::test($order_info);die;

        $payment_data['ReferrerID'] = '511F950F-AE7F-4E33-93BF-5F607D119275';
        $payment_data['Vendor'] = $paymethodSettings['vendor_name'];
        $payment_data['VendorTxCode'] = $order_id . 'T' . strftime("%Y%m%d%H%M%S") . mt_rand(1, 999);
        $payment_data['Amount'] = $payment_gateway_charge;
        $payment_data['Currency'] = $currency['currency_code'];
        $payment_data['Description'] = substr($orderDescription, 0, 100);
        $payment_data['NotificationURL'] = 'https://testing.4demo.biz/sagepayserver/notify.php/';
        $payment_data['TxType'] = 'PAYMENT';


        $payment_data['RedirectURL'] = 'https://testing.4demo.biz/sagepayserver/notify.php/';
        $payment_data['siteFqdns'] = 'https://testing.4demo.biz/sagepayserver/notify.php/';
        $payment_data['BillingSurname'] = $firstname;
        $payment_data['BillingFirstnames'] = $lastName;
        $payment_data['BillingAddress1'] = $order_info['address_1'];
        $payment_data['BillingCity'] = $order_info['city'];
        $payment_data['BillingPostCode'] = $order_info['postcode'];
        $payment_data['BillingCountry'] = $order_info['country_id'];
        $payment_data['billingPhone'] = $order_info['order_user_phone'];

        $payment_data['DeliverySurname'] = $firstname;
        $payment_data['DeliveryFirstnames'] = $lastName;
        $payment_data['DeliveryAddress1'] = $order_info['address_1'];
        $payment_data['DeliveryCity'] = $order_info['city'];
        $payment_data['DeliveryPostCode'] = $order_info['postcode'];
        $payment_data['DeliveryCountry'] = $order_info['country_id'];
        $payment_data['Profile'] = 'LOW';

        $payment_data['CustomerEMail'] = substr($email, 0, 255);
        $payment_data['Apply3DSecure'] = '0';
        $payment_data['ClientIPAddress'] = $_SERVER['REMOTE_ADDR'];
        if (isset($_POST['CreateToken'])) {
            $payment_data['CreateToken'] = $_POST['CreateToken'];
            $payment_data['StoreToken'] = 1;
        }
        if (isset($_POST['Token'])) {
            $payment_data['Token'] = $_POST['Token'];
            $payment_data['StoreToken'] = 1;
        }

        $response_data = $this->sendCurl($url, $payment_data);

        $json = array();
        if ((substr($response_data['Status'], 0, 2) == "OK") || $response_data['Status'] == 'AUTHENTICATED' || $response_data['Status'] == 'REGISTERED') {
            $json['redirect'] = $response_data['NextURL'];
            $json['Status'] = $response_data['Status'];
            $json['StatusDetail'] = $response_data['StatusDetail'];
            $response_data['order_id'] = $this->session->data['order_id'];
            $response_data['VendorTxCode'] = $payment_data['VendorTxCode'];
            $order_info = array_merge($order_info, $response_data);
            //$this->model_payment_sagepay_server_v3->addOrder($order_info); // TODO Save into database
        } else {
            $json['error'] = $response_data['StatusDetail'];
        }
        echo json_encode($json);exit;
        FatUtility::dieJsonSuccess($json);
        
    }

}
