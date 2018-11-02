<?php

trait PaginateTrait {

    protected $pageSize = 10;
    protected $sortFields = array();
    protected $paginateSorting = false;
    protected $paginateSearch = false;

    


    public function listing($page = 1) {

        $data = FatApp::getPostedData();

        if (isset($data['page']))
            $page = isset($data['page']) ? FatUtility::int($data['page']) : 1;

        $requestPage = FatUtility::int($page);

        $search = $this->getSearchObject($requestPage);
        if(FatApp::getController() === 'ReviewsController'){
            $search = $this->getSearchObject($requestPage,true);
            $search->addMultipleFields(array(
                'count('.ReviewMessage::DB_TBL_PREFIX.'id) as numMessages',
                'group_concat('.ReviewMessage::DB_TBL_PREFIX.'user_type) replyUserTypes',
                    )
            );
        }

        $this->addFilters($search, $data);
        $rs = $search->getResultSet();
        $list = FatApp::getDb()->fetchAll($rs);

        $this->set('page', $requestPage);
        $this->set('totalPage', $search->pages());
        $this->set('pageSize', $this->pageSize);
        $this->sortFields();
        $this->set('table', $this->getTableHtml($this->listFields(), $list));

        $htm = $this->_template->render(false, false, "_partial/traits/paginate/default.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function getTableHtml($arr_flds, $arr_listing) {
        $data = FatApp::getPostedData();
        $tbl = new HtmlElement('table', array('width' => '100%', 'id' => 'categoryList', 'class' => "table table-responsive"));
        $thead = $tbl->appendElement('thead')->appendElement('tr', array('class' => 'nodrag nodrop'));
        $sortArray = isset($data['sort']) ? $data['sort'] : array();
        $sortKey = "";
        $sortOrder = "";
        if (!empty($sortArray)) {
            list($sortKey, $sortOrder) = explode(":", $sortArray);
        }
        foreach ($arr_flds as $key => $val) {
            if ($this->paginateSorting && in_array($key, $this->sortFields)) {
                $currentRowSortKey = $key;
                $currentSortOrder = "asc";
                $sortClass = "";
                if ($currentRowSortKey == $sortKey) {
                    $currentSortOrder = $sortOrder == "asc" ? 'desc' : 'asc';
                    $sortClass = $sortOrder == "asc" ? 'active' : '';
                }
                $data['sort'] = $currentRowSortKey . ":" . $currentSortOrder;
                $query = http_build_query($data);
                $thead->appendElement('th', array(), $val . '<a href="javascript:void(0)" class="iconsort ' . $sortClass . '" data-href=' . $query . ' onClick="sortTable(this)"></a>', true);
            } else {
                $thead->appendElement('th', array(), $val);
            }
        }
        $tbody = $tbl->appendElement('tbody');
        if ($arr_listing) {
            $counter = 1;
            foreach ($arr_listing as $index => $row) {
                $tr = $tbody->appendElement('tr');
                $this->getTableRow($tr, $arr_flds, $row, $counter);
                $counter++;
            }
        } else {
            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), "No Result Found");
        }
        return $tbl;
    }

    abstract public function getSearchObject();

    abstract public function addFilters();

    abstract public function listFields();

    abstract public function getTableRow();
    
    abstract public function setPaginateSettings();

    public function sortFields(){
        
    }

    public function breadcrumb() {
        
    }

}
