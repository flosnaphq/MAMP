<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
if($canView === true ){
	$arr_flds = array(
		'listserial' => CommonHelper::getLabel('PAGE_TEXT_S._NO.'),
		BlogCategories::DB_TBL_PREFIX.'title' => CommonHelper::getLabel('PAGE_TEXT_TITLE'),
		BlogCategories::DB_TBL_PREFIX.'description' => CommonHelper::getLabel('PAGE_TEXT_DESCRIPTION'),
		BlogCategories::DB_TBL_PREFIX.'status' => CommonHelper::getLabel('PAGE_TEXT_STATUS')
	);
	// if($canEdit)
	// {
		$arr_flds['Action'] = CommonHelper::getLabel('PAGE_TEXT_ACTION');
	// }
	$tbl = new HtmlElement('table', array('id'=>'list', 'class'=>"table"));
	$thead = $tbl->appendElement('thead')->appendElement('tr',array('class' => 'nodrag nodrop'));
	foreach ($arr_flds as $val){
		$thead->appendElement('th', array(), $val);
	}		
	$tbody = $tbl->appendElement('tbody');
	if (!empty($list) && is_array($list) && count($list) > 0){
		foreach ($list as $index => $row) {
		$tr = $tbody->appendElement('tr',array('class'=>( FatUtility::convertToType($row[ BlogCategories::DB_TBL_PREFIX . 'status'],FatUtility::VAR_INT) == 1 ) ? '' : 'inactive nodrag nodrop','id' => $row[BlogCategories::DB_TBL_PREFIX.'id'] ));
		
			foreach ($arr_flds as $key => $val) {
				$td = $tr->appendElement('td',array('style'=>'width:10%')); 
				 
				switch ($key) {
					case 'listserial':
						$td->setAttribute('style','width:8%');
						$td->appendElement('plaintext', array(), $index+1);
					break;
					case BlogCategories::DB_CHILD_TBL_PREFIX . 'title':
						$td->setAttribute('style','width:25%');
						$td->appendElement('plaintext', array(),ucfirst($row[$key]));
					break;					
					case BlogCategories::DB_TBL_PREFIX.'status' :
						$active = ( FatUtility::convertToType($row[ BlogCategories::DB_TBL_PREFIX . 'status'] , FatUtility::VAR_INT) == 1 )?" Inactive":' active';
						$statucAct = ($canEdit === true) ? 'changeEntityStatus(this)' : '';
						$label = $td->appendElement('label' , array('data-id' => $row[ BlogCategories::DB_TBL_PREFIX . 'id'] , 'data-status' => $row[ BlogCategories::DB_TBL_PREFIX . 'status' ] , 'class' => 'statustab'.$active , 'onclick' =>"$statucAct"  ));
						$label->appendElement('span',array('class'=>'switch-labels','data-on' => CommonHelper::getLabel('PAGE_TEXT_INACTIVE') , 'data-off' => CommonHelper::getLabel('PAGE_TEXT_ACTIVE')));
						$label->appendElement('span',array('class'=>'switch-handles'));
					break;	
					case 'Action':
					   $ul = $td->appendElement('ul', array('class'=>'actions'));
					   if($canEdit==true){
						   $li = $ul->appendElement('li'); 
						   $li->appendElement('a',array('title'=>CommonHelper::getLabel('PAGE_TEXT_EDIT'),'href'=>FatUtility::generateUrl( 'BlogCategories', 'form', array($row[ BlogCategories::DB_TBL_PREFIX . 'id'] ) ) ),'<i class="ion-edit icon"></i>',true);
					   }
					   if ($row[BlogCategories::DB_TBL_PREFIX.'status'] == ENTITY_ACTIVE) {
							$li = $ul->appendElement('li');
							$li->appendElement('a', array('href' => FatUtility::generateUrl('BlogCategories', 'blogchildcategories', array($row[ BlogCategories::DB_TBL_PREFIX . 'id'])), 'title' => CommonHelper::getLabel('PAGE_TEXT_MANAGE_SUB_CATEGORIES')), '<i class="ion-drag icon"></i>', true);	 
						}
					break;
					default:
						$td->appendElement('plaintext', array(), $row[$key], true);
					break;
				}
			}
		
		}
	}
	else{
		$tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), CommonHelper::getLabel('PAGE_TEXT_NO_RECORDS_FOUND') );
	}
	echo $tbl->getHtml();
	
}