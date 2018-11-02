<?php

class OmisePayController extends PaymentController {

    private $keyName = "omise";

    public function omise() {
        require_once CONF_INSTALLATION_PATH . 'library/omise/vendor/autoload.php';
        $this->_template->render();
    }

    public function charge() {
        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != 0) {
            $_SESSION['login_as'] = 'traveler';
            Message::addErrorMessage(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
            FatApp::redirectUser(FatUtility::generateUrl('guest-user', 'social'));
        }
        $pmObj = new PaymentSettings($this->keyName);
        if (!$paymethodSettings = $pmObj->getPaymentSettings()) {
            $this->set('error', Info::t_lang('Invalid_payment_method'));
        } else {
            // Info::test($paymethodSettings);exit;
            if ($paymethodSettings['pmethod_active'] != 1) {
                $this->set('error', Info::t_lang('Invalid_payment_method'));
            } else {
                $ct = new Cart();
                $detail = $ct->getCartDetail();
                $attributes = $detail['attributes'];

                if (!isset($detail['total']) || $detail["total"] <= 0) {
                    $this->set('error', Info::t_lang('Unable_To_Create_Order'));
                } else {
                    $frm = $this->getPaymentForm();
                    $this->set('frm', $frm);
                }
            }
        }

        echo $this->_template->render(false, false, null, true, false);
    }

    private function getPaymentForm() {
        $frm = new Form('frmPaymentForm');
        $frm->setValidatorJsObjectName('system_validator');

        $frm->addRequiredField(Info::t_lang('ENTER_CREDIT_CARD_NUMBER'), 'cc_number', '', array('autocomplete' => 'off'));
        $frm->addRequiredField(Info::t_lang('CARD_HOLDER_NAME'), 'cc_owner', '', array('autocomplete' => 'off'));
        $data['months'] = array();
        for ($i = 1; $i <= 12; $i++) {
            $data['months'][sprintf('%02d', $i)] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
        }
        $today = getdate();
        $data['year_expire'] = array();
        for ($i = $today['year']; $i < $today['year'] + 22; $i++) {
            $data['year_expire'][strftime('%Y', mktime(0, 0, 0, 1, 1, $i))] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
        }
        $fldMon = $frm->addSelectBox(Info::t_lang('EXPIRY_DATE'), 'cc_expire_date_month', $data['months'], '0', array(), 'MM');
        $fldMon->html_after_field = ' ';
        $fldYear = $frm->addSelectBox('', 'cc_expire_date_year', $data['year_expire'], '0', array(), 'YEAR');

        $fld = $frm->addPasswordField(Info::t_lang('CVV_SECURITY_CODE'), 'cc_cvv');

        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('PAY_NOW'));

        return $frm;
    }

    public function process() {
        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != 0) {
            Message::addErrorMessage(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
            FatApp::redirectUser(FatUtility::generateUrl('guest-user', 'login-form'));
        }

        $pmObj = new PaymentSettings($this->keyName);
        if (!$paymethodSettings = $pmObj->getPaymentSettings()) {
            FatUtility::dieJsonError(Message::addErrorMessage(Info::t_lang('Invalid_payment_method')));
        } else {
            if ($paymethodSettings['pmethod_active'] != 1) {
                FatUtility::dieJsonError(Message::addErrorMessage(Info::t_lang('Invalid_payment_method')));
            } else {
                $post = FatApp::getPostedData();
                $ct = new Cart();
                $carts = $ct->getCartDetail();
                $ord = new Order();
                $usr = new User();
                $user = $usr->getUserByUserId(User::getLoggedUserId());
                $order_id = $ord->getOrderId();
                $order = array();
                $order['order_id'] = $order_id;
                $order['order_user_id'] = User::getLoggedUserId();
                $order['order_user_email'] = $user['user_email'];
                $order['order_user_phone'] = $user['user_phone_code'] . $user['user_phone'];
                $order['order_type'] = 1;
                $order['order_date'] = Info::currentDatetime();
                $order['order_payment_method'] = 0;
                $order['order_payment_status'] = 0;
                $order['order_net_amount'] = $carts['net_amount'];
                $order['order_received_amount'] = $carts['rcv_amount'];
                $order['order_total_amount'] = $carts['total'];

                $ord->addOrder($order);
                $booking_id = $ord->getBookingId($order_id);

                $booking_prefix = FatApp::getConfig('CONF_BOOKING_PREFIX');
                foreach ($carts['usercarts'] as $cart) {
                    $event = array();
                    $booking_id = $ord->getValidBookingId($booking_id);

                    $event['oactivity_order_id'] = $order_id;
                    $event['oactivity_booking_id'] = $booking_prefix . $booking_id;
                    $event['oactivity_activity_id'] = $cart['events']['activity_id'];
                    $event['oactivity_event_id'] = $cart['events']['activityevent_id'];
                    $event['oactivity_members'] = $cart['member'];
                    $event['oactivity_activity_name'] = $cart['events']['activity_name'];
                    $event['oactivity_event_timing'] = $cart['events']['activityevent_time'];
					$event['oactivity_activityevent_anytime'] = $cart['events']['activityevent_anytime'];
					$event['oactivity_event_confirmation_requrired'] = $cart['events']['activityevent_confirmation_requrired'];
                    $event['oactivity_unit_price'] = $cart['events']['activity_price'];
                    $event['oactivity_request_id'] = FatUtility::int($cart['request_id']);
                    $event['oactivity_trans_fee'] = $cart['trans_fee'];
                    $event['oactivity_vat'] = $cart['vat'];
                    $event['oactivity_donation'] = $cart['donation'];
                    $event['oactivity_total_amount'] = $cart['total_amount'];
                    $event['oactivity_received_amount'] = $cart['received_amount'];
                    $totalEventPrice = $cart['member'] * $cart['events']['activity_price'];
                    $event_id = $ord->addOrderEvent($event);

                    if (isset($cart['addons']) && !empty($cart['addons'])) {
                        foreach ($cart['addons'] as $addon) {
                            $addn = array();
                            $addn['oactivityadd_oactivity_id'] = $event_id;

                            $addn['oactivityadd_addon_id'] = $addon['activityaddon_id'];
                            $addn['oactivityadd_addon_name'] = $addon['activityaddon_text'];
                            $addn['oactivityadd_quantity'] = $addon['size'];
                            $addn['oactivityadd_unit_price'] = $addon['activityaddon_price'];
                            $totalEventPrice = $totalEventPrice + $addon['activityaddon_price'] * $addon['size'];
                            $ord->addOrderAddon($addn);
                        }
                    }
                    $event = array(
								'oactivity_booking_amount' => $totalEventPrice
							);
                    $event['oactivity_id'] = $event_id;

                    $ord->updateOrderEvent($event);
                    $booking_id++;
                }
                $charge = array();
                $charge['ordercharge_type'] = 1;
                $charge['ordercharge_order_id'] = $order_id;
                $charge['ordercharge_desc'] = Info::orderExtraChangeTypeByKey(1);
                $charge['ordercharge_amount'] = $carts['donation'];
                $ord->addOrderCharge($charge);
                $charge = array();
                $charge['ordercharge_type'] = 2;
                $charge['ordercharge_order_id'] = $order_id;
                $charge['ordercharge_desc'] = Info::orderExtraChangeTypeByKey(2);
                $charge['ordercharge_amount'] = $carts['trans_fee'];
                $ord->addOrderCharge($charge);
                $charge['ordercharge_type'] = 3;
                $charge['ordercharge_order_id'] = $order_id;
                $charge['ordercharge_desc'] = Info::orderExtraChangeTypeByKey(3);
                $charge['ordercharge_amount'] = $carts['vat'];
                $ord->addOrderCharge($charge);

                $json = array();
                /*                 * *********live details********** */
                /* make sure live keys has been updated from admin section */
                // $livemode = true;
                // define('OMISE_PUBLIC_KEY', FatApp::getConfig('CONF_OMISE_PUBLIC_KEY'));
                // define('OMISE_SECRET_KEY', FatApp::getConfig('CONF_OMISE_SECRET_KEY'));
                /*                 * *********live details********** */

                /*                 * *********test details********** */


                $livemode = false;

                if ($paymethodSettings['omise_transaction_mode'] == 1) {
                    $livemode = true;
                }

                define('OMISE_PUBLIC_KEY', $paymethodSettings['omise_public_key']);
                define('OMISE_SECRET_KEY', $paymethodSettings['omise_secret_key']);

                /*                 * *********test details********** */

                require_once CONF_INSTALLATION_PATH . 'library/omise/vendor/autoload.php';
                try {
                    $token = OmiseToken::create(array(
                                'card' => array('name' => html_entity_decode($post['cc_owner'], ENT_QUOTES, 'UTF-8'),
                                    'number' => str_replace(' ', '', $post['cc_number']),
                                    'expiration_month' => $post['cc_expire_date_month'],
                                    'expiration_year' => $post['cc_expire_date_year'],
                                    'security_code' => $post['cc_cvv'],
                                    'livemode' => $livemode
                    )));
                    $token_ref = $token->offsetGet('id');
                    $customer = OmiseCustomer::create(array(
                                'email' => User::getLoggedUserAttribute('user_email'),
                                'description' => User::getLoggedUserAttribute('user_firstname') . ' (id: ' . User::getLoggedUserAttribute('user_id') . ')',
                                'card' => $token_ref,
                                'livemode' => $livemode
                    ));

                    $currency = Currency::getAttributesById(FatApp::getConfig('conf_default_currency'));

                    $sendData = array(
                        'amount' => $this->formatGatewayAmount($order['order_total_amount']),
                        'currency' => 'thb', //$order_info["order_currency_code"],
                        'description' => 'Order-' . $order_id,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'customer' => $customer->offsetGet('id'),
                        'livemode' => $livemode
                    );
                    // Info::test($sendData);exit;
                    $response = OmiseCharge::create($sendData);

                    if (!$response) {
                        throw new Exception(Info::t_lang('EMPTY_GATEWAY_RESPONSE'));
                    }

                    if (strtolower($response->offsetGet('status')) != 'successful' || strtolower($response->offsetGet('paid')) != true) {

                        throw new Excetpion($response->offsetGet('failure_message'));
                    }

                    $trans = OmiseTransaction::retrieve($response->offsetGet('transaction'));

                    ////////////////////// Tax deduction \\\\\\\\\\\\\\\\\\\\\\\
                    $total = $carts['total'];
                    /* $trans_fee  = ($sub_total /(1-((( FatApp::getConfig('OMISE_TRANSACTION_FEE') )/100) + ( ( FatApp::getConfig('OMISE_TRANSACTION_FEE') )/100)* FatApp::getConfig('OMISE_VAT') / 100 ))) - $sub_total; */
                    ////////////////////////////////////////////////////////////

                    $amountPaid = $this->formatGatewayAmount($trans->offsetGet('amount'), true);
                    // echo $amountPaid . ' < '. $carts['rcv_amount'];
                    if ($amountPaid < $carts['rcv_amount']) {
                        FatUtility::dieJsonError(Info::t_lang('INVALID_TRANSACTION_AMOUNT'));
                        throw new Exception(Info::t_lang('INVALID_TRANSACTION_AMOUNT'));
                    } else {
                        $this->updateResponse($trans, $order);
                        $json['status'] = 1;
                        $json['msg'] = Info::t_lang("ORDER_HAS_BEEN_PLACED_SUCCESFULLY");
                        $json['redirect'] = FatUtility::generateUrl('order', 'success', array($order_id));
                    }
                } catch (exception $e) {
                    $json['status'] = 0;
                    $json['msg'] = 'ERROR: ' . $e->getMessage();
                }
                die(FatUtility::convertToJson($json));
            }
        }
    }

    private function formatGatewayAmount($amt, $reverse = false) {
        if ($amt <= 0) {
            return false;
        }

        if ($reverse === true) {
            return ($amt / 100);
        } else {
            return ($amt * 100);
        }

        return false;
    }

    private function updateResponse($trans, $order) {

        $order_id = $order['order_id'];

        $amountPaid = $this->formatGatewayAmount($trans->offsetGet('amount'), true);

        $data['amount'] = $amountPaid;
        $data['gateway_transaction_id'] = $trans->offsetGet('id');
        $data['response_data'] = json_encode($trans);
        $data['mode'] = "omise";
        $data['transaction_completed'] = 1;


        Sms::orderSmsToHost($order['order_id']);
        Sms::paymentSuccessSmsToTraveler($order['order_id'], User::getLoggedUserId());
        Transaction::addNew($order_id, $data);

        return true;
    }

}

?>