<?php

class OrdersController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    const PAGESIZE = 50;

    public function __construct($action) {
        $ajaxCallArray = array("lists", "transactionForm", 'transactionformAction', 'cuisinesForm');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewOrder($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditOrder($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    function index($order_type = 0, $user_id = 0) {
        $user_id = FatUtility::int($user_id);
        FatApp::redirectUser(FatUtility::generateUrl('orders', 'listing'));
    }

    function listing($order_type = 0, $user_id = 0) {
        $brcmb = new Breadcrumb();
        $brcmb->add("Bookings Management");

        $user_id = FatUtility::int($user_id);
        $form = $this->getSearchForm();
        $this->set('breadcrumb', $brcmb->output());
        $this->set('user_id', $user_id);
        $this->set('order_type', $order_type);
        $this->set('search', $form);
        $this->_template->render();
    }

    function lists($page = 1, $tab = 1, $user_id = 0, $order_type = 0) {
        $user_id = FatUtility::int($user_id);
        $tab = FatUtility::int($tab);
        $page = FatUtility::int($page);
        $tab = $tab == 0 ? 1 : $tab;
        $page = $page == 0 ? 1 : $page;
        $post = FatApp::getPostedData();

        $form = $this->getSearchForm();
        $post = $form->getFormDataFromArray($post,array('activity_id'));
        $odr = new Orders();
        $search = $odr->getOrderSearch();
        if (!empty($post['keyword'])) {
            $key_con = $search->addCondition('order_user_email', 'like', '%' . $post['keyword'] . '%');
            $key_con->attachCondition("mysql_func_CONCAT_WS('',user_firstname,user_lastname)", 'like', '%' . $post['keyword'] . '%', 'or',true);
        }
        if (!empty($post['order_id'])) {
            $key_con = $search->addCondition('order_id', '=', $post['order_id']);
        }
        if (!empty($post['start_date']) && FatDate::validateDateString($post['start_date'])) {
            $key_con = $search->addDirectCondition('date(order_date) >= "' . $post['start_date'] . '"');
        }
        if (!empty($post['end_date']) && FatDate::validateDateString($post['end_date'])) {
            $key_con = $search->addDirectCondition('date(order_date) <= "' . $post['end_date'] . '"');
        }
        if (!empty($post['booking_id'])) {
            $key_con = $search->addCondition('oactivity_booking_id', '=', $post['booking_id']);
        }
        if (isset($post['host_id']) && $post['host_id'] != '' && $post['host_id'] > 0) {
            $post['host_id'] = FatUtility::int($post['host_id']);
            $search->joinTable(Activity::DB_TBL, 'inner join', Activity::DB_TBL_PREFIX . 'id = oactivity_activity_id and ' . Activity::DB_TBL_PREFIX . 'user_id = ' . $post['host_id']);
        }
        if (isset($post['activity_id']) && $post['activity_id'] != '' && $post['activity_id'] > 0) {
            $post['activity_id'] = FatUtility::int($post['activity_id']);
            $search->addCondition('oactivity_activity_id', '=', $post['activity_id']);
        }


        if (isset($post['order_payment_status']) && $post['order_payment_status'] != '' && $post['order_payment_status'] != -1) {
            $order_payment_status = FatUtility::int($post['order_payment_status']);
            $search->addCondition('order_payment_status', '=', $order_payment_status);
        }

        $search->addGroupBy('order_id');
        $search->addOrder('oactivity_id', 'DESC');
        $search->addMultipleFields(array(ORDERS::ORDER_TBL . '.*', "group_concat(oactivity_activity_name SEPARATOR ' + ') as ordered", "concat(user_firstname, '', user_lastname) as user_name,sum(oactivity_admin_commission) as admin_commision"));
        $search->setPageNumber($page);
        $search->setPageSize(static::PAGESIZE);
        $rs = $search->getResultSet();
      
        $db = FatApp::getDb();
        $records = $db->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('tab', $tab);
        $this->set('postedData', $post);
        $this->set('pageSize', static::PAGESIZE);
        $htm = $this->_template->render(false, false, "orders/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getSearchForm() {
        $frm = new Form('frmUserSearch');
        $order_payment_status = Info::paymentStatus();
        $order_payment_status[-1] = 'Does not matter';

        $frm->addTextBox('Name or Email', 'keyword', '', array('class' => 'search-input'));
        $frm->addTextBox('Order Id', 'order_id', '', array('class' => 'search-input'));
        $frm->addTextBox('Booking Id', 'booking_id', '', array('class' => 'search-input'));
        $frm->addSelectBox('Payment Status', 'order_payment_status', $order_payment_status, -1, array('class' => 'search-input'), '');
        $usrs = new Users();
        $hosts = $usrs->getHostUsers();
        $hosts[0] = 'Does not matter';
		
		$frm->addHiddenField('', 'host_id',0);
		$frm->addTextBox(Info::t_lang('HOST'),'host_name');		
		
        //$frm->addSelectBox('Host', 'host_id', $hosts, 0, array('class' => 'search-input','data-href'=>FatUtility::generateUrl('activities','getHostActivities'),'onClick'=>'loadDependValues(this,"activitiesId")'), '');

        $acts = array();
        $acts[''] = 'Select a Host';
        $frm->addSelectBox('Activity', 'activity_id', $acts, 0, array('class' => 'search-input','id'=>'activitiesId'), '');
        $frm->addDateField('Start Date', 'start_date', '', array('readonly' => 'readonly'));
        $frm->addDateField('End Date', 'end_date', '', array('readonly' => 'readonly'));
        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        return $frm;
    }

    function transactionForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        if (empty($post['order_id'])) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $frm = $this->getTransactionForm($post['order_id']);
        $this->set('frm', $frm);
        $html = $this->_template->render(false, false, 'orders/_partial/transaction-form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    function transactionformAction() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        if (empty($post['order_id'])) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $frm = $this->getTransactionForm($post['order_id']);
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError('Something went wrong!');
        }
        $order_id = $post['order_id'];
        $data['amount'] = $post['amount'];
        $data['gateway_transaction_id'] = "admin";
        $data['response_data'] = "admin";
        $data['transaction_completed'] = 1;
        $data['mode'] = "admin";
        $data['tran_admin_comments'] = $post['comment'];
        if (!Transaction::addNew($order_id, $data)) {
            FatUtility::dieJsonError("Something went Wrong");
        }
        FatUtility::dieJsonSuccess('Transaction Have Been Added.');
    }

    private function getTransactionForm($order_id) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = new Form('frmUserSearch');
        $frm->addHiddenField('', 'order_id', $order_id);
        $frm->addHiddenField('', 'fIsAjax', 1);
        $frm->addRequiredField('Transaction Amount (Enter amout with (- sign) for revert transaction)', 'amount');
        $frm->addTextArea('Comment(for hint)', 'comment');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Add');
        return $frm;
    }

    function viewTransactions($order_id) {
        $brcmb = new Breadcrumb();
        $brcmb->add("Order Management", FatUtility::generateUrl('orders', 'listing'));
        $brcmb->add("View Transactions");
        if (empty($order_id)) {
            FatApp::redirectUser(FatUtility::generateUrl('orders', 'listing'));
        }
        $this->set('breadcrumb', $brcmb->output());
        $this->set('order_id', $order_id);
        $this->_template->render();
    }

    function listsTransactions($order_id) {
        if (empty($order_id)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $odr = new Orders();
        $transactions = $odr->getOrderTransaction($order_id);
        $this->set('arr_listing', $transactions);
        $this->set('order_id', $order_id);
        $html = $this->_template->render(false, false, 'orders/_partial/lists-transactions.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    function detail($order_id) {
        if (empty($order_id)) {
            FatApp::redirectUser(FatUtility::generateUrl('orders'));
        }
        $ords = new Orders();
        $ord = new Order();
        $order = $ords->getOrder($order_id);

        $activities = $ords->getOrderActivity($order_id);
        foreach ($activities as $k => $v) {
            $activities[$k]['addons'] = $ords->getOrderAddons($v['oactivity_id']);
        }

        $extra_charges = $ord->getOrderExtraCharges($order_id);
        $this->set('extra_charges', $extra_charges);
        $this->set('order', $order);
        $this->set('activities', $activities);
        $this->_template->render();
    }

    function changeOrderStatus() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        if (empty($post['order_id'])) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        if (!isset($post['order_status'])) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $order_process_status = $post['order_status'];
        $order_id = $post['order_id'];
        $odr = new Order();
        if (!$odr->updateOrderStatus($order_id, $order_process_status)) {
            FatUtility::dieJsonError('Something Went Wrong!');
        }
        Order::sentOrderStatusChangeMail($order_id, $order_process_status);
        FatUtility::dieJsonSuccess('Status Changed!');
    }
    
    
  

}

?>