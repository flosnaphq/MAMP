<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				'user_name'=>'Name',
				'user_email'=>'Email',
				'ordercancel_booking_id'=>'Booking Id',
				'ordercancel_datetime'=>'Request Time',
				'user_type'=>'Request By',
				'ordercancel_status' => 'Status',
			//	'ordercancel_host_approved' => 'Host Approved',
				'action' => 'Action',
				);
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
			}
			$sr_no = $page==1?0:$pageSize*($page-1);
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				$tr = $tbl->appendElement('tr');
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'user_name':
							$td->appendElement('plaintext', array(), $row['user_firstname'].' '.$row['user_lastname']);
							break;
						case 'ordercancel_status':
							$td->appendElement('plaintext', array(), Info::getOrderCancelRequestStatusByKey($row[$key]));
							break;
						case 'user_type':
							$td->appendElement('plaintext', array(), Info::getUserTypeByKey($row[$key]));
							break;
						case 'ordercancel_host_approved':
							$td->appendElement('plaintext', array(), Info::getOrderCancelHostApprovedStatusByKey($row[$key]));
							break;
						case 'ordercancel_datetime':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key],true));
							break;
						case 'withdrawalrequest_amount':
							$td->appendElement('plaintext', array(), Currency::displayPrice(abs($row[$key])),true);
							break;
						
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							
							 
							$li = $ul->appendElement("li");
							$li->appendElement('a', array('href'=>FatUtility::generateUrl('order-cancel-requests','view',array('ordercancel_id'=>$row['ordercancel_id'])), 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
								 
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
                                                  

