<?php

class ActivityAbuseController extends UserController {
    /*
     *  Activity Abuse Form
     */

    function markAsAbuseForm() {
      
        if ($this->user_type != 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if ($activity_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = $this->getMarkAsAbuseForm();
        $frm->fill(array('activity_id' => $activity_id));
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'activity-abuse/_partial/abuse-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getMarkAsAbuseForm() {

        $frm = new Form('markAsAbuseFrm');
        $frm->addHiddenField('', 'activity_id');
        $fld = $frm->addTextArea(Info::t_lang('COMMENT'), 'comment');
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function markAsAbuse() {

        if ($this->user_type != 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        if ($activity_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = $this->getMarkAsAbuseForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($this->getValidationErrors()));
        }
        $activity = new Activity($activity_id);
        $abuseReport = new AbuseReport();
        $activity->loadFromDb();
        $activity_data = $activity->getFlds();
        if (empty($activity_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }

        $abreport_record_id = $activity_id;
        $abreport_record_type = AbuseReport::ACTIVITY_ABUSE;
        $abreport_user_id = $this->userId;
        $abReport = $abuseReport->getAbuseReport($abreport_record_id, $abreport_record_type, $abreport_user_id);
        if (!empty($abReport)) {
            FatUtility::dieJsonError(Info::t_lang('ACTIVITY_ABUSE_ALREADY_REPORTED_BY_YOU'));
        }
        $data[AbuseReport::DB_TBL_PREFIX . 'record_id'] = $abreport_record_id;
        $data[AbuseReport::DB_TBL_PREFIX . 'record_type'] = $abreport_record_type;
        $data[AbuseReport::DB_TBL_PREFIX . 'user_id'] = $abreport_user_id;
        $data[AbuseReport::DB_TBL_PREFIX . 'user_comment'] = $post['comment'];
        $data[AbuseReport::DB_TBL_PREFIX . 'posted_on'] = Info::currentDatetime();
        $data[AbuseReport::DB_TBL_PREFIX . 'taken_care'] = 0;
        $abuseReport->assignValues($data);
        if (!$abuseReport->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG.PlEASE_TRY_AGAIN'));
        }
        $notify = new Notification();
        $usr = new User($this->userId);
        $usr->loadFromDb();
        $host_data = $usr->getFlds();
        $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
        $notify_msg = $host_name . ' ' . Info::t_lang('REPORTED_A_ACTIVITY_AS_INAPPROPRIATE_:_') . $activity_data[Activity::DB_TBL_PREFIX . 'name'];
        $notify->notify(0, 0, FatUtility::generateFullUrl('admin', 'reviews', array(), '/'), $notify_msg);
        $vars = array(
            '{host_name}' => $host_name,
            '{activity_name}' => $activity_data[Activity::DB_TBL_PREFIX . 'name']
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 37, $vars);
        FatUtility::dieJsonSuccess(Info::t_lang('YOUR_REQUEST_SUBMITTED'));
    }

}
