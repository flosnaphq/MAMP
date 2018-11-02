<?php
class RestaurantsController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	const PAGESIZE=50;
	public function __construct($action) {
		$ajaxCallArray = array("cuisinesListing","changeCuisinesOrder",'cuisinesAction','cuisinesForm');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewRestaurants($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditRestaurants($this->admin_id);
		$this->canViewReview = AdminPrivilege::canViewReview($this->admin_id);
		$this->canEditReview = AdminPrivilege::canEditReview($this->admin_id);
	
		if(!$this->canView){
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
		$this->set("canViewReview",$this->canViewReview);
		$this->set("canEditReview",$this->canEditReview);
	
	}
	
	public function index($merchant_id=0) {
		$merchant_id = FatUtility::int($merchant_id);
		$brcmb = new Breadcrumb();
		$brcmb->add("Restaurants Management");
		$frm_1 = $this->getSearchForm(1);
		$frm_2 = $this->getSearchForm(2);
		$this->set('breadcrumb',$brcmb->output());
		$this->set('merchant_id',$merchant_id);
		$this->set('frm_1',$frm_1);
		$this->set('frm_2',$frm_2);
		$this->_template->render();
		
	}
	
	public function detail($restaurant_id) {
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatApp::redirectUser(FatUtility::generateUrl('restaurants'));
		}
		$rest = new Restaurant();
		$user = new Users();
		$records = $rest->getRestaurant($restaurant_id);
		$userData = $user->getUser($records['restaurant_user_id']);
		
		$restaurant_name = isset($records['restaurant_name'][Info::defaultLang()])?$records['restaurant_name'][Info::defaultLang()]:'';;
		$brcmb = new Breadcrumb();
		$brcmb->add("Restaurants Management",FatUtility::generateUrl('restaurants'));
		$brcmb->add($restaurant_name);
		$search = $this->getRegionSearchForm();
		$this->set('search',$search);
		$this->set('breadcrumb',$brcmb->output());
		$this->set('restaurant_id',$restaurant_id);
		$this->set('records',$records);
		$this->set('userData',$userData);
		$this->_template->render();
		
	}

	function listsTab($restaurant_id,$tab=1,$page=1){
		$restaurant_id = isset($restaurant_id)?FatUtility::int($restaurant_id):0;
		if($restaurant_id <=0){
			FatUtility::dieJsonError('Invalid request!');
		}
		
		$tab = isset($tab)?FatUtility::int($tab):1;
		$page = isset($page)?FatUtility::int($page):1;
		
		$this->set('tab',$tab);
		if($tab == 1){
			$this->toppingGroupListing($restaurant_id,$page);
		}
		elseif($tab == 2){
			$this->toppingListing($restaurant_id,$page);
		}
		elseif($tab == 3){
			$this->menuGroupListing($restaurant_id,$page);
		}
		elseif($tab == 4){
			$this->menuListing($restaurant_id,$page);
		}
		elseif($tab == 5){
			$this->regionsLists($restaurant_id,$page);
		}
		elseif($tab == 6){
			$this->taxLists($restaurant_id);
		}
		elseif($tab == 8){
			$this->reviewsLists($restaurant_id);
		}
		elseif($tab == 9){
			$this->report($restaurant_id);
		}
		else{
			FatUtility::dieJsonError('Invalid request!');
		}
	}
	
	function report($restaurant_id){
		$rest =new Restaurant();
		$arr_listing = $rest->getRestaurantReport($restaurant_id);
		
		$this->set('arr_listing',$arr_listing);
		$htm = $this->_template->render(false,false,"restaurants/_partial/report.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}

	function downloadReport($restaurant_id){
		if(!$this->canView){
			FatUtility::dieJsonError("Unauthorized Access");
		}
		$rows = array();
		$rest =new Restaurant();
		$records = $rest->getRestaurantReport($restaurant_id);
		if(!empty($records)){
			$rows[] = array(
						'time'=>'Today',
						'orders'=>$records['today_total'],
						'sales'=>$records['today_sale'],
						);
			$rows[] = array(
						'time'=>'Current Month',
						'orders'=>$records['current_month_total'],
						'sales'=>$records['current_month_sale'],
						);
			$rows[] = array(
						'time'=>'Current Year',
						'orders'=>$records['current_year_total'],
						'sales'=>$records['current_year_sale'],
						);
			$rows[] = array(
						'time'=>'Last 6 Months',
						'orders'=>$records['last_6_month_total'],
						'sales'=>$records['last_6_month_sale'],
						);
			$rows[] = array(
						'time'=>'Last Year',
						'orders'=>$records['last_year_total'],
						'sales'=>$records['last_year_sale'],
						);
		}
		//Info::test($rows);
		 header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=report.csv');
		$output = fopen('php://output', 'w');
		$csv_headers = array('Report Time', 'Total Orders','Total Sales');
		
		fputcsv($output, $csv_headers);
		foreach ($rows as $rec)
		fputcsv($output, $rec); 
	}
	
	function reviewsLists($restaurant_id, $page=1){
		if(!$this->canViewReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$page = FatUtility::int($page);
		$page = $page<=0?1:$page;
		$pagesize = static::PAGESIZE;
		
		$reviews = new Reviews();
		$src = $reviews->getReviewSearch($restaurant_id);
		if($page > 0 && $pagesize >0){
			$src->setPageNumber($page);
			$src->setPageSize($pagesize);
		}
		$rs = $src->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$src->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"restaurants/_partial/review-list.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function viewReview($review_id){
		if(!$this->canViewReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$reviewObj = new Reviews();
		$records = $reviewObj->getRestaurantReview($review_id);
		$this->set('records',$records);
		$this->_template->render(false,false,'restaurants/_partial/view-review.php');
	}
	
	function abuseform($restaurant_id){
		if(!$this->canEditReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$abreport_id = isset($post['abreport_id'])?FatUtility::int($post['abreport_id']):0;
		if($abreport_id <= 0){
			FatUtility::dieJsonError('Invalid Request');
		}
		$abReport =new AbuseReport();
		$frm = $this->getAbuseReportForm();
		$report = $abReport->getAbuseReportById($abreport_id);
		$frm->fill($report);
		$this->set('frm', $frm);
		$htm = $this->_template->render(false,false,'restaurants/_partial/abuse-form.php',true,true);
		FatUtility::dieJsonSuccess($htm);
		
	}
	
	
	function abuseAction(){
		if(!$this->canEditReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = $this->getAbuseReportForm();
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		if($post == false){
			FatUtility::dieJsonError('Something Went Wrong');
		}
		$abReport = new AbuseReport();
		if(!$abReport->addUpdate($post)){
			FatUtility::dieJsonError('Something Went Wrong');
		}
		FatUtility::dieJsonSuccess('Record Updated');
	}
	private function getAbuseReportForm(){
		if(!$this->canEditReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('reviewForm');
		$frm->addHiddenField('','abreport_id' );
		$frm->addHiddenField('','abreport_record_id' );
		$frm->addHiddenField('','abreport_record_type' );
		$frm->addHiddenField('','abreport_user_id' );
		$frm->AddTextArea('Comment','abreport_comments')->requirements()->setRequired();
		$frm->addSelectBox('Status','abreport_taken_care',Info::getAbuseReportStatus());
		$frm->addSubmitButton('','submit_btn','UPDATE');
		return $frm;
	}
	

	function reviewForm($restaurant_id){
		if(!$this->canEditReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid Request');
		}
		$post = FatApp::getPostedData();
		$review_id = isset($post['review_id'])?$post['review_id']:0;
		if($review_id <= 0){
			FatUtility::dieJsonError('Invalid Request');
		}
		$frm = $this->getReviewForm($restaurant_id);
		$reviewObj = new Reviews();
		$review = $reviewObj->getRestaurantReview($review_id);
		$frm->fill($review);
		$this->set('frm', $frm);
		$this->set('restaurant_id', $restaurant_id);
		$htm = $this->_template->render(false,false,'restaurants/_partial/review-form.php',true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function reviewAction($restaurant_id){
		if(!$this->canEditReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid Request');
		}
		$frm = $this->getReviewForm($restaurant_id);
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		if($post == false){
			FatUtility::dieJsonError('Something went wrong');
		}
		$reviewObj = new Reviews();
		if(!$reviewObj->saveReview($post, $post['review_id'])){
			FatUtility::dieJsonError('Something went wrong');
		}
		FatUtility::dieJsonSuccess('Record Updated');
	}
	
	private function getReviewForm($rest_id){
		if(!$this->canEditReview){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('reviewForm');
		$frm->addHiddenField('','review_id' );
		$frm->addHiddenField('','review_entity_id', $rest_id);
		$frm->addHiddenField('','review_entity_type');
		$frm->addHiddenField('','review_user_id');
		$frm->addTextBox('Title','review_title')->requirements()->setRequired();
		$frm->AddTextArea('Content','review_content')->requirements()->setRequired();
		$frm->addSelectBox('Status','review_active',Info::getReviewStatus());
		$frm->addSubmitButton('','submit_btn','UPDATE');
		return $frm;
	}
	
	function taxLists($restaurant_id){
		
		$restaurant_id = FatUtility::int($restaurant_id);
		if(empty($restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
		$rest = new Restaurant();
		$extra = $rest->getRestaurantExtra($restaurant_id);
		$this->set("arr_listing",$extra);
		$html = $this->_template->render(false,false,'restaurants/_partial/tax-listing.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	
	
	function menuListing($restaurant_id, $page=1){
		$page = FatUtility::int($page);
		$product = new Product();
		$search = $product->getProductSearch($restaurant_id);
		$pagesize = static::PAGESIZE;
		if($page > 0 && $pagesize >0){
			$search->setPageNumber($page);
			$search->setPageSize($pagesize);
		}
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($search->getResultSet());
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"restaurants/_partial/menu-listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function viewMenu($product_id){
		$product_id = FatUtility::int($product_id);
		if($product_id <= 0){
			FatUtility::dieJsonError('Invalid Request');
		}
		$product = new Product();
		$menu_item = $product->getProduct($product_id);
		$size_prices = $product->getProductPrices($product_id);
		$languages = Info::getLanguages();
		$this->set('records',$menu_item);
		$this->set('size_prices',$size_prices);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"restaurants/_partial/view-menu.php");
		
	}
	
	function menuForm($restaurant_id){
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid request!');
		}
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		$form = $this->getMenuForm($restaurant_id);
		$post = FatApp::getPostedData();
		$post['product_id'] = isset($post['product_id'])?FatUtility::int($post['product_id']):0;
		$product = new Product();
		$menu = new Menu();
		if($post['product_id'] > 0){
			$menu_item = $product->getProduct($post['product_id']);
			if(empty($menu_item)){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
			$menu_group = $menu->getMenuGroup($menu_item['product_restmenu_id']);
			if(empty($menu_group) || $menu_group['restmenu_restaurant_id'] != $restaurant_id){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
			$item_topping = $menu->getItemToppings($menu_item['product_id']);
			$product_size = $product->getProductSizePriceForForm($menu_item['product_id']);
			$menu_item['toppings'] = $item_topping;
			$menu_item['prodsize_price'] = $product_size;
			$form->fill($menu_item);
		}
		$this->set('restaurant_id',$restaurant_id);
		$this->set('frm',$form);
		$this->set('imageFrm',$this->getMenuImageForm($post['product_id']));
		$htm = $this->_template->render(false,false,"restaurants/_partial/menu-form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
			
	}
	
	function menuFormAction($restaurant_id){
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid request!');
		}
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getMenuForm($restaurant_id);
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['product_id'] = isset($post['product_id'])?FatUtility::int($post['product_id']):0;
		$menu = new Menu();
		$product = new Product();
		
		if(!empty($post['product_id'])){
			$menu_item = $product->getProduct($post['product_id']);
			if(empty($menu_item)){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
			$menu_group = $menu->getMenuGroup($menu_item['product_restmenu_id']);
			if(empty($menu_group) || $menu_group['restmenu_restaurant_id'] != $restaurant_id){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
		}
		$menu_group = $menu->getMenuGroup($post['product_restmenu_id']);
		if(empty($menu_group) || $menu_group['restmenu_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		
		if(!$product_id = $product->saveProduct($post, $post['product_id'])){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['image_file_id'] = isset($post['image_file_id'])?FatUtility::int($post['image_file_id']):0;
		if(!empty($post['image_file_id'])){
			AttachedFile::removeAllFiles(AttachedFile::FILETYPE_PRODUCT_PHOTO, $product_id);
			$update_data['afile_record_id'] = $product_id;
			if(!AttachedFile::updateByAfileId($update_data,$post['image_file_id'])){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		$language = Info::getLanguages();
		foreach($language as $lang){
			$data['productlang_product_id'] = $product_id;
			$data['productlang_lang_id'] = $lang['language_id'];
			$data['product_name'] = $post['product_name'][$lang['language_id']];
			$data['product_desc'] = $post['product_desc'][$lang['language_id']];
			if(!$product->saveUpdateProductLang($data)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		$toppings = isset($post['toppings'])?$post['toppings']:array();
		if(!$menu->saveItemToppings($toppings,$product_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
			if(empty($post['prodsize_price'])){
			FatUtility::dieJsonError(Info::t_lang('PLEASE_ADD_ATLEAST_ONE_PRICE!'));
		}
		;
		if(!$product->saveProductPrice($post['prodsize_price'], $product_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}

	function changeMenuDisplayOrder($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid request!');
		}
		$post = FatApp::getPostedData();
		$product_id = isset($post['product_id'])?FatUtility::int($post['product_id']):0;
		$post['display_order'] = isset($post['display_order'])?$post['display_order']:0;
		if($product_id <=0 ){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$menu = new Menu();
		$product = new Product();
		$product_detail = $product->getProduct($product_id);
		if(empty($product_detail)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$menu_group = $menu->getMenuGroup($product_detail['product_restmenu_id']);
		if(empty($menu_group) || $menu_group['restmenu_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$data['product_display_order'] = $post['display_order'];
		if(!$product_id = $product->saveProduct($data, $product_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('DISPLAY_ORDER_CHANGED!'));
	}
	
	private function getMenuForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$langFields =array(
				'product_name' =>array('caption'=>Info::t_lang("ITEM_NAME"),'type'=>'textbox','required'=>true),
				'product_desc' =>array('caption'=>Info::t_lang("DESCRIPTION"),'type'=>'textarea','required'=>true),
		);
		$frm = new Form('frmMenuForm');
		$frm->addHiddenField("", 'product_id');
		$frm->addHiddenField('','image_file_id');
		$language = Info::getLanguages();
		foreach($langFields as $field_name=>$field){
			foreach($language as $lang){
				switch($field['type']){
					case 'textarea':
						$fld = $frm->addTextArea($field['caption']."[".Info::t_lang($lang['language_name'])."]", $field_name."[".$lang['language_id']."]",'',array('class'=>$lang['language_css']));	
						break;
					default:
						$fld = $frm->addTextBox($field['caption']."[".Info::t_lang($lang['language_name'])."]", $field_name."[".$lang['language_id']."]",'',array('class'=>$lang['language_css']));	
				}
				if($field['required']){
					$fld->requirements()->setRequired();
				}
			}	
		}
		$menu = new Menu();
		$menu_group = $menu->getMenuGroupForForm($restaurant_id);
		$topping = $menu->getAddonForForm($restaurant_id);
		$frm->addSelectBox(Info::t_lang('MENU_GROUP'),'product_restmenu_id',$menu_group,'',array(),Info::t_lang('SELECT_MENU_GROUP'))->requirements()->setRequired();
		//$frm->addRequiredField(Info::t_lang('PRICE'),'product_price');
		$sizes = Info::getProductSizes();
		foreach($sizes as $size_key=>$size_name){
			$frm->addTextBox($size_name.' ['.Info::t_lang('PRICE').']','prodsize_price['.$size_key.']');
		}
		$frm->addTextBox(Info::t_lang('DISPLAY_ORDER'),'product_display_order	');
		$veg_status = Info::getRestaurantVegStatus();
		unset($veg_status[2]);
		$frm->addRadioButtons(Info::t_lang('VEG_STATUS'),'product_veg_status',$veg_status);
		$frm->addRadioButtons(Info::t_lang('STATUS'),'product_active',Info::getStatus());
		$frm->addRadioButtons(Info::t_lang('SPICY'),'product_spicy',Info::getSpicy());
		$frm->addCheckBoxes(Info::t_lang('Toppings'),'toppings',$topping);
		$frm->addSubmitButton('','sumbit_btn',Info::t_lang('SAVE'));
		return $frm;
	}
	
	private function getMenuImageForm($product_id=0){
		$product_id = FatUtility::int($product_id);
		$image_url =($product_id>0)?FatUtility::generateUrl('image','product',array($product_id,100,100,rand(111,999)),'/'):FatUtility::generateUrl('image','showDemoImage',array(0,100,100),CONF_WEBROOT_URL);
		$frm = new Form('frmRestaurantImage');
		$frm->addHtml('','demo-image',"<img src='".$image_url."' id='demo-image'>");
		$frm->addHiddenField('','img-data','');
		$frm->addHiddenField('','type',AttachedFile::FILETYPE_PRODUCT_PHOTO);
		$frm->addFileUpload(Info::t_lang('LOGO/IMAGE'),'photo');
		return $frm;
	}
	
	
	function menuGroupListing($restaurant_id, $page=1){
		$page = FatUtility::int($page);
		$menu = new Menu();
		$search = $menu->getMenuGroupSearch($restaurant_id);
		$pagesize = static::PAGESIZE;
		if($page > 0 && $pagesize >0){
			$search->setPageNumber($page);
			$search->setPageSize($pagesize);
		}
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($search->getResultSet());
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"restaurants/_partial/menu-group-listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function menuGroupForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid request!');
		}
		
		$form = $this->getMenuGroupForm($restaurant_id);
		$post = FatApp::getPostedData();
		$post['restmenu_id'] = isset($post['restmenu_id'])?FatUtility::int($post['restmenu_id']):0;
		$menu = new Menu();
		if($post['restmenu_id'] > 0){
			$menu_group = $menu->getMenuGroup($post['restmenu_id']);
			if(empty($menu_group) || $menu_group['restmenu_restaurant_id'] != $restaurant_id){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
		
			/* $start_time = Info::timeToArray($menu_group['restmenu_start_time']);
			$end_time = Info::timeToArray($menu_group['restmenu_end_time']);
			$menu_group['restmenu_start_time'] = $start_time['hour'].':'.$start_time['min'];
			$menu_group['start_meridiem'] = $start_time['meridiem'];
			$menu_group['restmenu_end_time'] = $end_time['hour'].':'.$end_time['min'];
			$menu_group['end_meridiem'] = $end_time['meridiem'];
			 */
			$menu_group['timings'] = $menu->getMenuTimingForFillForm($post['restmenu_id']);
			$form->fill($menu_group);
		}
		$this->set('restaurant_id',$restaurant_id);
		$this->set('frm',$form);
		$htm = $this->_template->render(false,false,"restaurants/_partial/menu-group-form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
			
	}
	
	function menuGroupFormAction($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid request!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getMenuGroupForm($restaurant_id);
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['restmenu_id'] = isset($post['restmenu_id'])?FatUtility::int($post['restmenu_id']):0;
		$menu = new Menu();
		
		if(!empty($post['restmenu_restaurant_id']) && $post['restmenu_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		 $post['restmenu_restaurant_id'] = $restaurant_id;
		/*$start_time = explode(':',$post['restmenu_start_time']);
		$post['restmenu_start_time'] = Info::converToTime($start_time[0],$start_time[1],$post['start_meridiem']);
		$end_time = explode(':',$post['restmenu_end_time']);
		$post['restmenu_end_time'] = Info::converToTime($end_time[0],$end_time[1],$post['end_meridiem']);
		 */
		if(!$restmenu_id = $menu->saveMenuGroup($post, $post['restmenu_id'])){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
		$language = Info::getLanguages();
		foreach($language as $lang){
			$data['restmenulang_restmenu_id'] = $restmenu_id;
			$data['restmenulang_lang_id'] = $lang['language_id'];
			$data['menu_name'] = $post['menu_name'][$lang['language_id']];
			if(!$menu->saveUpdateMenuGroupLang($data)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		$menu_timings = isset($post['timings'])?$post['timings']:array();
		if(!$menu->saveMenuTimings($menu_timings, $restmenu_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}
	
	
	function changeMenuGroupDisplayOrder($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$restmenu_id = isset($post['restmenu_id'])?FatUtility::int($post['restmenu_id']):0;
		$post['display_order'] = isset($post['display_order'])?$post['display_order']:0;
		if($restmenu_id <=0 ){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$menu = new Menu();
		$menu_group = $menu->getMenuGroup($restmenu_id);
		if(empty($menu_group) || $menu_group['restmenu_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$data['restmenu_display_order'] = $post['display_order'];
		if(!$restmenu_id = $menu->saveMenuGroup($data, $restmenu_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('DISPLAY_ORDER_CHANGED!'));
		
	}
	
	private function getMenuGroupForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('frmMenuGroup');
		$frm->addHiddenField("", 'restmenu_id');
		$frm->addHiddenField("", 'restmenu_restaurant_id');
		$language = Info::getLanguages();
		foreach($language as $lang){
			$frm->addRequiredField(Info::t_lang("TOPPING_GROUP_NAME")."[".Info::t_lang($lang['language_name'])."]", "menu_name[".$lang['language_id']."]",'',array('class'=>$lang['language_css']));			
		}
		
		/* $hours_options = Info::getTimingOptionsForForm();
		$frm->addSelectBox(Info::t_lang('START_TIMING'),'restmenu_start_time',$hours_options,0,array(),'');
		$frm->addSelectBox('','start_meridiem',array(0=>Info::t_lang('AM'),1=>Info::t_lang('PM')),0,array(),'');
		$frm->addSelectBox(Info::t_lang('END_TIMING'),'restmenu_end_time',$hours_options,0,array(),'');
		$frm->addSelectBox('','end_meridiem',array(0=>Info::t_lang('AM'),1=>Info::t_lang('PM')),0,array(),''); */
		$rest = new Restaurant();
		$rest_timings = $rest->getRestaurantMenuTimings($restaurant_id);
		foreach($rest_timings as $rest_timing){
			$menu_timings[$rest_timing['resmenutiming_timing_id']] = Info::getMenuItemTimingByKey($rest_timing['resmenutiming_timing_id']);
		}
		if(!empty($menu_timings)){
			$frm->addCheckBoxes(Info::t_lang('TIMINGS'),'timings',$menu_timings);
		}
		$frm->addSelectBox(Info::t_lang('STATUS'),'restmenu_active',Info::getStatus());
		$frm->addSubmitButton('','sumbit_btn',Info::t_lang('SAVE'));
		return $frm;
	}
	
	
	function toppingListing($restaurant_id, $page=1){
		
		$page = FatUtility::int($page);
		$menu = new Menu();
		$search = $menu->getToppingSearch($restaurant_id);
		$pagesize = static::PAGESIZE;
		
		if($page > 0 && $pagesize >0){
			$search->setPageNumber($page);
			$search->setPageSize($pagesize);
		}
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($search->getResultSet());
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"restaurants/_partial/topping-listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function toppingForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if(empty($restaurant_id)){
			FatUtility::dieJsonError('Invalid request!');
		}
		
		$form = $this->getToppingForm($restaurant_id);
		$post = FatApp::getPostedData();
		$post['addon_id'] = isset($post['addon_id'])?FatUtility::int($post['addon_id']):0;
		$menu = new Menu();
		if($post['addon_id'] > 0){
			$topping = $menu->getTopping($post['addon_id']);
			if(empty($topping)){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
			$group = $menu->getToppingGroup($topping['addon_addongroup_id']);
			if($group['addongroup_restaurant_id'] != $restaurant_id){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
			$form->fill($topping);
		}
		$this->set('frm',$form);
		$this->set('restaurant_id',$restaurant_id);
		$htm = $this->_template->render(false,false,"restaurants/_partial/topping-form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
			
	}
	
	function toppingFormAction($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if(empty($restaurant_id)){
			FatUtility::dieJsonError('Invalid request!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getToppingForm($restaurant_id);
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['addon_id'] = isset($post['addon_id'])?FatUtility::int($post['addon_id']):0;
		$menu = new Menu();
		$group_detail = $menu->getToppingGroup($post['addon_addongroup_id']);
	
		if(empty($group_detail) || $group_detail['addongroup_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		if(!$addon_id = $menu->saveTopping($post, $post['addon_id'])){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$language = Info::getLanguages();
		foreach($language as $lang){
			$data['addonlang_addon_id'] = $addon_id;
			$data['addonlang_lang_id'] = $lang['language_id'];
			$data['addon_name'] = $post['addon_name'][$lang['language_id']];
			if(!$menu->saveUpdateToppingLang($data)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}
	
	function changeToppingStatus(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$addon_id = isset($post['addon_id'])?FatUtility::int($post['addon_id']):0;
		if($addon_id <=0 ){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$menu = new Menu();
		$topping = $menu->getTopping($addon_id);
		if(empty($topping)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$group_detail = $menu->getToppingGroup($topping['addon_addongroup_id']);
		if(empty($group_detail) || $group_detail['addongroup_restaurant_id'] != $this->restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$data['addon_active'] = $topping['addon_active'] == 1?0:1;
		if(!$addon_id = $menu->saveTopping($data, $addon_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}
	
	function changeToppingDisplayOrder($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$post = FatApp::getPostedData();
		$addon_id = isset($post['addon_id'])?FatUtility::int($post['addon_id']):0;
		$post['display_order'] = isset($post['display_order'])?$post['display_order']:0;
		if($addon_id <=0 ){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$menu = new Menu();
		$topping = $menu->getTopping($addon_id);
		if(empty($topping)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$group_detail = $menu->getToppingGroup($topping['addon_addongroup_id']);
		if(empty($group_detail) || $group_detail['addongroup_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$data['addon_display_order'] = $post['display_order'];
		if(!$addon_id = $menu->saveTopping($data, $addon_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('DISPLAY_ORDER_CHANGED!'));
		
	}
	
	private function getToppingForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('frmTopping');
		$frm->addHiddenField("", 'addon_id');
		$language = Info::getLanguages();
		foreach($language as $lang){
			$frm->addRequiredField(Info::t_lang("TOPPING_NAME")."[".Info::t_lang($lang['language_name'])."]", "addon_name[".$lang['language_id']."]",'',array('class'=>$lang['language_css']));			
		}	
		$menu = new Menu();
		$topping_groups = $menu->getToppingGroupForForm($restaurant_id);
		$frm->addSelectBox(Info::t_lang('TOPPING_GROUP'),'addon_addongroup_id',$topping_groups,'',array(),Info::t_lang('SELECT_TOPPING_GROUP'))->requirements()->setRequired();
		$veg_status = Info::getRestaurantVegStatus();
		unset($veg_status[2]);
		$frm->addRadioButtons(Info::t_lang('VEG_STATUS'),'addon_veg_status',$veg_status);
		$frm->addTextBox(Info::t_lang('PRICE'),'addon_price');
		$frm->addRadioButtons(Info::t_lang('STATUS'),'addon_active',Info::getStatus());
		
		$frm->addSubmitButton('','sumbit_btn',Info::t_lang('SAVE'));
		
		return $frm;
	}
	
	function toppingGroupListing($restaurant_id, $page=1){
		$page = FatUtility::int($page);
		$menu = new Menu();
		$topping_groups = $menu->getToppingGroupSearch($restaurant_id);
		$pagesize = static::PAGESIZE;
		
		if($page > 0 && $pagesize >0){
			$topping_groups->setPageNumber($page);
			$topping_groups->setPageSize($pagesize);
		}
		$rs = $topping_groups->getResultSet();
		$records = FatApp::getDb()->fetchAll($topping_groups->getResultSet());
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$topping_groups->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"restaurants/_partial/topping-group-listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function toppingGroupForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		
		
		$form = $this->getToppingGroupForm();
		$post = FatApp::getPostedData();
		$post['group_id'] = isset($post['group_id'])?FatUtility::int($post['group_id']):0;
		$menu = new Menu();
		if($post['group_id'] > 0){
			$group = $menu->getToppingGroup($post['group_id']);
			if($group['addongroup_restaurant_id'] != $restaurant_id){
				FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
			}
			$form->fill($group);
		}
		$this->set('frm',$form);
		$this->set('restaurant_id',$restaurant_id);
		$htm = $this->_template->render(false,false,"restaurants/_partial/topping-group-form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
			
	}
	
	function changeToppingGroupDisplayOrder($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$group_id = isset($post['group_id'])?FatUtility::int($post['group_id']):0;
		$post['display_order'] = isset($post['display_order'])?$post['display_order']:0;
		if($group_id <=0 ){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$menu = new Menu();
		$group_detail = $menu->getToppingGroup($group_id);
		if($group_detail['addongroup_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$data['addongroup_display_order'] = $post['display_order'];
		if(!$group_id = $menu->saveToppingGroup($data, $group_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('DISPLAY_ORDER_CHANGED!'));
	}
	
	function groupFormAction($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getToppingGroupForm();
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['addongroup_id'] = isset($post['addongroup_id'])?FatUtility::int($post['addongroup_id']):0;
		$menu = new Menu();
		if(!empty($post['addongroup_restaurant_id']) && $post['addongroup_restaurant_id'] != $restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$post['addongroup_restaurant_id'] = $restaurant_id;
		if(!$group_id = $menu->saveToppingGroup($post, $post['addongroup_id'])){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$language = Info::getLanguages();
		foreach($language as $lang){
			$data['addongrouplang_addongroup_id'] = $group_id;
			$data['addongrouplang_lang_id'] = $lang['language_id'];
			$data['addongroup_name'] = $post['addongroup_name'][$lang['language_id']];
			if(!$menu->saveUpdateToppingGroupLang($data)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}
	
	private function getToppingGroupForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('frmToppingGroup');
		$frm->addHiddenField("", 'addongroup_id');
		$frm->addHiddenField("", 'addongroup_restaurant_id');
		$language = Info::getLanguages();
		foreach($language as $lang){
			$frm->addRequiredField(Info::t_lang("TOPPING_GROUP_NAME")."[".Info::t_lang($lang['language_name'])."]", "addongroup_name[".$lang['language_id']."]",'',array('class'=>$lang['language_css']));			
		}	
		$frm->addSelectBox(Info::t_lang('STATUS'),'addongroup_active',Info::getStatus());
		$frm->addTextBox(Info::t_lang('Display Order'),'addongroup_display_order');
		$frm->addSubmitButton('','sumbit_btn',Info::t_lang('SAVE'));
		return $frm;
	}
	
	public function regions($restaurant_id) {
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatApp::redirectUser(FatUtility::generateUrl('restaurants'));
		}
		$rest = new Restaurant();
		$user = new Users();
		$records = $rest->getRestaurant($restaurant_id);
		$userData = $user->getUser($records['restaurant_user_id']);
		$restaurant_name = $records['restaurant_name'][Info::defaultLang()];
		$brcmb = new Breadcrumb();
		$brcmb->add("Restaurants Management",FatUtility::generateUrl('restaurants'));
		$brcmb->add($restaurant_name);
		$brcmb->add('Regions');
		$search = $this->getRegionSearchForm();
		$this->set('breadcrumb',$brcmb->output());
		$this->set('search',$search);
		$this->set('restaurant_id',$restaurant_id);
		$this->set('records',$records);
		$this->set('userData',$userData);
		$this->_template->render();
		
	}
	
	function viewRegions($restregion_id){
		$restregion_id = FatUtility::int($restregion_id);
		$tbl = new Restaurants();
		$search = $tbl->getRestaurantRegionsSearch();
		$search->addCondition('restregion_id','=',$restregion_id);
		$records = FatApp::getDb()->fetch($search->getResultSet());
		$location = new Location();
		$city_id = $location->getCityIdByRegionId($records['restregion_region_id']);
		$records['city_id']= $city_id;
		$this->set('records',$records);
		$this->_template->render(false,false,'restaurants/_partial/viewRegions.php');
	}
	
	function deleteRegion(){
		$post = FatApp::getPostedData();
		$restregion_id = isset($post['restregion_id'])?FatUtility::int($post['restregion_id']):0;
		if($restregion_id <= 0){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$rest = new Restaurant();
		$region = $rest->getRestaurantRegion($restregion_id);
		if(empty($region)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$restaurant_id = $region['restregion_restaurant_id'];
		if(!$rest->deleteRestaurantRegion($restregion_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}
	
	public function regionsLists($restaurant_id, $page=1){
		$restaurant_id = FatUtility::int($restaurant_id);
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$pagesize = static::PAGESIZE;
		$page = FatUtility::int($page);
		$page = $page==0?1:$page;
		$form =  $this->getSearchForm();
		$post = $form->getFormDataFromArray(FatApp::getPostedData());
		$tbl = new Restaurants();
		$search = $tbl->getRestaurantRegionsSearch();
		$search->addCondition('restregion_restaurant_id','=',$restaurant_id);
		if(!empty($post['location'])){
			$key_con = $search->addCondition('region_name','like','%'.$post['location'].'%');
			$key_con->attachCondition('city_name','like','%'.$post['location'].'%', 'or');
		}
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', static::PAGESIZE);
		$htm = $this->_template->render(false,false,"restaurants/_partial/regions-listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function regionForm($restaurant_id){
		$restaurant_id = FatUtility::int($restaurant_id);
		$city_id =0;
		$data =array();
		if($restaurant_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$post = FatApp::getPostedData();
		$restregion_id = isset($post['restregion_id'])?FatUtility::int($post['restregion_id']):0;
		if($restregion_id > 0){
			$rest = new Restaurant();
			$data = $rest->getRestaurantRegion($restregion_id);
			$location = new Location();
			$city_id = $location->getCityIdByRegionId($data['restregion_region_id']);
			$data['city_id']= $city_id;
		}
		
		$frm = $this->getRegionsForm($restaurant_id, $city_id);
		$frm->fill($data);
		$this->set('frm',$frm);
		$html = $this->_template->render(false,false,'restaurants/_partial/regions-form.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	function regionsFormAction(){
		$post = FatApp::getPostedData();
		$restaurant_id = isset($post['restregion_restaurant_id'])?FatUtility::int($post['restregion_restaurant_id']):0;
		if(empty($restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$rest = new Restaurant();
		
		$frm = $this->getRegionsForm($restaurant_id);
		$post = $frm->getFormDataFromArray($post,array('restregion_region_id'));
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['restregion_restaurant_id'] = $restaurant_id;
		if(!$rest->saveRestaurantRegion($post)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess(Info::t_lang('RECORD_UPDATED!'));
	}
	
	private function getRegionsForm($restaurant_id,$city_id=0){
		$city_id = FatUtility::int($city_id);
		$frm = new Form('frmDelivery');
		$frm->addHiddenField('','restregion_restaurant_id', $restaurant_id);
		$frm->addHiddenField('','restregion_id');
		$cities = Location::getCitiesForForm();
		reset($cities);
		if(empty($city_id))	$city_id = key($cities);
		$regions = Location::getRegionforForm($city_id);
		$frm->addSelectBox(Info::t_lang('CITY'),'city_id',$cities,'',array(),Info::t_lang('SELECT_CITY'))->requirements()->setRequired();
		$frm->addSelectBox(Info::t_lang('REGION'),'restregion_region_id',$regions,'',array(),Info::t_lang('SELECT_REGION'))->requirements()->setRequired();
		$frm->addRequiredField(Info::t_lang('ZIP_CODE'),'restregion_zipcode');
		$frm->addTextBox(Info::t_lang('Charge'),'restregion_charge');
		$frm->addSubmitButton('','submit_btn',Info::t_lang('SAVE/UPDATE'));
		return $frm;
		
	}
	
	public function lists($page=1,$tab=1, $merchant_id=0){
		$merchant_id = FatUtility::int($merchant_id);
		$tab = FatUtility::int($tab);
		$page = FatUtility::int($page);
		$tab = $tab==0?1:$tab;
		$page = $page==0?1:$page;
		$form =  $this->getSearchForm($tab);
		$post = $form->getFormDataFromArray(FatApp::getPostedData());
		$tbl = new Restaurants();
		$search = $tbl->getSearch($page,static::PAGESIZE);
		 if(!empty($post['keyword'])){
			$key_con = $search->addCondition('restaurant_name','like','%'.$post['keyword'].'%');
			$key_con->attachCondition('restaurant_email','like','%'.$post['keyword'].'%', 'or');
		}
		if($merchant_id > 0){
			$search->addCondition('restaurant_user_id','=',$merchant_id);
		}
		if(!empty($post['location'])){
			$key_con = $search->addCondition('region_name','like','%'.$post['location'].'%');
			$key_con->attachCondition('city_name','like','%'.$post['location'].'%', 'or');
		}
		if(isset($post['restaurant_approved']) && $post['restaurant_approved'] !='' && $post['restaurant_approved'] != -1){
			$restaurant_approved = FatUtility::int($post['restaurant_approved']);
			$search->addCondition('restaurant_approved','=',$restaurant_approved);
		}
		if(isset($post['restaurant_veg_status']) && $post['restaurant_veg_status'] !='' && $post['restaurant_veg_status'] != -1){
			$restaurant_veg_status = FatUtility::int($post['restaurant_veg_status']);
			$search->addCondition('restaurant_veg_status','=',$restaurant_veg_status);
		}
		if($tab == 1){
			$search->addCondition('restaurant_active','=',0);
		}
		elseif(isset($post['restaurant_active']) && $post['restaurant_active'] !=''  && $post['restaurant_active'] != -1){
			$restaurant_active = FatUtility::int($post['restaurant_active']);
			$search->addCondition('restaurant_active','=',$restaurant_active);
		}
		 
		
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('tab', $tab);
		$this->set('postedData', $post);
		$this->set('pageSize', static::PAGESIZE);
		$htm = $this->_template->render(false,false,"restaurants/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function view($restaurant_id){
		$restaurant_id = FatUtility::int($restaurant_id);
		$tbl = new Restaurants();
		$records = $tbl->getRestaurant($restaurant_id);
		$languages = Info::getLanguages();
		$this->set('records',$records);
		$this->set('languages',$languages);
		$this->set('restaurant_id',$restaurant_id);
		$this->_template->render(false,false,'restaurants/_partial/view.php');
	}
	
	function taxForm($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$restaurant_id = FatUtility::int($restaurant_id);
		if(empty($restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$frm = $this->getTaxForm($restaurant_id);
		$rest = new Restaurant();
		$records = $rest->getRestaurant($restaurant_id);
		$extra = $rest->getRestaurantExtra($restaurant_id);
		$frm->fill($extra);
		$restaurant_name = isset($records['restaurant_name'][Info::defaultLang()])?$records['restaurant_name'][Info::defaultLang()]:'';
		$this->set('frm',$frm);
		$this->set('restaurant_id',$restaurant_id);
		$this->set('restaurant_name',$restaurant_name);
		$html = $this->_template->render(false,false,'restaurants/_partial/tax-form.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	function taxFormAction($restaurant_id){
		$restaurant_id = FatUtility::int($restaurant_id);
		if(empty($restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$frm = $this->getTaxForm($restaurant_id);
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$rest = new Restaurant();
		$restaurant = $rest->getRestaurant($restaurant_id);
		if(empty($restaurant)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['restextra_restaurant_id'] = $restaurant_id;
		if(!$rest->saveRestaurantExtra($post, $restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		FatUtility::dieJsonSuccess('Record Updated');
	}
	
	private function getTaxForm($restaurant_id){
		$frm = new Form('frmRestaurant');
		$frm->addHiddenField('','restaurant_id',$restaurant_id);
		$frm->addTextBox(Info::t_lang('SERVICE_CHARGES'),'restextra_service_charges');
		$frm->addTextBox(Info::t_lang('VAT'),'restextra_vat_charge');
		$frm->addTextBox(Info::t_lang('SERVICE_TAX'),'restextra_service_tax');
		$frm->addTextBox(Info::t_lang('Proportion on which service tax is charged'),'restextra_tax_proportion');
		$frm->addTextBox(Info::t_lang('Min Order'),'restextra_min_order');
		$frm->addTextBox(Info::t_lang('Max Order'),'restextra_max_order');
		$frm->addRadioButtons(Info::t_lang('PRE_ORDER'),'restextra_is_preorder',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1);
		$frm->addRadioButtons(Info::t_lang('DELIVERY'),'restextra_is_delivery',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1);
		$frm->addRadioButtons(Info::t_lang('PICK_UP'),'restextra_is_pickup',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1);
		$frm->addRadioButtons(Info::t_lang('FREE_DELIVERY'),'restextra_is_free_delivery',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1);
		$payment_method = Info::getPaymentMethod();
		$payment_method[0] ='Both'; 
		$frm->addRadioButtons(Info::t_lang('PAYMENT_METHOD'),'restextra_payment_method',$payment_method,1);
		$frm->addSubmitButton('','submit_btn',Info::t_lang('UPDATE'));
		return $frm;
	}
	
	function timingForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$restaurant_id = isset($post['restaurant_id'])?$post['restaurant_id']:0;
		
		if(empty($restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$rest = new Restaurant();
		$frm = $this->getTimingForm($restaurant_id);
		$records = $rest->getRestaurant($restaurant_id);
		$extra = $rest->getRestaurantExtra($restaurant_id);
		$delivery_timing = $rest->getDeliveryTimingsForForm($restaurant_id);
		$pickup_timing = $rest->getPickupTimingsForForm($restaurant_id);
		$menu_timing = $rest->getRestaurantMenuTimingsForForm($restaurant_id);
		$restaurant_name = isset($records['restaurant_name'][Info::defaultLang()])?$records['restaurant_name'][Info::defaultLang()]:'';
		
		$data = array_merge($extra,$delivery_timing);
		$data = array_merge($data,$menu_timing);
		$data = array_merge($data,$pickup_timing);
		
		$frm->fill($data);
		$this->set('frm',$frm);
		$this->set('restaurant_id',$restaurant_id);
		$this->set('restaurant_name',$restaurant_name);
		$html = $this->_template->render(false,false,'restaurants/_partial/timing-form.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	function timingFormAction($restaurant_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$restaurant_id = FatUtility::int($restaurant_id);
		if(empty($restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
		}
		$frm = $this->getTimingForm($restaurant_id);
		$post = $frm->getFormDataFromArray(FatApp::getPostedData());
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
		$data['restextra_is_preorder'] = isset($post['restextra_is_preorder'])?FatUtility::int($post['restextra_is_preorder']):0;
		$data['restextra_is_pickup'] = isset($post['restextra_is_pickup'])?FatUtility::int($post['restextra_is_pickup']):0;
		$data['restextra_is_delivery'] = isset($post['restextra_is_delivery'])?FatUtility::int($post['restextra_is_delivery']):0;
		$data['restextra_restaurant_id'] = $restaurant_id;
		$rest = new Restaurant();
		$restaurant = $rest->getRestaurant($restaurant_id);
		if(empty($restaurant)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
		if(!$rest->saveRestaurantExtra($data, $restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		
		
		$weekDays = Info::weekdays();
		$rest->deleteTimings($restaurant_id, 1);
		$rest->deleteTimings($restaurant_id, 2);
		
		foreach($weekDays as $day_key=>$day_name){
			if(!empty($post['restextra_is_pickup']) && $post['restextra_is_pickup'] == 1){
				if($post['trt_pickup_start_time'][$day_key] != -1 && $post['trt_pickup_end_time'][$day_key] != -1){
					$pickup_timing['trt_restaurant_id'] = $restaurant_id; 
					$pickup_timing['trt_type'] = 1; 
					$pickup_timing['trt_weekday'] = $day_key; 
					$pickup_timing['trt_start_time'] =  Info::converShowToDbTime($post['trt_pickup_start_time'][$day_key], strtoupper($post['pickup_start_meridiem'][$day_key]));
					$pickup_timing['trt_end_time'] =  Info::converShowToDbTime($post['trt_pickup_end_time'][$day_key], strtoupper($post['pickup_end_meridiem'][$day_key]));
					
					if(!$rest->saveTimings($pickup_timing)){
						FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
					}
				}
				
			}
			if(!empty($post['restextra_is_delivery']) && $post['restextra_is_delivery'] == 1){
				if($post['trt_delivery_start_time'][$day_key] != -1 && $post['trt_delivery_end_time'][$day_key] != -1){
					$pickup_timing['trt_restaurant_id'] = $restaurant_id; 
					$pickup_timing['trt_type'] = 2; 
					$pickup_timing['trt_weekday'] = $day_key; 
					$pickup_timing['trt_start_time'] =  Info::converShowToDbTime($post['trt_delivery_start_time'][$day_key], strtoupper($post['delivery_start_meridiem'][$day_key]));
					$pickup_timing['trt_end_time'] =  Info::converShowToDbTime($post['trt_delivery_end_time'][$day_key], strtoupper($post['delivery_end_meridiem'][$day_key]));
					if(!$rest->saveTimings($pickup_timing)){
						FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
					}
				}
				
			}
			
		}
		$rest->deleteMenuTimings($restaurant_id);
		$menu_item_timing = Info::getMenuItemTiming();
		foreach($menu_item_timing as $timing_id=>$timing_name){
			if($post['resmenutiming_start_time'][$timing_id] != -1 && $post['resmenutiming_end_timing'][$timing_id]){
				$menu_timing['resmenutiming_start_time'] = Info::converShowToDbTime($post['resmenutiming_start_time'][$timing_id], $post['resmenutiming_start_time_meridiem'][$timing_id]);
				$menu_timing['resmenutiming_end_timing'] = Info::converShowToDbTime($post['resmenutiming_end_timing'][$timing_id], $post['resmenutiming_end_timing_meridiem'][$timing_id]);
				$menu_timing['resmenutiming_restaurant_id'] = $restaurant_id;
				$menu_timing['resmenutiming_timing_id'] = $timing_id;
				
				if(!$rest->saveRestaurantMenuTimings($menu_timing)){
					FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
				}
			}
		}
		FatUtility::dieJsonSuccess('Record Updated');
		
	}
	
	private function getTimingForm($restaurant_id){
		$frm = new Form('frmRestaurant');
		$frm->addHiddenField('','restaurant_id',$restaurant_id);
		$weekDays = Info::weekdays();
		
		$hours_options = Info::getTimingOptionsForForm();
		$fld = $frm->addHtml('','menu_heading','<h1>Menu Timing</h1>');
		$fld->developerTags['col'] =12;

		$fld = $frm->addHtml('','menu_start_heading','<h5>Menu Start Timing</h5>');
		$fld->developerTags['col'] =6;
		$fld = $frm->addHtml('','menu_end_heading','<h5>Menu End Timing</h5>');
		$fld->developerTags['col'] =6;
		$menu_item_timing = Info::getMenuItemTiming();
		foreach($menu_item_timing as $timing_id=>$timing_name){
			$frm->addSelectBox($timing_name,'resmenutiming_start_time['.$timing_id.']',$hours_options,'00:00',array(),'');
			$frm->addSelectBox($timing_name,'resmenutiming_start_time_meridiem['.$timing_id.']',Info::timeMeridiem(),0,array(),'');
			$frm->addSelectBox($timing_name,'resmenutiming_end_timing['.$timing_id.']',$hours_options,'00:00',array(),'');
			
			$frm->addSelectBox($timing_name,'resmenutiming_end_timing_meridiem['.$timing_id.']',Info::timeMeridiem(),0,array(),'');
			
		}
		
		$fld = $frm->addHtml('','pickup_heading','<h1>Pickup Timing</h1>');
		$fld->developerTags['col'] =12;

		$fld = $frm->addHtml('','pickup_start_heading','<h5>Pickup Start Timing</h5>');
		$fld->developerTags['col'] =6;
		$fld = $frm->addHtml('','pickup_end_heading','<h5>Pickup End Timing</h5>');
		$fld->developerTags['col'] =6;
		foreach($weekDays as $day_key=>$day_name){
			$frm->addSelectBox($day_name,'trt_pickup_start_time['.$day_key.']',$hours_options,'00:00',array(),'');
			$frm->addSelectBox($day_name,'pickup_start_meridiem['.$day_key.']',Info::timeMeridiem(),0,array(),'');
			
			$frm->addSelectBox($day_name,'trt_pickup_end_time['.$day_key.']',$hours_options,'00:00',array(),'');
			$frm->addSelectBox($day_name,'pickup_end_meridiem['.$day_key.']',Info::timeMeridiem(),0,array(),'');
			
		}
		$fld = $frm->addHtml('','delivery_heading','<h1>Delivery Timing</h1>');
		$fld->developerTags['col'] =12;
	
		$fld = $frm->addHtml('','delivery_start_heading','<h5>Delivery Start Timing</h5>');
		$fld->developerTags['col'] =6;
		$fld = $frm->addHtml('','delivery_end_heading','<h5>Delivery End Timing</h5>');
		$fld->developerTags['col'] =6;
		foreach($weekDays as $day_key=>$day_name){
			$frm->addSelectBox($day_name,'trt_delivery_start_time['.$day_key.']',$hours_options,'00:00',array(),'');
			$frm->addSelectBox($day_name,'delivery_start_meridiem['.$day_key.']',Info::timeMeridiem(),0,array(),'');
			$frm->addSelectBox($day_name,'trt_delivery_end_time['.$day_key.']',$hours_options,'00:00',array(),'');
			$frm->addSelectBox($day_name,'delivery_end_meridiem['.$day_key.']',Info::timeMeridiem(),0,array(),'');
		}
		 $frm->addRadioButtons(Info::t_lang('PRE_ORDER'),'restextra_is_preorder',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1);
		$frm->addRadioButtons(Info::t_lang('DELIVERY'),'restextra_is_delivery',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1);
		$frm->addRadioButtons(Info::t_lang('PICK_UP'),'restextra_is_pickup',array(1=>Info::t_lang('YES'),0=>Info::t_lang('NO')),1); 
		$fld = $frm->addSubmitButton('','submit_btn',Info::t_lang('UPDATE'));
		$fld->developerTags['col'] =12;
		return $frm;
	}
	
	function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		if(!isset($post['restaurant_id'])){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$restaurant_id = FatUtility::int($post['restaurant_id']);
		$rest = new Restaurant();
		$data = $rest->getRestaurant($restaurant_id);
		$location = new Location();
		$city_id = $location->getCityIdByRegionId($data['restaurant_region_id']);
		$data['city_id'] = $city_id;
		$data['cuisines'] = $rest->getRestaurantCuisinesForForm($restaurant_id);
		$frm = $this->getForm($restaurant_id,$city_id);
		$extra = $rest->getRestaurantExtra($restaurant_id);
		$data['restextra_payment_method'] = $extra['restextra_payment_method'];
		$data['restextra_delivery_time'] = $extra['restextra_delivery_time'];
		$frm->fill($data);
		$this->set('frm',$frm);
		$this->set('imageFrm',$this->getImageForm($restaurant_id));
		$html = $this->_template->render(false,false,'restaurants/_partial/form.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	function formAction(){
		$post = FatApp::getPostedData();
	
		$store_data = array();
		$frm = $this->getForm();
		$post = $frm->getFormDataFromArray($post,array('restaurant_region_id'));
		$post['restaurant_region_id'] = isset($post['restaurant_region_id'])?FatUtility::int($post['restaurant_region_id']):0;
		if($post == false){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$rest = new Restaurant();
		$language = Info::getLanguages();
		if(!empty($post['restaurant_id'])){
			$store_data = $rest->getRestaurant($post['restaurant_id']);
		}
		if($rest->isExistRestaurantByEmail($post['restaurant_email'],$post['restaurant_id'])){
			FatUtility::dieJsonError(Info::t_lang('RESTAURANT_ALREADY_EXIST_WITH_THIS_EMAIL!'));
		}
		foreach($language as $lang){
			if($rest->isExistRestaurantByName($post['restaurant_name'][$lang['language_id']],$post['restaurant_id'])){
				FatUtility::dieJsonError(Info::t_lang('RESTAURANT_ALREADY_EXIST_WITH_THIS_NAME!'));
			}
		}
		$restaurant_id = $rest->saveUpdate($post,$store_data['restaurant_user_id'],$post['restaurant_id']);
		
		if(!$restaurant_id){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		}
		$post['image_file_id'] = isset($post['image_file_id'])?FatUtility::int($post['image_file_id']):0;
		if(!empty($post['image_file_id'])){
			AttachedFile::removeAllFiles(AttachedFile::FILETYPE_RESTAURANT_PHOTO, $restaurant_id);
			$update_data['afile_record_id'] = $restaurant_id;
			$update_data['afile_type'] = AttachedFile::FILETYPE_RESTAURANT_PHOTO;
			if(!AttachedFile::updateByAfileId($update_data,$post['image_file_id'])){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		$rest->deleteRestaurantCuisines($restaurant_id);
		foreach($post['cuisines'] as $cuisine_id){
			if(!$rest->addRestaurantCuisines($cuisine_id, $restaurant_id)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		
		foreach($language as $lang){
			$lang_data['restaurant_name'] = $post['restaurant_name'][$lang['language_id']];
			$lang_data['restaurant_description'] = $post['restaurant_description'][$lang['language_id']];
			$lang_data['restaurant_address'] = $post['restaurant_address'][$lang['language_id']];
			$lang_data['restaurantlang_restaurant_id'] = $restaurant_id;
			$lang_data['restaurantlang_lang_id'] = $lang['language_id'];
			if(!$rest->saveUpdateLang($lang_data)){
				FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
			}
		}
		$extra['restextra_delivery_time'] = $post['restextra_delivery_time'];
		$extra['restextra_payment_method'] = $post['restextra_payment_method'];
		$extra['restextra_restaurant_id'] = $restaurant_id;
		if(!$rest->saveRestaurantExtra($extra, $restaurant_id)){
			FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
		} 
		FatUtility::dieJsonSuccess(Info::t_lang('UPDATE_SUCCESSFULLY'));
		
	}
	
	function setPopular(){
		$post = FatApp::getPostedData();
		$store_data = array();
		$restaurant_id = isset($post['restaurant_id'])?FatUtility::int($post['restaurant_id']):0;
		$user_id = isset($post['user_id'])?FatUtility::int($post['user_id']):0;
		if($restaurant_id <=0 || $user_id <= 0){
			FatUtility::dieJsonError('Invalid Request!');
		}
		$rest = new Restaurant();
		$rest_detail = $rest->getRestaurant($restaurant_id);
		$data['restaurant_is_popular'] = $rest_detail['restaurant_is_popular'] == 1?0:1;
		if(!$rest->saveUpdate($data,$user_id,$restaurant_id)){
			FatUtility::dieJsonError('Something Went Wrong!');
		}
		FatUtility::dieJsonSuccess('Record updated!');
	}
	
	private function getForm($restaurant_id=0, $city_id=0){
		$lang_fields =array(
					'restaurant_name'=>array('caption'=>Info::t_lang('RESTAURANT_NAME'),'type'=>'textbox','required'=>false),
					'restaurant_description'=>array('caption'=>Info::t_lang('DESCRIPTION'),'type'=>'textarea','required'=>false),
					'restaurant_address'=>array('caption'=>Info::t_lang('ADDRESS'),'type'=>'textarea','required'=>false)
					);
		$city_id = FatUtility::int($city_id);
		$frm = new Form('frmRestaurant');
		$cities = Location::getCitiesForForm();
		reset($cities);
		if(empty($city_id)) 	$city_id = key($cities);
		$regions = Location::getRegionforForm($city_id);
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
		$frm->addHiddenField('','restaurant_id',$restaurant_id);
		$frm->addHiddenField('','image_file_id');
		$frm->addSelectBox(Info::t_lang('CITY'),'city_id',$cities,'',array(),Info::t_lang('SELECT_CITY'))->requirements()->setRequired();
		$frm->addSelectBox(Info::t_lang('REGION'),'restaurant_region_id',$regions,'',array(),Info::t_lang('SELECT_REGION'))->requirements()->setRequired();
		$frm->addSelectBox(Info::t_lang('STATUS'),'restaurant_active',Info::getRestaurantStatus(),'',array(),'');
		$frm->addSelectBox(Info::t_lang('APPROVED'),'restaurant_approved',Info::getRestaurantApprovedStatus(),'',array(),'');
		$payment_method = Info::getPaymentMethod();
		$payment_method[3] = Info::t_lang('BOTH');
		
		$frm->addRequiredField(Info::t_lang('ZIPCODE'),'restaurant_zip');
		$frm->addRequiredField(Info::t_lang('EMAIL'),'restaurant_email');
		$frm->addRequiredField(Info::t_lang('PHONE_NUMBER'),'restaurant_phone');
		$frm->addRequiredField(Info::t_lang('ALTERNATE_PHONE_NO.'),'restaurant_alternate_phone');
		//$frm->addRequiredField(Info::t_lang('MINIMUM_ORDER_VALUE.'),'restaurant_min_order');
		$frm->addRadioButtons(Info::t_lang('PAYMENT_METHOD.'),'restextra_payment_method',$payment_method);
		$frm->addRadioButtons(Info::t_lang('SERVES'),'restaurant_veg_status',array(0=>Info::t_lang('VEG'),1=>Info::t_lang('NON_VEG'),2=>Info::t_lang('BOTH')));
		
		$frm->addSelectBox(Info::t_lang('DELIVERY_TIME'),'restextra_delivery_time',Info::deliveryHour(),'00:30');
		$frm->addCheckBoxes(Info::t_lang('CUISINES'), 'cuisines',Cuisines::getCuisinesForForm(),array(),array('class'=>'three-col'));
		$frm->addSubmitButton('','submit_btn',Info::t_lang('UPDATE'));
		return $frm;
		
	}
	
	private function getImageForm($restaurant_id){
		$frm = new Form('frmRestaurantImage');
		$frm->addHiddenField('','img-data','');
		$frm->addHtml('demo_image','demo-image',"<img src='".FatUtility::generateUrl('image','restaurantLogo',array($restaurant_id,100,100),'/')."' id='demo-image'>");
		$frm->addFileUpload(Info::t_lang('LOGO/IMAGE'),'photo');
		
		return $frm;
	}
	
	function cuisines() {
		$brcmb = new Breadcrumb();
		$brcmb->add("Restaurants",FatUtility::generateUrl('Restaurants'));
		$this->set('breadcrumb',$brcmb->output());
		$this->set('search',$this->getCuisineSearchForm());
		$this->_template->render();
	}
	
	function cuisinesListing($page=1){
		$page = FatUtility::int($page);
		if(!$this->canView){
			dieError("Unauthorized Access");
		}
		$post = FatApp::getPostedData();
		$form = $this->getCuisineSearchForm();
		$post = $form->getFormDataFromArray($post);
		
		$tblobj = new Restaurants();
		$search = $tblobj->getCuisines();
		$pagesize = static::PAGESIZE;
		if($page > 0 && $pagesize >0){
			$search->setPageNumber($page);
			$search->setPageSize($pagesize);
		}
		if($post !== false){
			if(!empty($post['keyword'])){
				$search->addCondition('cuisines_name','like','%'.$post['keyword'].'%');
			}
		}
		$records = FatApp::getDb()->fetchAll($search->getResultSet());
		
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', static::PAGESIZE);
		$htm = $this->_template->render(false,false,"restaurants/_partial/cuisines_listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	 }
	 
	public function viewCuisines($cuisines_id){
		$cuisines_id = FatUtility::int($cuisines_id);
		$tblObj = new Restaurants();
		$record = $tblObj->getCuisine($cuisines_id);
		$languages = MyHelper::getLanguages();
		$this->set('records',$record);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"restaurants/_partial/viewCuisines.php");
	}

	function changeCuisinesOrder(){
		if(!$this->canEdit){
			FatUtility::dieJsonError(Info::t_lang('Unauthorized Access!'));
		}
		$post = FatApp::getPostedData();
		$record_id = FatUtility::int($post['record_id']);
		if(empty($record_id)){
			FatUtility::dieJsonError(Info::t_lang('Invalid Request!'));
		}
		$data['cuisines_display_order'] = FatUtility::int($post['display_order']);
		$tblObj = new Restaurants();
		$cuisines_id = $tblObj->saveCuisine($data,$record_id);
		if(!$cuisines_id){
			FatUtility::dieJsonError($tblObj->getError());
		}
		FatUtility::dieJsonSuccess(Info::t_lang('Display order changed'));
	}
	
	public function cuisinesForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$post['cuisines_id'] = empty($post['cuisines_id'])?0:FatUtility::int($post['cuisines_id']);
		$cuisines_id = $post['cuisines_id'];
		$form = $this->getCuisinesForm($cuisines_id);
		
		if($cuisines_id){
			$tblObj = new Restaurants();
			$post = $tblObj->getCuisine($cuisines_id);
			$form->fill($post);
		}
	
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"restaurants/_partial/cuisines_form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	function cuisinesAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getCuisinesForm($post['cuisines_id']);
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieJsonError($form->getValidationErrors());
			return;
		}
		$post['cuisines_id'] = FatUtility::int($post['cuisines_id']);
		$languages = MyHelper::getLanguages();
		$tblObj = new Restaurants();
		$cuisines_id = $tblObj->saveCuisine($post,$post['cuisines_id']);
		if(!$cuisines_id){
			FatUtility::dieJsonError($tblObj->getError());
		}
		foreach($languages as $lang_id=>$lang_data){
			$lang_id = $lang_data['language_id'];
			$data['cuisineslang_cuisines_id']=$cuisines_id;
			$data['cuisineslang_lang_id']=$lang_id;
			$data['cuisines_name']=$post['cuisines_name'][$lang_id];
			if(!$tblObj->saveCuisineLang($data)){
				FatUtility::dieJsonError($tblObj->getError());
				return;
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	private function getCuisinesForm($record_id = 0){
		$frm = new Form('frmCuisines',array('id'=>'action_form'));
		$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form"); return(false);' );
		
		$action='Add';
		if($record_id >0){
			$action='Update';
		}
		$languages = Info::getLanguages();
		foreach($languages as $lang_id=>$language){						
			$frm->addRequiredField(Info::t_lang("Cuisines").'['.$language['language_name'].']','cuisines_name['.$language['language_id'].']','',array('class'=>$language['language_css']));			
		}	
		$frm->addHiddenField('cuisines_id', 'cuisines_id',$record_id);		
		$frm->addSelectBox('Status','cuisines_active',Info::getStatus());
		$frm->addTextBox("Display Order","cuisines_display_order");
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
		
		return $frm;	
	}
	
	private function getSearchForm($tab=1) {
		$veg_status = Info::getRestaurantVegStatus();
		$status = Info::getRestaurantStatus();
		$veg_status['-1']='Does not Matter';
		$status['-1']='Does not Matter';
		$approved_status = Info::getRestaurantApprovedStatus();
		$approved_status['-1']='Does not Matter';
		if($tab==1){
			$frm = new Form('frmUserSearch_tab_1',array('class'=>'web_form', 'onsubmit'=>'search(this,1); return false;'));
			$frm->addTextBox('Name or Email', 'keyword','',array('class'=>'search-input'));
			$frm->addTextBox('Location', 'location','',array('class'=>'search-input'));
			$frm->addSelectBox('Serves', 'restaurant_veg_status', $veg_status, '-1',array('class'=>'search-input'), '');
			$frm->addSelectBox('Approved Status', 'restaurant_approved', $approved_status, '-1',array('class'=>'search-input'), '');
			$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		}
		elseif($tab==2){
			$frm = new Form('frmUserSearch_tab_2',array('class'=>'web_form', 'onsubmit'=>'search(this, 2); return false;'));
			$frm->addTextBox('Name or Email', 'keyword','',array('class'=>'search-input'));
			$frm->addTextBox('Location', 'location','',array('class'=>'search-input'));
			$frm->addSelectBox('Serves', 'restaurant_veg_status', $veg_status, '-1',array('class'=>'search-input'), '');
			$frm->addSelectBox('Status', 'restaurant_active', $status, '-1', array('class'=>'search-input'), '');
			$frm->addSelectBox('Approved Status', 'restaurant_approved', $approved_status, '-1',array('class'=>'search-input'), '');
			$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		}
		return $frm;
	}
	
	private function getRegionSearchForm() {
		$frm = new Form('frmRegionSearch');
		$frm->addTextBox('Location', 'location','',array('class'=>'search-input'));
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		return $frm;
	}
	
	private function getCuisineSearchForm() {
		$frm = new Form('frmcuisinesearch',array('class'=>'web_form'));
		$frm->addTextBox('keyword', 'keyword','',array('class'=>'search-input'));
		$frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
		return $frm;
	}
}