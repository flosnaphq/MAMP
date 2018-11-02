<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				'faqcat_name'=>'Category Name',
				//'faqcat_user_type'=>'User Type',
				'faqcat_display_order'=>'DIsplay Order',
				'faqcat_active' => 'Active',
				'action' => 'Action',
			);
			$status = array(0=>'Inactive',1=>'Active',2=>'Deleted');
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
				
			}
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				$tr = $tbl->appendElement('tr');
				if($row['faqcat_active']==0) {
					$tr->setAttribute ("class","inactive-tr");
				}
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'faqcat_active':
							$td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
							break;
						case 'faqcat_user_type':
							$td->appendElement('plaintext', array(), Info::getUserTypeByKey($row[$key]));
							break;
						case 'faqcat_display_order':
							if($canEdit){
								$td->appendElement('input', array('value'=>$row[$key],'onblur'=>'chanageFaqCategoryOrder("'.$row['faqcat_id'].'",this)'));
							}
							else{
								$td->appendElement('plaintext', array(), $row[$key]);
							}
							break;
				
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['faqcat_id'].")"),'<i class="ion-edit icon"></i>', true);	
							}
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'Javascript:popupView("'.FatUtility::generateUrl('faqs','category-view',array('faqcat_id'=>$row['faqcat_id'])).'");', 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
							}
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>FatUtility::generateUrl('faqs','faqs',array('faqcat_id'=>$row['faqcat_id'])), 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-android-menu icon"></i>', true);	
							}
							break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
				
			}
			
			if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData ( $postedData, array (
		'name' => 'frmUserSearchPaging','id'=>'pretend_search_form' 
) );
if($totalPage>1){
	?>
	<div class="footinfo">
	<aside class="grid_1">
	<ul class="pagination">
	<?php
	echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(xxpagexx);">xxpagexx</a></li>', 
	$totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, 
	'<li class="more"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
	' <li class="more"><a href="javascript:void(0);" onclick="listing(xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
	'<li class="prev"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>', 
	'<li class="next"> <a href="javascript:void(0);" onclick="listing(xxpagexx);"></a></li>');
	?>
	</ul>
	</aside>  

	</div>
	<?php
}	
?>


