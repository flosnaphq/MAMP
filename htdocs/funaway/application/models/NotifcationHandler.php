<?php

class NotifcationHandler {

    public static function sendNewCityRequestToAdmin($requestData) {

        $notify = new Notification();
        $username = $requestData['user_firstname']." ".$requestData['user_lastname'];
        $suggestion = $requestData['ucrequest_text'];
        $countryName = $requestData['country_name'];
        $message = "%s FOR %s IN %s BY %s";
        $notify_msg = sprintf($message,Info::t_lang('NEW_CITY_REQUEST'),$suggestion,$countryName,$username);
        $notify->notify(0, 0, '', $notify_msg);
    }

}
