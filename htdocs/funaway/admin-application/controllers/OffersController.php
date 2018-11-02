<?php
class OffersController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action) {
		$ajaxCallArray = array("cuisinesListing","changeCuisinesOrder",'cuisinesAction','cuisinesForm');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewOffer($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditOffer($this->admin_id);
		if(!$this->canView){
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	
	}
	
	function index() {
		if(!$this->canView){
			FatUtility::dieError("Unauthorized Access");
		} 
		$form =  $this->getSearchForm();
		$brcmb = new Breadcrumb();
		$brcmb->add("Offers Management");
		$this->set('breadcrumb',$brcmb->output());
		$this->set('search', $form);
		$this->_template->render();

	}
	
	function view($offer_id){
		$offer_id = FatUtility::int($offer_id);
		if($offer_id <=0){
			FatUtility::dieError('Invalid Request!');
		}
		$ofr = new Offers();
		$records = $ofr->getOffer($offer_id);
		$languages = Info::getLanguages();
		$this->set('records',$records);
		$this->set('languages',$languages);
		$this->set('offer_id',$offer_id);
		$this->_template->render(false,false,'offers/_partial/view.php');
	}
	
	function lists($page=1){
		$page = FatUtility::int($page);
		if($page <=0 ){
			$page = 1;
		}
		if(!$this->canView){
			dieError(t_lang("UNATUTHORIZED_ACCESS"));
		}
		$form =  $this->getSearchForm();
		$post = $form->getFormDataFromArray(FatApp::getPostedData());	
		$pagesize = static::PAGESIZE;
		$offers = new Offers();
		$srch = $offers->getOffersSearch();
		if(!empty($post['keyword'])){
			$con = $srch->addCondition('discoupon_code','like','%'.$post['keyword'].'%');
			$con->attachCondition('discount_description','=','%'.$post['keyword'].'%');
		}
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
	
		$this->set("arr_listing",$records);
		$this->set('totalPage',$srch->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		
		$this->set('pageSize', static::PAGESIZE);
		$htm = $this->_template->render(false,false,"offers/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getSearchForm() {
	
		$frm = new Form('frmUserSearch_tab_1');
		$frm->addTextBox('Code/Description', 'keyword','',array('class'=>'search-input'));
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
	
		return $frm;
	}
	
	function couponForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$post['discoupon_id'] = isset($post['discoupon_id'])?FatUtility::int($post['discoupon_id']):0;
		$frm = $this->getCouponForm();
		$offers = new Offers();
		$restForm = $this->getResturantForm($post['discoupon_id']);
		$productForm = $this->getProductForm($post['discoupon_id']);
		$cityForm = $this->getCityForm($post['discoupon_id']);
		if($post['discoupon_id'] > 0){
			$data = $offers->getOffer($post['discoupon_id']);
			$data['weekdays'] = $offers->getOfferWeekDaysForForm($post['discoupon_id']);
			$coupon_rest['discount_restaurant'] = $offers->getCouponRestaurant($post['discoupon_id']);
			$coupon_product['discount_product'] = $offers->getCouponProducts($post['discoupon_id']);
			$coupon_city['discount_city'] = $offers->getCouponCity($post['discoupon_id']);
			$frm->fill($data);
			$restForm->fill($coupon_rest);
			$productForm->fill($coupon_product);
			$cityForm->fill($coupon_city);
		}
		
		$this->set('frm',$frm);
		$this->set('restForm',$restForm);
		$this->set('productForm',$productForm);
		$this->set('cityForm',$cityForm);
		$htm = $this->_template->render(false,false,"offers/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function couponFormAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getCouponForm();
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
		$post['discoupon_id'] = !empty($post['discoupon_id'])?FatUtility::int($post['discoupon_id']):0;
		
		$offers = new Offers();
		$language = Info::getLanguages();
		$offer_id = $offers->saveUpdateOffer($post,$post['discoupon_id']);
		if(!$offer_id){
			FatUtility::dieJsonError($offers->getError());
		}
		foreach($language as $lang){
			$lang_data['discount_description'] = $post['discount_description'][$lang['language_id']];
			$lang_data['discountlang_discount_id'] = $offer_id;
			$lang_data['discountlang_lang_id'] = $lang['language_id'];
			if(!$offers->saveUpdateOfferLang($lang_data)){
				FatUtility::dieJsonError('Something went wrong!');
			}
		}
		$offers->deleteOfferWeekDays($offer_id);
		if($post['discoupon_weekday_specific'] == 1){
			if(!$offers->saveOfferWeekDays($post['weekdays'], $offer_id)){
				FatUtility::dieJsonError('Something went wrong!');
			}
		}
		if($post['discoupon_type'] != 1){
			$offers->deleteCouponRestaurants($offer_id);
		}
		if($post['discoupon_type'] != 2){
			$offers->deleteCouponProducts($offer_id);
		}
		FatUtility::dieJsonSuccess('Update successfully!');
		
	}
	
	function couponRestFormAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$offer_id = isset($post['offer_id'])?FatUtility::int($post['offer_id']):0;
		if($offer_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$frm = $this->getResturantForm($offer_id);
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$ofr = new Offers();
		if(!$ofr->deleteCouponRestaurants($offer_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		if(!$ofr->saveUpdateCouponRestaurant($post['discount_restaurant'], $offer_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		FatUtility::dieJsonSuccess('Update successfully!');
	}
	
	function couponCityFormAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$offer_id = isset($post['offer_id'])?FatUtility::int($post['offer_id']):0;
		if($offer_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$frm = $this->getCityForm($offer_id);
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$ofr = new Offers();
		if(!$ofr->deleteCityRestaurants($offer_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		if(!$ofr->saveUpdateCouponCity($post['discount_city'], $offer_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		FatUtility::dieJsonSuccess('Update successfully!');
	}
	
	
	function couponProductFormAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$offer_id = isset($post['offer_id'])?FatUtility::int($post['offer_id']):0;
		if($offer_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$frm = $this->getProductForm($offer_id);
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$ofr = new Offers();
		if(!$ofr->deleteCouponProducts($offer_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		if(!$ofr->saveUpdateCouponProducts($post['discount_product'], $offer_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		FatUtility::dieJsonSuccess('Update successfully!');
	}
	
	private function getCouponForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$lang_fields =array(
					'discount_description'=>array('caption'=>'Description','type'=>'textarea','required'=>true)
					);
		$frm = new Form('frmCoupon');
		$frm->addHiddenField('','discoupon_id');
		$frm->addHiddenField('','discoupon_by_admin',1);
		$frm->addRequiredField(Info::t_lang('CODE'),'discoupon_code');
		$language = Info::getLanguages();
		foreach($lang_fields as $lang_field_name=>$lang_field){
			foreach($language as $lang){
				switch($lang_field['type']){
					case 'textarea':
							$fld = $frm->addTextArea($lang_field['caption'].'['.Info::t_lang($lang['language_name']).']',$lang_field_name.'['.$lang['language_id'].']');
						break;
						
					default:
						$fld = $frm->addTextBox($lang_field['caption'].'['.Info::t_lang($lang['language_name']).']',$lang_field_name.'['.$lang['language_id'].']');
				}
				if($lang_field['required']){
					$fld->requirements()->setRequired();
				}
				
			}
		}
		$frm->addRequiredField('Discount Amount','discoupon_discount');
		$frm->addRequiredField('Coupon Limit','discoupon_limit',1);
		$frm->addSelectBox('Coupon type','discoupon_type',Info::getCouponType(),'',array(),'');
		$frm->addSelectBox('Discount type','discoupon_discount_type',Info::getCouponDiscountType(),'',array(),'');
		$frm->addRadioButtons('Weekday Specific','discoupon_weekday_specific',Info::getYesNo());
		$weekdays = Info::weekdays();
		$weekday = $frm->addCheckBoxes('Select Weekdays','weekdays',$weekdays);
		
		$frm->addTextBox('Minimum Order','discoupon_min_order');
		$frm->addTextBox('Maximum Discount','discoupon_max_discount');
		$d1 = $frm->addDateField('Valid From','discoupon_valid_from','',array('readonly'=>'readonly'));
		
		$frm->addDateField('Valid Upto','discoupon_valid_upto','',array('readonly'=>'readonly'));
		$frm->addSelectBox('Status','discoupon_active',Info::getStatus(),'',array(),''); 
		$frm->addSubmitButton('','submit_btn',Info::t_lang('ADD/UPDATE'));
		$frm->addButton('','cancel',Info::t_lang('Cancel'));
		return $frm;
	}
	
	private function getResturantForm($offer_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('frmRestaurant');
		if(empty($offer_id)){
			$frm->addHtml("","",'Please add Coupon first.');
			return $frm;
		}
		$offers = new Offers();
		
		$frm->addHiddenField('','offer_id',$offer_id);
		
		$restaurants = $offers->getRestaurantsForForm();
		
		$frm->addCheckBoxes('Restaurants','discount_restaurant',$restaurants)->requirements()->setRequired();
		$submit_btn = $frm->addSubmitButton('','submit_btn',Info::t_lang('ADD/UPDATE'));
		$submit_btn->developerTags['col']=3;
		$cancel = $frm->addButton('','cancel',Info::t_lang('Cancel'));
		$cancel->developerTags['col']=3;
		$cancel->setFieldTagAttribute('onclick','closeForm()');
		return $frm;
	}
	
	private function getProductForm($offer_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('frmRestaurantProduct');
		if(empty($offer_id)){
			$frm->addHtml("","",'Please add Coupon first.');
			return $frm;
		}
		$frm->addHiddenField('','offer_id',$offer_id);
		$offers = new Offers();
		$products = $offers->getProductsForForm();
		
		$frm->addCheckBoxes('Products','discount_product',$products)->requirements()->setRequired();
		$submit_btn = $frm->addSubmitButton('','submit_btn',Info::t_lang('ADD/UPDATE'));
		$submit_btn->developerTags['col']=3;
		$cancel = $frm->addButton('','cancel',Info::t_lang('Cancel'));
		$cancel->developerTags['col']=3;
		$cancel->setFieldTagAttribute('onclick','closeForm()');
		return $frm;
	}
	
	private function getCityForm($offer_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('frmRestaurantProduct');
		if(empty($offer_id)){
			$frm->addHtml("","",'Please add Coupon first.');
			return $frm;
		}
		$frm->addHiddenField('','offer_id',$offer_id);
		$location = new Location();
		$cities = $location->getCitiesForForm();
		
		$frm->addCheckBoxes('City','discount_city',$cities)->requirements()->setRequired();
		$submit_btn = $frm->addSubmitButton('','submit_btn',Info::t_lang('ADD/UPDATE'));
		$submit_btn->developerTags['col']=3;
		$cancel = $frm->addButton('','cancel',Info::t_lang('Cancel'));
		$cancel->developerTags['col']=3;
		$cancel->setFieldTagAttribute('onclick','closeForm()');
		return $frm;
	}
}
?>