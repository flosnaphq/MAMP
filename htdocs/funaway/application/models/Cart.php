<?php

class Cart extends FatModel {

    const SESSION_ELEMENT_NAME = 'CartSession';

    public function __construct() {
        
    }

    public function addToCart($event) {
        $_SESSION[Cart::SESSION_ELEMENT_NAME][] = $event;
    }

    function clearCart() {
        if (!empty($_SESSION[Cart::SESSION_ELEMENT_NAME])) {
            unset($_SESSION[Cart::SESSION_ELEMENT_NAME]);
        }
    }

    public function getCart() {
        $cart = array();
        if (isset($_SESSION[Cart::SESSION_ELEMENT_NAME]) && !empty($_SESSION[Cart::SESSION_ELEMENT_NAME])) {
            $cart = $_SESSION[Cart::SESSION_ELEMENT_NAME];
        }
        return $cart;
    }

    function getCartDetail() {

        $carts = $this->getCart();
        $act = new Activity();
        $actRel = new AttributeRelations();
        $usercarts = array();
        $i = 0;
        $s_total = 0;
        $net_amount = 0;
        $pay_amount = 0;
        $total_donation = 0;
        $attributes = array();
        foreach ($carts as $key => $ct) {
            $price = 0;
            $total_price = 0;
            $donation = 0;
            
			$activityRow = $act->getEventWithActivity($ct['activity_id'], $ct['event']);
			
            if (false === $activityRow) {
                unset($_SESSION[Cart::SESSION_ELEMENT_NAME][$key]);
                continue;
            }
            // Info::test($carts);
            $msg = '';
            if (!$act->checkEventBookingAvailability($ct['activity_id'], $ct['event'], $msg)) {
                if ((!array_key_exists('request_id', $ct)) || (!array_key_exists('request_approve_status', $ct)) || ($ct['request_approve_status'] != EventRequest::REQUEST_APPROVED_STATUS)) {
                    unset($_SESSION[Cart::SESSION_ELEMENT_NAME][$key]);
                    continue;
                }
            }
            
            $usercarts[$i]['events'] = $activityRow;
            $usercarts[$i]['member'] = $ct['member_count'];
            $usercarts[$i]['cart_id'] = $ct['cart_id'];
            $usercarts[$i]['request_id'] = @$ct['request_id'];
            $price = $usercarts[$i]['events']['activity_price'] * $ct['member_count'];
            if (!empty($ct['addons'])) {
                $j = 0;
                foreach ($ct['addons'] as $k => $v) {
                    $usercarts[$i]['addons'][$j] = $act->getAddonsByActivityAndId($ct['activity_id'], $k);
                    $usercarts[$i]['addons'][$j]['size'] = $v;
                    $price = $price + $v * $usercarts[$i]['addons'][$j]['activityaddon_price'];
                    $j++;
                }
            }
            $act_rel_data = $actRel->getActvityRelations($ct['activity_id']);

            if (!empty($act_rel_data)) {
                foreach ($act_rel_data as $rel_data) {
                    $attr_id = $rel_data[AttributeRelations::DB_TBL_PREFIX . 'aattribute_id'];
                    $activity_id = $rel_data[AttributeRelations::DB_TBL_PREFIX . 'activity_id'];
                    $attach_file = AttachedFile::getAttachment(AttachedFile::FILETYPE_ACTIVITY_ATTRIBUTE, $attr_id, $activity_id);
                    $file_name = @$attach_file[AttachedFile::DB_TBL_PREFIX . 'name'];
                    $attributes[$attr_id]['activities'][$activity_id] = array(
                        'name' => $usercarts[$i]['events']['activity_name'],
                        'activity_id' => $activity_id,
                        'file_name' => $file_name
                    );
                    $attributes[$attr_id]['details'] = array(
                        'attribute_id' => $attr_id,
                        'caption' => $rel_data[ActivityAttributes::DB_TBL_PREFIX . 'caption'],
                        'file_required' => $rel_data[ActivityAttributes::DB_TBL_PREFIX . 'file_required'],
                    );
                }
            }

            $usercarts[$i]['price'] = $price;
            $total_price = $price;
            $usercarts[$i]['trans_fee'] = 0;
            $usercarts[$i]['received_amount'] = 0;
            $usercarts[$i]['vat'] = 0;
            $usercarts[$i]['total_amount'] = $total_price;
            $s_total = $s_total + $price;
            $i++;
        }

        $array['total'] = $s_total;
        $array['donation'] = 0;
        $array['usercarts'] = $usercarts;
        $array['sub_total'] = $s_total;
        $array['rcv_amount'] = $s_total;
        $array['pay_amount'] = $s_total;
        $array['trans_fee'] = 0;
        $array['vat'] = 0;
        $array['net_amount'] = $s_total;
        $array['attributes'] = $attributes;
        /* Info::test($array);
          exit; */
        return $array;
    }

    public function getCartCount() {
        if (isset($_SESSION[Cart::SESSION_ELEMENT_NAME])) {
            return count($_SESSION[Cart::SESSION_ELEMENT_NAME]);
        }
        return 0;
    }

    public function updateMember($cart_id, $activity_id, $event_id, $member) {
        if (isset($_SESSION[Cart::SESSION_ELEMENT_NAME]) && !empty($_SESSION[Cart::SESSION_ELEMENT_NAME])) {
            foreach ($_SESSION[Cart::SESSION_ELEMENT_NAME] as $k => $v) {

                if ($v['activity_id'] == $activity_id && $v['event'] == $event_id && $v['cart_id'] == $cart_id) {

                    $_SESSION[Cart::SESSION_ELEMENT_NAME][$k]['member_count'] = $member;
                }
            }
        }
        return true;
    }

    public function removeFromCart($cart_id, $activity_id, $event_id) {
        if (isset($_SESSION[Cart::SESSION_ELEMENT_NAME]) && !empty($_SESSION[Cart::SESSION_ELEMENT_NAME])) {
            foreach ($_SESSION[Cart::SESSION_ELEMENT_NAME] as $k => $v) {

                if ($v['activity_id'] == $activity_id && $v['event'] == $event_id && $v['cart_id'] == $cart_id) {

                    unset($_SESSION[Cart::SESSION_ELEMENT_NAME][$k]);
                }
            }
        }
        return true;
    }


    function priceAfterTax($amount) {
        $trans_fee = $amount * FatApp::getConfig('OMISE_TRANSACTION_FEE') / 100;
        $vat = $trans_fee * FatApp::getConfig('OMISE_VAT') / 100;
        $rcv_amount = round($amount - $trans_fee - $vat, 2, PHP_ROUND_HALF_DOWN);
        return $rcv_amount;
    }

   

}
