<?php

class SagepaySettingsController extends AdminBaseController {

    private $key_name = "Sagepay";
    private $canView;
    private $canEdit;
    private $admin_id;

    const PAGESIZE = 50;

    public function __construct($action) {

        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewPaymentMehods($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditPaymentMehods($this->admin_id);

        if (!$this->canView) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {
        $paymentMethodsObj = new PaymentMethods();
        $payment_settings = $paymentMethodsObj->getPaymentMethodFields($this->key_name);
        $frm = $this->settingsForm();
        $frm->fill($payment_settings);
        $this->set('frm', $frm);
        $this->set('payment_settings', $payment_settings);
        $this->_template->render(true, true);
    }

    protected function settingsForm() {
        $frm = new Form('frmPaymentMethods');

        $frm->setValidatorJsObjectName('PaymentMethodfrmValidator');
        $frm->addRequiredField('Vendor Name', 'vendor_name');
        $frm->addSelectBox('Transaction Mode', 'transaction_mode', array(0 => "Test/Sandbox", "1" => "Live"))->requirements()->setRequired();
        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Save changes');
        return $frm;
    }

    function saveSettings() {
        if (!FatUtility::isAjaxCall()) {
            FatUtility::dieJsonError('Invalid Request');
        }
        // $canEdit = $this->adminPriviledge->canEditPaymentMethods(AdminAuthentication::getLoggedAdminId(), true);
        // if(!$canEdit){
        if (!$this->canEdit) {
            Message::addErrorMessage("Invalid Access.");
            $this->set('msg', Message::getHtml());
            $this->_template->render(false, false, 'json-error.php', true);
        }

        $frm = $this->settingsForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());

        if (!$frm->validate($post)) {
            Message::addErrorMessage(current($frm->getValidationErrors()));
            $this->set('msg', Message::getHtml());
            $this->_template->render(false, false, 'json-error.php', true, false);
        }

        $srch = new SearchPaymentMethods();
        $srch->addCondition('pmethod_code', '=', $this->key_name);
        $rs = $srch->getResultSet();
        $payment_method = FatApp::getDb()->fetch($rs);
        if (!$payment_method) {
            Message::addErrorMessage("Error: Payment key not found");
            $this->set('msg', Message::getHtml());
            $this->_template->render(false, false, 'json-error.php', true, false);
        }
        $pmethod_id = $payment_method["pmethod_id"];
        if (!FatApp::getDb()->deleteRecords('tbl_payment_method_fields', array('smt' => 'pmf_pmethod_id = ?', 'vals' => array($pmethod_id)))) {
            Message::addErrorMessage("Error: something went wrong, please contact Technical Team.");
            $this->set('msg', Message::getHtml());
            $this->_template->render(false, false, 'json-error.php', true, false);
        }
        $paymentMethodsObj = new PaymentMethods($pmethod_id);
        unset($post['btn_submit']);
        foreach ($post as $key => $val) {
            $insert_arr = array('pmf_pmethod_id' => $pmethod_id, 'pmf_key' => $key, 'pmf_value' => $val);
            FatApp::getDb()->insertFromArray('tbl_payment_method_fields', $insert_arr);
        }
        Message::addMessage('Record saved');
        $this->set('msg', Message::getHtml());
        $this->_template->render(false, false, 'json-success.php', true, false);
    }


}
