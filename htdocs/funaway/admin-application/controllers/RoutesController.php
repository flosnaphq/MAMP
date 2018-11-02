<?php

class RoutesController extends AdminBaseController {

    public function routeUpdate($recordType,$recordId,$subRecordId=0) {

        $routeData = Route::searchActiveRoute($recordType,$recordId,$subRecordId);
        $form = $this->getForm();
        $form->fill($routeData);
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "routes/_partial/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getForm() {
        $frm = new Form('route_form', array('id' => 'route_form'));
        $frm->setFormTagAttribute('action', FatUtility::generateUrl("routes", "save"));
        $frm->addHiddenField("", 'url_rewrite_record_type');
        $frm->addHiddenField("", 'url_rewrite_record_id');
        $frm->addHiddenField("", 'url_rewrite_subrecord_id');
        $fld = $frm->addRequiredField('Slug', 'url_rewrite_custom');
        $frm->setFormTagAttribute('onsubmit', ' jQuery.fn.submitForm(formValidator,"route_form",successCallback); return(false);');
        $frm->addSubmitButton('', 'btn_submit', 'Update', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function save() {
        $this->isValidRequest("post");

        $frm = $this->getForm();
        $data = $frm->getFormDataFromArray(FatApp::getPostedData());
        $route = new Routes();
       
        if (!$route->createNewRoute($data)) {
            FatUtility::dieWithError($route->getError());
        }
        $this->set('reload', true);
        $this->set('msg', 'Route Setup Successfull');
        $this->_template->render(false, false, 'json-success.php');
        
    }

}
