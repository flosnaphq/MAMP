<?php

class PaymentController extends MyAppController {

    protected function getPaymentGatewaySettings() {
        $pmObj = new PaymentSettings($this->keyName);
        $paymethodSettings = $pmObj->getPaymentSettings();
        return $paymethodSettings;
    }

    protected function getOrderInfo($orderId) {
        $odrObj = new Order();
        $orderInfo = $odrObj->getOrderDetail($orderId);
        return $orderInfo;
    }

    protected function createOrder() {

        $ct = new Cart();
        $detail = $ct->getCartDetail();

        if (!isset($detail['total']) && $detail["total"] < 1) {
            return true;
        }
        $paymethodSettings = $this->getPaymentGatewaySettings();
        $order_id = 0;

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
        $order['order_payment_method'] = $paymethodSettings['pmethod_id'];
        $order['order_payment_status'] = 0;
        $order['order_net_amount'] = $detail['net_amount'];
        $order['order_received_amount'] = $detail['total'];
        $order['order_total_amount'] = $detail['total'];
        $order['order_currency_id'] = Currency::getDefaultId();

        $ord->addOrder($order);
        $booking_id = $ord->getBookingId($order_id);
        $booking_prefix = FatApp::getConfig('CONF_BOOKING_PREFIX');

        foreach ($detail['usercarts'] as $cart) {
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
            $event['oactivity_trans_fee'] = 0;
            $event['oactivity_vat'] = 0;
            $event['oactivity_donation'] = 0;
            $event['oactivity_total_amount'] = $cart['total_amount'];
            $event['oactivity_received_amount'] = $cart['total_amount'];
            $totalEventPrice = $cart['member'] * $cart['events']['activity_price'];
            $event_id = $ord->addOrderEvent($event);
			
			if(false === $event_id) {
				Message::addErrorMessage($ord->getError());
				return false;
			}
			
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
            /* $event['oactivity_booking_amount'] = $totalEventPrice;
            $event['oactivity_id'] = $event_id; */
			
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
        $charge['ordercharge_amount'] = $detail['donation'];
        $ord->addOrderCharge($charge);
        $charge = array();
        $charge['ordercharge_type'] = 2;
        $charge['ordercharge_order_id'] = $order_id;
        $charge['ordercharge_desc'] = Info::orderExtraChangeTypeByKey(2);
        $charge['ordercharge_amount'] = $detail['trans_fee'];
        $ord->addOrderCharge($charge);
        $charge['ordercharge_type'] = 3;
        $charge['ordercharge_order_id'] = $order_id;
        $charge['ordercharge_desc'] = Info::orderExtraChangeTypeByKey(3);
        $charge['ordercharge_amount'] = $detail['vat'];
        $ord->addOrderCharge($charge);

        return $order_id;
    }

    protected function isEligibleToOrder() {
        $ct = new Cart();
        $detail = $ct->getCartDetail();
        if (isset($detail['total']) && $detail["total"] > 0) {
            return true;
        }
        return false;
    }

}
