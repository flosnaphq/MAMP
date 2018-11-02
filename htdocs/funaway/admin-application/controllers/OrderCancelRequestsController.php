<?php
class OrderCancelRequestsController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {

        $ajaxCallArray = array('listing');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewOrder($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditOrder($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {
        $frm = $this->getSearchForm();
        $brcmb = new Breadcrumb();
        $brcmb->add("Booking Cancel Requests");
        $this->set('breadcrumb', $brcmb->output());
        $this->set('search', $frm);
        $this->_template->render();
    }

    public function lists($page = 1) {
        $pagesize = static::PAGESIZE;
        $page = FatUtility::int($page);
        $search = OrderCancel::getSearchObject();
        $search->joinTable(User::DB_TBL, 'inner Join', User::DB_TBL_PREFIX . 'id = ' . OrderCancel::DB_TBL_PREFIX . 'user_id');
        //	$post = $form->getFormDataFromArray(FatApp::getPostedData());
        $post = FatApp::getPostedData();
        if (!empty($post['keyword'])) {
            $con = $search->addCondition(User::DB_TBL_PREFIX . 'firstname', 'like', '%' . $post['keyword'] . '%');
            $con = $con->attachCondition(User::DB_TBL_PREFIX . 'email', 'like', '%' . $post['keyword'] . '%', 'or');
            $con = $con->attachCondition('oactivity_booking_id', '=', $post['keyword'], 'or');
        }
        if (!empty($post['start_date'])) {
            $search->addDirectCondition('DATE(ordercancel_datetime) >="' . $post['start_date'] . '"');
        }
        if (!empty($post['end_date'])) {
            $search->addDirectCondition('DATE(ordercancel_datetime) <="' . $post['end_date'] . '"');
        }
        if (isset($post['order_payment_status']) && $post['order_payment_status'] != '' && $post['order_payment_status'] != -1) {
            $order_payment_status = FatUtility::int($post['order_payment_status']);
            $search->addCondition('order_payment_status', '=', $order_payment_status);
        }
        $search->setPageSize($pagesize);
        $search->setPageNumber($page);
        $search->addOrder(OrderCancel::DB_TBL_PREFIX . 'id', 'desc');
        $rs = $search->getResultSet();

        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "order-cancel-requests/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function view($cancel_id) {
        $cancel_id = FatUtility::int($cancel_id);
        if ($cancel_id <= 0) {
            FatUtility::dieWithError('Invalid Request!');
        }
        $brcmb = new Breadcrumb();
        $brcmb->add("Booking Cancel Requests");
        $this->set('breadcrumb', $brcmb->output());
        $ordCan = new OrderCancel($cancel_id);
        $ordCan->loadFromDb();
        $flds = $ordCan->getFlds();

        $this->set('cancel_id', $cancel_id);
        $this->set('flds', $flds);
        $this->_template->render();
    }

    function tab($tab = 1) {
        $post = FatApp::getPostedData();
        $cancel_id = FatUtility::int($post['cancel_id']);
        $tab = FatUtility::int($tab);
        $tab = $tab < 1 ? 1 : $tab;
        switch ($tab) {
            case 1:
                $ordCan = new OrderCancel($cancel_id);
                $ord = new Order();
                $ordCan->loadFromDb();
                $flds = $ordCan->getFlds();
                $orderAct = $ord->getOrderActivityByBookingId($flds['ordercancel_booking_id']);
                $order_id = $orderAct['oactivity_order_id'];
                if (empty($order_id)) {
                    FatApp::redirectUser(FatUtility::generateUrl('orders'));
                }
                $ords = new Orders();
                $order = $ords->getOrder($order_id);
                $activities = $ords->getOrderActivity($order_id);
                foreach ($activities as $k => $v) {
                    $activities[$k]['addons'] = $ords->getOrderAddons($v['oactivity_id']);
                }
                $extra_charges = $ord->getOrderExtraCharges($order_id);
                $this->set('order', $order);
                $this->set('cancel_details', $flds);
                $this->set('activities', $activities);
                $this->set('extra_charges', $extra_charges);

                $htm = $this->_template->render(false, false, 'order-cancel-requests/_partial/order-details.php', true, true);
                FatUtility::dieJsonSuccess($htm);
                break;

            case 4:
                $ordCan = new OrderCancel($cancel_id);
                $ord = new Order();
                $ordCan->loadFromDb();
                $flds = $ordCan->getFlds();
				$orderAct = $ord->getOrderActivityByBookingId($flds['ordercancel_booking_id']);
                $order_id = $orderAct['oactivity_order_id'];
				$ords = new Orders();
                $order = $ords->getOrder($order_id);
					
                $bnkact = new BankAccounts();
                $data = (array) $bnkact->getBankAccount($order['order_user_id']);
                $this->set('data', $data);
                $htm = $this->_template->render(false, false, 'order-cancel-requests/_partial/bank-details.php', true, true);
                FatUtility::dieJsonSuccess($htm);

                break;
			case 2:
				
				$cmt_srch = Comments::getSearchObject();
				$cmt_srch->joinTable(User::DB_TBL,'left join',User::DB_TBL_PREFIX.'id = '.Comments::DB_TBL_PREFIX.'user_id');
				$cmt_srch->addCondition(Comments::DB_TBL_PREFIX.'entity_type','=', Comments::ENTITY_TYPE_ORDER_CANCEL);
				$cmt_srch->addCondition(Comments::DB_TBL_PREFIX.'entity_id','=', $cancel_id);
				$cmt_srch->addOrder(Comments::DB_TBL_PREFIX.'id','desc');
				$rs = $cmt_srch->getResultSet();
				$comments = FatApp::getDb()->fetchAll($rs);
				$this->set('cancel_id', $cancel_id);
				$this->set('comments', $comments);
				$htm = $this->_template->render(false,false,'order-cancel-requests/_partial/comments.php',true, true);
				FatUtility::dieJsonSuccess($htm);
				break;


            default:
                if (!$this->canEdit) {
                    FatUtility::dieJsonError('Unauthorized Access!');
                }
                $ordCan = new OrderCancel($cancel_id);
                $act = new Activity();
                $ord = new Order();
                $wlt = new Wallet();
                $ordCan->loadFromDb();
                $flds = $ordCan->getFlds();
                $orderAct = $ord->getOrderActivityByBookingId($flds['ordercancel_booking_id']);

                $refund_amount = ($orderAct['oactivity_refund_amount'] > 0) ? $orderAct['oactivity_refund_amount'] : $orderAct['oactivity_received_amount'];
                $frm = $this->getOrderCancelForm();
                $actData = $act->getActivity($orderAct['oactivity_activity_id']);
                $data = array(
                    'refund_amount' => $refund_amount,
                    'cancel_id' => $cancel_id,
                    'status' => $orderAct['oactivity_is_cancel'],
                    'request_status' => $flds[OrderCancel::DB_TBL_PREFIX . 'status'],
                );
                $host_debit_amount = $wlt->getBookingTotalAmount($actData['activity_user_id'], $flds['ordercancel_booking_id']);
                $fld = $frm->getField('refund_amount');
                $fld->htmlAfterField = '<em>Amount(' . Currency::displayPrice($host_debit_amount) . ') Auto Deduct from Host Wallet</em>';
                $frm->fill($data);
                $this->set('frm', $frm);
                $this->set('orderAct', $orderAct);
                $htm = $this->_template->render(false, false, 'order-cancel-requests/_partial/edit-status.php', true, true);
                FatUtility::dieJsonSuccess($htm);
                break;
        }
    }

    function setup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }

        $post = FatApp::getPostedData();
        $frm = $this->getOrderCancelForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $ord = new Order();
        $usr = new User();
        $act = new Activity();
        $ordCan = new OrderCancel($post['cancel_id']);
        $ordCan->loadFromDb();
        $flds = $ordCan->getFlds();
        $booking_id = $flds['ordercancel_booking_id'];
        $comment = $post['comment'];
        $refund_amount = $post['refund_amount'];
        $status = $post['status'];
        $request_status = $post['request_status'];
        $can_data[OrderCancel::DB_TBL_PREFIX . 'status'] = $request_status;
        $ordCan->assignValues($can_data);

        if (!$ordCan->save()) {
            FatUtility::dieJsonError('Something went wrong. Please try again.');
        }
		
        $cmnt_data[Comments::DB_TBL_PREFIX . 'entity_type'] = Comments::ENTITY_TYPE_ORDER_CANCEL;
        $cmnt_data[Comments::DB_TBL_PREFIX . 'entity_id'] = $post['cancel_id'];
        $cmnt_data[Comments::DB_TBL_PREFIX . 'comment'] = $comment;
        $cmnt_data[Comments::DB_TBL_PREFIX . 'user_id'] = 0;
        $cmnt_data[Comments::DB_TBL_PREFIX . 'datetime'] = Info::currentDatetime();
        $cmt = new Comments();
        $cmt->assignValues($cmnt_data);
        
		if (!$cmt->save()) {
            FatUtility::dieJsonError('Something went wrong. Please try again.');
        }
        
		$data['oactivity_is_cancel'] = $status;
        $data['oactivity_refund_amount'] = $refund_amount;
        
		if (!$ord->updateOrderActivity($booking_id, $data)) {
            FatUtility::dieJsonError('Something went wrong. Please try again.');
        }
		
        $actOrder = $ord->getOrderActivityByBookingId($booking_id);
        $order = $ord->getOrderOnly($actOrder['oactivity_order_id']);
        if ($status == 1) {
            $trans['tran_order_id'] = $actOrder['oactivity_order_id'];
            $trans['tran_time'] = Info::currentDatetime();
            $trans['tran_amount'] = (-1) * $refund_amount;
            $trans['tran_completed'] = 1;
            $trans['tran_real_amount'] = $order['order_net_amount'];
            $trans['tran_payment_mode'] = 'admin';
            $trans['tran_admin_comments'] = 'Refund against Booking ID -' . $booking_id;
            Transaction::add($trans);
            $traveler_id = $order['order_user_id'];
            $traveler_data = $usr->getUserByUserId($traveler_id);
            $replace_vars_traveler = array(
                '{username}' => ucwords($traveler_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $traveler_data[User::DB_TBL_PREFIX . 'lastname']),
                '{booking_id}' => $booking_id,
                '{refund_amount}' => Currency::displayPrice($refund_amount)
            );
            $notify = new Notification();
            $url = FatUtility::generateUrl('traveler', 'detail', array($booking_id), CONF_BASE_DIR);
            $notify_text = Info::t_lang('YOUR_BOOKING_') . $booking_id . Info::t_lang('_HAS_BEEN_CANCELLED_BY_ADMIN');
            $notify->notify($traveler_data[User::DB_TBL_PREFIX . 'id'], 0, $url, $notify_text);

            Email::sendMail($traveler_data[User::DB_TBL_PREFIX . 'email'], 24, $replace_vars_traveler);
            $acts = $act->getActivity($actOrder['oactivity_activity_id']);
            $host_id = $acts[Activity::DB_TBL_PREFIX . 'user_id'];
            $host_data = $usr->getUserByUserId($host_id);
            $replace_vars_host = array(
                '{username}' => ucwords($host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname']),
                '{traveler_name}' => ucwords($traveler_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $traveler_data[User::DB_TBL_PREFIX . 'lastname']),
                '{booking_id}' => $booking_id,
                '{booking_timing}' => FatDate::format($actOrder['oactivity_event_timing'], true),
            );
            $url = FatUtility::generateUrl('host', 'bookingCancelDetail', array($booking_id), CONF_BASE_DIR);
            $notify_text = Info::t_lang('BOOKING_') . $booking_id . Info::t_lang('_HAS_BEEN_CANCELLED_BY_ADMIN');
            $notify = new Notification();
            $notify->notify($host_id, 0, $url, $notify_text);

            Email::sendMail($host_data[User::DB_TBL_PREFIX . 'email'], 25, $replace_vars_host);
        }
        //$post['auto_refund'] == 1 &&
        if ($status == 1) {
            if (!$ord->refundBooking($booking_id)) {
                FatUtility::dieJsonError('Something went wrong. Please try again.');
            }
        }

        FatUtility::dieJsonSuccess('Update Successfully!');
    }

    private function getOrderCancelForm() {
        $frm = new Form('requestFrm');
        $frm->addHiddenField('', 'cancel_id');
        $fld = $frm->addTextArea('Comment', 'comment');
        $fld->requirements()->setRequired();
        $frm->addRequiredField('Refund Amount', 'refund_amount');


        $frm->addSelectBox('Request Status', 'request_status', Info::getOrderCancelRequestStatus(), 0, array(), '');
        $frm->addSelectBox('Order Cancel Status', 'status', Info::getOrderCancelStatus(), 0, array(), '');
        // $fld = $frm->addSelectBox('Auto Refund', 'auto_refund', array(0 => 'No', 1 => 'Yes'), 1, array(), '');

        $frm->addSubmitButton('', 'btn_submit', 'Submit');
        return $frm;
    }

    function addComment() {
        $post = FatApp::getPostedData();
        $cancel_id = @$post['cancel_id'];
        $cancel_id = FatUtility::int($cancel_id);
        if ($cancel_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $frm = $this->getCommentForm();
        $frm->fill(array('cancel_id' => $cancel_id));
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'order-cancel-requests/_partial/add-comment.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setupComment() {
        $post = FatApp::getPostedData();
        $frm = $this->getCommentForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $data[Comments::DB_TBL_PREFIX . 'comment'] = $post['comment'];
        $data[Comments::DB_TBL_PREFIX . 'entity_type'] = Comments::ENTITY_TYPE_ORDER_CANCEL;
        $data[Comments::DB_TBL_PREFIX . 'entity_id'] = $post['cancel_id'];
        $data[Comments::DB_TBL_PREFIX . 'datetime'] = Info::currentDatetime();
        $data[Comments::DB_TBL_PREFIX . 'user_id'] = 0;
        $usr = new User();
        $ord = new Order();
        $cmnt = new Comments();
        $act = new Activity();
        $ordCan = new OrderCancel($post['cancel_id']);
        $ordCan->loadFromDb();
        $flds = $ordCan->getFlds();
        $booking_id = $flds['ordercancel_booking_id'];
        $actOrder = $ord->getOrderActivityByBookingId($booking_id);
        $cmnt->assignValues($data);
        if (!$cmnt->save()) {
            FatUtility::dieJsonError('Something Went Wrong. Please Try Again');
        }
        $acts = $act->getActivity($actOrder['oactivity_activity_id']);
        $host_id = $acts[Activity::DB_TBL_PREFIX . 'user_id'];
        $host_data = $usr->getUserByUserId($host_id);
        $replace_vars_host = array(
            '{username}' => ucwords($host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname']),
            '{comment}' => nl2br($post['comment']),
            '{booking_id}' => $booking_id,
        );
        Email::sendMail($host_data[User::DB_TBL_PREFIX . 'email'], 29, $replace_vars_host);

        $notify = new Notification();
        $url = FatUtility::generateUrl('host', 'bookingCancelDetail', array($booking_id), CONF_BASE_DIR);
        $notify_text = Info::t_lang('ADMIN_ADD_A_COMMENT_ON_BOOKING_CANCEL_REQUEST.BOOKING_ID - ') . $booking_id;
        $notify->notify($host_data[User::DB_TBL_PREFIX . 'id'], 0, $url, $notify_text);

        FatUtility::dieJsonSuccess('Comment Posted!');
    }

    private function getCommentForm() {
        $frm = new Form('addComment');
        $frm->addHiddenField('', 'cancel_id');
        $fld = $frm->addTextArea('Comment', 'comment');
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', 'Submit');
        return $frm;
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch', array('class' => 'web_form', 'onsubmit' => 'search(this); return false;'));
        $frm->addTextBox('Name/Email/Booking Id', 'keyword', '', array('class' => 'search-input'));
        $frm->addDateField('Start Date', 'start_date', '', array('readonly' => 'readonly', 'class' => 'search-input'));
        $frm->addDateField('End Date', 'end_date', '', array('readonly' => 'readonly', 'class' => 'search-input'));

        $order_payment_status = Info::paymentStatus();
        $order_payment_status[-1] = 'Does not matter';
        $frm->addSelectBox('Payment Status', 'order_payment_status', $order_payment_status, -1, array('class' => 'search-input'), '');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        return $frm;
    }

}
