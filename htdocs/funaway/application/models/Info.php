<?php

class Info extends FatModel
{

    function __construct()
    {
        parent::__construct();
    }

    /* static function t_lang($key)
      {
      $modAPCEnabled = false;
      $memcacheSupported = false;

      if(true === CacheAPC::isSupported()) {
      $modAPCEnabled = true;
      $objApcCache = new CacheAPC();
      $apcKey = $objApcCache->apcSitePrefix . $key;
      if(true === $modAPCEnabled && $objApcCache->isCacheKeyExists($apcKey)) {
      return $objApcCache->get($apcKey);
      }
      } else if(true === CacheMemcache::isSupported()) {
      $memcacheSupported = true;
      $objMemcache = new CacheMemcache();
      $cachedValue = $objMemcache->get($key);
      if(false !== $cachedValue) {
      return $cachedValue;
      }
      } else {
      global $arr_lang_vals;
      if (isset($arr_lang_vals[$key])) {
      return $arr_lang_vals[$key];
      }
      }

      $db = FatApp::getDb();

      $srch = new SearchBase('tbl_translations');

      $srch->addCondition('trans_key', 'LIKE', $key);
      $srch->addFld('trans_val as lang_val');

      $srch->doNotCalculateRecords();
      $srch->doNotLimitRecords();

      $rs = $srch->getResultSet();

      if ($row = $db->fetch($rs)) {
      $str = $row['lang_val'];
      } else {
      $str = ucwords(str_replace('_', ' ', strtolower($key)));
      $db->InsertFromArray('tbl_translations', array(
      'trans_key' => $key,
      'trans_val' => $str
      ));
      }

      if(true === $modAPCEnabled) {
      $objApcCache->set($apcKey, $str);
      } else if (true === $memcacheSupported) {
      $objMemcache->set($key, $str);
      } else {
      $arr_lang_vals[$key] = $str;
      }

      return $str;
      } */

    static function t_lang($key)
    {

        $cacheKey = $_SERVER['SERVER_NAME'] . '_' . $key;

        $cacheAvailable = extension_loaded('apcu') && ini_get('apcu.enabled');

        if ($cacheAvailable) {
            if (apcu_exists($cacheKey)) {
                return apcu_fetch($cacheKey);
            }
        } else {
            global $arr_lang_vals;
            if (isset($arr_lang_vals[$key])) {
                return $arr_lang_vals[$key];
            }
        }

        $db = FatApp::getDb();

        $srch = new SearchBase('tbl_translations');
        $srch->addCondition('trans_key', '=', $key);
        $srch->addFld('trans_val as lang_val');

        $rs = $srch->getResultSet();

        if ($row = $db->fetch($rs)) {
            $str = $row['lang_val'];
        } else {
            $str = ucwords(str_replace('_', ' ', strtolower($key)));
            $db->InsertFromArray('tbl_translations', array(
                'trans_key' => $key,
                'trans_val' => $str
            ));
        }

        if ($cacheAvailable) {
            apcu_store($cacheKey, $str);
        } else {
            global $arr_lang_vals;
            $arr_lang_vals[$key] = $str;
        }

        return $str;
    }

    static function getFileApprovedStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('APPROVED'),
            2 => self::t_lang('DECLINED'),
        );
    }

    static function getFileApprovedStatusByKey($key)
    {
        $ar = self::getFileApprovedStatus();
        return $ar[$key];
    }

    static function getRequestStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('APPROVED'),
            2 => self::t_lang('DECLINED'),
        );
    }

    static function getRequestStatusByKey($key)
    {
        $ar = self::getRequestStatus();
        return $ar[$key];
    }

    static function getVideoStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('APPROVED'),
            2 => self::t_lang('DECLINED'),
        );
    }

    static function getVideoStatusByKey($key)
    {
        $ar = self::getVideoStatus();
        return $ar[$key];
    }

    static function getStatus()
    {
        return array("1" => Info::t_lang("ACTIVE"), "0" => Info::t_lang("INACTIVE"));
    }

    static function getSortBy()
    {
        return array("price" => Info::t_lang("PRICE"), "duration" => Info::t_lang("DURATION"), "popular" => Info::t_lang("POPULAR"));
    }

    static function getStatusByKey($key)
    {
        $status = Info::getStatus();
        return $status[$key];
    }

    static function getReviewStatus()
    {
        return array("1" => Info::t_lang("ACTIVE"), "0" => Info::t_lang("INACTIVE"), "2" => Info::t_lang("DECLINED"), "3" => Info::t_lang("BLOCKED"));
    }

    static function getReviewStatusByKey($key)
    {
        $ar = Info::getReviewStatus();
        return $ar[$key];
    }

    static function getAbuseReportStatus()
    {
        return array("0" => Info::t_lang("PENDING"), "1" => Info::t_lang("APPROVED"), "2" => Info::t_lang("DECLINE"));
    }

    static function getAbuseReportStatusByKey($key)
    {
        $ar = Info::getAbuseReportStatus();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getLocationStatus()
    {
        return array("1" => Info::t_lang("ACTIVE"), "0" => Info::t_lang("INACTIVE"), "2" => Info::t_lang("DELETE"));
    }

    static function getDiscount()
    {
        return array("1" => "Percentage", "0" => "Fixed");
    }

    static function getLocationStatusByKey($key)
    {
        $status = Info::getLocationStatus();
        return $status[$key];
    }

    static function getCmsPositions($key = '')
    {
        $positions = array(Cms::CMS_BROWSE_POSITION_TYPE => 'Footer Browser', Cms::CMS_ABOUT_POSITION_TYPE => 'Footer About');
        if (!empty($key)) {
            return $positions[$key];
        }
        return $positions;
    }

    static function getSearchUserStatus()
    {
        return array("0" => Info::t_lang('Inactive'), "1" => Info::t_lang('Active'), "2" => Info::t_lang('Declined'), '3' => Info::t_lang('Pending'));
    }

    static function getSearchUserStatusByKey($key)
    {
        $status = Info::getSearchUserStatus();
        return $status[$key];
    }

    static function getUserConfirm()
    {
        return array(0 => Info::t_lang('Pending'), 1 => Info::t_lang('Confirmed'));
    }

    static function getUserConfirmByKey($key)
    {
        $c = Info::getUserConfirm();
        return $c[$key];
    }

    static function getActivityConfirmStatus()
    {
        return array(0 => Info::t_lang('PENDING'), 1 => Info::t_lang('CONFIRMED'), 2 => Info::t_lang('DECLINED'));
    }

    static function getUserRequestConfirmStatus()
    {
        return array(0 => Info::t_lang('PENDING'), 1 => Info::t_lang('CONFIRMED'), 2 => Info::t_lang('DECLINED'));
    }

    static function getActivityConfirmStatusByKey($key)
    {
        $c = Info::getActivityConfirmStatus();
        return $c[$key];
    }

    static function getEmailStatus()
    {
        return array("0" => 'Pending', "1" => 'Verified');
    }

    static function getEmailStatusByKey($key)
    {
        $status = Info::getEmailStatus();
        return $status[$key];
    }

    static function generateKey($id)
    {
        $key = (4253 + $id) * 3;
        return "#GYOL" . $key . "AR";
    }

    static function getIdFromKey($key)
    {
        $key = str_replace("#GYOL", "", $key);
        $key = str_replace("AR", "", $key);
        $key = intval($key);
        $id = ($key / 3) - 4253;
        return $id;
    }

    static function getIs()
    {
        return array("1" => Info::t_lang("YES"), "0" => Info::t_lang("NO"));
    }

    static function getIsValue($key)
    {
        $arr = Info::getIs();
        return $arr[$key];
    }

    static function getApprovalType()
    {
        return array("0" => Info::t_lang("PENDING"), "1" => Info::t_lang("APPROVED"), "2" => Info::t_lang("REJECTED"));
    }

    static function getApprovalTypeByKey($key)
    {
        $arr = Info::getApprovalType();
        return $arr["" . $key];
    }

    static function paymentStatus()
    {
        return array(0 => Info::t_lang("UNPAID"), 1 => info::t_lang("PAID"));
    }

    static function getPaymentStatus($key)
    {
        $status = Info::paymentStatus();
        return $status[$key];
    }

    static function orderStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('IN_PROCESS'),
            2 => self::t_lang('COMPLETED'),
        );
    }

    static function getOrderStatus($key)
    {
        $status = Info::orderStatus();
        return $status[$key];
    }

    static function getOrderCancelStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('CANCELLED'),
        );
    }

    static function getOrderCancelStatusByKey($key)
    {
        $status = Info::getOrderCancelStatus();
        return $status[$key];
    }

    static function paymentType()
    {
        return array(
            "0" => 'Subscription',
            "2" => 'Event',
        );
    }

    static function getUserType()
    {
        return array(
            "0" => 'Traveler',
            "1" => 'Host',
        );
    }

    static function getUserTypeByKey($key)
    {
        $type = Info::getUserType();
        return $type[$key];
    }

    static function getPaymentType($key)
    {
        $status = Info::paymentType();
        return $status[$key];
    }

    static function orderViewStatus()
    {
        return array("0" => '<span class="status yellow">' . Info::t_lang("PENDING") . '</span>', "1" => '<span class="status yellow">' . Info::t_lang("IN_PROCESS") . '</span>', "2" => '<span class="status grey">' . Info::t_lang("SHIPPED") . '</span>', "3" => '<span class="status green">' . Info::t_lang("DELIVERED") . '</span>', "4" => '<span class="status green">' . Info::t_lang("Cancelled") . '</span>');
    }

    static function getOrderViewStatus($key)
    {
        $status = Info::orderViewStatus();
        return $status[$key];
    }

    static function getSex()
    {
        return array("1" => Info::t_lang("MALE"), "2" => Info::t_lang("FEMALE"));
    }

    static function getSexValue($key)
    {
        if (empty($key))
            return;
        $arr = Info::getSex();
        return $arr[$key];
    }

    static function sendCMSMail($subject, $content, $text, $filename = "", $path = "")
    {
        $mail = "false";
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/";
        $body = file_get_contents(CONF_INSTALLATION_PATH . '/public/mail-template/cms.html');
        $body = str_replace('{url}', $url, $body);
        $body = str_replace('{msg}', $text, $body);
        $body = str_replace('{content}', $content, $body);
        $body = str_replace('{fb}', CONF_FACEBOOK_URL, $body);
        $body = str_replace('{ln}', CONF_LINKEDIN_URL, $body);
        $body = str_replace('{yt}', CONF_YOUTUBE_URL, $body);
        if ($filename != "") {
            mail_attachment($filename, $path, CONF_ADMIN_EMAIL_ID, $subject, $body);
        } else {
            sendMail(CONF_ADMIN_EMAIL_ID, $subject, $body);
        }
    }

    static function sendAdminMail($subject, $text)
    {
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/";
        $body = file_get_contents(CONF_INSTALLATION_PATH . '/public/mail-template/admin.html');
        $body = str_replace('{url}', $url, $body);
        $body = str_replace('{msg}', $text, $body);
        $body = str_replace('{fb}', CONF_FACEBOOK_URL, $body);
        $body = str_replace('{ln}', CONF_LINKEDIN_URL, $body);
        $body = str_replace('{yt}', CONF_YOUTUBE_URL, $body);
        sendMail(CONF_ADMIN_EMAIL_ID, $subject, $body);
    }

    static function getPackagePlan()
    {
        return array("0" => t_lang("INDIVIDUAL"), "1" => t_lang("CORPORATE"));
    }

    static function paymentModes()
    {
        $modes = array("omise" => 1, "admin" => 0, 'paypal' => 2, 'payuindia' => 4, 'ccavenue' => 3);
        return $modes;
    }

    static function getPaymentmode($key)
    {
        $modes = Info::paymentModes();
        return $modes[$key];
    }

    static function getPaymentmodeKey($value)
    {
        $modes = Info::paymentModes();
        return array_search($value, $modes);
    }

    static function hours()
    {
        return array(
            "0" => '00',
            "1" => '01',
            "2" => '02',
            "3" => '03',
            "4" => '04',
            "5" => '05',
            "6" => '06',
            "7" => '07',
            "8" => '08',
            "9" => '09',
            "10" => '10',
            "11" => '11',
            "12" => '12',
            "13" => '13',
            "14" => '14',
            "15" => '15',
            "16" => '16',
            "17" => '17',
            "18" => '18',
            "19" => '19',
            "20" => '20',
            "21" => '21',
            "22" => '22',
            "23" => '23',
        );
    }

    static function getHour($key)
    {
        $hour = Info::hours();
        return $hour[$key];
    }

    static function minutes()
    {
        return array("0" => '00',
            "5" => '05',
            "10" => '10',
            "15" => '15',
            "20" => '20',
            "25" => '25',
            "30" => '30',
            "35" => '35',
            "40" => '40',
            "45" => '45',
            "50" => '50',
            "55" => '55',
        );
    }

    static function getMinutes($key)
    {
        $minute = Info::minutes();
        return $minute[$key];
    }

    static function timeFormat()
    {
        return array(0 => Info::t_lang('AM'), 1 => Info::t_lang('PM'));
    }

    static function timeMeridiem()
    {
        return array(Info::t_lang('AM') => Info::t_lang('AM'), Info::t_lang('PM') => Info::t_lang('PM'));
    }

    static function getTimeformatByKey($key)
    {
        $ar = self::timeFormat();
        return $ar[$key];
    }

    static function getTimeFormat($key)
    {
        $timeFormat = Info::timeFormat();
        return $timeFormat[$key];
    }

    static function weekdays()
    {
        return array(
            "1" => Info::t_lang("MONDAY"),
            "2" => Info::t_lang("TUESDAY"),
            "3" => Info::t_lang("WEDNESDAY"),
            "4" => Info::t_lang("THURSDAY"),
            "5" => Info::t_lang("FRIDAY"),
            "6" => Info::t_lang("SATURDAY"),
            "0" => Info::t_lang("SUNDAY"),
        );
    }

    static function getWeekdays($key)
    {
        $weekdays = Info::weekdays();
        return $weekdays[$key];
    }

    function distance()
    {
        $arr = array(
            "1" => "1",
            "2" => "5",
            "3" => "10",
            "4" => "25",
            "5" => "50+ miles",
        );
        return $arr;
    }

    function getDistance($key)
    {
        $arr = Info::distance();
        return $arr[$key];
    }

    static function getDefaultLang()
    {
        return Info::t_lang("ENGLISH");
    }

    function getNotifyMsg($key, $lang_id)
    {
        $arr = array(
            "offer" => array(1 => "updated its Offer.", 2 => "updated its Offer."),
            "price" => array(1 => "updated its Price.", 2 => "updated its Price."),
            "gym" => array(1 => "updated its Gym Detail.", 2 => "updated its Gym Detail."),
            "redeem_point_used" => array(1 => "Points Have been deducted from your account", 2 => "Points Have been deducted from your account"),
            "redeem_point_gaind" => array(1 => "Points Have been added in your account.", 2 => "Points Have been added in your account."),
            "try_it_generated" => array(1 => "New Try It Pass Generated", 2 => "New Try It Pass Generated"),
            "try_it_marked" => array(1 => "Try It pass marked as used.", 2 => "Try It pass marked as used."),
            "buy_it_generated" => array(1 => "New Buy It Pass Generated", 2 => "New Buy It Pass Generated"),
            "buy_it_marked" => array(1 => "Buy It pass marked as used.", 2 => "Buy It pass marked as used."),
            "product_processed" => array(1 => "Product is in process.", 2 => "Product is in process."),
            "product_delivered" => array(1 => "Product delivered.", 2 => "Product delivered."),
        );
        return $arr[$key][$lang_id];
    }

    static function month()
    {
        return array(
            "01" => t_lang("JAN"),
            "02" => t_lang("FEB"),
            "03" => t_lang("MAR"),
            "04" => t_lang("APR"),
            "05" => t_lang("MAY"),
            "06" => t_lang("JUN"),
            "07" => t_lang("JUL"),
            "08" => t_lang("AUG"),
            "09" => t_lang("SEP"),
            "10" => t_lang("OCT"),
            "11" => t_lang("NOV"),
            "12" => t_lang("DEC"),
        );
    }

    static function getRangeInArray($start, $end, $interval = 1)
    {
        return array_combine(range($start, $end, $interval), range($start, $end, $interval));
    }

    static function getServes($key)
    {
        $arr = Info::serves();
        return $arr[$key];
    }

    /* static function getSlugFromName($title){
      $title = strtolower($title);
      //return urlencode($title);
      return preg_replace("/[\s]/", "-", $title);
      } */

    static function getSlugFromName($title)
    {
        $title = strtolower($title);
        //return urlencode($title);
        return preg_replace("/[^A-Za-z0-9-]+/", "-", $title);
    }

    static function getNameFromSlug($slug)
    {
        //return urldecode($slug);
        return preg_replace("/[-]/", " ", $slug);
    }

    static function side()
    {
        return array(1 => Info::t_lang("LEFT"), 2 => Info::t_lang("RIGHT"));
    }

    static function getSideByKey($key)
    {
        $arr = Info::side();
        return $arr[$key];
    }

    static function currentDate()
    {
        // return date("Y-m-d");
        return FatDate::nowInTimezone(FatApp::getConfig('conf_timezone'), 'Y-m-d');
    }

    static function addDaysWithDate($date, $days)
    {
        $date = strtotime("+" . $days . " days", strtotime($date));
        return date("Y-m-d", $date);
    }

    static function currentDatetime()
    {
        // return date('Y-m-d H:i:s');
        return FatDate::nowInTimezone(FatApp::getConfig('conf_timezone'), 'Y-m-d H:i:s');
    }

    public static function sysCurrentDateTime($dateFormat = null, $dateTime = true, $timeFormat = null, $displayTimeZone = false, $timeZone = null)
    {
        if ($timeZone == null) {
            $timeZone = FatApp::getConfig('conf_timezone', FatUtility::VAR_STRING, date_default_timezone_get());
        }

        if ($dateFormat == null) {
            $dateFormat = FatApp::getConfig('conf_date_format_php', FatUtility::VAR_STRING, 'Y-m-d');
        }

        if ($dateTime) {
            if ($timeFormat == null) {
                $timeFormat = FatApp::getConfig('conf_date_format_time', FatUtility::VAR_STRING, 'H:i:s');
            }
        }

        $format = $dateFormat . ' ' . $timeFormat;

        if (true == $displayTimeZone) {
            $format = $format . ' (T P)';
        }

        return FatDate::nowInTimezone($timeZone, trim($format));
    }

    static function currentYear()
    {
        $currentDatetime = Info::currentDatetime();
        return date('Y', strtotime($currentDatetime));
    }

    static function currentMonth()
    {
        $currentDatetime = Info::currentDatetime();
        return date('m', strtotime($currentDatetime));
    }

    static function currentTime()
    {
        $datetime = Info::currentDatetime();
        return date('H:i:s', strtotime($datetime));
    }

    static function getDayNumber($current_datetime = '')
    {
        if (empty($current_datetime))
            $current_datetime = self::currentDatetime();
        return date('N', strtotime($current_datetime));
    }

    static function test($arr)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }

    static function timestamp()
    {
        $dt = new DateTime();
        return $dt->getTimestamp();
    }

    public static function escapeStringAndAddQuote($string)
    {
        $db = FatApp::getDb();
        if (method_exists($db, 'quoteVariable'))
            return $db->quoteVariable($string);
        else
            return "'" . mysql_real_escape_string($string) . "'";
    }

    public static function escapeString($string)
    {
        return trim(self::escapeStringAndAddQuote($string), "'");
    }

    static function price($price)
    {
        $price = abs($price);
        $sign = FatApp::getConfig('conf_currency_symbol');
        if (FatApp::getConfig('conf_price_location') == 0) {
            return $sign . " " . $price;
        }
        return $price . " " . $sign;
    }

    static function createWLTransacatKey($id)
    {
        return "WL_" . $id . "_" . Info::timestamp();
    }

    static function getTransactKeyFromWL($key)
    {
        $display = explode('_', $key);
        if ($display[0] == "WL")
            return $display[1];
    }

    static function converToTime($hour, $min, $format)
    {

        if ($format == '') {
            $format = 'AM';
        } elseif ($format == '1') {
            $format = 'PM';
        }

        return date('H:i:s', strtotime($hour . ':' . $min . ":00" . ' ' . $format));
    }

    static function converShowToDbTime($time, $meridiem)
    {
        $time_array = explode(':', $time);
        return Info::converToTime($time_array[0], $time_array[1], $meridiem);
    }

    static function getTimingOptionsForForm()
    {
        $hours_options = array();
        $hours_options[-1] = Info::t_lang('NOT_AVAILABLE');
        for ($i = 0; $i <= 11; $i++) {
            $h = $i;
            if ($i < 9) {
                $h = '0' . $i;
            }
            $s = $h;
            if ($h == '00') {
                $s = 12;
            }
            $hours_options[$s . ':00'] = $s . ':00';
            $hours_options[$s . ':30'] = $s . ':30';
        }

        return $hours_options;
    }

    static function convertDbTimeTOShowTime($db_time)
    {
        $time_array = Info:: timeToArray($db_time);
        return array(
            'time' => $time_array['hour'] . ':' . $time_array['min'],
            'meridiem' => Info::getTimeFormat($time_array['meridiem'])
        );
    }

    static function timeToArray($time)
    {
        $t = date('g:i:A', strtotime($time));

        $t2 = explode(':', $t);

        if ($t2[0] < 9) {
            $t2[0] = '0' . $t2[0];
        }
        if ($t2[2] == 'PM') {
            $t2[2] = 1;
        } else {
            $t2[2] = 0;
        }
        return array('hour' => $t2[0], 'min' => $t2[1], 'meridiem' => $t2[2]);
    }

    static function getCouponType()
    {
        return array(
            0 => Info::t_lang('SITE_SPECIFIC'),
            //1=>Info::t_lang('RESTAURANT_SPECIFIC'),
            //2=>Info::t_lang('PRODUCT_SPECIFIC'),
            3 => Info::t_lang('CITY_SPECIFIC'),
        );
    }

    static function getCouponTypeByKey($key)
    {
        $ar = Info::getCouponType();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getCouponUseType()
    {
        return array(
            1 => Info::t_lang('SINGLE_USE'),
            2 => Info::t_lang('MULTI_USE'),
        );
    }

    static function getCouponUseTypeByKey($key)
    {
        $ar = Info::getCouponUseType();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getCouponDiscountType()
    {
        return array(
            0 => Info::t_lang('FIXED'),
            1 => Info::t_lang('PERCENTAGE'),
        );
    }

    static function getCouponDiscountTypeByKey($key)
    {
        $ar = Info::getCouponDiscountType();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getUserController()
    {
        if (UserAuthentication::isUserLogged()) {
            $user_type = UserAuthentication::getUserSession('user_is_merchant');
            if ($user_type == 0) {
                return 'member';
            } elseif ($user_type == 1) {
                return 'merchant';
            } elseif ($user_type == 2) {
                return 'restaurant';
            }
        }
    }

    static function getCostList()
    {
        $costList = array(
            '1-100' => Info::price(1) . " - " . Info::price(100),
            '101-200' => Info::price(101) . " - " . Info::price(200),
            '201-300' => Info::price(201) . " - " . Info::price(300),
            '301-400' => Info::price(301) . " - " . Info::price(400),
            '401-500' => Info::price(401) . " - " . Info::price(500),
            '501->' => Info::price(501) . " - " . Info::t_lang("MORE"),
        );
        return $costList;
    }

    function transactionType()
    {
        return array(
            1 => Info::t_lang('WALLET'),
            2 => Info::t_lang('PAYPAL'),
            3 => Info::t_lang('CAHS_ON_DELIVERY'),
            4 => Info::t_lang('ADMIN'),
        );
    }

    static function getShipTypeByKey($key)
    {
        $ar = Info::getShipType();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getPaymentMethod()
    {
        return array(
            0 => Info::t_lang('Omise'),
            1 => Info::t_lang('ADMIN')
        );
    }

    static function deliveryHour()
    {
        return array(
            '00:30:00' => '00:30',
            '01:00:00' => '01:00',
            '01:30:00' => '01:30',
            '02:00:00' => '02:00',
            '02:30:00' => '02:30',
            '03:00:00' => '03:00',
            '03:30:00' => '03:30',
            '04:00:00' => '04:00',
            '04:30:00' => '04:30',
            '05:00:00' => '05:00',
            '05:30:00' => '05:30',
            '06:00:00' => '06:00',
            '06:30:00' => '06:30',
            '07:00:00' => '07:00',
            '07:30:00' => '07:30',
            '08:00:00' => '08:00',
        );
    }

    static function getPaymentMethodByKey($key)
    {
        $ar = Info::getPaymentMethod();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getProductSizesByKey($key)
    {
        $ar = Info::getProductSizes();
        return $ar[$key];
    }

    static function rating($rate, $edit = false, $class = 'rating--small')
    {
        $rate = $rate * 100 / 5;
        $styleProperty = "";
        /* if($rate == 0){
          $nrate = "<span class='no-rate'>".Info::t_lang("NO_RATING")."</span>";
          } */

        $toMakeFiftyPercentStarArr = array(10, 30, 50, 70, 90);
        if (in_array($rate, $toMakeFiftyPercentStarArr)) {
            $styleProperty = ' left: 1px; ';
        }

        if ($edit) {
            $class .= ' crating';
        }
        $rating_html = '<div class="rating ' . $class . '">
                        <div class="rating__overlay" ></div>
                        <div class="rating__score" style="width:' . $rate . '%; ' . $styleProperty . ' "></div>
                        <div class="rating__bg"></div>
			</div>';

        return $rating_html;
    }

    static function getReviewEntityType()
    {
        return array(
            1 => Info::t_lang('RESTAURANT')
        );
    }

    static function abuseReportType()
    {
        return array(
            1 => Info::t_lang('RESTAURANT')
        );
    }

    static function getReviewEntityTypeByKey($key)
    {
        $ar = Info::getReviewEntityType();
        return isset($ar[$key]) ? $ar[$key] : '';
    }

    static function getTimeDifference($datetime)
    {
        $timestamp = strtotime($datetime);
        $current_datetime = info::currentDatetime();
        $diff = FatDate::diff($datetime, $current_datetime);
        $current_timestamp = strtotime($current_datetime);
        if ($diff != 0) {
            return $diff . ' ' . Info::t_lang('DAYS_AGO');
        }
        $diff = $current_timestamp - $timestamp;

        $hour = FatUtility::int(($diff / (60 * 60)));
        if ($hour != 0) {
            return $hour . ' ' . Info::t_lang('HOUR_AGO');
        }
        $min = FatUtility::int($diff / 60);
        if ($min != 0) {
            return $min . ' ' . Info::t_lang('MINUTES_AGO');
        }

        return $diff . ' ' . Info::t_lang('SECOND_AGO');
    }

    static function removeUnderScoreFromText($str)
    {
        $str = ucwords(str_replace('_', ' ', $str));
        return $str;
    }

    static function copyImageByPath($path)
    {
        $size = getimagesize($path);
        if ($size === false) {
            return false;
        }

        $fname = "";

        while (file_exists(CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)) {
            $fname .= '_' . rand(10, 99);
        }
        if (!copy($path, CONF_INSTALLATION_PATH . 'user-uploads/' . $fname)) {
            return false;
        }
        return $fname;
    }

    static function getRandomPassword($n)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = '';
        for ($i = 0; $i < $n; $i++) {
            $pass .= substr($chars, rand(0, strlen($chars) - 1), 1);
        }
        return $pass;
    }

    static function getGoogleCode()
    {
        $param = FatApp::getQueryStringData();
        if (empty($param['code']))
            return false;
        return $param['code'];
    }

    static function getFileType($key)
    {
        $images = array("island" => 1);
        return $images[$key];
    }

    static function getSubHeader()
    {

        $submenu = array(
            "message" => array(
                array("contoller" => "message", "action" => 'index'),
                array("contoller" => "notification", "action" => 'index'),
                array("contoller" => "review", "action" => 'index'),
            ),
            "host" => array(
                array("contoller" => "activity", "action" => 'index'),
                array("contoller" => "activity", "action" => 'manage'),
                array("contoller" => "report", "action" => 'index'),
            ),
            "profile" => array(
                array("contoller" => "user", "action" => 'profile'),
                array("contoller" => "activity", "action" => 'manage'),
                array("contoller" => "report", "action" => 'index'),
            ),
        );
        $submenu1 = array('', '', '', '', '', '', '');
        $submenu2 = array('', '', '', '', '', '', '');
        $submenu3 = array('', '', '', '', '', '', '');
        $submenu4 = array('', '', '', '', '', '', '');

        if (in_array($controller, $submenu1) || in_array($controller, $submenu1)) {
            return $submenu1;
        }
        if (in_array($controller, $submenu2) || in_array($controller, $submenu2)) {
            return $submenu2;
        }
        if (in_array($controller, $submenu3) || in_array($controller, $submenu3)) {
            return $submenu3;
        }
        if (in_array($controller, $submenu4) || in_array($controller, $submenu4)) {
            return $submenu4;
        }
    }

    static function searchFilters()
    {
        return array(
            'price' => self::t_lang('PRICE'),
            'popular' => self::t_lang('POPULAR'),
            'duration' => self::t_lang('DURATION'),
        );
    }

    static function contactUsOptions()
    {
        return array(
            1 => self::t_lang("GENERAL_INQUIRY"),
            2 => self::t_lang("TRAVELER_SUPPORT"),
            3 => self::t_lang("HOST_SUPPORT"),
            4 => self::t_lang("PARTNERSHIP"),
            5 => self::t_lang("CAREERS"),
        );
    }

    static function contactUsOptionsByKey($key)
    {
        $ar = Self::contactUsOptions();
        return $ar[$key];
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function activityDuration()
    {
        return array('2' => self::t_lang('< 2Hr'), '4' => self::t_lang('2-4 Hr'), '5' => self::t_lang('4-6 Hr'), '12' => self::t_lang('6-12 Hr'), '100' => self::t_lang('No of days'));
    }

    /* public static function activityDuration(){
      return array('1'=>'Single Day Trips','5'=>'2 - 5 Day Trips','10'=>'6 - 10 Day Trip','11'=>'More than 11 Day Trip');
      }
     */

    public static function getStaticActivityDurationByKey($key)
    {
        $ar = Info::activityDuration();
        return @$ar[$key];
    }

    public static function searchDuration()
    {
        // return array('2'=>'Less Than 2Hr','4'=>'2-4 Hr','5'=>'4-6 Hr','12'=>'6-12 Hr','100'=> 'More Than 1 day');
        return array('2' => self::t_lang('< 2Hr'), '4' => self::t_lang('2-4 Hr'), '5' => self::t_lang('4-6 Hr'), '12' => self::t_lang('6-12 Hr'), '100' => self::t_lang('No of days'));
    }

    public static function searchPrice()
    {
        return array(
            '0-2000' => 'Less Than ' . Currency::displayPrice(2000),
            '2000-5000' => Currency::displayPrice(2000) . ' to ' . Currency::displayPrice(5000),
            '5000-10000' => Currency::displayPrice(5000) . ' to ' . Currency::displayPrice(10000),
            '10000' => 'More than ' . Currency::displayPrice(10000)
        );
    }

    /* public static function searchPrice(){
      return array(
      '0-10000'=>'Less Than '.Currency::displayPrice(10000),
      '10000-50000'=>Currency::displayPrice(10000).' to '.Currency::displayPrice(50000),
      '50000-100000'=>Currency::displayPrice(50000).' to '.Currency::displayPrice(100000),
      '100000-300000'=>Currency::displayPrice(100000).' to '.Currency::displayPrice(300000),
      '300000'=>'More than '.Currency::displayPrice(300000)
      );
      } */

    public static function activityBookings()
    {
        return array(
            '0' => 'Last Minute',
            '12' => '12 hrs prior to activity',
            '24' => '24 hrs prior to activity',
            '100' => 'No. of days prior to activity'
        );
    }

    public static function getBannerTypes()
    {
        return array(
            '0' => 'Home Page Slider',
            '1' => 'Home Page Statistics',
            '2' => 'Home page Contact',
        );
    }

    public static function getBannerTypesByKey($key)
    {
        $ar = self::getBannerTypes();
        return @$ar[$key];
    }

    public static function activityBookingsByKey($key)
    {
        $ar = self::activityBookings();
        return @$ar[$key];
    }

    static function XML2Array(SimpleXMLElement $parent)
    {
        $array = array();
        foreach ($parent as $name => $element) {
            ($node = & $array[$name]) && (1 === count($node) ? $node = array($node) : 1) && $node = & $node[];
            $node = $element->count() ? XML2Array($element) : trim($element);
        }
        return $array;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getVideoDetail($url)
    {
        $data = array();
        $data['video_id'] = "";
        $data['video_thumb'] = "";
        $data['video_type'] = "";
        if (strpos($url, 'youtube') !== false) {
            $pattern = '%^# Match any youtube URL
				(?:https?://)?  # Optional scheme. Either http or https
				(?:www\.)?      # Optional www subdomain
				(?:             # Group host alternatives
				  youtu\.be/    # Either youtu.be,
				| youtube\.com  # or youtube.com
				  (?:           # Group path alternatives
					/embed/     # Either /embed/
				  | /v/         # or /v/
				  | .*v=        # or /watch\?v=
				  )             # End path alternatives.
				)               # End host alternatives.
				([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
				($|&).*         # if additional parameters are also in query string after video id.
				$%x'
            ;
            $result = preg_match($pattern, $url, $matches);

            if (false !== $result) {
                $data['video_type'] = 1;
                $data['video_id'] = $matches[1];
                $data['video_thumb'] = 'http://img.youtube.com/vi/' . $data['video_id'] . '/1.jpg';
            }
        }
        if (strpos($url, 'vimeo') !== false) {
            $regexstr = '~
			# Match Vimeo link and embed code
			(?:&lt;iframe [^&gt;]*src=")?		# If iframe match up to first quote of src
			(?:							# Group vimeo url
				https?:\/\/				# Either http or https
				(?:[\w]+\.)*			# Optional subdomains
				vimeo\.com				# Match vimeo.com
				(?:[\/\w]*\/videos?)?	# Optional video sub directory this handles groups links also
				\/						# Slash before Id
				([0-9]+)				# $1: VIDEO_ID is numeric
				[^\s]*					# Not a space
			)							# End group
			"?							# Match end quote if part of src
			(?:[^&gt;]*&gt;&lt;/iframe&gt;)?		# Match the end of the iframe
			(?:&lt;p&gt;.*&lt;/p&gt;)?		        # Match any title information stuff
			~ix';

            $result = preg_match($regexstr, $url, $matches);
            if (false !== $result) {
                $data['video_type'] = 2;
                $data['video_id'] = $matches[1];
                $rec = file_get_contents("https://vimeo.com/api/oembed.json?url=https://vimeo.com/{$data['video_id']}");

                $rec = json_decode($rec);
                $url = $rec->thumbnail_url;
                $url = str_replace('_1280', '_200', $url);
                $data['video_thumb'] = $url;
            }
        }

        return $data;
    }

    static function validVideoDomains()
    {
        return array(
            'vimeo.com',
            'youtube.com',
            'youtu.be',
        );
    }

    static function signupMedia()
    {
        return array(
            0 => self::t_lang('NORMAL_SIGNUP'),
            1 => self::t_lang('FACEBOOK_SIGNUP'),
            2 => self::t_lang('LINKEDIN_SIGNUP'),
            3 => self::t_lang('TWITTER_SIGNUP'),
        );
    }

    static function subContent($string, $count)
    {
        if (strlen($string) > $count) {
            $string = substr(strip_tags(html_entity_decode($string)), 0, $count) . "...";
        }
        return $string;
    }

    static function getCmsType()
    {
        return array(
            0 => 'None',
            1 => 'Terms Pages'
        );
    }

    static function getCmsTypeByKey($key)
    {
        $ar = self::getCmsType();
        return @$ar[$key];
    }

    static function getCurrentCurrency()
    {
        if (isset($_SESSION['currency'])) {
            return $_SESSION['currency'];
        } else {
            return FatApp::getConfig('conf_default_currency', FatUtility::VAR_INT);
        }
    }

    static function setCurrentCurrency($currencyId = 0)
    {
        if ($currencyId == 0) {
            $currencyId = FatApp::getConfig('conf_default_currency', FatUtility::VAR_INT);
        }
        $csrch = Currency::getSearchObject();
        $csrch->addCondition('currency_active', '=', 1);
        $csrch->addCondition('currency_id', '=', $currencyId);

        $currency = FatApp::getDb()->fetch($csrch->getResultSet());
        if ($currency) {
            $_SESSION['currency'] = $currencyId;
        } else {
            $_SESSION['currency'] = Info::getCurrentCurrency();
        }
    }

    static function getPartnerStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('APPROVED'),
            2 => self::t_lang('DECLINED'),
        );
    }

    static function getPartnerStatusByKey($key)
    {
        $ar = self::getPartnerStatus();
        return @$ar[$key];
    }

    static function activityType()
    {
        return array(0 => Info::t_lang('PER_PERSON'), 1 => Info::t_lang('PER_DAY'), 2 => Info::t_lang('PER_PACKAGE'), 3 => Info::t_lang('PER_TRIP'), 4 => Info::t_lang('PER_UNIT'));
    }

    static function activityTypeLabel()
    {
        return array(0 => Info::t_lang('PEOPLE'), 1 => Info::t_lang('DAYS'), 2 => Info::t_lang('PACKAGES'), 3 => Info::t_lang('TRIPS'), 4 => Info::t_lang('UNITS'));
    }

    static function activityTypeByKey($key)
    {
        $ar = self::activityType();
        return @$ar[$key];
    }

    static function activityTypeLabelByKey($key)
    {
        $ar = self::activityTypeLabel();
        return @$ar[$key];
    }

    static function PartnerDescribe()
    {
        return array(
            1 => Info::t_lang('TRAVEL_AGENCY'),
            2 => Info::t_lang('HOTEL_CONCIERGE'),
        );
    }

    static function PartnerDescribeByKey($key)
    {
        $ar = self::PartnerDescribe();
        return @$ar[$key];
    }

    static function activityMeetingPoints()
    {
        return array(
            1 => Info::t_lang('SHARE_BY_HOST_AFTER_BOOKING')
        );
    }

    static function activityMeetingPointsByKey($key)
    {
        $ar = self::activityMeetingPoints();
        return @$ar[$key];
    }

    static function orderExtraChangeType()
    {
        return array(
            1 => self::t_lang('DONATION'),
            2 => self::t_lang('PAYMENT_GATEWAY_FEE'),
            3 => self::t_lang('VAT_CHANGES')
        );
    }

    static function orderExtraChangeTypeByKey($key)
    {
        $ar = self::orderExtraChangeType();
        return @$ar[$key];
    }

    static function getWithdrawalRequestStatus()
    {
        return array(
            0 => self::t_lang('PENDING'),
            1 => self::t_lang('APPROVED'),
            2 => self::t_lang('CANCELLED'),
        );
    }

    static function getWithdrawalRequestStatusByKey($key)
    {
        $ar = self::getWithdrawalRequestStatus();
        return @$ar[$key];
    }

    static function getOrderCancelRequestStatus()
    {
        return array(
            OrderCancel::STATUS_PENDING => self::t_lang('PENDING'),
            OrderCancel::STATUS_APPROVED => self::t_lang('APPROVED'),
            OrderCancel::STATUS_CANCELLED => self::t_lang('CANCELLED'),
        );
    }

    static function getOrderCancelRequestStatusByKey($key)
    {
        $ar = self::getOrderCancelRequestStatus();
        return @$ar[$key];
    }

    static function getOrderCancelHostApprovedStatus()
    {
        return array(
            OrderCancel::HOST_APPROVED_TYPE_PENDING => self::t_lang('PENDING'),
            OrderCancel::HOST_APPROVED_TYPE_APPROVED => self::t_lang('APPROVED'),
            OrderCancel::HOST_APPROVED_TYPE_CANCELLED => self::t_lang('CANCELLED'),
        );
    }

    static function getOrderCancelHostApprovedStatusByKey($key)
    {
        $ar = self::getOrderCancelHostApprovedStatus();
        return @$ar[$key];
    }

    static function getEventStatus()
    {
        return array(
            0 => self::t_lang('INACTIVE'),
            1 => self::t_lang('ACTIVE'),
            2 => self::t_lang('STOP_BOOKING'),
        );
    }

    static function getEventStatusByKey($key)
    {
        $ar = self::getEventStatus();
        return @$ar[$key];
    }

    static function getAvtivityBookingStatus()
    {
        return array(
            0 => self::t_lang('NO'),
            1 => self::t_lang('YES'),
        );
    }

    static function getAvtivityBookingStatusByKey($key)
    {
        $ar = self::getAvtivityBookingStatus();
        return @$ar[$key];
    }

    static function getRatingArray()
    {
        return array(
            '0.5' => '0.5',
            '1.0' => '1.0',
            '1.5' => '1.5',
            '2.0' => '2.0',
            '2.5' => '2.5',
            '3.0' => '3.0',
            '3.5' => '3.5',
            '4.0' => '4.0',
            '4.5' => '4.5',
            '5.0' => '5.0',
        );
    }

    public static function generateCustomUrl($controller = '', $action = '', $queryData = null, $use_root_url = '', $url_rewriting = null)
    {

        $queryString = array();

        if ($url_rewriting === null) {
            $url_rewriting = CONF_URL_REWRITING_ENABLED;
        }

        if ($use_root_url == '') {
            $use_root_url = CONF_USER_ROOT_URL;
        }

        if (is_array($queryData) && !empty($queryData)) {
            foreach ($queryData as $key => $val) {
                if (!strlen($val)) {
                    unset($queryData[$key]);
                }
                $queryString[$key] = rawurlencode($val);
            }
        } elseif (strlen($queryData) > 0) {
            $queryString[] = $queryData;
        }

        $url = strtolower($controller) . '/' . strtolower($action) . (is_array($queryString) && !empty($queryString) ? '/' . implode('/', $queryString) : '');

        $srch = UrlRewrite::search();
        $escapedUrl = rtrim(self::escapeString($url), '/');
        $srch->addCondition(UrlRewrite::DB_TBL_PREFIX . 'original', 'LIKE', $escapedUrl);
        // ->attachCondition(UrlRewrite::DB_TBL_PREFIX . 'custom', 'LIKE', $escapedUrl . '/%');

        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        // echo $srch->getQuery();exit;
        $url = FatApp::getDb()->fetch($srch->getResultSet(), UrlRewrite::DB_TBL_PREFIX . 'original');
        // self::test($url); exit;
        if ($url) {
            $extra = substr($url[UrlRewrite::DB_TBL_PREFIX . 'original'], strlen($url[UrlRewrite::DB_TBL_PREFIX . 'original']));
            $url = $url[UrlRewrite::DB_TBL_PREFIX . 'custom'] . $extra;
            if ($url_rewriting) {
                $url = rtrim($use_root_url . $url, '/ ');
                if ($url == '')
                    $url = '/';
                return $url;
            }
            else {
                $url = rtrim($use_root_url . 'index.php?url=' . strtolower($controller) . '/' . strtolower($action) . '/' . implode('/', $queryString), '/');
                return $url;
            }
        }
        return FatUtility::generateUrl($controller, $action, $queryString, $use_root_url, $url_rewriting);
    }

    public function getBaseUrl()
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
    }

}

?>