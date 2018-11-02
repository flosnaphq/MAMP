<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 *  Fired When User Request For New City
 */
define("EVENT_USER_REQUEST_CREATED", "EVENT_USER_REQUEST_CREATED");
/*
 *  Fired When Activity Added Or Updated by Host
 */
define("ACTIVITY_ADDED_OR_UPDATED", "ACTIVITY_ADDED_OR_UPDATED");



EventHandler::subscribe(EVENT_USER_REQUEST_CREATED, function ($params) {

    $recordId = isset($params['record_id']) ? intval($params['record_id']) : 0;
    if ($recordId < 1) {
        return false;
    }
    $recordData = UserRequest::getUserRequestDataById($recordId);
    NotifcationHandler::sendNewCityRequestToAdmin($recordData);
    EmailHandler::sendNewCityRequestToAdmin($recordData);
});


