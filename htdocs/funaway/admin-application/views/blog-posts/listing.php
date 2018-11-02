<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
if($canView === true ){
	$arr_flds = array(
		'listserial' => Info::t_lang('S._NO.'),
		BlogPosts::DB_TBL_PREFIX.'title' => Info::t_lang('TITLE'),
		'categories' => Info::t_lang('CATEGORY'),
		BlogPosts::DB_TBL_PREFIX.'published' => Info::t_lang('PUBLISHED_ON'),
		BlogPosts::DB_TBL_PREFIX.'status' => Info::t_lang('STATUS'),
		BlogPosts::DB_TBL_PREFIX.'date_time' => Info::t_lang('POSTED_ON'),
		BlogPosts::DB_TBL_PREFIX.'featured' => Info::t_lang('FEATURED')
	);
	if($canEdit)
	{
		$arr_flds['Edit'] = Info::t_lang('EDIT');
	}
	$tbl = new HtmlElement('table', array('id'=>'list', 'class'=>"table"));
	$thead = $tbl->appendElement('thead')->appendElement('tr');
	foreach ($arr_flds as $val){
		$thead->appendElement('th', array(), $val);
	}		
	$tbody = $tbl->appendElement('tbody');
	
	$i = ($pageNumber - 1) * $pageSize + 1;
	
	if (is_array($list) && count($list) > 0){
		foreach ($list as $index => $row) {
		$tr = $tbody->appendElement('tr',array('id' => $row[BlogPosts::DB_TBL_PREFIX.'id'] ));
		
			foreach ($arr_flds as $key => $val) {
				$td = $tr->appendElement('td',array('style'=>'width:10%')); 
				 
				switch ($key) {
					case 'listserial':
						$td->setAttribute('style','width:8%');
						$td->appendElement('plaintext', array(), $i);
					break;
					case BlogPosts::DB_TBL_PREFIX.'featured':
						if($canEdit){
								$active = $row[$key] !=1?'active':'';
								$toggle = '<label class="statustab addmarg '.$active.'" onclick="setPopular(this, \''.$row[BlogPosts::DB_TBL_PREFIX.'id'].'\')">
								  <span data-off="'.Info::getIsValue(1).'" data-on="'.Info::getIsValue(0).'" class="switch-labels"></span>
								  <span class="switch-handles"></span>
								</label>';
								$td->appendElement('plaintext', array(), $toggle, true);
							}
							else{
								$td->appendElement('plaintext', array(), Info::getIs($row[$key]));
							}
					break;
					case BlogPosts::DB_CHILD_TBL_PREFIX . 'title':
						$td->setAttribute('style','width:20%');
						$td->appendElement('plaintext', array(),ucfirst($row[$key]));
					break;					
					case BlogPosts::DB_TBL_PREFIX.'status' :
						$status = FatUtility::convertToType( $row[$key],FatUtility::VAR_INT);
				$td->appendElement('plaintext', array() , BlogConstants::blogPostStatusByKey($status));
					break;	
					case 'Edit':
					   $ul = $td->appendElement('ul', array('class'=>'actions'));
					   if($canEdit==true){
						   $li = $ul->appendElement('li'); 
						   $li->appendElement('a',array('title'=>Info::t_lang('EDIT'),'href'=>FatUtility::generateUrl( 'BlogPosts', 'form', array($row[ BlogPosts::DB_TBL_PREFIX . 'id'] ) ) ),'<i class="ion-edit icon"></i>',true);
						   $li = $ul->appendElement('li'); 
						   $li->appendElement('a',array('title'=>Info::t_lang('DELETE') ,'onclick'=>"return confirm('Are you sure you want to delete?');" ,'href'=>FatUtility::generateUrl( 'BlogPosts', 'delete', array($row[ BlogPosts::DB_TBL_PREFIX . 'id'] ) ) ),'<i class="ion-close-circled icon"></i>',true);
						   
						   //,'onclick'=>"return confirmDelete(this);"
					   }
					break;
					default:
						$td->appendElement('plaintext', array(), $row[$key], true);
					break;
				}
			}
		 $i++;
		}
	}
	else{
		$tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Info::t_lang('NO_RECORDS_FOUND') );
	}
	echo $tbl->getHtml();
	
	if ($pageCount > 1) echo html_entity_decode($pagination);
}