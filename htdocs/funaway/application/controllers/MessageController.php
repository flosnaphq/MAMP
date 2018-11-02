<?php

class MessageController extends UserController {

    public function __construct($action) {
        parent::__construct($action);
        $this->set('controller', 'message');
        $this->set("class", "is--dashboard");
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    public function index() {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('MESSAGES'));
        $brcmb->add(Info::t_lang('Message'));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function listing() {
        $pagesize = static::PAGESIZE;
        //	$pagesize=2;
        $data = FatApp::getPostedData();
        $messageTypeUser = isset($data['userType']) ? FatUtility::int($data['userType']) : 0;

        $search = Chats::getSearchObject();
        $search->joinTable("tbl_messages_thread", "inner join", "msg1.message_thread_id = messagethread_id");
        $search->joinTable("tbl_activities", "left join", "activity_id = messagethread_activity_id");
        $search->joinTable("tbl_users", "left JOIN", "user_id = IF({$this->userId} != messagethread_first_user_id,messagethread_first_user_id,messagethread_second_user_id)");
        $search->joinTable("tbl_messages", "LEFT OUTER JOIN", "msg1.message_date < msg2.message_date and msg1.message_thread_id = msg2.message_thread_id", "msg2");
        if ($messageTypeUser == 0) {
            $search->addCondition('messagethread_first_user_id', '=', 0)->attachCondition('messagethread_second_user_id', '=', 0);
        } elseif ($messageTypeUser == 1) {
            $search->addCondition('messagethread_first_user_id', '!=', 0)->attachCondition('messagethread_second_user_id', '!=', 0, 'and');
        }
        $search->addDirectCondition("msg2.message_date IS NULL");
        $search->addCondition('messagethread_first_user_id', '=', $this->userId)->attachCondition('messagethread_second_user_id', '=', $this->userId);
        $search->addOrder("msg1.message_date", "desc");
        $search->addFld("msg1.*");
        $search->addFld("tbl_users.*,activity_name");
        $page = $data['page'];
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
        //echo $search->getQuery();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);

        $htm = $this->_template->render(false, false, "message/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function form() {
        $message_heading = Info::t_lang('WRITE_YOUR_REPLY');
        $post = FatApp::getPostedData();
        $activity_id = empty($post['activity_id']) ? 0 : FatUtility::int($post['activity_id']);
        $post['message_thread'] = empty($post['message_thread']) ? 0 : FatUtility::int($post['message_thread']);
        $form = $this->getForm($post['message_thread']);
        if (!empty($activity_id)) {
            $form->addHiddenField('', 'activity_id', $activity_id);
            $form->getField('btn_submit')->value = Info::t_lang('SEND');
        }
        if ($post['message_thread'] == 0) {
            //	$message_text = $form->getField('message_text');
            //	$message_text = $message_text->setFieldTagAttribute('placeholder',Info::t_lang('KINDLY_ASK_YOUR_QUESTION_AND_WE_WILL_GET_BACK_TO_YOU_SHORTLY'));
            $message_heading = Info::t_lang('KINDLY_ASK_YOUR_QUESTION_AND_WE_WILL_GET_BACK_TO_YOU_SHORTLY');
        }
        $this->set("frm", $form);
        $this->set("message_heading", $message_heading);
        $htm = $this->_template->render(false, false, "message/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function msgToTraveler() {
        $message_heading = Info::t_lang('SEND_YOUR_MESSAGE');
        $post = FatApp::getPostedData();
        $booking_id = empty($post['booking_id']) ? 0 : $post['booking_id'];
        $post['message_thread'] = empty($post['message_thread']) ? 0 : FatUtility::int($post['message_thread']);
        $form = $this->getForm($post['message_thread']);

        if (!empty($booking_id)) {
            $form->addHiddenField('', 'booking_id', $booking_id);
            $form->getField('btn_submit')->value = Info::t_lang('SEND');
        }

        $this->set("frm", $form);
        $this->set("message_heading", $message_heading);
        $htm = $this->_template->render(false, false, "message/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getForm($thread_id = 0) {

        $frm = new Form('action_form', array('id' => 'action_form'));
        $frm->addHiddenField("", 'message_thread', $thread_id);
        $frm->addTextArea('', 'message_text', '', array('placeholder' => Info::t_lang('YOUR_REPLY_WRITE_HERE...'), 'title' => Info::t_lang('MESSAGE'), 'id' => 'message_text'))->requirements()->setRequired();
        $frm->setFormTagAttribute('action', FatUtility::generateUrl("message", "setup"));
        $frm->setValidatorJsObjectName('formValidator');
        $frm->setFormTagAttribute('onsubmit', 'submitForm(formValidator,"action_form","' . $thread_id . '"); return(false);');
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('REPLY'));
        return $frm;
    }

    public function setup() {
        $post = FatApp::getPostedData();
        $activity_id = @$post['activity_id'];
        $booking_id = @$post['booking_id'];
        $booking_id = trim($booking_id);
        $activity_id = FatUtility::int($activity_id);
        if ($post['message_thread'] == 0) {

            $userId = $this->userId;
            if ($booking_id != '') {
                $ord = new Order();
                $act = new Activity();
                $activities = $ord->getOrderActivityByBookingId($booking_id);
                if (empty($activities)) {
                    FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
                }

                $order = $ord->getOrderDetail($activities['oactivity_order_id']);
                $actDetail = $act->getActivity($activities['oactivity_activity_id']);
                $activity_id = $activities['oactivity_activity_id'];
                if ($this->user_type == 1) {// by host
                    if (empty($actDetail) || $actDetail['activity_user_id'] != $this->userId) {
                        FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
                    }

                    $otherUserId = $order['order_user_id'];
                } else {// By traveler
                    if (empty($order) || $order['order_user_id'] != $this->userId) {
                        FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
                    }
                    $otherUserId = $actDetail['activity_user_id'];
                }
            } elseif ($activity_id > 0) {
                $act = new Activity($activity_id);
                $act->loadFromDb();
                $activity_data = $act->getFlds();
                if ($activity_data[Activity::DB_TBL_PREFIX . 'user_id'] == $this->userId) {
                    FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
                }

                $otherUserId = $activity_data[Activity::DB_TBL_PREFIX . 'user_id'];
            }

            $thred = new MessageThread();
            if (!$thred->createThread($userId, $otherUserId, $activity_id)) {
                FatUtility::dieJsonError(Info::t_lang('UNABLE_TO_SEND_MESSAGE'));
            }

            $post['message_thread'] = $thred->getMainTableRecordId();

            $thread = $thred->getAttributesById($post['message_thread']);
        } else {
            $thred = new Thread($post['message_thread']);
            $thred->loadFromDb();
            $thread = $thred->getFlds();
            if (empty($thread) || (!in_array($this->userId, array($thread['messagethread_first_user_id'], $thread['messagethread_second_user_id'], $thread['messagethread_third_user_id'])))) {
                FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
            }
        }


        $chats = new Chats($chatId = 0);
        $data = array();
        $data['message_id'] = 0;
        $data['message_thread_id'] = $post['message_thread'];
        $data['message_user_id'] = $this->userId;
        $data['message_text'] = htmlspecialchars($post['message_text']);
        $data['message_date'] = Info::currentDatetime();

        $data['message_unseen'] = 0;
        $data['message_deleted'] = 0;

        $post_message_id = @$post['message_id'];
        $post_message_id = FatUtility::int($post_message_id);
        $chats->assignValues($data);

        if (!$message_id = $chats->save()) {
            FatUtility::dieWithError($chats->getError());
        }
        $last_msg_id = $chats->getMainTableRecordId();
        $record = $chats->getAttributesById($last_msg_id);
        $this->getMessages($post['message_thread'], $post_message_id,false);
        $this->set('thread', $thread);

        $htm = $this->_template->render(false, false, "message/last_msg.php", true, true);
        //$this->set('html',$htm);
        //$this->set('msg', Info::t_lang('MESSAGES POSTED!'));
        FatUtility::dieJsonSuccess(array('msg' => Info::t_lang('MESSAGES POSTED!'), 'html' => $htm, 'message_id' => $last_msg_id));
        //$this->_template->render(false, false, 'json-success.php');
    }

    private function getMessages($message_thread, $last_message_id = 0,$markread=true) {

        $message_thread = FatUtility::int($message_thread);
        if ($message_thread < 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $thred = new Thread($message_thread);
        $thred->loadFromDb();
        $thread = $thred->getFlds();
        if (empty($thread) || (!in_array($this->userId, array($thread['messagethread_first_user_id'], $thread['messagethread_second_user_id'], $thread['messagethread_third_user_id'])))) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $search = Chats::getSearchObject();
        $search->addCondition("message_thread_id", "=", $message_thread);
        $search->addCondition("message_id", ">", $last_message_id);
        $search->joinTable("tbl_users", "LEFT JOIN", "user_id = message_user_id");
        $search->addOrder("msg1.message_date", "asc");
        $search->addFld("msg1.*");
        $search->addFld("tbl_users.*");

        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set('records', $records);
        $this->set('thread', $thread);
        $this->set("frm", $this->getForm($message_thread));
        if($markread)
        Chats::markAsRead($message_thread, $this->userId);

        return;
    }

    public function view($message_thread) {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('MESSAGES'));
        $brcmb->add(Info::t_lang('Message'), FatUtility::generateUrl('message'));
        $brcmb->add(Info::t_lang('View'));
        $this->set('breadcrumb', $brcmb->output());

        $this->set('thread_id', $message_thread);
        $this->_template->render();
        //FatUtility::dieJsonSuccess($htm);
    }

    public function chat($message_thread) {

        $this->getMessages($message_thread);
        $htm = $this->_template->render(false, false, 'message/chat.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

}
