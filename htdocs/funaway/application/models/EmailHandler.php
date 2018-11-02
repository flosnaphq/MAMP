<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailHandler {

    public static function sendNewCityRequestToAdmin($requestData) {
        $templateId = 36;
        $vars = array(
            '{city_name}' => ucfirst($requestData['ucrequest_text']),
            '{country_name}' => ucfirst($requestData['country_name']),
            '{request_date}' => FatDate::format($requestData['ucrequest_date']),
            '{user_name}' => $requestData['user_firstname'] . " " . $requestData['user_lastname'],
        );

        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), $templateId, $vars);
    }

}
