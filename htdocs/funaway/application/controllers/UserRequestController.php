<?php

class UserRequestController extends UserController {

    public function create() {
        $this->set('frm', $this->getForm());
        $this->set('formHeader', Info::t_lang('REQUEST_FOR_CITY'));
        $this->_template->render(false, false);
    }

    private function getForm() {
        $frm = new Form('RequestForm');
        $frm->addRequiredField(Info::t_lang('CITY_NAME'), 'ucrequest_text');
        $frm->addSelectBox(Info::t_lang('COUNTRY_NAME'), 'ucrequest_country_id', Country::getCountries())->requirements()->setRequired();
        $frm->addSubmitButton('', 'button', Info::t_lang('SEND_REQUEST'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    public function saveRequest() {

        if (!FatUtility::isAjaxCall()) {
            die("Invalid Access");
        }

        $frm = $this->getForm();
        $formData = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($formData == false) {
            FatUtility::dieJsonError(implode("<br>", $frm->getValidationErrors()));
        }

        $request = new UserRequest();
        $formData['ucrequest_user_id'] = $this->userId;

        $request->assignValues($formData);


        if (!$request->save()) {
            FatUtility::dieJsonError($request->getError());
        }

        EventHandler::publish(EVENT_USER_REQUEST_CREATED, array("record_id" => $request->getMainTableRecordId()));

        FatUtility::dieJsonSuccess("Request Successfully Send");
    }

}
