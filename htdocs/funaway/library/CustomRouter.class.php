<?php

class CustomRouter {
    /*
     *  :num => accept No,
     *  :string=> only Strings
     *  :any => accept Any
     */

    private static $customRoutesHandler = array(
        'country/(:any)' => Route::COUNTRY_ROUTE,
        'city/(:any)' => Route::CITY_ROUTE,
        'activity-type/(:any)' => Route::ACTIVITYTYPE_ROUTE,
        'activity/(:any)' => Route::ACTIVITY_ROUTE,
        'cms/(:any)' => Route::CMS_ROUTE,
    );
    private static $customRoutes = array(
        'activity/(:any)' => 'activity/detail',
        'country/(:any)' => 'country/details',
        'cms/(:any)' => 'cms/view',
        'city/(:any)' => 'city/details/$1',
        'activity-type/(:any)' => 'services/index/$1',
        'serviceslug' => 'services/index',
        'activity-search' => 'search',
        'cancellation-policy' => 'cancellation-policy/index/traveler',
        'cancellation-policy/traveler' => 'cancellation-policy/index/traveler',
        'cancellation-policy/host' => 'cancellation-policy/index/host',
        'faq' => 'cms/help',
        'terms' => 'cms/terms/index',
        'hosts' => 'guest-user/host-form',
        'help' => 'cms/help',
            //'(:any)' => 'cms/view/$1',
    );

    static function setRoute(&$controller, &$action, &$queryString) {
        if (defined('SYSTEM_FRONT') && SYSTEM_FRONT === true && !FatUtility::isAjaxCall()) {
            $requestUrl = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';

            self::getExactRoute($requestUrl, $controller, $action, $queryString);
          
        }
    }

    static function getExactRoute($requestUrl, &$controller, &$action, &$queryString) {
        $requestUrl = trim($requestUrl, "/");
        if ($routeMatch = self::getHandler($requestUrl, $matches)) {
            
            $handler = self::$customRoutesHandler[$routeMatch];
            $data = Route::getRouteByRouteName($matches[1], $handler);

            if (empty($data)) {
                return;
            }

            $url = self::$customRoutes[$routeMatch];
            $arr = explode('/', $url);
            $controller = (isset($arr[0])) ? $arr[0] : 'home';
            array_shift($arr);
            $action = (isset($arr[0])) ? $arr[0] : 'index';
            array_shift($arr);
            $queryString = array($data['url_rewrite_record_id']);

            //Check If route is Inactive
            if ($data['url_rewrite_active'] == 0) {
                $redirectUrl = Route::getRoute($controller, $action, $queryString);
                FatApp::redirectUser($redirectUrl);
            }

            return;
        } else if ($url = self::matchRoute($requestUrl)) {
            $arr = explode('/', $url);
            $controller = (isset($arr[0])) ? $arr[0] : 'home';
            array_shift($arr);
            $action = (isset($arr[0])) ? $arr[0] : 'index';
            array_shift($arr);
            $queryString = $arr;
            return;
        } 
    }

    public static function getHandler($url, &$matches) {

        foreach (self::$customRoutesHandler as $requestUrl => $handler) {
            $replacedUrl = str_replace(array(':any', ':num', ':string'), array('[^/]+', '[0-9]+', '[a-zA-Z]+'), $requestUrl);
            $isMatched = preg_match('#^' . $replacedUrl . '$#', $url, $matches);

            if ($isMatched) {
                return $requestUrl;
            }
        }
        return false;
    }

    static function matchRoute($url) {

        foreach (self::$customRoutes as $requestUrl => $actualUrl) {

            $requestUrl = str_replace(array(':any', ':num', ':string'), array('[^/]+', '[0-9]+', '[a-zA-Z]+'), $requestUrl);
            $isMatched = preg_match('#^' . $requestUrl . '$#', $url, $matches);


            if ($isMatched) {

                if (strpos($actualUrl, '$') !== FALSE && strpos($requestUrl, '(') !== FALSE) {
                    $actualUrl = preg_replace('#^' . $requestUrl . '$#', $actualUrl, $url);
                }

                return $actualUrl;
            }
        }
        return false;
    }

}
