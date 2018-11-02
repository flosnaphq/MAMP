<?php
class InfoController extends FatController {
	private $common;
	private $task;
    
	public function captcha() {
		require_once CONF_INSTALLATION_PATH . 'library/securimage/securimage.php';
	#	print_r($_SESSION);
		$img = new Securimage();
		$s = $img->show(); 
	}
	
	function lang($lang_id){
		if(!in_array($lang_id,array(1,2))){
			$lang_id = 1;
		}
		Info::setDefaultLang($lang_id);
		FatApp::redirectUser($_SERVER["HTTP_REFERER"]);
	}
	
	function regions($city_id){
		$regions = Location::getRegionforForm($city_id);
		$options = "<option value=''>".Info::t_lang("Select Region")."</option>";
		foreach($regions as $k=>$v){
			$options .= "<option value='$k'>$v</option>";
		}
		die(FatUtility::convertToJson(array("options"=>$options)));
	}
	
	
	function get_zip($zip){
		$detail = getCityState($zip);
		if($detail){
			FatUtility::dieWithResponse(array("status"=>1,"detail"=>$detail));
		}else{
			FatUtility::dieWithResponse(array("status"=>0,"error"=>Info::t_lang("Invalid Zip Code")));
		}
		
	}
	
}