<?php

class CartController extends MyAppController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function price()
    {
        $post = FatApp::getPostedData();
        $date = date('Y-m-d', strtotime($post['year'] . '-' . $post['month'] . '-' . $post['date']));
        $event_id = $post['event_id'];
        $activity_id = $post['activity_id'];
        $member_count = $post['member_count'];
        $addons = isset($post['addons']) ? $post['addons'] : array();
        $act = new Activity();
        $activity = $act->getEventWithActivity($activity_id, $event_id);
        // print_r($activity);exit;
        $enrolledMember = $act->getAnrolledMember($activity_id, $event_id);

        if (!$activity) {
            FatUtility::dieJsonError("Invalid Activity or Event Selected.");
        }

        if ($enrolledMember + $member_count > $activity['activity_members_count']) {
            if ($enrolledMember + $member_count >= $activity['activity_members_count']) {
                FatUtility::dieJsonError("Can't Add More Member.");
            } else {
                FatUtility::dieJsonError("You Can Enrolled only " . ($activity['activity_members_count'] - $enrolledMember) . " member(s)");
            }
        }
        $adds = array();
        $price = 0;
        if (!empty($addons)) {
            foreach ($addons as $k => $v) {
                if (!($ads = $act->getAddonsByActivityAndId($activity_id, $k))) {
                    FatUtility::dieJsonError("Wrong Add-on Selection");
                }
                $price = $price + ($v * $ads['activityaddon_price']);
            }
        }

        $price = $price + $member_count * $activity['activity_price'];
        $this->set('price', $price);
        if ($activity['activityevent_confirmation_requrired'] == 1) {
            $htm = $this->_template->render(false, false, "cart/_partial/confirm.php", true);
        } else {
            $htm = $this->_template->render(false, false, "cart/_partial/price.php", true);
        }
        FatUtility::dieJsonSuccess($htm);
    }

    public function index()
    {
        $ct = new Cart();
        $cart_count = $ct->getCartCount();
        $detail = $ct->getCartDetail();
        $this->_template->render();
    }

    public function listing()
    {
        $ct = new Cart();
        $cart_count = $ct->getCartCount();
        $detail = $ct->getCartDetail();
        // Info::test($detail);exit;
        $this->set('carts', $detail['usercarts']);
        $this->set('sub_total', $detail['sub_total']);
        $this->set('total', $detail['total']);

        $htm = $this->_template->render(false, false, 'cart/_partial/listing.php', true);
        die(FatUtility::convertToJson(array('status' => 1, 'htm' => $htm, 'cart_count' => $cart_count)));
    }

    public function updateMember()
    {
        /* if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type")!=0) {
          if (FatUtility::isAjaxCall()) {
          FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
          }
          FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
          } */
        $post = FatApp::getPostedData();
        $activity_id = $post['activity_id'];
        $event_id = $post['event_id'];
        $member = $post['member'];
        $cart_id = $post['cart_id'];
        $act = new Activity();
        $activity = $act->getEventWithActivity($activity_id, $event_id);
        $enrolledMember = $act->getAnrolledMember($activity_id, $event_id);

        if ($enrolledMember + $member > $activity['activity_members_count']) {
            if ($enrolledMember + $member >= $activity['activity_members_count']) {
                FatUtility::dieJsonError(Info::t_lang("CAN'T_ADD_MORE_") . Info::activityTypeLabelByKey($activity['activity_price_type']));
            } else {
                FatUtility::dieJsonError(Info::t_lang("YOU_CAN_ENROLLED_ONLY") . ($activity['activity_members_count'] - $enrolledMember) . Info::activityTypeLabelByKey($activity['activity_price_type']));
            }
        }
        $ct = new Cart();
        $ct->updateMember($cart_id, $activity_id, $event_id, $member);
        $this->listing();
    }

    public function removeActivity()
    {
        /* if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type")!=0) {
          if (FatUtility::isAjaxCall()) {
          FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
          }
          FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
          } */
        $post = FatApp::getPostedData();
        $activity_id = $post['activity_id'];
        $event_id = $post['event_id'];
        $cart_id = $post['cart_id'];
        $ct = new Cart();
        $ct->removeFromCart($cart_id, $activity_id, $event_id);
        $this->listing();
    }

    public function inCart()
    {
        /* if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type")!=0) {
          if (FatUtility::isAjaxCall()) {
          FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
          }
          FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
          } */
        $post = FatApp::getPostedData();
        $date = date('Y-m-d', strtotime($post['year'] . '-' . $post['month'] . '-' . $post['date']));
        $event_id = $post['event_id'];
        $activity_id = $post['activity_id'];
        $member_count = $post['member_count'];
        $addons = isset($post['addons']) ? $post['addons'] : array();
        $actObj = new Activity();
        // $activity = $actObj->getEventWithActivity($activity_id, $event_id);

        if (!$activity = $actObj->getEventWithActivity($activity_id, $event_id)) {
            FatUtility::dieJsonError(Info::t_lang("INVALID_ACTIVITY_OR_EVENT_SELECTED."));
        }
        $msg = '';
        if (!$actObj->checkEventBookingAvailability($activity_id, $event_id, $msg)) {
            FatUtility::dieJsonError($msg);
        }

        $enrolledMember = $actObj->getAnrolledMember($activity_id, $event_id);


        $booking_status = Activity::isActivityOpen($activity);
        if ($booking_status == 0) {
            FatUtility::dieJsonError(Info::t_lang("ACTIVITY_CLOSED."));
        }
        if ($booking_status == 2) {
            FatUtility::dieJsonError(Info::t_lang("ACTIVITY_NOT_STARTED_YET."));
        }
        if ($enrolledMember + $member_count > $activity['activity_members_count']) {
            if ($enrolledMember + $member_count >= $activity['activity_members_count']) {
                FatUtility::dieJsonError(Info::t_lang("CAN'T_ADD_MORE_MEMBER."));
            } else {
                FatUtility::dieJsonError(Info::t_lang("YOU_CAN_ENROLLED_ONLY") . ($activity['activity_members_count'] - $enrolledMember) . Info::t_lang("_MEMBER(S)"));
            }
        }
        $adds = array();
        $price = 0;
        if (!empty($addons)) {
            foreach ($addons as $k => $v) {
                if (!($ads = $actObj->getAddonsByActivityAndId($activity_id, $k))) {
                    FatUtility::dieJsonError(Info::t_lang('WRONG_ADD-ON_SELECTION'));
                }
                $price = $price + ($v * $ads['activityaddon_price']);
            }
        }

        $price = $price + $member_count * $activity['activity_price'];
        $crt = new Cart();
        $cart_array = array('cart_id' => Info::timestamp(), "event" => $event_id, "addons" => $addons, 'activity_id' => $activity_id, 'member_count' => $member_count);
        //Info::test($cart_array);        die('stoped');        
        $crt->addToCart($cart_array);
        $count = $crt->getCartCount();
        $this->set('price', 0);
        $htm = $this->_template->render(false, false, "cart/_partial/price.php", true);
        $array = array("price" => $htm, "cart_count" => $count, "status" => 1, "msg" => Info::t_lang("GREAT_PICK!_ACTIVITY_HAS_BEEN_ADDED_TO_CART")); //Great pick! Activity has been added to cart.
        die(FatUtility::convertToJson($array));
    }

    public function requestForApproval()
    {
        if (!User::isUserLogged() || User::getLoggedUserAttribute("user_type") != 0) {
            $_SESSION['login_as'] = 'traveler';
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
            }
            FatUtility::dieJsonError(Info::t_lang('PLEASE_LOGIN_AS_TRAVELER'));
        }
		
        $post = FatApp::getPostedData();
        $request = array();
		
        $date = date('Y-m-d', strtotime($post['year'] . '-' . $post['month'] . '-' . $post['date']));

        $event_id = $post['event_id'];
        $activity_id = $post['activity_id'];
        $member_count = $post['member_count'];
        $post['user_id'] = User::getLoggedUserId();
        $addons = isset($post['addons']) ? $post['addons'] : array();
        $act = new Activity();
        $activity = $act->getEventWithActivity($activity_id, $event_id);
        $enrolledMember = $act->getAnrolledMember($activity_id, $event_id);
        if (!$activity) {
            FatUtility::dieJsonError("Invalid Activity or Event Selected.");
        }
        $booking_status = Activity::isActivityOpen($activity);
        if ($booking_status == 0) {
            FatUtility::dieJsonError(Info::t_lang("ACTIVITY_CLOSED."));
        }
        if ($booking_status == 2) {
            FatUtility::dieJsonError(Info::t_lang("ACTIVITY_NOT_STARTED_YET."));
        }
        $request['requestevent_event_id'] = $post['event_id'];
        $request['requestevent_activity_id'] = $post['activity_id'];
        $request['requestevent_requested_by'] = User::getLoggedUserId();
        $request['requestevent_members'] = $post['member_count'];
        $request['requestevent_content'] = json_encode($post);
        $request['requestevent_status'] = 0;
        $request['requestevent_date'] = Info::currentDatetime();
        $er = new EventRequest();
        $er->addEventRequest($request);
        FatUtility::dieJsonSuccess(Info::t_lang("THANK_YOU_GIVE_24_HOURS_FOR_HOST"));
    }

}
?>	

