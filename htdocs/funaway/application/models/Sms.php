<?php

class Sms extends MyAppModel
{

    const DB_TBL = 'tbl_sms_templates';
    const DB_TBL_PREFIX = 'smstpl_';

    public function __construct($messageId = 0)
    {
        $messageId = FatUtility::convertToType($messageId, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $messageId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    public static function send($tpl_id, $phone, $replace_vars = array())
    {
        $sms = new Sms();
        $row = $sms->getAttributesById($tpl_id);
        if (empty($row)) {
            return false;
        }
        $msg = $row['smstpl_body'];
        $replace_vars['{site_name}'] = FatApp::getConfig('conf_website_name', FatUtility::VAR_STRING);
        if (!empty($replace_vars)) {
            foreach ($replace_vars as $key => $val) {
                $msg = str_replace($key, $val, $msg);
            }
        }
        if (empty($phone)) {
            return false;
        }
        $key = FatApp::getConfig('CONF_SMS_API_KEY');

        $secret = FatApp::getConfig('CONF_SMS_SECRET_KEY');

        /*
          qH2ZWR095EmwUbwd7TnflA==
          94b9b12d-3daf-4532-b754-6ef9a067ad2c
          pk.eyJ1IjoicnVwZW5kcmEiLCJhIjoiY2ltY3h1YzBiMDAzb3Vpa2tzaGc0YnFlYyJ9.rOq8FhdEGha82ob3NfhPog
          $phone_number = "+66833333603";
         */

        $user = "application\\" . $key . ":" . $secret;
        $message = array("message" => $msg);

        $data = json_encode($message);
        $ch = curl_init('https://messagingapi.sinch.com/v1/sms/' . $phone);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $user);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        if ($curl_errno || $http_status != 200) {
            return false;
        }
        $result = json_decode($result, true);
        if (isset($result['status']) && (strtolower($result['status']) == 'successful' || strtolower($result['status']) == 'pending')) {
            return true;
        }
        return false;
    }

    static function orderSmsToHost($order_id)
    {
        $ord = new Order();
        $srch = $ord->getOrderActivitySearch($order_id);
        $srch->joinTable(User::DB_TBL, 'inner join', User::DB_TBL_PREFIX . 'id = ' . Activity::DB_TBL_PREFIX . 'user_id');
        $srch->addFld('user_firstname');
        $srch->addFld('user_id');
        $srch->addFld('user_email');
        $srch->addFld('user_phone');
        $srch->addFld('user_phone_code');
        $srch->addFld('activity_name');
        $srch->addFld('oactivity_members');
        $srch->addFld('oactivity_booking_id');
        $rs = $srch->getResultSet();
        $order_acts = FatApp::getDb()->fetchAll($rs);
        if (!empty($order_acts)) {
            foreach ($order_acts as $act) {
                $replace_vars = array(
                    '{username}' => $act['user_firstname'],
                    '{activity_name}' => $act['activity_name'],
                    '{members}' => $act['oactivity_members'],
                    '{booking_id}' => $act['oactivity_booking_id'],
                );
                if (!empty($act['user_phone']) && !empty($act['user_phone_code'])) {
                    $phone = $act['user_phone_code'] . $act['user_phone'];
                    self::send(1, $phone, $replace_vars);
                }

                Email::sendMail($act['user_email'], 19, $replace_vars);
                $url = FatUtility::generateUrl('host', 'detail', array($act['oactivity_booking_id']), CONF_WEBROOT_URL);
                $text = Info::t_lang('NEW_BOOKING') . '-' . $act['oactivity_booking_id'];
                $notify = new Notification();
                $notify->notify($act['user_id'], 0, $url, $text);
            }
        }
    }

    static function paymentSuccessSmsToTraveler($order_id, $user_id)
    {
        $usr = new User();
        $user = $usr->getUserByUserId($user_id);
        $user_name = @$user['user_firstname'];

        $user_phone = $user['user_phone_code'] . $user['user_phone'];
        $user_email = @$user['user_email'];
        $replace_vars = array(
            '{username}' => $user_name,
            '{order_id}' => $order_id
        );
        if (!empty($user['user_phone'])) {
            self::send(2, $user_phone, $replace_vars);
        }
        Email::sendMail($user_email, 20, $replace_vars);
        $url = FatUtility::generateUrl('traveler', 'detail', array($order_id), CONF_WEBROOT_URL);
        $text = Info::t_lang('PAYMENT_SUCCESSFULL') . '-' . $order_id;
        $notify = new Notification();
        $notify->notify($user_id, 0, $url, $text);
    }

    static function requestUpdateSmsToTraveler($request_id)
    {
        $usr = new User();
        $er = new EventRequest();
        $act = new Activity();

        $request = $er->getEventRequestById($request_id);
        $user = $usr->getUserByUserId($request['requestevent_requested_by']);
        $activity = $act->getActivity($request['requestevent_activity_id'], -1);
        $user_name = @$user['user_firstname'] . ' ' . $user['user_lastname'];
        $user_id = $request['requestevent_requested_by'];
        $user_phone = $user['user_phone_code'] . $user['user_phone'];
        $user_email = @$user['user_email'];

        //*[************* Addedby 0142 **********/
        $activityLocation = '';
        $hostUser = $usr->getUserByUserId($activity['activity_user_id']);
      
        $hostFullName = @$hostUser['user_firstname'] . ' ' . @$hostUser['user_lastname'];
        $hostPhoneNumber = @$hostUser['user_phone'];
        $hostEmailId = @$hostUser['user_email'];
        
        $activityCityRow = City::getAttributesById($activity['activity_city_id'], array('city_name', 'city_country_id'));
        $activityCityName = $activityCityRow['city_name'];
        $activityCountryRow = Country::getAttributesById($activityCityRow['city_country_id'], array('country_name', 'country_region_id'));
        $activityCountryName = $activityCountryRow['country_name'];
        $activityRegionName = current(Region::getAttributesById($activityCountryRow['country_region_id'], array('region_name')));
        $activityLocation = $activityRegionName . ',' . $activityCountryName . ',' . $activityCityName;
        /* end * * ]*** */

        $replace_vars = array(
            '{username}' => $user_name,
            '{status}' => Info::getRequestStatusByKey($request['requestevent_status']),
            '{activity_name}' => $activity['activity_name'],
            '{request_date}' => FatDate::format($request['requestevent_date']),
            '{host_name}' => $hostFullName,
            '{host_email_id}' => $hostEmailId,
            '{host_contact_details}' => $hostPhoneNumber,
            '{activity_location}' => $activityLocation,
        );

        /*if (!empty($user['user_phone']) && !empty($user['user_phone_code'])) {
            self::send(5, $user_phone, $replace_vars);
        }*/
        
        Email::sendMail($user_email, 32, $replace_vars);
        $url = FatUtility::generateUrl('traveler', 'request', array(), CONF_WEBROOT_URL);
        $text = Info::t_lang('Your Request For Activity ') . '-' . $activity['activity_name'] . Info::t_lang('has been ') . Info::getRequestStatusByKey($request['requestevent_status']);
        $notify = new Notification();
        $notify->notify($user_id, 0, $url, $text);
    }

    static function requestConfirmationSmsToHost($request_id)
    {
        $request_id = FatUtility::int($request_id);
        if ($request_id <= 0)
            return false;
        $usr = new User();
        $er = new EventRequest();
        $act = new Activity();

        $request = $er->getEventRequestById($request_id);
        $activity = $act->getActivity($request['requestevent_activity_id'], -1);
        $user = $usr->getUserByUserId($activity['activity_user_id']);
        $traveler = $usr->getUserByUserId($request['requestevent_requested_by']);

        $user_name = @$user['user_firstname'] . ' ' . $user['user_lastname'];
        $traveler_name = @$traveler['user_firstname'] . ' ' . $traveler['user_lastname'];
        $user_id = $user['user_id'];
        $user_phone = $user['user_phone_code'] . $user['user_phone'];
        $user_email = @$user['user_email'];
        $replace_vars = array(
            '{username}' => $user_name,
            '{activity_name}' => $activity['activity_name'],
            '{request_by}' => $traveler_name,
            '{request_date}' => FatDate::format($request['requestevent_date']),
        );
        if (!empty($user['user_phone']) && !empty($user['user_phone_code'])) {
            self::send(6, $user_phone, $replace_vars);
        }
        Email::sendMail($user_email, 33, $replace_vars);
        $url = FatUtility::generateUrl('host', 'request', array(), CONF_WEBROOT_URL);
        $text = Info::t_lang('New Confirmation Request For Activity ') . '-' . $activity['activity_name'] . Info::t_lang(' has been placed');
        $notify = new Notification();
        $notify->notify($user_id, 0, $url, $text);
    }

    static function sendActivityUpdateNotification($activity_id)
    {
        $srch = new SearchBase(Order::ORDER_EVENT_TBL);
        $srch->joinTable(Order::ORDER_TBL, 'inner join', 'order_id = oactivity_order_id and order_payment_status = 1');
        $srch->joinTable(User::DB_TBL, 'inner join', 'order_user_id = user_id and user_active = 1');
        $srch->addDirectCondition('oactivity_event_timing > \'' . Info::currentDatetime() . '\'');
        $srch->addCondition('oactivity_activity_id', '=', $activity_id);
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addFld('oactivity_order_id');
        $srch->addFld('user_firstname');
        $srch->addFld('user_phone_code');
        $srch->addFld('user_phone');
        $srch->addFld('user_email');
        $srch->addFld('user_id');
        $srch->addFld('oactivity_activity_name as activity_name');
        $srch->addGroupBy('user_id');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $replace_vars = array(
                    '{username}' => $row['user_firstname'],
                    '{activity_name}' => $row['activity_name'],
                );
                if (!empty($row['user_phone'])) {
                    self::send(1, $row['user_phone_code'] . $row['user_phone'], $replace_vars);
                }
                $url = FatUtility::generateUrl('traveler', 'detail', array($row['oactivity_order_id']), CONF_WEBROOT_URL);
                $text = Info::t_lang('ACIVITY_UPDATED_BY_HOST') . '-' . $row['activity_name'];
                $notify = new Notification();
                $notify->notify($row['user_id'], 0, $url, $text);
                Email::sendMail($row['user_email'], 21, $replace_vars);
            }
        }
    }

    static function sendActivityEventUpdateNotification($activity_id, $event_id, $new_changes, $old_changes, $event_bookings)
    {
        $activity_id = FatUtility::int($activity_id);
        $event_id = FatUtility::int($event_id);
        if ($activity_id <= 0 || $event_id <= 0 || empty($new_changes) || empty($old_changes) || empty($event_bookings)) {
            return false;
        }
        $act = new Activity();
        if (isset($new_changes['activityevent_time']) && isset($old_changes['activityevent_time']) && $old_changes['activityevent_time'] != $new_changes['activityevent_time']) {
            $activity = $act->getActivity($activity_id, -1);
            $activity_name = $activity['activity_name'];
            foreach ($event_bookings as $event_booking) {
                $replace_vars = array(
                    '{username}' => $event_booking['user_firstname'] . ' ' . $event_booking['user_lastname'],
                    '{activity_name}' => $activity_name,
                    '{old_time}' => FatDate::format($old_changes['activityevent_time'], true),
                    '{new_time}' => FatDate::format($new_changes['activityevent_time'], true),
                    '{booking_id}' => $event_booking['oactivity_booking_id'],
                    '{order_id}' => $event_booking['order_id'],
                );
                if (!empty($event_booking['user_phone'])) {
                    self::send(4, $event_booking['user_phone_code'] . $event_booking['user_phone'], $replace_vars);
                }
                $url = FatUtility::generateUrl('traveler', 'detail', array($event_booking['order_id']), CONF_WEBROOT_URL);
                $text = Info::t_lang('ACIVITY_EVENT_TIME_UPDATED_BY_HOST_,ACTIVITY_NAME_:') . $activity_name . ' ' . Info::t_lang(',_BOOKING_ID_:_') . $event_booking['oactivity_booking_id'];
                $notify = new Notification();
                $notify->notify($event_booking['user_id'], 0, $url, $text);
                Email::sendMail($event_booking['user_email'], 31, $replace_vars);
            }
        }
    }

}
