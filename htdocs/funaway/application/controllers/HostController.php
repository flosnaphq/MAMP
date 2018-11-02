<?php

class HostController extends UserController {

    public function __construct($action) {
		parent::__construct($action);

        $this->set('action', $action);
        $this->set("class", "is--dashboard");
    }

    public function index() {
        FatApp::redirectUser(FatUtility::generateUrl('notification'));
        $this->_template->render();
    }

    public function ePending() {
        $this->set('user_name', User::getLoggedUserAttribute("user_name"));
        $this->_template->render();
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    public function profile() {
        $block = new Block();
        $introduce_yourself = $block->getBlock(17);
        $brcmb = new Breadcrumb();
        $brcmb->add("Account");
        $brcmb->add("Profile");
        $this->set('breadcrumb', $brcmb->output());
        $this->set('introduce_yourself', $introduce_yourself);
        $this->_template->addJs(array('js/cropper.min.js', 'js/croper.js'));
        $this->_template->addCss(array('css/cropper.min.css', 'css/croper.css'));
        $this->_template->render();
    }

    function step() {
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
                $html = $this->_template->render(false, false, 'host/_partial/password-form.php', true, true);
                FatUtility::dieJsonSuccess($html);
                break;
            case 4:

                $this->set('emailFrm', $this->getEmailForm());
                $html = $this->_template->render(false, false, 'host/_partial/email-form.php', true, true);
                FatUtility::dieJsonSuccess($html);
                break;
            default:
                $this->profileForm();
                $html = $this->_template->render(false, false, 'host/_partial/profile-form.php', true, true);
                FatUtility::dieJsonSuccess($html);
                break;
        }
    }

    private function getEmailForm() {
        $frm = new Form('updateEmailFrm');
        $frm->addEmailField(Info::t_lang('EMAIL'), 'user_email');
        $frm->addSubmitButton(Info::t_lang('SUBMIT'), 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function updateEmail() {
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

    function payout() {

        $brcmb = new Breadcrumb();
        $brcmb->add("Account");
        $brcmb->add("Payout");
        $this->set('breadcrumb', $brcmb->output());

        $this->_template->render();
    }

    function payoutStep() {
        $step = FatApp::getPostedData('tab', FatUtility::VAR_INT);

        switch ($step) {
            case 2:
                $this->getFee();
                break;
            case 3:
                $this->getSalesReport();
                break;

            default:
                $this->payoutForm();
                break;
        }
    }

    function salesReports() {
        $brcmb = new Breadcrumb();
        $brcmb->add("Account");
        $brcmb->add(Info::t_lang("SALES_REPORTS"));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    function getSalesReport() {
        $htm = Info::t_lang('Our smart reporting tools will be available end of June when platform opens for travelers. Stay tuned for more features!');
        FatUtility::dieJsonSuccess($htm);
    }

    private function payoutForm() {

        $frm = $this->getPayoutForm();
        $bnkact = new BankAccounts();
        $data = $bnkact->getBankAccount($this->userId);
        $frm->fill($data);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'host/_partial/bank-account-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getFee() {
        $commission_chart = CommissionChart::getCommissionChart();
        $block = new Block();
        $payout_terms = $block->getBlock(16);

        $this->set('payout_terms', $payout_terms);

        $this->set('commission_chart', $commission_chart);
        $htm = $this->_template->render(false, false, 'host/_partial/fee.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getPayoutForm() {
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

    function setupBankAccount() {
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

    public function order() {
        FatApp::redirectUser(FatUtility::generateUrl('host', 'bookings'));
    }

    public function bookings() {
        /* $bal = Wallet::getBlockedAmount($this->userId);
          Info::test($bal);exit;
         */
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('MY_BOOKINGS'));
        $this->set('breadcrumb', $brcmb->output());
        $rpt = new Reports();
        $report = $rpt->getHostTotalBooking($this->userId);

        $frm = $this->getSearchForm();
        $this->set('frm', $frm);
        $this->set('report', $report);
        $this->_template->render();
    }

    function orderListing($page = 1) {
		$page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $post = FatApp::getPostedData();
        $form = $this->getSearchForm();
        $post = $form->getFormDataFromArray($post);
        $odr = new Order();
        $search = $odr->getOrderByActivity();
        $search->joinTable('tbl_activities', "inner join", "activity_user_id = " . $this->userId . " and oactivity_activity_id = activity_id");
        $search->joinTable('tbl_users', "INNER JOIN", "order_user_id = user_id");
        $search->joinTable(OrderCancel::DB_TBL, 'left join', OrderCancel::DB_TBL_PREFIX . 'booking_id = oactivity_booking_id');
        //	$search->addCondition('activity_user_id','=',$this->userId);
        if (!empty($post['booking_id'])) {
            $key_con = $search->addCondition('oactivity_booking_id', '=', $post['booking_id']);
        }
        if (!empty($post['start_date'])) {
            $start_date = $post['start_date'];
            $start_date = $start_date . ' 00:00:00';

            $key_con = $search->addDirectCondition('order_date >= "' . $start_date . '"');
        }
        if (!empty($post['end_date'])) {
            $end_date = $post['end_date'];
            $end_date = $end_date . ' 23:59:59';
            $key_con = $search->addDirectCondition('order_date <=  "' . $end_date . '"');
        }

        if (!empty($post['activity_id'])) {
            $key_con = $search->addCondition('oactivity_activity_id', '=', $post['activity_id']);
        }
        if (!empty($post['booking_type'])) {
            $booking_type = FatUtility::int($post['booking_type']);
            if ($booking_type == 1) {
                $key_con = $search->addDirectCondition('oactivity_event_timing < "' . Info::currentDatetime() . '"');
            } elseif ($booking_type == 2) {
                $key_con = $search->addDirectCondition('oactivity_event_timing > "' . Info::currentDatetime() . '"');
            } elseif ($booking_type == 3) {
                $key_con = $search->addCondition('oactivity_is_cancel', '=', 1);
            }
        }

        if (isset($post['payment_status']) && $post['payment_status'] != '' && $post['payment_status'] != -1) {
            $order_payment_status = FatUtility::int($post['payment_status']);

            $search->addCondition('order_payment_status', '=', $order_payment_status);
        }

        $search->addOrder('order_date', 'desc');
        $search->addMultipleFields(array(ORDER::ORDER_TBL . '.*', 'oactivity_activity_name as ordered', 'oactivity_unit_price', 'oactivity_booking_id', 'ordercancel_id', 'ordercancel_status', 'oactivity_event_timing', 'oactivity_booking_amount', 'user_firstname', 'user_email', 'user_lastname', 'user_phone_code', 'user_phone'));
        $search->setPageNumber($page);
        $search->setPageSize(static::PAGESIZE);
        //	$search->addGroupBy('oactivity_booking_id');
        $rs = $search->getResultSet();

        $db = FatApp::getDb();
        $records = $db->fetchAll($rs);
		// print_r($records); exit;
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "host/_partial/order-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getSearchForm() {
        $frm = new Form('orderSearchFrm');

        $fld = $frm->addDateField(Info::t_lang('START_DATE'), 'start_date', '', array('readonly' => 'readonly'));

        $fld = $frm->addDateField(Info::t_lang('END_DATE'), 'end_date', '', array('readonly' => 'readonly'));

        $frm->addTextBox(Info::t_lang('BOOKING_ID'), 'booking_id');
        $payment_status = Info::paymentStatus();
        $payment_status[-1] = Info::t_lang('PAYMENT_STATUS');
        $frm->addSelectBox(Info::t_lang('PAYMENT_STATUS'), 'payment_status', $payment_status, -1, array(), '');



        $act = new Activity();
        $activites = $act->getActivitiesForForm($this->userId);
        $activites[0] = Info::t_lang('ACTIVITY');
        $frm->addSelectBox(Info::t_lang('ACTIVITY'), 'activity_id', $activites, 0, array(), '');
        $booking_types = array(
            0 => Info::t_lang('BOOKING_TYPE'),
            1 => Info::t_lang('COMPLETED'),
            2 => Info::t_lang('UPCOMINGS'),
            3 => Info::t_lang('CANCELLED'),
        );
        $frm->addSelectBox(Info::t_lang('BOOKING_TYPE'), 'booking_type', $booking_types, 0, array(), '');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SEARCH'));
        return $frm;
    }

    public function detail($booking_id) {

        $ord = new Order();
        $usr = new User();
        $act = new Activity();
        if (!$activities = $ord->getOrderActivityByBookingId($booking_id)) {
            FatUtility::exitWithErrorCode(404);
        }
        $order = $ord->getOrderDetail($activities['oactivity_order_id']);
        $user_data = $usr->getUserByUserId($order['order_user_id']);
        if (!$actDetail = $act->getActivity($activities['oactivity_activity_id'])) {
            FatApp::redirectUser(FatUtility::generateUrl('home', 'error404'));
        }

        // Info::test($actDetail);exit;

        if ($actDetail['activity_user_id'] != $this->userId) {
            FatUtility::exitWithErrorCode(404);
        }

        $activities['activity_price_type'] = $actDetail['activity_price_type'];

        $activities['addons'] = $ord->getOrderAddons($activities['oactivity_id']);
        #	Info::test($activities);
        $this->set('activities', $activities);
        $this->set('user_data', $user_data);
        $this->_template->render();
    }

    public function bookingDetail($booking_id) {
        $cms = new Cms();
        $block = new Block();
        $ord = new Order();
        $usr = new User();
        $act = new Activity();
        $activities = $ord->getOrderActivityByBookingId($booking_id);
        if (empty($activities)) {
            FatApp::redirectUser(FatUtility::generateUrl('home', 'error404'));
        }
        $order = $ord->getOrderDetail($activities['oactivity_order_id']);
        $user_data = $usr->getUserByUserId($order['order_user_id']);
        $actDetail = $act->getActivity($activities['oactivity_activity_id']);
        if (empty($actDetail) || $actDetail['activity_user_id'] != $this->userId) {
            FatUtility::exitWithErrorCode(404);
        }

        $activities['activity_price_type'] = $actDetail['activity_price_type'];
        $activities['addons'] = $ord->getOrderAddons($activities['oactivity_id']);

        $this->set('activities', $activities);

        $this->set('give_back', $cms->getCms(11));
        $this->set('transaction_info', $block->getBlock(21));

        $this->set('user_data', $user_data);
        $this->_template->render();
    }

    function printInvoice($booking_id) {
        $ord = new Order();
        $usr = new User();
        $act = new Activity();
        $cms = new Cms();
        $activities = $ord->getOrderActivityByBookingId($booking_id);
        if (empty($activities)) {
            FatUtility::exitWithErrorCode(404);
        }
        $order = $ord->getOrderDetail($activities['oactivity_order_id']);
        $user_data = $usr->getUserByUserId($order['order_user_id']);
        $actDetail = $act->getActivity($activities['oactivity_activity_id']);
        if (empty($actDetail) || $actDetail['activity_user_id'] != $this->userId) {
            FatUtility::exitWithErrorCode(404);
        }

        $activities['activity_price_type'] = $actDetail['activity_price_type'];
        $activities['addons'] = $ord->getOrderAddons($activities['oactivity_id']);

        $this->set('activities', $activities);
        $this->set('give_back', $cms->getCms(11));

        $this->set('user_data', $user_data);
        $this->set('current_datetime', Info::currentDatetime());
        $html = $this->_template->render(false, false, 'host/invoice.php', true);
        //echo $html; exit;
        Order::generatePdf($html, $booking_id . '.pdf');
    }

    public function history() {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('MY_WALLET'));
        $this->set('breadcrumb', $brcmb->output());
        $frm = $this->getHistorySearchForm();
        $wallet = Wallet::getWalletBalanceByUser($this->userId);
        $this->set('frm', $frm);
        $this->set('wallet', $wallet);
        $this->_template->render();
    }

    public function historyListing($page = 1) {
        $page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $post = FatApp::getPostedData();
        $frm = $this->getHistorySearchForm();
        $post = $frm->getFormDataFromArray($post);

        $search = Wallet::getWalletList($this->userId);
        if (!empty($post['start_date']) && FatDate::validateDateString($post['start_date'])) {
            $search->addDirectCondition('date(wtran_date) >= "' . $post['start_date'] . '"');
        }
        if (!empty($post['end_date']) && FatDate::validateDateString($post['end_date'])) {
            $search->addDirectCondition('date(wtran_date) <= "' . $post['end_date'] . '"');
        }
        $search->addCondition('wtran_user_type', '=', 1);
        $search->addOrder('wtran_date', 'desc');
        $search->setPageNumber($page);
        $search->setPageSize(static::PAGESIZE);
        $search->addOrder('wtran_id', 'desc');
        $rs = $search->getResultSet();

        $db = FatApp::getDb();
        $records = $db->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "host/_partial/history-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getHistorySearchForm() {
        $frm = new Form('historySearchFrm');
        $frm->addDateField(Info::t_lang('START_DATE'), 'start_date');
        $frm->addDateField(Info::t_lang('END_DATE'), 'end_date');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SEARCH'));
        return $frm;
    }

    public function request() {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('CONFIRMATION_REQUESTS'));
        $this->set('breadcrumb', $brcmb->output());

        $this->_template->render();
    }

    function requestListing($page = 1) {
        $page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $post = FatApp::getPostedData();
        #	$form =  $this->getSearchForm();
        #	$post = $form->getFormDataFromArray($post); 
        $er = new EventRequest();
        $search = $er->getEventRequestByActivity($this->userId);
        //	$search->joinTable("tbl_users","inner join","user_id = requestevent_requested_by and user_id = ".$this->userId);
        $search->addOrder('requestevent_date', 'desc');
        $search->addMultipleFields(array("requestevent_id", "requestevent_members as member", "requestevent_status", "requestevent_date", "requestevent_is_order", "activity_name",  "activityevent_time","activityevent_anytime", "concat(traveler.user_firstname,' ',traveler.user_lastname) as traveler_name", "activity_id"));
        $search->setPageNumber($page);
        $search->setPageSize(static::PAGESIZE);
        $search->addOrder('requestevent_id', 'desc');
        $rs = $search->getResultSet();
        $db = FatApp::getDb();
        $records = $db->fetchAll($rs);
		
		//echo '<pre>';
		//print_r($records);die;
		
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "host/_partial/request-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function updateRequest() {
        $post = FatApp::getPostedData();
        $data['requestevent_id'] = $post['request_id'];
        $data['requestevent_status'] = $post['request_status'];

        $er = new EventRequest();
        if (!$er->validRequest($post['request_id'], $this->userId)) {
            FatUtility::dieJsonSuccess(Info::t_lang('INVALID_REQUEST'));
        }
        $er->updateEventRequest($data);
        if ($data['requestevent_status'] > 0) {
            Sms::requestUpdateSmsToTraveler($post['request_id']);
        }
        FatUtility::dieJsonSuccess(Info::t_lang('REQUEST_HAVE_BEEN_UPDATED'));
    }

    function withdrawalRequests() {
        $block = new Block();
        $withdrawal_info = $block->getBlock(20);
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('REQUEST_WITHDRAWAL'));

        $this->set('breadcrumb', $brcmb->output());
        $this->set('withdrawal_info', $withdrawal_info);

        $this->_template->render();
    }

    function withdrawalRequestLists($page = 1) {
        $pageSize = static::PAGESIZE;
        $page = FatUtility::int($page);
        $page = $page < 1 ? 1 : $page;
        $srch = WithdrawalRequests::getSearchObject();
        $srch->addOrder(WithdrawalRequests::DB_TBL_PREFIX . 'id', 'desc');
        $srch->addCondition(WithdrawalRequests::DB_TBL_PREFIX . 'user_id', '=', $this->userId);
        $srch->setPageNumber($page);
        $srch->setPageSize($pageSize);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "host/_partial/withdrawal-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function addWithdrawalRequest() {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('REQUEST_WITHDRAWAL'), FatUtility::generateUrl('host', 'withdrawalRequests'));
        $brcmb->add(Info::t_lang('ADD_REQUEST_WITHDRAWAL'));
        $this->set('breadcrumb', $brcmb->output());

        $frm = $this->getWithdrawalRequestForm();
        $this->set('frm', $frm);
        
        $wallet = Wallet::getWalletBalanceByUser($this->userId);
        $this->set('walletAmount', $wallet);
        $this->_template->render();
    }

    function setupWithdrawalRequest() {
        $frm = $this->getWithdrawalRequestForm();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($this->getValitionErrors()));
        }
        $withdrawalReq = new WithdrawalRequests();
        $wallet = Wallet::getWalletBalanceByUser($this->userId);
        $post[WithdrawalRequests::DB_TBL_PREFIX . 'user_id'] = $this->userId;
        $post[WithdrawalRequests::DB_TBL_PREFIX . 'datetime'] = Info::currentDatetime();
        $amount = $post['withdrawalrequest_amount'];
        if ($amount <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_AMOUNT!'));
        }
        if ($amount > $wallet['wallet_balance']) {
            FatUtility::dieJsonError(Info::t_lang('YOU_CAN_NOT_REQUEST_MORE_THAN_YOUR_AVAILABLE_BALANCE!'));
        }

        $userWallet = new Userwallet($this->userId, 1);
        if (!$userWallet->addWithdrawalRequest($amount, Info::t_lang("WITHDRAWAL_REQUEST"))) {
            FatUtility::dieJsonError($userWallet->getError());
        }

        $withdrawalReq->assignValues($post);
        if (!$withdrawalReq->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }


        $replace_vars = array(
            '{amount}' => $post[WithdrawalRequests::DB_TBL_PREFIX . 'amount'],
            '{comment}' => nl2br($post[WithdrawalRequests::DB_TBL_PREFIX . 'comment']),
            '{datetime}' => FatDate::format($post[WithdrawalRequests::DB_TBL_PREFIX . 'datetime']),
            '{hostname}' => User::getLoggedUserAttribute("user_name"),
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 23, $replace_vars);
        $url = FatUtility::generateUrl('admin', 'withdrawal-requests', array());
        $text = Info::t_lang('NEW_WITHDRAWAL_REQUEST_HAS_BEEN_POSTED_ON_') . FatDate::format(Info::currentDatetime());
        $notify = new Notification;
        $notify->notify(0, 0, $url, $text);
        FatUtility::dieJsonSuccess(Info::t_lang('REQUEST_SUBMITTED_SUCCESSFULLY!'));
    }

    private function getWithdrawalRequestForm() {
        $defaultSystemCurrency = Currency::getSystemCurrency();
        
        $frm = new Form('withdrawalRequestFrm');
        $fld = $frm->addRequiredField(Info::t_lang('AMOUNT'), 'withdrawalrequest_amount','',array("placeholder"=>Info::t_lang('WITHDRAWAL_REQUEST_AMOUNT_PLACEHOLDER')));
        $fld->htmlBeforeField = '<span id="country_code" class="field_add-on add-on--left">'.$defaultSystemCurrency['currency_symbol'].'</span>';
        $frm->addTextArea(Info::t_lang('COMMENT'), 'withdrawalrequest_comment');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SEND_REQUEST'));
        return $frm;
    }

    function withdrawalRequestDetails($request_id) {
        $request_id = FatUtility::Int($request_id);
        $withdrawalReq = new WithdrawalRequests($request_id);
        $withdrawalReq->loadFromDb();
        $withdrawalRequest = $withdrawalReq->getFlds();
        if (empty($withdrawalRequest) || $withdrawalRequest[WithdrawalRequests::DB_TBL_PREFIX . 'user_id'] != $this->userId) {
            Message::addErrorMessage(Info::t_lang('INVALID_REQUEST!'));
            FatApp::redirectUser(FatUtility::generateUrl('host', 'withdrawalRequests'));
        }
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang('REQUEST_WITHDRAWAL'), FatUtility::generateUrl('host', 'withdrawalRequests'));
        $brcmb->add(Info::t_lang('REQUEST_WITHDRAWAL_DETAILS'));
        $this->set('breadcrumb', $brcmb->output());
        $this->set('data', $withdrawalRequest);
        $this->_template->render();
    }

    function orderCancel($booking_id) {

        $booking_id = trim($booking_id);
        if ($booking_id == '') {
            FatApp::redirectUser(FatUtility::generateUrl('home', 'error404'));
        }
        $ord = new Order();
        $ordCancel = new OrderCancel();
        $act = new Activity();
        $order_act = $ord->getOrderActivityByBookingId($booking_id);
        if (empty($order_act)) {
            FatUtility::exitWithErrorCode(404);
        }
        $act_data = $act->getActivity($order_act['oactivity_activity_id']);
        if (empty($act_data)) {
            FatUtility::exitWithErrorCode(404);
        }
        if ($act_data[Activity::DB_TBL_PREFIX . 'user_id'] != $this->userId) {
            FatUtility::exitWithErrorCode(404);
        }
        if ($ordCancel->isExistCancelBooking($booking_id)) {
            FatUtility::exitWithErrorCode(404);
        }
        $frm = $this->getOrderCancelForm();
        $frm->getField('comment')->htmlAfterField = "<small>".Info::t_lang('ENTER_SMALL_DESCRIPTION_WHY_YOU_WANT_TO_CANCEL')."</small>";
        $frm->fill(array('booking_id' => $booking_id));
        $this->set('frm', $frm);
        $this->set('booking_id', $booking_id);
        $this->_template->render(false, false, 'host/order-cancel.php');
    }

    function setupOrderCancel() {
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
        $order_act = $ord->getOrderActivityByBookingId($booking_id);
        if (empty($order_act)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act_data = $act->getActivity($order_act['oactivity_activity_id']);
        if (empty($act_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if ($act_data[Activity::DB_TBL_PREFIX . 'user_id'] != $this->userId) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if ($ordCancel->isExistCancelBooking($booking_id)) {
            FatUtility::dieJsonError(Info::t_lang('CANCEL_REQUEST_ALREADY_SENT!'));
        }
        $post[OrderCancel::DB_TBL_PREFIX . 'user_id'] = $this->userId;
        $post[OrderCancel::DB_TBL_PREFIX . 'booking_id'] = $booking_id;
        $post[OrderCancel::DB_TBL_PREFIX . 'host_approved'] = OrderCancel::HOST_APPROVED_TYPE_APPROVED;

        if (!$ordCancel->addCancelBooking($post, $this->userId, $post['comment'])) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }
        /* Send Notification to admin */
        $notify = new Notification();
        $url = FatUtility::generateUrl('admin', 'orderCancelRequests', array(), CONF_WEBROOT_URL);
        $notify_text = Info::t_lang('BOOKING_CANCELLATION_HAS_BEEN_POSTED_BY_HOST.REGRADING - ') . $booking_id;
        $notify->notify(0, 0, $url, $notify_text);
        $replace_vars = array(
            '{booking_id}' => $booking_id,
            '{booking_timing}' => FatDate::format($order_act['oactivity_event_timing'], true)
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 26, $replace_vars);
        FatUtility::dieJsonSuccess(Info::t_lang('CANCEL_REQUEST_POSTED!'));
    }

    private function getOrderCancelForm() {
        $frm = new Form('orderCancelFrm');
        $frm->addHiddenField('', 'booking_id');
        $fld = $frm->addTextArea(Info::t_lang('COMMENT'), 'comment');
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function orderCancelRequests() {
        FatApp::redirectUser(FatUtility::generateUrl('host', 'bookingCancelRequests'));
    }

    function bookingCancelRequests() {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('BOOKINGS'));
        $brcmb->add(Info::t_lang("CANCELLATIONS"));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    function orderCancelLists($page = 1) {
        $page = FatUtility::int($page);
        $page = $page == 0 ? 1 : $page;
        $srch = OrderCancel::getSearchObject();
        $srch->joinTable(Activity::DB_TBL, 'inner join', Activity::DB_TBL_PREFIX . 'id = oactivity_activity_id and ' . Activity::DB_TBL_PREFIX . 'user_id = ' . $this->userId);
        $srch->addOrder(OrderCancel::DB_TBL_PREFIX . 'id', 'desc');
        $srch->addMultipleFields(array(OrderCancel::DB_TBL . '.*', 'oactivity_activity_name as ordered', 'oactivity_unit_price', 'oactivity_booking_id', 'ordercancel_id', 'ordercancel_status', 'oactivity_event_timing'));
        $srch->setPageNumber($page);
        $srch->setPageSize(static::PAGESIZE);
        $rs = $srch->getResultSet();
        //	echo $srch->getError();
        $records = FatApp::getDb()->fetchAll($rs);

        $this->set("arr_listing", $records);
        $this->set('totalPage', $srch->pages());
        $this->set('page', $page);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "host/_partial/order-cancel-listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function bookingCancelDetail($booking_id) {
        $ord = new Order();
        $act = new Activity();
        $ordCancel = new OrderCancel();
        $activities = $ord->getOrderActivityByBookingId($booking_id);
        if (empty($activities)) {
            FatUtility::exitWithErrorCode(404);
        }

        $actDetail = $act->getActivity($activities['oactivity_activity_id']);
        if (empty($actDetail) || $actDetail['activity_user_id'] != $this->userId) {
            FatUtility::exitWithErrorCode(404);
        }
        $activities['activity_price_type'] = $actDetail['activity_price_type'];

        $activities['addons'] = $ord->getOrderAddons($activities['oactivity_id']);
        #	Info::test($activities);
        $cancel_data = $ordCancel->getCancelBooking($booking_id, OrderCancel::DB_TBL_PREFIX . 'booking_id');
        if (empty($cancel_data)) {
            FatApp::redirectUser(FatUtility::generateUrl('host', 'bookingCancelRequests'));
        }
        $cancel_id = $cancel_data[OrderCancel::DB_TBL_PREFIX . 'id'];
        $cmt_srch = Comments::getSearchObject();
        $cmt_srch->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Comments::DB_TBL_PREFIX . 'user_id');
        $cmt_srch->addCondition(Comments::DB_TBL_PREFIX . 'entity_type', '=', Comments::ENTITY_TYPE_ORDER_CANCEL);
        $cmt_srch->addCondition(Comments::DB_TBL_PREFIX . 'entity_id', '=', $cancel_id);
        $cmt_srch->addOrder(Comments::DB_TBL_PREFIX . 'id', 'desc');
        $rs = $cmt_srch->getResultSet();
        $comments = FatApp::getDb()->fetchAll($rs);
        $this->set('cancel_data', $cancel_data);
        $this->set('comments', $comments);
        $this->set('activities', $activities);
        $this->set('booking_id', $booking_id);
        $this->_template->render();
    }

    function addOrderCancelComment($booking_id) {
        $frm = $this->getOrderCancelForm();
        $ord = new Order();
        $ordCancel = new OrderCancel();
        $srch = $ord->getOrderActivityDetail($booking_id);
        $srch->joinTable('tbl_activities', "inner join", "activity_user_id = " . $this->userId . " and oactivity_activity_id = activity_id");
        $rs = $srch->getResultSet();
        $db = FatApp::getDb();
        $activities = $db->fetch($rs);
        if (empty($activities)) {
            FatUtility::exitWithErrorCode(404);
        }
        $frm->fill(array('booking_id' => $booking_id));
        $this->set('frm', $frm);
        $this->set('booking_id', $booking_id);
        $this->_template->render(false, false, 'host/_partial/add-order-cancel-comment.php');
    }

    function setupOrderCancelComment() {
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
        $order_act = $ord->getOrderActivityByBookingId($booking_id);
        if (empty($order_act)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $act_data = $act->getActivity($order_act['oactivity_activity_id']);
        if (empty($act_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if ($act_data[Activity::DB_TBL_PREFIX . 'user_id'] != $this->userId) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $cancel_data = $ordCancel->getCancelBooking($booking_id, OrderCancel::DB_TBL_PREFIX . 'booking_id');
        if (empty($cancel_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }

        $cmnt = new Comments();
        $comment_data[Comments::DB_TBL_PREFIX . 'entity_type'] = Comments::ENTITY_TYPE_ORDER_CANCEL;
        $comment_data[Comments::DB_TBL_PREFIX . 'entity_id'] = $cancel_data[OrderCancel::DB_TBL_PREFIX . 'id'];
        $comment_data[Comments::DB_TBL_PREFIX . 'user_id'] = $this->userId;
        $comment_data[Comments::DB_TBL_PREFIX . 'comment'] = $post['comment'];
        $comment_data[Comments::DB_TBL_PREFIX . 'datetime'] = Info::currentDatetime();
        $cmnt->assignValues($comment_data);
        if (!$cmnt->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN!'));
        }
        $replace_vars = array(
            '{comment}' => nl2br($post['comment']),
            '{booking_id}' => $booking_id,
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 30, $replace_vars);
        $notify = new Notification();
        $url = FatUtility::generateUrl('admin', 'orderCancelRequests', array(), '/');
        $notify_text = Info::t_lang('HOST_ADD_A_COMMENT_ON_BOOKING_CANCEL_REQUEST.BOOKING_ID - ') . $booking_id;
        $notify->notify(0, 0, $url, $notify_text);

        FatUtility::dieJsonSuccess(Info::t_lang('COMMENT_POSTED!'));
    }

}
