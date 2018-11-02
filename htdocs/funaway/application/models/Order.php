<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class Order extends FatModel {

    private $db;

    const ORDER_TBL = 'tbl_orders';
    const ORDER_EVENT_TBL = 'tbl_order_activities';
    const ORDER_ADDON_TBL = 'tbl_order_activity_addons';
    const ORDER_CHARGE_TBL = 'tbl_order_extra_charges';

    public function __construct() {
        parent::__construct();
        $this->db = FatApp::getDb();
    }

    public function addOrder($order = array()) {
        $this->db->insertFromArray(Order::ORDER_TBL, $order);
    }

    public function addOrderEvent($event = array())
	{
        if(false === $this->db->insertFromArray(Order::ORDER_EVENT_TBL, $event))
		{
			$this->error = Info::t_lang('Error_Unable_to_store_order_event');
			return false;
		}
        return $this->db->getInsertId();
    }

    public function updateOrderEvent($event = array()) {
        $this->db->updateFromArray(Order::ORDER_EVENT_TBL, $event, array('smt' => "oactivity_id = ?", 'vals' => array($event['oactivity_id'])));
    }

    public function addOrderAddon($addons = array()) {
        $this->db->insertFromArray(Order::ORDER_ADDON_TBL, $addons);
    }

    public function addOrderCharge($charge = array()) {
        $this->db->insertFromArray(Order::ORDER_CHARGE_TBL, $charge);
    }

    function getOrderId() {

        /* if (CONF_DEVELOPMENT_MODE) {
            return $activityId;
        } */
        $order_prefix = FatApp::getConfig('CONF_ORDER_PREFIX');


        $order_id = $order_prefix . OrderNumber::getNewOrderNumber();
        while (!$this->isNewOrderId($order_id)) {
            $order_id = $order_prefix . OrderNumber::getNewOrderNumber();
        }
        return $order_id;
    }

    private function isNewOrderId($order_id) {
        $record = array();
        $srch = new SearchBase('tbl_orders');
        $srch->addCondition("order_id", "=", $order_id);
        $rs = $srch->getResultSet();
        $record = $this->db->fetch($rs);
        if (empty($record))
            return true;
        return false;
    }

    function getBookingId($order_id) {
        $order_prefix = FatApp::getConfig('CONF_ORDER_PREFIX');

        $order_id = str_replace($order_prefix, '', $order_id);
        $booking_id = $order_id;
        return $booking_id;
    }

    function getValidBookingId($booking_id) {
        $booking_prefix = FatApp::getConfig('CONF_BOOKING_PREFIX');
        while (!$this->isNewBookingId($booking_prefix . $booking_id)) {
            $booking_id ++;
        }
        return $booking_id;
    }

    private function isNewBookingId($booking_id) {
        $record = array();
        $srch = new SearchBase('tbl_order_activities');
        $srch->addCondition("oactivity_booking_id", "=", $booking_id);
        $rs = $srch->getResultSet();
        $record = $this->db->fetch($rs);
        if (empty($record))
            return true;
        return false;
    }

    function updateOrderActivity($booking_id, $data) {
        $booking_id = trim($booking_id);
        $tbl = new TableRecord('tbl_order_activities');
        $tbl->assignValues($data);
        return $tbl->update(array('smt' => 'oactivity_booking_id = ?', 'vals' => array($booking_id)));
    }

    function refundBooking($booking_id) {
        $order_act = $this->getOrderActivityByBookingId($booking_id);
        $act = new Activity();
        $wlt = new Wallet();
        if (empty($order_act)) {
            return false;
        }
        $actData = $act->getActivity($order_act['oactivity_activity_id'], -1);
        
		if (empty($actData)) {
            return false;
        }
		
        $host_user_id = $actData['activity_user_id'];

        $credit_amount = $wlt->getBookingTotalAmount($host_user_id, $booking_id);

        $wallet_data[wallet::DB_TBL_PREFIX . 'user_id'] = $host_user_id;
        $wallet_data[wallet::DB_TBL_PREFIX . 'activity_id'] = $order_act['oactivity_activity_id'];
        $wallet_data[wallet::DB_TBL_PREFIX . 'user_type'] = 1;
        $wallet_data[wallet::DB_TBL_PREFIX . 'type'] = 1;
        $wallet_data[wallet::DB_TBL_PREFIX . 'date'] = Info::currentDatetime();
        $wallet_data[wallet::DB_TBL_PREFIX . 'amount'] = '-' . $credit_amount;
        $wallet_data[wallet::DB_TBL_PREFIX . 'desc'] = 'Refund Booking - ' . $booking_id;
        $wallet_data[wallet::DB_TBL_PREFIX . 'status'] = 1;
        if (!Wallet::addToWallet($wallet_data)) {
            return false;
        }

        return true;
    }

    public function getOrderDetail($order_id) {
        $srch = new SearchBase('tbl_orders');
        $srch->addCondition("order_id", "=", $order_id);
        $rs = $srch->getResultSet();
        return $record = $this->db->fetch($rs);
    }

    public function getOrderActivity($order_id) {
        $search = new SearchBase(static::ORDER_EVENT_TBL);
        $search->joinTable('tbl_activities', 'inner join', 'oactivity_activity_id = activity_id');
        $search->addCondition("oactivity_order_id", "=", $order_id);
        $rs = $search->getResultSet();
        $record = $this->db->fetchAll($rs);
        return $record;
    }

    public function getOrderActivitySearch($order_id) {
        $search = new SearchBase(static::ORDER_EVENT_TBL);
        $search->joinTable('tbl_activities', 'inner join', 'oactivity_activity_id = activity_id');
        $search->addCondition("oactivity_order_id", "=", $order_id);

        return $search;
    }

    public function getOrderExtraCharges($order_id) {
        $search = new SearchBase(static::ORDER_CHARGE_TBL);
        $search->addCondition("ordercharge_order_id", "=", $order_id);
        $rs = $search->getResultSet();
        $record = $this->db->fetchAll($rs, 'ordercharge_type');
        return $record;
    }

    public function getOrderExtraCharge($order_id, $type = 0) {
        $search = new SearchBase(static::ORDER_CHARGE_TBL);
        $search->addCondition("ordercharge_order_id", "=", $order_id);
        $search->addCondition("ordercharge_type", "=", $type);
        $rs = $search->getResultSet();
        $record = $this->db->fetch($rs);
        return $record;
    }

    public function getOrderAddons($activity_id) {
        $search = new SearchBase(static::ORDER_ADDON_TBL);
        $search->addCondition("oactivityadd_oactivity_id", "=", $activity_id);
        $rs = $search->getResultSet();
        $record = $this->db->fetchAll($rs);

        return $record;
    }

    public function getOrderSearch() {
        $srch = new SearchBase('tbl_orders');
        $srch->joinTable('tbl_order_activities', "inner join", "order_id = oactivity_order_id");
        return $srch;
    }

    public function getOrderByActivity() {
        $srch = new SearchBase('tbl_order_activities');
        $srch->joinTable('tbl_orders', "inner join", "oactivity_order_id = order_id");

        return $srch;
    }

    function updateOrder($order_id, $pending = "") {
        if ($pending == "") {
            $data['order_payment_status'] = 1;
        }
        if ($pending == "declined")
            $data['order_payment_status'] = 0;

        $success = $this->db->updateFromArray('tbl_orders', $data, array('smt' => 'order_id = ?', 'vals' => array($order_id)));
        if ($success) {
            if ($data['order_payment_status'] == 1) {
                $order_activities = $this->getOrderActivity($order_id);
                if (!empty($order_activities)) {
                    $event_req = new EventRequest();
                    foreach ($order_activities as $act) {
                        if ($act['oactivity_request_id'] != 0) {
                            $event_req->markRequestAsCompleted($act['oactivity_request_id']);
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    function getOrderActivityDetail($booking_id) {
        $srch = new SearchBase('tbl_order_activities');
        $srch->joinTable('tbl_orders', "inner join", "oactivity_order_id = order_id");
        $srch->addCondition('oactivity_booking_id', '=', $booking_id);
        return $srch;
    }
    function getOrderActivityDetailData($booking_id) {
        $srch = $this->getOrderActivityDetail($booking_id);
        $rs = $srch->getResultSet();
        $row = $this->db->fetch($rs);
        return @$row;
    }

    function getOrderActivityByRequestId($request_id) {
        $request_id = FatUtility::int($request_id);
        $srch = new SearchBase('tbl_order_activities');
        $srch->addCondition('oactivity_request_id', '=', $request_id);
        $rs = $srch->getResultSet();
        $row = $this->db->fetch($rs);
        return @$row;
    }

    function getOrderActivityByBookingId($booking_id) {
        $srch = new SearchBase('tbl_order_activities');
        $srch->addCondition('oactivity_booking_id', '=', $booking_id);
        $rs = $srch->getResultSet();
        return $this->db->fetch($rs);
    }

    function getCommission($amount, $user_id, $is_admin = false) {
        $comm = 0;
        if ($user_id > 0) {
            $usr = new User($user_id);
            $comm = $usr->getAttributesById($user_id, User::DB_TBL_PREFIX . 'commission');
        }
        if ($comm <= 0) {
            $range = CommissionChart::getCommissionChart();

            $comm = FatApp::getConfig('ADMIN_DEFAULT_COMMISSION');
            if (!empty($range)) {
                foreach ($range as $r) {
                    if (($r['min_amount'] > 0 && $amount >= $r['min_amount']) && ($amount <= $r['max_amount'] && $r['max_amount'] > 0 )) {
                        $comm = $r['commission_rate'];
                    } elseif ($r['min_amount'] <= 0 && $amount <= $r['max_amount']) {
                        $comm = $r['commission_rate'];
                    } elseif ($r['max_amount'] <= 0 && $amount >= $r['min_amount']) {
                        $comm = $r['commission_rate'];
                    }
                }
            }
        }

        /* $range = array("13"=>17000,"15"=>7000,"19"=>2800);
          $comm = 10;
          foreach($range as $k=>$v){
          if($amount <= $v ){
          $comm = $k;
          }
          } */


        if (!$is_admin) {
            $comm = 100 - $comm;
        }

        $commission = round($amount * ($comm / 100));
        if ($is_admin) {
            //Info::test($amount . ' AND ' . $commission);
            //exit;
        }


        return $commission;
    }

    static function canTravelerCancelBooking($policy_day, $event_timing, $order_payment_status) {
        $policy_time = $policy_day * (60 * 60 * 24);
        $diff = strtotime($event_timing) - strtotime(Info::currentDatetime());
        return ($diff > $policy_time && $order_payment_status == 1);
    }
    static function canTravelerReviewBooking($event_timing, $order_payment_status) {

         $diff = strtotime(Info::currentDatetime())-strtotime($event_timing);

        return   ( $diff >(60 * 60 * 24)  && $order_payment_status == 1);
	
    }
    function getOrderOnly($order_id) {
        $srch = new SearchBase('tbl_orders');
        $srch->addCondition('order_id', '=', $order_id);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetch($rs);
        return $rows;
    }

    function getEventBooking($event_id, $datetime = '') {
        if ($datetime == '') {
            $datetime = Info::currentDatetime();
        }
        $srch = new SearchBase('tbl_order_activities');
        $srch->joinTable('tbl_orders', 'inner join', 'order_id = oactivity_order_id and order_payment_status = 1');
        $srch->joinTable('tbl_users', 'inner join', 'user_id = order_user_id');
        $srch->addCondition('oactivity_is_cancel', '=', 0);
        $srch->addCondition('oactivity_event_id', '=', $event_id);
        $srch->addDirectCondition('oactivity_event_timing > "' . $datetime . '"');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addMultipleFields(array(
            'oactivity_booking_id',
            'order_id',
            'user_id',
            'user_firstname',
            'user_lastname',
            'user_email',
            'user_phone',
            'user_phone_code',
            'oactivity_is_cancel',
        ));
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs);
    }

    static function generatePdf($htm, $file_name = 'order.pdf', $attachment = false) {
        define("DOMPDF_ENABLE_REMOTE", true);
        require_once CONF_INSTALLATION_PATH . 'library/dompdf/autoload.inc.php';
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);

        $dompdf = new DOMPDF($options);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => TRUE
            ]
        ]);
        $dompdf->setHttpContext($context);
        $dompdf->set_option('enable_css_float', false);
        $dompdf->set_option('isHtml5ParserEnabled', false);
        $dompdf->load_html($htm);
        $dompdf->set_paper('a4', 'potrait');
        $dompdf->render();
        $dompdf->stream($file_name, array("Attachment" => $attachment));
        exit(0);
    }

    static function generatePdf2($htm, $file_name = 'order.pdf', $attachment = false) {
        define("DOMPDF_ENABLE_REMOTE", true);

        require_once(CONF_INSTALLATION_PATH . "library/dompdf/dompdf_config.inc.php");
        $dompdf = new DOMPDF();

        $dompdf->load_html($htm);
        $dompdf->set_paper('a4', 'potrait');
        $dompdf->render();
        $dompdf->stream($file_name);
        exit(0);
    }

    function isEventBooked($event_id) {
        $event_id = FatUtility::int($event_id);
        $srch = new SearchBase('tbl_order_activities');
        $srch->setPageSIze(1);
        $srch->setPageNumber(1);
        $srch->doNotLimitRecords();
        $srch->addCondition('oactivity_event_id', '=', $event_id);
        $srch->addOrder('oactivity_id', 'desc');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetch($rs);
        return !empty($rows) ? true : false;
    }

}

?>
