<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
//Info::test($arr_listing);
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				'trans_key'=>'Key',
				'trans_val'=>'Label',
				'action' => 'Action'
			);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			foreach ($arr_flds as $key=>$val) {
				$width = '5%';
				if($key == 'trans_key' || $key == 'trans_val')
				{
					$width = '45%';
				}
				
				$e = $th->appendElement('th', array('width'=>$width), $val);	
			}
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				$tr = $tbl->appendElement('tr');
					
				foreach ($arr_flds as $key=>$val){
					
					switch ($key){
						case 'listserial':
							$td = $tr->appendElement('td');
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'action':
							$td = $tr->appendElement('td');
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm('".addslashes($row['trans_key'])."')"),'<i class="ion-edit icon"></i>', true);	
							}
							break;
						default:
							$td = $tr->appendElement('td');
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


