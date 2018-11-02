<?php

class Orders {

    private $error;
    private $db;

    const ORDER_TBL = 'tbl_orders';
    const ORDER_EVENT_TBL = 'tbl_order_activities';
    const ORDER_ADDON_TBL = 'tbl_order_activity_addons';
    const ORDER_CHARGE_TBL = 'tbl_order_extra_charges';

    function __construct() {
        $this->db = FatApp::getDb();
    }

    function getOrderSearch() {
        $search = new SearchBase(static::ORDER_TBL);
        $search->joinTable(static::ORDER_EVENT_TBL, 'INNER JOIN', 'oactivity_order_id = order_id');
        $search->joinTable('tbl_users', 'LEFT JOIN', 'order_user_id = user_id');
        return $search;
    }

    public function getOrder($order_id) {
        $search = new SearchBase(static::ORDER_TBL);
        $search->joinTable('tbl_users', 'LEFT JOIN', 'order_user_id = user_id');
        $search->addCondition("order_id", "=", $order_id);
        $rs = $search->getResultSet();
        $record = $this->db->fetch($rs);
        return $record;
    }

    public function getOrderActivity($order_id) {
        $search = new SearchBase(static::ORDER_EVENT_TBL);
        $search->addCondition("oactivity_order_id", "=", $order_id);
        $rs = $search->getResultSet();
        $record = $this->db->fetchAll($rs);
        return $record;
    }

    public function getOrderAddons($activity_id) {
        $search = new SearchBase(static::ORDER_ADDON_TBL);
        $search->addCondition("oactivityadd_oactivity_id", "=", $activity_id);
        $rs = $search->getResultSet();
        $record = $this->db->fetchAll($rs);
        return $record;
    }

    public function getOrderTransaction($order_id) {
        $search = new SearchBase('tbl_order_transactions');
        $search->addCondition('tran_order_id', '=', $order_id);
        $search->addOrder('tran_id', 'Desc');
        return $this->db->fetchAll($search->getResultSet());
    }

}

?>