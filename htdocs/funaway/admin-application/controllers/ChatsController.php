<?php
class ChatsController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
	/* 	$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		} */
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewMessage($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditMessage($this->admin_id);
		if(!$this->canView){
			if(FatUtility::isAjaxCall()){
				FatUtility::dieJsonError('Unauthorized Access!');
			}
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("action",$action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	function index(){
		$brcmb = new Breadcrumb();
		$brcmb->add("Messages");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	public function listing(){
		$pagesize=static::PAGESIZE;
		if(!$this->canView){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		//$pagesize=2;
	//	$searchForm = $this->getSearchForm();
		
		$post = FatApp::getPostedData();
	//	$post = $searchForm->getFormDataFromArray($post);
		$search = Chats::getSearchObject();
	/* 	if(!empty($post['cms_name'])){
			$search->addCondition('cms_name','like','%'.$post['cms_name'].'%');
		} */
		$search->joinTable("tbl_messages_thread","inner join","msg1.message_thread_id = messagethread_id");
		$search->joinTable("tbl_activities","inner join","messagethread_activity_id = activity_id");
	
		// $search->joinTable("tbl_users", "LEFT JOIN", "user_id = IF({$post['user_id']} = messagethread_first_user_id,messagethread_first_user_id,messagethread_second_user_id)");
		
		$search->joinTable("tbl_users", "LEFT JOIN", "user_id = message_user_id");
		
		$search->joinTable("tbl_messages","LEFT OUTER JOIN","msg1.message_date < msg2.message_date and msg1.message_thread_id = msg2.message_thread_id","msg2");
		$search->addDirectCondition("msg2.message_date IS NULL");
	//	$search->addGroupBy("msg1.message_thread");
		$search->addCondition('messagethread_first_user_id','=',$post['user_id'])->attachCondition('messagethread_second_user_id','=',$post['user_id']);

		$search->addFld("msg1.*, tbl_users.*, activity_name");
		
		$search->addOrder("msg1.message_date","desc");
		
		
		$page = isset($post['page'])?FatUtility::int($post['page']):1;
		if(!empty($post['start_date']) && FatDate::validateDateString($post['start_date'])){
			$search->addDirectCondition('DATE(msg1.message_date) >="'.$post['start_date'].'"');
		}
		if(isset($post['end_date']) && FatDate::validateDateString($post['end_date'])){
			$search->addDirectCondition('DATE(msg1.message_date) <="'.$post['end_date'].'"');
		}
		$page = FatUtility::int($page);
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
	//	echo $search->getError();exit;
		$records = FatApp::getDb()->fetchAll($rs);
		/* foreach($records as $record){
			if($record == messageToUser_id)
		} */
		
		// echo $search->getQuery();
	
		// Info::test($records); exit;
		$this->set("arr_listing",$records);
		$this->set("user_id",$post['user_id']);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"chats/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['message_thread'] = empty($post['message_thread'])?0:FatUtility::int($post['message_thread']);
		$form = $this->getForm($post['message_thread']);
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"chats/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm($thread_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Send';
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'message_thread',$thread_id);
		$text_area_id = 'text_area';
		$frm->addTextArea('Message','message_text');
		$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("chats","setup") );
		$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form"); return(false);' );
		$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		return $frm;
	}
	
	public function setup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		if($post['message_thread'] == 0){
			$thread['messagethread_id'] = 0; 
			$thread['messagethread_first_user_id'] = 0;
			$thread['messagethread_second_user_id'] = $post['user_id'];
			$thred = new Thread();
			$thred->assignValues($thread);
			$thred->save();
			$post['message_thread'] = $thred->getMainTableRecordId();
		}
		
		
		
		$chats = new Chats($chatId = 0);
		$data = array();
		$data['message_id'] = 0;
		$data['message_thread_id'] = $post['message_thread'];
		$data['message_user_id'] = 0;
		$data['message_text'] = $post['message_text'];
		$data['message_date'] = Info::currentDatetime();;
		$data['message_unseen'] = 0;
		$data['message_deleted'] = 0;
		
		
		$chats->assignValues($data);

		if (!$message_id = $chats->save()) {
			FatUtility::dieWithError($chats->getError());
			
		}
		
		

		$this->set('msg', 'chats Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	public function view($message_thread){
		$activity_data = array();
		$message_thread = FatUtility::int($message_thread);
		if($message_thread < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$messageThread = new MessageThread();
		$search = Chats::getSearchObject();
		$search->addCondition("message_thread_id","=",$message_thread);
		$search->joinTable("tbl_users", "LEFT JOIN", "user_id = message_user_id");
		$search->addOrder("msg1.message_date","asc");
		$search->addFld("msg1.*");
		$search->addFld("tbl_users.*");
		$message_thread_data = $messageThread->getAttributesById($message_thread);
		$activity_id = $message_thread_data[MessageThread::DB_TBL_PREFIX.'activity_id'];
		if($activity_id > 0){
			$act = new Activity($activity_id);
			$act->loadFromDb();
			$activity_data = $act->getFlds();
		}
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		//Chats::markAsRead($message_thread, 0);
		
		$this->set('records',$records);
		$this->set('activity_data',$activity_data);
		$this->set("frm",$this->getForm($message_thread));
		$htm = $this->_template->render(false,false,"chats/views.php",true,true);
		
		FatUtility::dieJsonSuccess($htm);
		
	}
}
