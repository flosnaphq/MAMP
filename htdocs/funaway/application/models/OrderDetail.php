<?php

class OrderDetail extends FatModel {

    private $orderDetail = array();
    private $orderId = null;
    private $db;
    public function __construct($orderId) {
        parent::__construct();
          $this->db = FatApp::getDb();
        if (!$orderData = $this->getOrderDetails($orderId)) {
            trigger_error("Order Id Not Found");
        }
      
        $this->orderId = $orderId;
        $this->orderDetail = $orderData;
    }

    public function getOrderDetails($orderId) {

        $srch = new SearchBase('tbl_orders');
        $srch->joinTable("tbl_users", 'LEFT JOIN', 'user_id = order_user_id');
        $srch->addCondition("order_id", "=", $orderId);
        $rs = $srch->getResultSet();
    
        $orderInfo = $this->db->fetch($rs);
        return $orderInfo;
    }
    public function orderDetails() {

        return $this->orderDetail;
    }
    public function getOrderInvoice() {
        return $this->orderDetail['order_id'];
    }

    public function getOrderUserEmail() {
        return $this->orderDetail['order_user_email'];
    }

    public function getOrderUserPhone() {
        return $this->orderDetail['order_user_phone'];
    }

    public function getOrderPayableAmount() {
        return $this->orderDetail['order_total_amount'];
    }

    public function getOrderProductInfo() {
        $order_payment_gateway_description = "Activity";

        return $order_payment_gateway_description;
    }

    public function getOrderUserFirstName() {
        return $this->orderDetail['user_firstname'];
    }

    public function getOrderUserLastName() {
        return $this->orderDetail['user_lastname'];
    }

    public function getOrderUserFullName() {
        return $this->orderDetail['user_firstname'] . " " . $this->orderDetail['user_lastname'];
    }

}
