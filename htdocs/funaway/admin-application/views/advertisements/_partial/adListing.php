<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				'ad_title'=>'Title',
				'afile_id'=>'Image',
				'ad_place_id'=>'Place',
				'ad_created' => 'Created Date',
				'ad_starting_date' => 'Start Date',
				'ad_ending_date' => 'End Date',
				'ad_display_order' => 'Display order',
				'ad_active' => 'Status',
				'action' => 'Action',
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			$status = array(0=>'Inactive',1=>'Active');
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
			}
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				$tr = $tbl->appendElement('tr');
				if($row['ad_active']==0) {
					$tr->setAttribute ("class","inactive-tr");
				}
				foreach ($arr_flds as $key=>$val){
					
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'afile_id':
							$a = $td->appendElement('a',array('href'=>$row['ad_link'],'target'=>'_blank'));
							$a->appendElement('img', array('src'=>FatUtility::generateUrl('banners','image',array('afile_id'=>$row['afile_id'],100,100))), $sr_no);
							break;
						
						case 'ad_active':
							$td->appendElement('plaintext', array(), Info::getAdStatusByKey($row[$key]));
							break;
						case 'ad_place_id':
							$td->appendElement('plaintext', array(), Info::getAdPlaceByKey($row[$key]));
							break;
						case 'ad_created':
						case 'ad_starting_date':
						case 'ad_ending_date':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key]));
							break;
						case 'ad_display_order':
							$td->appendElement('input',array('class'=>'text-display-order','value'=>$row[$key],'onblur'=>"changeOrder('".$row['ad_id']."',this)"));
							break;
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['ad_id'].")"),'<i class="ion-edit icon"></i>', true);	
							}
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'Javascript:popupView("'.FatUtility::generateUrl('advertisements','view-ad',array('ad_id'=>$row['ad_id'])).'");', 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
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


