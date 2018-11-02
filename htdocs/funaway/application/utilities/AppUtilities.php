<?php

class AppUtilities extends FatUtility
{
	const RECORD_INACTIVE_STATUS = 0;
	const RECORD_ACTIVE_STATUS = 1;
	
	public static $_instance = null;
	
	private function __construct() {}
	
	public static function getInstance()
	{
		if(null == static::$_instance) {
			static::$_instance = new AppUtilities();
		}
		return static::$_instance;
	}
	
	public static function includeFonts()
	{
		$str = '';
		if(defined('SYSTEM_FRONT') && true === SYSTEM_FRONT) {
				$str .= "@font-face {
					font-family: 'Dry Brush';
					src: url(" . static::generateFullUrl('fonts','dry_brush-webfont.eot', array(), CONF_WEBROOT_URL) . ")  format('embedded-opentype');
					src: url(" . static::generateFullUrl('fonts','dry_brush-webfont.eot?#iefix', array(), CONF_WEBROOT_URL) . ") format('embedded-opentype'),
					url(" . static::generateFullUrl('fonts','dry_brush-webfont.woff2', array(), CONF_WEBROOT_URL) . ") format('woff2'),
					url(" . static::generateFullUrl('fonts','dry_brush-webfont.woff', array(), CONF_WEBROOT_URL) . ") format('woff'),
					url(" . static::generateFullUrl('fonts','dry_brush-webfont.ttf', array(), CONF_WEBROOT_URL) . ") format('truetype'),
					url(" . static::generateFullUrl('fonts','dry_brush-webfont.svg#dry_brushregular', array(), CONF_WEBROOT_URL) . ") format('svg');
					font-weight: normal;
					font-style: normal;
				}";
				
				$str .= '@import url("https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700");';
		} else {
			$str .= "@font-face {
						font-family: 'WebRupee';
						src: url('" . static::generateUrl('fonts', '', array('WebRupee.V2.0.eot'), CONF_BASE_DIR) . "');
						src: local('" . static::generateUrl('fonts', '', array('WebRupee'), CONF_BASE_DIR) . "'), 
						url('" . static::generateUrl('fonts', '', array('WebRupee.V2.0.ttf'), CONF_BASE_DIR) . "') format('truetype'), 
						url('" . static::generateUrl('fonts', '', array('WebRupee.V2.0.woff'), CONF_BASE_DIR) . "') format('woff'), 
						url('" . static::generateUrl('fonts', '', array('WebRupee.V2.0.svg'), CONF_BASE_DIR) . "') format('svg');
						font-weight:normal;
						font-style:normal;
					}";
					
			$str .= '@font-face { font-family: "Ionicons"; src: url("' . static::generateUrl('fonts', '', array('ionicons.eot?v=2.0.0'), CONF_BASE_DIR) . '"); src: url("' . static::generateUrl('fonts', '', array('ionicons.eot?v=2.0.0#iefix'), CONF_BASE_DIR) . '") format("embedded-opentype"), url("' . static::generateUrl('fonts', '', array('ionicons.ttf?v=2.0.0'), CONF_BASE_DIR) . '") format("truetype"), url("' . static::generateUrl('fonts', '', array('ionicons.woff?v=2.0.0'), CONF_BASE_DIR) . '") format("woff"), url("' . static::generateUrl('fonts', '', array('ionicons.svg?v=2.0.0#Ionicons'), CONF_BASE_DIR) . '") format("svg"); font-weight: normal; font-style: normal; }';
			
			$str .= '@import url(http://fonts.googleapis.com/css?family=Open+Sans:800,800italic,700italic,700,600italic,600,400italic,400,300italic,300);';
			
		}
		return $str;
	}
}