<?php

class Navigation extends MyAppModel {

    const DB_TBL = 'tbl_navigations';
    const DB_TBL_PREFIX = 'navigation_';

    public function __construct($id = 0) {
        $id = FatUtility::convertToType($id, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    static function windowType() {
        return array(0 => 'Same Window', 1 => 'New Window');
    }

    static function otherNavigation() {
        $nav = array();

        // $nav[] = array("name"=>Info::t_lang("BECOME_A_HOST"),"link"=>$become_a_host_link);
        // $nav[] = array("name"=>Info::t_lang("ABOUT_US"),"link"=>"/about-us");
        $nav[] = array("name" => Info::t_lang("Cancellation_Policy"), "link" => "/cancellation-policy");
        $nav[] = array("name" => Info::t_lang("FAQ"), "link" => "/faq");
        $nav[] = array("name" => Info::t_lang("BLOG"), "link" => "/blog");
        // $nav[] = array("name"=>Info::t_lang("CONTACT_US"),"link"=>"/contact-us");
        // $nav[] = array("name"=>Info::t_lang("PARTNERSHIPS"),"link"=>FatUtility::generateUrl('partnerships','',array(),generateUrl));
        return $nav;
    }

    static function getNavigations($pos) {
        $srch = Navigation::getSearchObject();
        $srch->addCondition('navigation_loc', '=', $pos);
        $srch->joinTable('tbl_cms', 'left join', 'cms_id = navigation_cms_id and cms_active = 1');
        $srch->addOrder("navigation_display_order", "asc");
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $navs = array();
        foreach ($records as $k => $rec) {
            if ($rec['navigation_cms_id'] != 0) {
                if($rec['cms_id']<1){
                    continue;
                    
                }
                //$navs[$k]['link'] = FatUtility::generateUrl('cms','view',array($rec['cms_slug']));
                $navs[$k]['link'] = Route::getRoute('cms','view',array($rec['cms_id']));
                $navs[$k]['caption'] = $rec['navigation_caption'];
                $navs[$k]['target'] = $rec['navigation_open'] == 0 ? "" : "_blank";
            } else {
                /* 0081[ */
                if (!strpos($rec['navigation_link'], 'http://') == false) {
                    $navs[$k]['link'] = $rec['navigation_link'];
                } else {
                    $navs[$k]['link'] = CONF_WEBROOT_URL . ltrim($rec['navigation_link'], '/');
                }
                /* ] */

                $navs[$k]['caption'] = $rec['navigation_caption'];
                $navs[$k]['target'] = $rec['navigation_open'] == 0 ? "" : "_blank";
            }
        }
        return $navs;
    }

}
