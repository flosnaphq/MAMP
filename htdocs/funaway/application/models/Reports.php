<?php

class Reports extends FatModel {

    public static function getAdminCommissionOnPrice($price) {
    
        return round(($price * FatApp::getConfig('ADMIN_DEFAULT_COMMISSION'))/ 100);
    }

    function getOrderReports($start_date, $end_date, $host_id = 0, $activity_id = 0, $is_cancel = 0) {
        $host_id = FatUtility::int($host_id);
        $activity_id = FatUtility::int($activity_id);
        $is_cancel = FatUtility::int($is_cancel);
        $srch = new SearchBase(Order::ORDER_TBL);
        $srch->joinTable('tbl_order_activities', 'inner join', 'order_id = oactivity_order_id');

        if ($host_id) {

            if ($host_id == -1) {
                $srch->joinTable(Activity::DB_TBL, 'inner join', 'oactivity_activity_id = activity_id');
                $srch->joinTable(User::DB_TBL, 'inner join', 'user_id = activity_user_id ');
            } else {
                $srch->joinTable(Activity::DB_TBL, 'inner join', 'oactivity_activity_id = activity_id and activity_user_id = ' . $host_id);
                $srch->joinTable(User::DB_TBL, 'inner join', 'user_id = activity_user_id ');
            }
        }

        $srch->addDirectCondition('order_date >= "' . $start_date . '"');
        $srch->addDirectCondition('order_date <= "' . $end_date . '"');
        $srch->addCondition('order_payment_status', '=', 1);
        if ($is_cancel > -1) {
            $srch->addCondition('oactivity_is_cancel', '=', 0);
        }
        if ($activity_id > 0) {
            $srch->addCondition('oactivity_activity_id', '=', $activity_id);
        }
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addMultipleFields(array(
            'oactivity_booking_id',
            'order_id',
            'order_date',
            'oactivity_members',
            'oactivity_unit_price',
            'oactivity_vat',
            'oactivity_donation',
            'oactivity_booking_amount',
            'oactivity_event_timing',
            'oactivity_refund_amount',
            'oactivity_is_cancel',
            'oactivity_activity_id',
            'oactivity_trans_fee',
        ));
        if ($host_id) {
            $srch->addFld('activity_user_id');
            $srch->addFld('user_firstname');
            $srch->addFld('user_lastname');
        }
        $srch->addOrder('order_date');
        $rs = $srch->getResultSet();
        //	echo $srch->getQuery();
        return FatApp::getDb()->fetchAll($rs);
    }

    function getRecordCount($start_date, $end_date, $host_id) {
        $records = array(
            'total_grand_records' => 0,
            'total_net_records' => 0,
            'total_grand_amount' => 0,
            'total_net_amount' => 0,
            'total_cancelled_booking' => 0,
            'trans_fee' => 0,
            'donation' => 0,
        );
        $rows = $this->getOrderReports($start_date, $end_date, $host_id);
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $records['total_grand_records'] ++;
                $records['total_grand_amount'] += $row['oactivity_booking_amount'];
                $records['trans_fee'] += $row['oactivity_trans_fee'];
                $records['donation'] += $row['oactivity_donation'];
                if ($row['oactivity_is_cancel'] == 0) {
                    $records['total_net_records'] ++;
                    $records['total_net_amount'] += $row['oactivity_booking_amount'];
                } else {
                    $records['total_cancelled_booking'] ++;
                }
            }
        }
        return $records;
    }

    function getReport($start_date, $end_date, $host_id = -1, $report_type = 1, $activity_id = 0) {
        $records = array();
        $rows = $this->getOrderReports($start_date, $end_date, $host_id, $activity_id, -1);
        //var_dump($rows);
        //var_dump($report_type);
        if (!empty($rows)) {

            if (in_array($report_type, array(1, 2, 3, 4))) {
                if ($report_type == 3) {
                    $act = new Activity();
                    $activities = $act->getActivitiesForForm($host_id);
                }
                foreach ($rows as $row) {

                    if ($report_type == 1) {
                        $key = date('Y-m-d', strtotime($row['order_date']));
                        $report_key = $key;
                    } elseif ($report_type == 2) {
                        $key = date('m', strtotime($row['order_date']));
                        $report_key = date('M, Y', strtotime($row['order_date']));
                    } elseif ($report_type == 3) {
                        $key = $row['oactivity_activity_id'];
                        $report_key = $activities[$key];
                    } elseif ($report_type == 4) {
                        $key = $row['activity_user_id'];
                        $report_key = $row['user_firstname'] . ' ' . $row['user_lastname'];
                    }

                    if (!array_key_exists($key, $records)) {
                        $records[$key] = array(
                            'total_grand_records' => 0,
                            'total_net_records' => 0,
                            'total_grand_amount' => 0,
                            'total_net_amount' => 0,
                            'total_cancelled_booking' => 0,
                            'total_cancelled_booking_amount' => 0,
                            'trans_fee' => 0,
                            'donation' => 0,
                            'report_key' => $report_key,
                            'date' => date('Y-m-d', strtotime($row['order_date']))
                        );
                    }
                    $records[$key]['total_grand_records'] ++;
                    $records[$key]['total_grand_amount'] += $row['oactivity_booking_amount'];
                    $records[$key]['donation'] += $row['oactivity_donation'];
                    $records[$key]['trans_fee'] += $row['oactivity_trans_fee'];
                    if ($row['oactivity_is_cancel'] == 0) {
                        $records[$key]['total_net_records'] ++;
                        $records[$key]['total_net_amount'] += $row['oactivity_booking_amount'];
                    } else {
                        $records[$key]['total_cancelled_booking'] ++;
                        $records[$key]['total_cancelled_booking_amount'] += $row['oactivity_booking_amount'];
                    }
                }
            }
        }
        return $records;
    }

    static function getReportTypes() {
        return array(
            1 => Info::t_lang('DATE_WISE'),
            2 => Info::t_lang('MONTH_WISE'),
            3 => Info::t_lang('ACTIVITIY_WISE'),
        );
    }

    function getUserCount() {
        $current_datetime = Info::currentDatetime();
        $current_month = date('m', strtotime($current_datetime));
        $current_year = date('Y', strtotime($current_datetime));
        $srch = new SearchBase(User::DB_TBL);
        $srch->addMultipleFields(array(
            'sum(if(month(user_regdate) = "' . $current_month . '" and year(user_regdate) = "' . $current_year . '" and user_type = 1,1,0)) as current_month_total_host',
            'sum(if(month(user_regdate) = "' . $current_month . '" and year(user_regdate) = "' . $current_year . '" and user_type = 0,1,0)) as current_month_total_traveler',
            'sum(if(user_type = 0,1,0)) total_traveler',
            'sum(if(user_type = 1,1,0)) total_host',
                )
        );
        $srch->addCondition('user_active', '=', 1);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetch($rs);
    }

    function getOrderCount() {
        $current_datetime = Info::currentDatetime();
        $current_month = date('m', strtotime($current_datetime));
        $current_year = date('Y', strtotime($current_datetime));
        $srch = new SearchBase('tbl_orders');
        $srch->joinTable('tbl_order_activities','INNER JOIN','oactivity_order_id=order_id');
        $srch->addMultipleFields(array(
            'sum(if(month(order_date) = "' . $current_month . '" and year(order_date) = "' . $current_year . '" ,1,0)) as current_month_total',
            'sum(if(month(order_date) = "' . $current_month . '" and year(order_date) = "' . $current_year . '" ,order_net_amount,0)) as current_month_sales',
            'count(*) total_orders',
            'sum(order_net_amount) total_sales',
            'sum(oactivity_admin_commission) as admin_commision',
            'sum(if(month(order_date) = "' . $current_month . '" and year(order_date) = "' . $current_year . '" ,oactivity_admin_commission,0)) as current_month_admin_commission',
                )
        );
        $srch->addCondition('order_payment_status', '=', 1);
 
        $srch->doNotCalculateRecords();
        
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetch($rs);
    }

    function getReviewCount() {
        $current_datetime = Info::currentDatetime();
        $current_month = date('m', strtotime($current_datetime));
        $current_year = date('Y', strtotime($current_datetime));
        $srch = new SearchBase('tbl_reviews');
        $srch->addMultipleFields(array(
            'sum(if(month(review_date) = "' . $current_month . '" and year(review_date) = "' . $current_year . '" ,1,0)) as current_month_total',
            'count(*) total_review',
                )
        );
        $srch->addCondition('review_active', '=', 1);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetch($rs);
    }

    function getHostTotalBooking($host_id = 0) {
        $current_date = Info::currentDate();

        $host_id = FatUtility::int($host_id);
        $srch = new SearchBase(Order::ORDER_TBL);
        $srch->joinTable('tbl_order_activities', 'inner join', 'order_id = oactivity_order_id');
        if ($host_id > 0) {
            $srch->joinTable(Activity::DB_TBL, 'inner join', 'oactivity_activity_id = activity_id and activity_user_id = ' . $host_id);
        }
        $srch->addMultipleFields(array(
            'sum(if(date(oactivity_event_timing) < "' . $current_date . '" and oactivity_is_cancel = 0, 1,0)) as total_complete_booking',
            'sum(if(date(oactivity_event_timing) < "' . $current_date . '" and oactivity_is_cancel = 0, oactivity_booking_amount,0)) as total_complete_booking_amount',
            'sum(if(date(oactivity_event_timing) >= "' . $current_date . '" and oactivity_is_cancel = 0, 1,0)) as total_pending_booking',
            'sum(if(date(oactivity_event_timing) >= "' . $current_date . '" and oactivity_is_cancel = 0, oactivity_booking_amount,0)) as total_pending_booking_amount',
            'sum(if(oactivity_is_cancel = 1,1,0)) as total_cancelled',
            'sum(if(oactivity_is_cancel = 1,oactivity_booking_amount,0)) as total_cancelled_amount',
        ));
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetch($rs);
    }

    function getActivtyCount() {

        $srch = new SearchBase(Activity::DB_TBL);
        $srch->addMultipleFields(array(
            'sum(if(activity_active = 1, 1, 0)) as   total_active',
            'count(*) as total'
        ));
        //$srch->addCondition(Activity::DB_TBL_PREFIX . 'confirm', '=', 1);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

}

?>