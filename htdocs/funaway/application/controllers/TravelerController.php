<?php

class TravelerController extends UserController
{

    public function __construct($action)
    {
        parent::__construct($action);
        $this->set("action", $action);
        $this->set("class", "is--dashboard");
    }

    function fatActionCatchAll()
    {
        FatUtility::exitWithErrorCode(404);
    }

    public function index()
    {
        FatApp::redirectUser(FatUtility::generateUrl('notification'));
        $this->_template->render();
    }

    public function ePending()
    {
        $this->set('user_name', User::getLoggedUserAttribute("user_name"));
        $this->_template->render();
    }

    public function account()
    {
        $user = new User(UserAuthentication::getLoggedUserId());

        $this->set('data', $user->getProfileData());

        $this->_template->render();
    }

    public function profile()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add("Account");
        $brcmb->add("Profile");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->addJs(array('js/cropper.min.js', 'js/croper.js'));
        $this->_template->addCss(array('css/cropper.min.css', 'css/croper.css'));
        $this->_template->render();
    }

    function step()
    {
        $step = FatApp::getPostedData('tab', FatUtility::VAR_INT);

        switch ($step) {
            case 2:
                $this->set("userId", $this->userId);
                $image_data = AttachedFile::getAttachment(AttachedFile::FILETYPE_USER_PHOTO, $this->userId, $recordSubid = 0);

                $this->set('imageUploaded', !empty($image_data));
                $html = $this->_template->render(false, false, 'host/_partial/image-form.php', true, true);
                FatUtility::dieJsonSuccess($html);

                break;
            case 3:
                $this->passwordForm();
                $html = $this->_template->render(false, false, 'traveler/_partial/password-form.php', true, true);
                FatUtility::dieJsonSuccess($html);
                break;
            case 4:

                $this->set('emailFrm', $this->getEmailForm());
                $html = $this->_template->render(false, false, 'traveler/_partial/email-form.php', true, true);
                FatUtility::dieJsonSuccess($html);
                break;
            default:
                $this->profileForm();
                $html = $this->_template->render(false, false, 'traveler/_partial/profile-form.php', true, true);
                FatUtility::dieJsonSuccess($html);
                break;
        }
    }

    private function getEmailForm()
    {
        $frm = new Form('updateEmailFrm');
        $frm->addEmailField(Info::t_lang('EMAIL'), 'user_email');
        $frm->addSubmitButton(Info::t_lang('SUBMIT'), 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function updateEmail()
    {
        $post = FatApp::getPostedData();
        $frm = $this->getEmailForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValitionErrors()));
        }

        $changeRequest = new EmailChangeRequest();
        $changeRequest->deleteOldRequest($this->userId);
        $token_data = $changeRequest->getToken($this->userId);
        if (!empty($token_data)) {
            FatUtility::dieJsonError(Info::t_lang('YOUR_REQUEST_TO_CHANGE_EMAIL_HAS_ALREADY_BEEN_PLACED_WITHIN_LAST_24_HOURS. PLEASE_CHECK_YOUR_EMAIL_OR_RETRY_AFTER_24_HOURS_OF_YOUR_PREVIOUS_REQUEST'));
        }
        $usr = new User($this->userId);
        if ($usr->getUserByEmail($post['user_email'])) {
            FatUtility::dieJsonError(Info::t_lang('EMAIL_ALREADY_EXIST!'));
        }
        $token = $changeRequest->getValidToken();
        $expiry = $data = array(
            EmailChangeRequest::DB_TBL_PREFIX . 'user_id' => $this->userId,
            EmailChangeRequest::DB_TBL_PREFIX . 'email_id' => $post['user_email'],
            EmailChangeRequest::DB_TBL_PREFIX . 'verification_code' => $token,
            EmailChangeRequest::DB_TBL_PREFIX . 'expiry' => Info::currentDatetime(),
        );
        $changeRequest->assignValues($data);
        if (!$changeRequest->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }

        $usr->loadFromDb();
        $user_data = $usr->getFlds();
        $user_email = @$user_data[User::DB_TBL_PREFIX . 'email'];
        $user_firstname = @$user_data[User::DB_TBL_PREFIX . 'firstname'];
        $user_lastname = @$user_data[User::DB_TBL_PREFIX . 'lastname'];
        $user_name = $user_firstname . ' ' . $user_lastname;
        $reset_url = FatUtility::generateFullUrl('guest-user', 'changeEmailVerify', array($token, $this->userId));
        $vars = array(
            '{username}' => $user_name,
            '{reset_url}' => $reset_url,
            '{new_email}' => $post['user_email'],
        );
        Email::sendMail($user_email, 10, $vars);
        Email::sendMail($post['user_email'], 11, $vars);
        FatUtility::dieJsonSuccess(Info::t_lang('EMAIL_CHANGE_VERIFICATION_LINK_SENT'));
    }

    function requestDetail($reuqest_id)
    {
        $reuqest_id = FatUtility::int($reuqest_id);
        if ($reuqest_id <= 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'request'));
        }
        $ord = new Order();
        $ordCancel = new OrderCancel();
		$canPolicy = new CancellationPolicy();
		$cancellation_policies = $canPolicy->getPolicies();
        $row = $ord->getOrderActivityByRequestId($reuqest_id);
        $order_id = @$row['oactivity_order_id'];
        if (empty($order_id)) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'request'));
        }
        $order = $ord->getOrderDetail($order_id);
        if (empty($order) || $this->userId != $order['order_user_id']) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'order'));
        } else {
            $this->set('order', $order);
        }
        $activities = $ord->getOrderActivity($order_id);
        foreach ($activities as $k => $v) {
            $activities[$k]['addons'] = $ord->getOrderAddons($v['oactivity_id']);
            $activities[$k]['cancel_data'] = $ordCancel->getCancelBooking($v['oactivity_booking_id'], OrderCancel::DB_TBL_PREFIX . 'booking_id');
            $policy_day = @$cancellation_policies[$v['activity_cancelation']][CancellationPolicy::DB_TBL_PREFIX . 'days'];
            $activities[$k]['can_cancel'] = Order::canTravelerCancelBooking($policy_day, $v['oactivity_event_timing'], $order['order_payment_status']);
        }

        $this->set('order', $order);
        $this->set('activities', $activities);
        $this->_template->render(true, true, 'traveler/detail.php');
    }

    public function detail($order_id)
    {
        $ord = new Order();
        $canPolicy = new CancellationPolicy();
        $ordCancel = new OrderCancel();
        $cancellation_policies = $canPolicy->getPolicies();
        $order = $ord->getOrderDetail($order_id);

        if (empty($order) || $this->userId != $order['order_user_id']) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'order'));
        } else {
            $this->set('order', $order);
        }
        $activities = $ord->getOrderActivity($order_id);

        foreach ($activities as $k => $v) {
            $activities[$k]['addons'] = $ord->getOrderAddons($v['oactivity_id']);
            $activities[$k]['cancel_data'] = $ordCancel->getCancelBooking($v['oactivity_booking_id'], OrderCancel::DB_TBL_PREFIX . 'booking_id');
            $policy_day = @$cancellation_policies[$v['activity_cancelation']][CancellationPolicy::DB_TBL_PREFIX . 'days'];
            $activities[$k]['can_cancel'] = Order::canTravelerCancelBooking($policy_day, $v['oactivity_event_timing'], $order['order_payment_status']);
            $activities[$k]['can_review'] = Order::canTravelerReviewBooking($v['oactivity_event_timing'], $order['order_payment_status']);
        }

        $orderExtra = $ord->getOrderExtraCharges($order_id);
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('ORDERS'), FatUtility::generateUrl('traveler', 'order'));
        $brcmb->add(Info::t_lang('DETAILS') . ' - ' . $order_id);
        $this->set('breadcrumb', $brcmb->output());
        $this->set('order', $order);
        $this->set('activities', $activities);
        $this->set('orderExtra', $orderExtra);
        $this->_template->addJs('activity/page-js/social.js');
        $this->_template->render();
    }

    public function printInvoice($order_id)
    {
        $ord = new Order();
        $canPolicy = new CancellationPolicy();
        $ordCancel = new OrderCancel();
        $cancellation_policies = $canPolicy->getPolicies();
        $order = $ord->getOrderDetail($order_id);
        if (empty($order) || $this->userId != $order['order_user_id']) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'order'));
        } else {
            $this->set('order', $order);
        }
        $activities = $ord->getOrderActivity($order_id);

        $activityId = 0;
        foreach ($activities as $k => $v) {
            $activities[$k]['addons'] = $ord->getOrderAddons($v['oactivity_id']);
            $activities[$k]['cancel_data'] = $ordCancel->getCancelBooking($v['oactivity_booking_id'], OrderCancel::DB_TBL_PREFIX . 'booking_id');
            $policy_day = @$cancellation_policies[$v['activity_cancelation']][CancellationPolicy::DB_TBL_PREFIX . 'days'];
            $activities[$k]['can_cancel'] = Order::canTravelerCancelBooking($policy_day, $v['oactivity_event_timing'], $order['order_payment_status']);

            if ($activityId != $v['oactivity_activity_id']) {
                $activityId = $v['oactivity_activity_id'];
                $activityUserId = Activity::getAttributesById($v['oactivity_activity_id'], 'activity_user_id');
                $hostName = User::getAttributesById($activityUserId, array('user_firstname', 'user_lastname'));
        }

            $activities[$k]['host_name'] = ucfirst($hostName['user_firstname']) . ' ' . ucfirst($hostName['user_lastname']);
        }
        $orderExtra = $ord->getOrderExtraCharges($order_id);
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('ORDERS'), FatUtility::generateUrl('traveler', 'order'));
        $brcmb->add(Info::t_lang('DETAILS') . ' - ' . $order_id);


        $travelerDetail = User::getAttributesById($order['order_user_id'], array('user_firstname', 'user_lastname'));

        $order['traveler_name'] = ucfirst($travelerDetail['user_firstname']) . ' ' . ucfirst($travelerDetail['user_lastname']);

        $this->set('breadcrumb', $brcmb->output());
        $this->set('order', $order);
        $this->set('activities', $activities);
        $this->set('orderExtra', $orderExtra);
        $this->set('current_datetime', Info::currentDatetime());
        $html = $this->_template->render(false, false, 'traveler/invoice.php', true);

        Order::generatePdf($html, $order_id . '.pdf');
    }

    public function order()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('MY_BOOKINGS'));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    function orderListing($page = 1)
    {
        $page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $post = FatApp::getPostedData();
        #	$form =  $this->getSearchForm();
        #	$post = $form->getFormDataFromArray($post); 
        $odr = new Order();
        $search = $odr->getOrderSearch();
        $search->joinTable("tbl_users", "inner join", "user_id = order_user_id and user_id = " . $this->userId);
		
        $search->joinTable(OrderCancel::DB_TBL, "LEFT JOIN", "ordercancel_booking_id = oactivity_booking_id ");
        
		$search->addCondition('order_user_id', '=', $this->userId);

        $search->addGroupBy('order_id');
        $search->addOrder('order_date', 'desc');
        $search->addMultipleFields(array(ORDER::ORDER_TBL . '.*', 'oactivity_booking_id', 'ordercancel_id', 'ordercancel_status', "group_concat(oactivity_activity_name SEPARATOR ' [-] ') as ordered"));
        $search->setPageNumber($page);
        $search->setPageSize(static::PAGESIZE);
		// echo $search->getQuery();exit;
        //	$search->addOrder('order_date','desc');
        $rs = $search->getResultSet();
        $db = FatApp::getDb();
        $records = $db->fetchAll($rs);
		// echo '<pre>'. print_r($records, true);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "traveler/_partial/order-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function request()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('BOOKING_REQUESTS'));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    function requestListing($page = 1)
    {
        $page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $post = FatApp::getPostedData();
        #	$form =  $this->getSearchForm();
        #	$post = $form->getFormDataFromArray($post); 
        $er = new EventRequest();
        $search = $er->getEventRequestByMerchant($this->userId);
        //	$search->joinTable("tbl_users","inner join","user_id = requestevent_requested_by and user_id = ".$this->userId);
        $search->addOrder('requestevent_id', 'desc');
        $search->setPageNumber($page);
        $search->setPageSize(static::PAGESIZE);
        $rs = $search->getResultSet();
        //	echo $search->getError();
        $db = FatApp::getDb();
        $records = $db->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "traveler/_partial/request-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function requestInCart()
    {
        $post = FatApp::getPostedData();
        $requestId = $post['request_id'];
        $er = new EventRequest();
        $request = array();
        $request = $er->getEventRequestById($requestId);        
        if (empty($request) || $request['requestevent_requested_by'] != $this->userId || $request['requestevent_status'] != EventRequest::REQUEST_APPROVED_STATUS || $request['requestevent_is_order'] != 0) {
            FatUtility::dieJsonError("INVALID_REQUEST");
        }
        $record = json_decode($request['requestevent_content'], true);
        $crt = new Cart();
        $cart_array = array(
            'cart_id' => Info::timestamp(),
            "event" => $record['event_id'],
            "addons" => isset($record['addons']) ? $record['addons'] : array(),          
            'activity_id' => $record['activity_id'],
            'member_count' => $record['member_count'],
            'request_id' => $requestId,
            'request_approve_status' => $request['requestevent_status']
        );
        
        //Info::test($cart_array); die;        
        $crt->addToCart($cart_array);
        $count = $crt->getCartCount();
        $array = array("cart_count" => $count, "status" => 1, "msg" => Info::t_lang("ACTIVITY_HAVE_BEEN_ADDED_IN_CART."));
        die(FatUtility::convertToJson($array));
    }

    function cancelBooking($booking_id)
    {
        $booking_id = trim($booking_id);
        if ($booking_id == '') {
            FatUtility::dieWithError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        $ord = new Order();
        $act = new Activity();
        $ordCancel = new OrderCancel();
        $canPolicy = new CancellationPolicy();
        $cancellation_policies = $canPolicy->getPolicies();
        $srch = $ord->getOrderActivityDetail($booking_id);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row) || $row['order_user_id'] != $this->userId) {
            FatUtility::dieWithError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        if ($ordCancel->isExistCancelBooking($booking_id)) {
            FatUtility::dieWithError(Info::t_lang('CANCEL_REQUEST_ALREADY_SENT!'));
        }
        $act_data = $act->getActivity($row['oactivity_activity_id'], -1);
        $policy_day = @$cancellation_policies[$act_data['activity_cancelation']][CancellationPolicy::DB_TBL_PREFIX . 'days'];
        if (!Order::canTravelerCancelBooking($policy_day, $row['oactivity_event_timing'], $row['order_payment_status'])) {
            FatUtility::dieWithError(Info::t_lang('Cancellation time has been passed.'));
        }

        $frm = $this->getOrderCancelForm();
        $frm->fill(array('booking_id' => $booking_id));
        $this->set('frm', $frm);
        $this->set('booking_id', $booking_id);
        $this->_template->render(false, false, 'traveler/order-cancel.php');
    }

    function setupOrderCancel()
    {
        $post = FatApp::getPostedData();
        $frm = $this->getOrderCancelForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValitionErrors()));
        }
        $booking_id = @$post['booking_id'];
        $booking_id = trim($booking_id);

        $ord = new Order();
        $ordCancel = new OrderCancel();
        $act = new Activity();
        $canPolicy = new CancellationPolicy();
        $cancellation_policies = $canPolicy->getPolicies();
        $srch = $ord->getOrderActivityDetail($booking_id);
        $rs = $srch->getResultSet();
        $row = FatApp::getDb()->fetch($rs);
        if (empty($row) || $row['order_user_id'] != $this->userId) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if ($ordCancel->isExistCancelBooking($booking_id)) {
            FatUtility::dieJsonError(Info::t_lang('CANCEL_REQUEST_ALREADY_SENT!'));
        }
        $act_data = $act->getActivity($row['oactivity_activity_id'], -1);
        if (empty($act_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $policy_day = @$cancellation_policies[$act_data['activity_cancelation']][CancellationPolicy::DB_TBL_PREFIX . 'days'];
        if (!Order::canTravelerCancelBooking($policy_day, $row['oactivity_event_timing'], $row['order_payment_status'])) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }

        $post[OrderCancel::DB_TBL_PREFIX . 'user_id'] = $this->userId;
        $post[OrderCancel::DB_TBL_PREFIX . 'booking_id'] = $booking_id;


        if (!$ordCancel->addCancelBooking($post, $this->userId, $post['comment'])) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }
        $notify = new Notification();
        $url = FatUtility::generateUrl('admin', 'orderCancelRequests', array(), CONF_WEBROOT_URL);
        $notify_text = Info::t_lang('BOOKING_CANCELLATION_HAS_BEEN_POSTED_BY_TRAVELER.REGRADING - ') . $booking_id;
        $notify->notify(0, 0, $url, $notify_text);

        /* send to host */
        $usr = new User();
        $traveler_id = $row['order_user_id'];
        $traveler_data = $usr->getUserByUserId($traveler_id);
        $host_id = $act_data[Activity::DB_TBL_PREFIX . 'user_id'];
        $notify = new Notification();
        $url = FatUtility::generateUrl('host', 'bookingCancelRequests', array(), CONF_WEBROOT_URL);
        $notify_text = Info::t_lang('BOOKING_CANCELLATION_HAS_BEEN_POSTED_BY_TRAVELER.REGRADING - ') . $booking_id;
        $notify->notify($host_id, 0, $url, $notify_text);

        $host_data = $usr->getUserByUserId($host_id);
        $replace_var = array(
            '{host_name}' => ucwords($host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname']),
            '{traveler_name}' => ucwords($traveler_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $traveler_data[User::DB_TBL_PREFIX . 'lastname']),
            '{booking_id}' => $booking_id,
            '{booking_timing}' => FatDate::format($row['oactivity_event_timing'], true)
        );
        Email::sendMail($host_data[User::DB_TBL_PREFIX . 'email'], 27, $replace_var);
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 28, $replace_var);
        FatUtility::dieJsonSuccess(Info::t_lang('CANCEL_REQUEST_POSTED!'));
    }

    private function getOrderCancelForm()
    {
        $frm = new Form('orderCancelFrm');
        $frm->addHiddenField('', 'booking_id');
        $fld = $frm->addTextArea(Info::t_lang('REASON'), 'comment');
        $fld->requirements()->setRequired();
        $fld->htmlAfterField = "<small>" . Info::t_lang('ENTER_SMALL_DESCRIPTION_WHY_YOU_WANT_TO_CANCEL') . "</small>";
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function bookingCancelRequests()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('CANCELLATIONS'));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    function bookingCancelLists($page = 1)
    {
        $page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $srch = OrderCancel::getSearchObject();
        $srch->joinTable(Activity::DB_TBL, 'inner join', Activity::DB_TBL_PREFIX . 'id = oactivity_activity_id');
        $srch->joinTable(User::DB_TBL, 'inner join', 'user_id =  ' . OrderCancel::DB_TBL_PREFIX . 'user_id  ');
        $srch->addOrder(OrderCancel::DB_TBL_PREFIX . 'id', 'desc');
        $srch->addMultipleFields(array(OrderCancel::DB_TBL . '.*', 'oactivity_activity_name as ordered', 'oactivity_unit_price', 'oactivity_booking_id', 'ordercancel_id', 'ordercancel_status', 'oactivity_event_timing', User::DB_TBL_PREFIX . 'id', User::DB_TBL_PREFIX . 'type'));
        $srch->addCondition('order_user_id', '=', $this->userId);
        $srch->setPageNumber($page);
        $srch->setPageSize(static::PAGESIZE);
        $rs = $srch->getResultSet();
        //echo $srch->getError();
        $records = FatApp::getDb()->fetchAll($rs);

        $this->set("arr_listing", $records);
        $this->set('totalPage', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "traveler/_partial/booking-cancel-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    /*
     *  Payout Form
     */

    function payout()
    {

        $brcmb = new Breadcrumb();
        $brcmb->add("Account");
        $brcmb->add("Payout");
        $this->set('breadcrumb', $brcmb->output());

        $this->_template->render();
    }

    function payoutStep()
    {
        $step = FatApp::getPostedData('tab', FatUtility::VAR_INT);

        $this->payoutForm();
    }

    function salesReports()
    {
        $brcmb = new Breadcrumb();
        $brcmb->add("Account");
        $brcmb->add(Info::t_lang("SALES_REPORTS"));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    function getSalesReport()
    {
        $htm = Info::t_lang('Our smart reporting tools will be available end of June when platform opens for travelers. Stay tuned for more features!');
        FatUtility::dieJsonSuccess($htm);
    }

    private function payoutForm()
    {

        $frm = $this->getPayoutForm();
        $bnkact = new BankAccounts();
        $data = $bnkact->getBankAccount($this->userId);
        $frm->fill($data);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'traveler/_partial/bank-account-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getFee()
    {
        $commission_chart = CommissionChart::getCommissionChart();
        $block = new Block();
        $payout_terms = $block->getBlock(16);

        $this->set('payout_terms', $payout_terms);

        $this->set('commission_chart', $commission_chart);
        $htm = $this->_template->render(false, false, 'host/_partial/fee.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getPayoutForm()
    {
        $frm = new Form('payoutFrm');
        $frm->addRequiredField(Info::t_lang('BANK_NAME'), 'bankaccount_bank_name');
        $frm->addRequiredField(Info::t_lang('BRANCH'), 'bankaccount_branch');
        $frm->addRequiredField(Info::t_lang('ACCOUNT_NUMBER'), 'bankaccount_account_no');
        $frm->addRequiredField(Info::t_lang('ACCOUNT_NAME'), 'bankaccount_account_name');
        $fld = $frm->addTextArea(Info::t_lang('ACCOUNT_ADDRESS'), 'bankaccount_account_address');
        $fld->requirements()->setRequired();
        $frm->addRequiredField(Info::t_lang('IFSC_CODE'), 'bankaccount_ifsc_code');
        $frm->addSubmitButton(Info::t_lang('SAVE'), 'submit_btn', Info::t_lang('SAVE'));
        return $frm;
    }

    function setupBankAccount()
    {
        $post = FatApp::getPostedData();
        $frm = $this->getPayoutForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(cuurent($frm->getValitionErrors()));
        }
        $post[BankAccounts::DB_TBL_PREFIX . 'user_id'] = $this->userId;
        $bnkact = new BankAccounts();
        if (!$bnkact->saveBankAccount($post)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN.'));
        }
        FatUtility::dieJsonSuccess(Info::t_lang('BANK_DETAILS_SAVED!'));
    }

}
