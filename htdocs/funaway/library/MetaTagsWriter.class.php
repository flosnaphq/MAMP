<?php

class MetaTagsWriter {

    static function getHeaderTags($controller, $action, $arrParameters, $anotherTitle = '', $metaData = array()) {
        $title = '';
        $data  = '';
        if ($anotherTitle == '') {

            $controller = explode('-', FatUtility::camel2dashed($controller));

            if (current($controller) == 'home')
                $title = '';
            else
                $title = implode(' ', $controller);

            if ($title != '')
                $title = trim(str_replace('controller', '', $title));

            if ($action != '') {

                $action = explode('-', FatUtility::camel2dashed($action));
                if (current($action) == 'index')
                    $actionTitle = '';
                else
                    $actionTitle = implode(' ', $action);

                if ($actionTitle != '')
                    $title .= ( ( $title ) ? ' - ' : '' ) . $actionTitle;
            }
        } else{
            $title = $anotherTitle;
            $data .= self::getFrontendTitle(ucwords($title));
            }

        
        $data .= self::getMetaTags($metaData);

        return $data;
    }

    static function getDefaultMeta() {
        $metaData['title'] = FatApp::getConfig('meta_title');
        $metaData['keywords'] = FatApp::getConfig('meta_keyword');
        $metaData['description'] = FatApp::getConfig('meta_description');
        $metaData['og:image'] = FatUtility::generateFullUrl('image', 'ogImage', array(FatApp::getConfig('og_image')), '/');
        $metaData['og:type'] = FatApp::getConfig('og_type');
        $metaData['og:title'] = FatApp::getConfig('og_title');
        $metaData['og:url'] = FatUtility::generateFullUrl('', '', array(), '/');
        $metaData['og:description'] = FatApp::getConfig('og_description');
        $metaData['other_tags'] = FatApp::getConfig('CONF_META_OTHER_TAGS');
        return $metaData;
    }

    static function getAdminTitle($title = '') {
        if (strlen(trim($title)) > 0) {
            echo '<title> Administrator | ' . $title . ' | ' . FatApp::getConfig("conf_website_name") . '</title>' . "\n";
        } else {
            echo '<title> Administrator | ' . FatApp::getConfig("conf_website_name") . '</title>' . "\n";
        }
    }

    static function getFrontendTitle($title = '') {

        if (strlen(trim($title)) > 0) {
            return '<title>' . $title . ' | ' . FatApp::getConfig("conf_website_title") . '</title>' . "\n";
        } else {
            return '<title>' . FatApp::getConfig("conf_website_title") . '</title>' . "\n";
        }
    }

    static function getMetaTags($metaData = array()) {

        $data = '';
        if (is_array($metaData) && !empty($metaData)) {
            foreach ($metaData as $name => $content) {
                if ($name == 'other_tags') {
                    $data .= html_entity_decode($content);
                } else if ($name == 'title') {

                    $title = $content . ' | ' . FatApp::getConfig('CONF_WEBSITE_NAME');
                    $data .= '<title>' . $title . '</title>' . "\n";
                } else {

                    $data .= '<meta name="' . $name . '" content="' . $content . '" />' . "\n";
                }
            }
        }

        return $data;
    }

}
