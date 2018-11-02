<?php

class Admin {

    const DB_TBL = 'tbl_admin';
    const DB_TBL_PREFIX = 'admin_';
    
    public function getSearchObject() {
        $srch = new SearchBase(self::DB_TBL);
        return $srch;
    }
    
}
