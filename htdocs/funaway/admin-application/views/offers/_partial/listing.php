<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				//'photo'=>'Photo',
				'discoupon_code'=>'Code',
				'discoupon_limit'=>'Coupon Limit',
				//'discoupon_type'=>'Coupon Type',
				//'discoupon_discount_type'=>'Discount Type',
				'discoupon_discount'=>'Discount',
				//'discoupon_min_order'=>'Minimum Order',
				//'discoupon_max_discount'=>'Maximum Discount',
				//'discoupon_weekday_specific' => 'Weekday Specific',
				'discoupon_valid_from' => 'Valid From',
				'discoupon_valid_upto' => 'Valid Upto',
				//'discoupon_by_admin' => 'Added by',
				'discoupon_active' => 'Status',
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
				if($row['discoupon_active']==0) {
					$tr->setAttribute ("class","inactive-tr");
				}
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'discoupon_valid_from':
						case 'discoupon_valid_upto':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key],true));
							break;
						case 'discoupon_type':
							$td->appendElement('plaintext', array(), Info::getCouponTypeByKey($row[$key]));
							break;
						case 'discoupon_use':
							$td->appendElement('plaintext', array(), Info::getCouponUseTypeByKey($row[$key]));
							break;
						case 'discoupon_discount':
							if($row['discoupon_discount_type'] == 1){
								$td->appendElement('plaintext', array(), $row[$key].'%');
							}
							else{
								$td->appendElement('plaintext', array(), Info::price($row[$key]),true);
							}
							
							break;
						case 'discoupon_discount_type':
							$td->appendElement('plaintext', array(), Info::getCouponDiscountTypeByKey($row[$key]));
							break;
						case 'discoupon_active':
							$td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
							break;
						
						case 'discoupon_weekday_specific':
							$td->appendElement('plaintext', array(), Info::getYesNoByKey($row[$key]));
							break;
						
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'Javascript:popupView("'.FatUtility::generateUrl('offers','view',array('discoupon_id'=>$row['discoupon_id'])).'");', 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
							}
							
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['discoupon_id'].")"),'<i class="ion-edit icon"></i>', true);	
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
                                                  

