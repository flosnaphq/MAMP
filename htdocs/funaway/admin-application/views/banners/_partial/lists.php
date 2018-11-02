<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = array(
	'listserial'=>'Sr no.',
	'banner_id'=>'Image',
	'banner_title'=>'Title',
	'banner_display_order'=>'Display Order',
	/* 'banner_type'=>'Banner Type', */
	'banner_active' => 'Status',
	'action' => 'Action',
);
$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
$status = array(0=>'Inactive',1=>'Active');
foreach ($arrFlds as $val) {				
	$e = $th->appendElement('th', array(), $val);		
}
if (count($arr_listing) > 0) {
	$srNo = $page == 1 ? 0: $pageSize*($page-1);
	foreach ($arr_listing as $sn=>$row) {
		$srNo++;
		$tr = $tbl->appendElement('tr');
		if($row['banner_active']==0) {
			$tr->setAttribute ("class","inactive-tr");
		}
		foreach ($arrFlds as $key=>$val){
			
			$td = $tr->appendElement('td');
			switch ($key){
				case 'listserial':
					$td->appendElement('plaintext', array(), $srNo);
					break;
				case 'banner_id':
					
					$td->appendElement('img', array('src'=>FatUtility::generateUrl('image','banner',array('banner_id'=>$row['banner_id'],100,100),CONF_BASE_DIR)), $srNo);
					break;
						
				case 'banner_active':
					$td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
					break;
				case 'banner_display_order':
					if($canEdit){
						$td->appendElement('input',array('class'=>'text-display-order','value'=>$row[$key],'onblur'=>"changeOrder('".$row['banner_id']."',this)"));
					}
					else{
						$td->appendElement('plaintext',array(),$row[$key]);
					}
					
					break;
				case 'action':
					$ul = $td->appendElement("ul",array("class"=>"actions"));
					if($canEdit){
						$li = $ul->appendElement("li");
						$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['banner_id'].")"),'<i class="ion-edit icon"></i>', true);
						
						$li = $ul->appendElement("li");
						$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Remove',"onclick"=>"removeBanner(".$row['banner_id'].")"),'<i class="ion-android-delete icon"></i>', true);	
					}
					if($canView){
						$li = $ul->appendElement("li");
						$li->appendElement('a', array('href'=>'Javascript:popupView("'.FatUtility::generateUrl('banners','view',array('banner_id'=>$row['banner_id'])).'");', 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
					}
					break;
				default:
					$td->appendElement('plaintext', array(), $row[$key], true);
					break;
			}
		}
		
	}
}

if (count($arr_listing) == 0) {
	$tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arrFlds)), 'No records found');
}
echo $tbl->getHtml();

echo FatUtility::createHiddenFormFromData ( $postedData, array (
															'name' => 'frmUserSearchPaging',
															'id'=>'pretend_search_form' 
														)
										);

if($totalPage>1) {
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


