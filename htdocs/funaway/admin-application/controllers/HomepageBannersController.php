<?php

class HomepageBannersController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;
    private $fileTypes = array();

    public function __construct($action) {

        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewBanners($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditBanners($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }


        $this->fileTypes = array(AttachedFile::FILETYPE_HOME_PAGE_BANNER_CONTACT, AttachedFile::FILETYPE_HOME_PAGE_BANNER_STATS);

        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {
        $this->_template->render();
    }

    public function lists() {
        $this->set("arr_listing", AttachedFile::getAllAttachmentsByType($this->fileTypes));
        $htm = $this->_template->render(false, false, "homepage-banners/_partial/lists.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function form() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $banner_id = $post['banner_id'];
        $banner_type = $post['banner_type'];
        $form = $this->getForm($banner_id,$banner_type);
     
        $this->set("frm", $form);
        $htm = $this->_template->render(false, false, "homepage-banners/_partial/form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }
    public function setup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }


        if (!empty($_FILES['image']['tmp_name']) && !is_uploaded_file($_FILES['image']['tmp_name'])) {
            FatUtility::dieJsonError('Image couldn\'t not uploaded.');
        } elseif (!empty($_FILES['image']['tmp_name'])) {
            $size = getimagesize($_FILES['image']['tmp_name']);
           // if ($size[0] < 1600 || $size[1] < 900) {
           //     FatUtility::dieJsonError(Info::t_lang("Banner_image_size"));
          //  }
        }
        $post = FatApp::getPostedData();
        $post['afile_id'] = $post['afile_id'];
        $post['afile_type'] = $post['afile_type'];
        $form = $this->getForm($post['afile_id'],$post['afile_type']);
        $post = $form->getFormDataFromArray($post);
        if ($post === false) {
            FatUtility::dieJsonError($form->getValidationErrors());
        }
        if (empty($_FILES['image']['tmp_name']) && empty($post['afile_id'])) {
            FatUtility::dieJsonError('Image is mandatory. field');
        }
       
        
        $attachment = new AttachedFile();
        $attachment->saveAttachment($_FILES['image']['tmp_name'],$post['afile_type'],0,0,$_FILES['image']['name'],0,1,1);
        FatUtility::dieJsonSuccess('Setup Successful');
    }
    private function getForm($banner_id,$banner_type) {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $action = 'Add';

        $frm = new Form('action_form');
        $frm->addHtml('', 'old_image', "<img src='" . FatUtility::generateUrl('image', 'homepageBanner', array($banner_type, 100, 100,time()), CONF_BASE_DIR) . "' >");
        $frm->addFileUpload('Image', 'image');

        $frm->addHiddenField("", 'afile_id',$banner_id);
        $frm->addHiddenField("", 'afile_type',$banner_type);
        $frm->addSubmitButton('', 'btn_submit', 'Update');

        return $frm;
    }

}
