<?php

class Route extends MyAppModel {

    const COUNTRY_ROUTE = 1;
    const CITY_ROUTE = 2;
    const ACTIVITYTYPE_ROUTE = 3;
    const ACTIVITY_ROUTE = 4;
    const CMS_ROUTE = 5;
    
    const DB_TBL = 'tbl_url_rewrite';
    const DB_TBL_PREFIX = 'url_rewrite_';

    private static $customRoutes = array(
        'country/details' => self::COUNTRY_ROUTE,
        'city/details' => self::CITY_ROUTE,
        'services/index' => self::ACTIVITYTYPE_ROUTE,
        'activity/detail' => self::ACTIVITY_ROUTE,
        'cms/view' => self::CMS_ROUTE,
    );

    public static function searchRoute($recordType, $recordid, $subrecordId = 0) {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_id', "=", $recordid);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', "=", $recordType);
        $srch->addCondition(self::DB_TBL_PREFIX . 'subrecord_id', "=", $subrecordId);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function searchActiveRoute($recordType, $recordid, $subrecordId = 0) {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_id', "=", $recordid);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', "=", $recordType);
        $srch->addCondition(self::DB_TBL_PREFIX . 'subrecord_id', "=", $subrecordId);
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', "=", 1);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function getRouteByRouteName($slug, $handler) {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'custom', "=", $slug);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', "=", $handler);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function getRoute($controller = "home", $action = "index", $params = array(), $fullUrl = false, $use_root_url = '/', $url_rewriting = true) {

        $urlString = trim($controller . "/" . $action, "/");

        if ($handler = self::getHandler($urlString)) {
            $recordId = isset($params[0]) ? intval($params[0]) : 0;
            $subRecordId = isset($params[1]) ? intval($params[1]) : 0;
            
            // $routeCacheKey = md5($handler."-".$recordId."-".$subRecordId);
            
            $routeData = self::searchActiveRoute($handler, $recordId, $subRecordId);
			// if($cachedUrl =  FatCache::get($routeCacheKey)){
			   // return $cachedUrl;
			// }
            
            if ($route = self::prepareUrl($routeData)) {
                
                $finalUrl = self::getUrl($route, $fullUrl);
               // FatCache::set($routeCacheKey,$finalUrl);
               return $finalUrl;
            }
        }

        if ($fullUrl) {
            return FatUtility::generateFullUrl($controller, $action, $params, $use_root_url, $url_rewriting);
        }

        return FatUtility::generateUrl($controller, $action, $params, $use_root_url, $url_rewriting);
    }

    public static function getHandler($url) {
		// print_r(static::$customRoutes);
		if(array_key_exists($url, static::$customRoutes)) {
			return static::$customRoutes[$url];
		}
		return false;
        /* foreach (self::$customRoutes as $requestUrl => $handler) {
            $isMatched = preg_match('#^' . $requestUrl . '$#', $url, $matches);

            if ($isMatched) {
                return $handler;
            }
        }
        return false; */
    }

    public static function prepareUrl($routeData) {

        $routeType = $routeData['url_rewrite_record_type'];
        $route = false;
        switch ($routeType) {
            case 1:
                $route = "country/" . $routeData['url_rewrite_custom'];
                break;
            case 2:
                $route = "city/" . $routeData['url_rewrite_custom'];
                break;
            case 3:
                $route = "activity-type/" . $routeData['url_rewrite_custom'];
                break;
            case 4:
                $route = "activity/" . $routeData['url_rewrite_custom'];
                break;
            case 5:
                $route = "cms/" . $routeData['url_rewrite_custom'];
                break;
        }

        return $route;
    }

    

    private static function getUrl($urlPrefix, $fullUrl) {
        if ($fullUrl) {
            return FatUtility::generateFullUrl($urlPrefix, '', array(), "/");
        }
        return FatUtility::generateUrl($urlPrefix, '', array(), "/");
    }

}

?>
