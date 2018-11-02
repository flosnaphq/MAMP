<?php

trait MetaTrait {

    protected $metaType = null;

    public function getMetaForm($recordId) {
        $meta = new MetaTags();
        $data = $meta->getMetaTagByRecordType($this->metaType, $recordId);
        $frm = $this->getMetaTagForm($recordId);
        $frm->fill($data);
        $this->set('frm',$frm );
        $htm = $this->_template->render(false, false, "_partial/traits/meta-form.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getMetaTagForm($record_id) {
        $frm = new Form('meta_tag', array('id' => 'meta_form'));


        $frm->addHiddenField('', 'meta_id');
        $frm->addHiddenField('', 'meta_record_id', $record_id);
        $text_area_id = 'meta_tag_text_area';
        $editor_id = 'meta_tag_editor';
        $title = $frm->addTextBox('Title', 'meta_title');
        $title->requirements()->setRequired();
        $keyword->developerTags['fld_default_col'] = 6;
        $keyword = $frm->addTextArea('Keyword', 'meta_keywords');
        $keyword->requirements()->setRequired();
        $keyword->developerTags['fld_default_col'] = 6;
        $frm->addTextArea('Description', 'meta_description', '', array('id' => $text_area_id));
        if ($this->canEdit) {
            $frm->addSubmitButton('', 'btn_submit', 'Add/Update', array('class' => 'themebtn btn-default btn-sm'));
        }
        return $frm;
    }

    public function metaTagAction() {

        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $post['meta_record_id'] = isset($post['meta_record_id']) ? FatUtility::int($post['meta_record_id']) : 0;
        $meta_id = isset($post['meta_id']) ? FatUtility::int($post['meta_id']) : 0;
        if (empty($post['meta_record_id'])) {
            FatUtility::dieJsonError('Invalid action!');
        }

        $form = $this->getMetaTagForm($post['meta_record_id']);
        $post = $form->getFormDataFromArray($post);
        if ($post === false) {
            FatUtility::dieJsonError(current($form->getValidationErrors()));
        }
        $post[MetaTags::DB_TBL_PREFIX . 'record_type'] = $this->metaType;
      
        $meta = new MetaTags($meta_id);
        $meta->assignValues($post);
        if (!$meta->save()) {
            FatUtility::dieJsonError('Something went Wrong. Please Try Again.');
        }
        FatUtility::dieJsonSuccess("Record updated!");
    }

}
