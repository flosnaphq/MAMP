<?php

class FatTemplate {

    protected $variables = array();
    protected $_controller;
    protected $_action;
    private $arr_page_js = array();
    private $arr_page_css = array();
    private $renderingTpl;

    public function __construct($controller, $action) {
        $this->_controller = $controller;
        $this->_action = $action;
    }

    /** Set Variables * */
    public function set($name, $value) {
        $this->variables[$name] = $value;
    }

    protected function writeMetaTags($__title = '', $__metaData = array()) {
        if (method_exists('MetaTagsWriter', 'getHeaderTags')) {
            return MetaTagsWriter::getHeaderTags($this->_controller, $this->_action, FatApp::getParameters(), $__title, $__metaData);
        }
        return '<title>' . FatApp::getConfig("conf_website_name") . ' </title>';
    }

    protected function getJsCssIncludeHtml($mergeFiles = true, $includeCommon = true) {
        $str = '';

        $use_root_url = '';
       
        $arrTpl = pathinfo(CONF_THEME_PATH . $this->renderingTpl);
        $fl = $arrTpl['dirname'] . DIRECTORY_SEPARATOR . 'page-css' . DIRECTORY_SEPARATOR . $arrTpl['filename'] . '.css';
        if (file_exists($fl)) {
            $this->addCss(substr($fl, strlen(CONF_THEME_PATH)));
        }
        $fl = $arrTpl['dirname'] . DIRECTORY_SEPARATOR . 'page-js' . DIRECTORY_SEPARATOR . $arrTpl['filename'] . '.js';
        if (file_exists($fl)) {
            $this->addJs(substr($fl, strlen(CONF_THEME_PATH)));
        }

        /* Include CSS */
        if ($includeCommon) {
            $pth = CONF_THEME_PATH . 'common-css';
            $last_updated = 0;

            $arrCommonfiles = scandir($pth, SCANDIR_SORT_ASCENDING);

            foreach ($arrCommonfiles as $fl) {
                if (!is_file($pth . DIRECTORY_SEPARATOR . $fl))
                    continue;
                if ('.css' != substr($fl, - 4))
                    continue;

                $time = filemtime($pth . DIRECTORY_SEPARATOR . $fl);
                if ($mergeFiles) {
                    $last_updated = max($last_updated, $time);
                } else {
					$cssUrl = FatUtility::generateUrl('JsCss', 'cssCommon', array(), $use_root_url, false) . '&f=' . rawurlencode($fl) . '&min=0&sid=' . $time;  
					$str .= '<link rel="stylesheet" type="text/css"
						href="' . FatCache::getCachedUrl($cssUrl, CONF_DEF_CACHE_TIME, '.css') . '" />' . "\n";
                }
            }

            if ($mergeFiles) {
				
				$cssUrl = FatUtility::generateUrl ( 'JsCss', 'cssCommon', array (), $use_root_url, false ) . '&min=1&sid=' . $last_updated;  
				$str .= '<link rel="stylesheet" type="text/css"
						href="' . FatCache::getCachedUrl($cssUrl, CONF_DEF_CACHE_TIME, '.css') . '" />' . "\n";
            }
        }


        if (count($this->arr_page_css) > 0) {
            $last_updated = 0;
            foreach ($this->arr_page_css as $val) {
                $time = filemtime(CONF_THEME_PATH . $val);
                if ($mergeFiles) {
                    $last_updated = max($last_updated, $time);
                } else {
					$cssUrl = FatUtility::generateUrl('JsCss', 'css', array(), $use_root_url, false) . '&f=' . rawurlencode($val) . '&min=0&sid=' . $time;
                   	$str .= '<link rel="stylesheet" type="text/css"
							href="' . FatCache::getCachedUrl($cssUrl, CONF_DEF_CACHE_TIME, '.css') . '" />' . "\n";
                }
            }
            if ($mergeFiles) {
                   $cssUrl = FatUtility::generateUrl('JsCss', 'css', array(), $use_root_url, false) . '&f=' . rawurlencode(implode(',', $this->arr_page_css)) . '&min=1&sid=' . $last_updated;  
                $str .= '<link rel="stylesheet" type="text/css"
						href="' . FatCache::getCachedUrl($cssUrl, CONF_DEF_CACHE_TIME, '.css'). '" />' . "\n";
            }
        }
        /* Include CSS Ends */


        /* Include JS */
        $str .= '<script type="text/javascript">
				var siteConstants = ' . json_encode(array(
                    'webroot' => CONF_WEBROOT_URL,
                    'webroot_traditional' => CONF_WEBROOT_URL_TRADITIONAL,
                    'rewritingEnabled' => (CONF_URL_REWRITING_ENABLED ? '1' : '0'),
                )) . ';
	    	</script>' . "\r\n";

        if ($includeCommon) {
            $pth = CONF_THEME_PATH . 'common-js';
			// $dir = opendir($pth);
            $last_updated = 0;

            $arrCommonfiles = scandir($pth, SCANDIR_SORT_ASCENDING);

            foreach ($arrCommonfiles as $fl) {
                if (!is_file($pth . DIRECTORY_SEPARATOR . $fl))
                    continue;
                if ('.js' != substr($fl, -3))
                    continue;
                if ('noinc-' == substr($fl, 0, 6))
                    continue;

                $time = filemtime($pth . DIRECTORY_SEPARATOR . $fl);
                if ($mergeFiles) {
                    $last_updated = max($last_updated, $time);
                } else {
                    $jsUrl = FatUtility::generateUrl('JsCss', 'jsCommon', array(), $use_root_url, false) . '&f=' . rawurlencode($fl) . '&min=0&sid=' . $time;
                    
                    $str .= '<script type="text/javascript" language="javascript"
							src="' .FatCache::getCachedUrl($jsUrl,CONF_DEF_CACHE_TIME,".js")  . '"></script>' . "\n";
                }
            }

            if ($mergeFiles) {
                        $jsUrl = FatUtility::generateUrl('JsCss', 'jsCommon', array(), $use_root_url, false) . '&min=0&sid=' . $last_updated;
                $str .= '<script type="text/javascript" language="javascript"
							src="' . FatCache::getCachedUrl($jsUrl,CONF_DEF_CACHE_TIME,".js") . '"></script>' . "\n";
            }
        }
        if (count($this->arr_page_js) > 0) {
            $last_updated = 0;
            foreach ($this->arr_page_js as $val) {
                $time = filemtime(CONF_THEME_PATH . $val);
                if ($mergeFiles) {
                    $last_updated = max($last_updated, $time);
                } else {
					/* $jsUrl = FatUtility::generateUrl('JsCss', 'js', array(), $use_root_url, false) . '&f=' . rawurlencode($val) . '&min=0&sid=' . $time;
					$str .= '<script type="text/javascript" language="javascript"
						src="' . FatCache::getCachedUrl($jsUrl,CONF_DEF_CACHE_TIME,".js") . '" ></script>' . "\n"; */
                    $str .= '<script type="text/javascript" language="javascript"
							src="' . FatUtility::generateUrl('JsCss', 'js', array(), $use_root_url, false) . '&f=' . rawurlencode($val) . '&min=0&sid=' . $time . '" ></script>' . "\n";
                }
            }
            if ($mergeFiles) {
                $jsUrl = FatUtility::generateUrl('JsCss', 'js', array(), $use_root_url, false) . '&f=' . rawurlencode(implode(',', $this->arr_page_js)) . '&min=1&sid=' . $last_updated;
                $str .= '<script type="text/javascript" language="javascript"
						src="' . FatCache::getCachedUrl($jsUrl,CONF_DEF_CACHE_TIME,".js") . '" ></script>' . "\n";
            }
        }
        /* Include JS Ends */

        return $str;
    }

    /** Display Template * */
    function render($include_header = true, $include_footer = true, $tplpath = NULL, $return_content = false, $convertVariablesToHtmlentities = true) {

        $themeDirName = FatUtility::camel2dashed(substr($this->_controller, 0, -(strlen('controller'))));
        $actionName = FatUtility::camel2dashed($this->_action) . '.php';
    
        if ($tplpath == NULL) {
            if (file_exists(CONF_THEME_PATH . $themeDirName . '/' . $actionName)) {
                $this->renderingTpl = $themeDirName . '/' . $actionName;
                
            } elseif (file_exists(CONF_THEME_PATH . $themeDirName . '/default.php')) {
                $this->renderingTpl = $themeDirName . '/default.php';
            } else {
                $this->renderingTpl = 'default.php';
            }
        } else {
            $this->renderingTpl = $tplpath;
        }

        if ($return_content) ob_start();
		
        if ($convertVariablesToHtmlentities) {
            $this->variables = $this->addHtmlEntities($this->variables);
        }

        extract($this->variables);

        /* Include header */
        if ($include_header) {
            if (file_exists(CONF_THEME_PATH . $themeDirName . '/header.php')) {
                include CONF_THEME_PATH . $themeDirName . '/header.php';
            } else {
                include CONF_THEME_PATH . 'header.php';
            }
        }
        /* Include header ends */

        /* Include Main */
        include CONF_THEME_PATH . $this->renderingTpl;
        /* Include Main ends */

        /* Include footer */
        if ($include_footer) {
            if (file_exists(CONF_THEME_PATH . $themeDirName . '/footer.php')) {
                include CONF_THEME_PATH . $themeDirName . '/footer.php';
            } else {
                include CONF_THEME_PATH . 'footer.php';
            }
        }
        /* Include footer ends */
		if ($return_content) return ob_get_clean();
    }

    function getVariablesAsHtmlEntities() {
        return $this->addHtmlEntities($this->variables);
    }

    private function addHtmlEntities($var) {
        if (is_array($var)) {
            foreach ($var as $key => $val)
                $var[$key] = $this->addHtmlEntities($val);
        } elseif (is_string($var) || is_numeric($var)) {
            $var = htmlentities($var);
        }
        return $var;
    }

    public function addJs($file) {
        if (is_array($file)) {
            foreach ($file as $fl) {
                $this->addJs($fl);
            }
            return;
        }
        if (!in_array($file, $this->arr_page_js))
            $this->arr_page_js[] = $file;
    }

    public function addCss($file) {
        if (is_array($file)) {
            foreach ($file as $fl) {
                $this->addCss($fl);
            }
            return;
        }
        if (!in_array($file, $this->arr_page_css))
            $this->arr_page_css[] = $file;
    }


}
