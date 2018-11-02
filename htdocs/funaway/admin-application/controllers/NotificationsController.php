<?php

require_once CONF_UTILITY_PATH . "PaginateTrait.php";

class NotificationsController extends AdminBaseController {

    use PaginateTrait;

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {

        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewNotification($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditNotification($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("action", $action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    /*
     *  List and Search Functionality
     */

     public function index() {

        $brcmb = new Breadcrumb();
        $brcmb->add("Notification");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }
    public function setPaginateSettings() {
        $this->pageSize = self::PAGESIZE;
    }



    public function getSearchObject($page) {
        $search = Notification::getSearchObject();
        $search->setPageSize($this->pageSize);
        $search->setPageNumber($page);
        $search->addOrder('notification_id', 'Desc');
        return $search;
    }

    public function addFilters(&$srch, $data) {
        if (isset($data['user_id'])) {
            $srch->addCondition('notification_user_id', '=', intval($data['user_id']));
        }
    }

    public function listFields() {

        $fields = array(
            'notification_content' => 'Notification',
            'notification_date' => 'Date',
        );
        return $fields;
    }

    public function getTableRow(&$tr, $arr_flds, $row) {
        foreach ($arr_flds as $key => $val) {
            $td = $tr->appendElement('td', array('style' => 'width:12%'));
            $td->appendElement('plaintext', array(), $row[$key], true);
        }
    }

}
