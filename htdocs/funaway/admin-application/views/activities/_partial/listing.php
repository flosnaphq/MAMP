<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				'activity_name'=>'Name',
			//	'user_name'=>'Host',
				'activity_user_id'=>'Host',
				'city_name'=>'City',
				'activity_price'=>'Price',
				'activity_start_date'=>'Start Date',
			//	'activity_end_date'=>'End Date',
				'activity_confirm' => 'Confirmed',
				'activity_popular' => 'Featured',
				'activity_active' => 'Status',
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
				if($row['activity_active']==0) {
					$tr->setAttribute ("class","inactive-tr");
				}
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'activity_user_id':
								if(!array_key_exists($row['activity_user_id'], $hosts)){
									$hosts[$row['activity_user_id']] = $row['user_name'];
								}
								
								
                                                                $td->appendElement('plaintext', array(), $hosts[$row['activity_user_id']]);
							break;
						
						case 'activity_active':
							if($canEdit ){
								$confirmed = Info::getStatus();
								$select = $td->appendElement('select', array('name'=>'active','onchange'=>"changeStatus('Do You Want To Change ?', '".$row['activity_id']."', this.value)"),'');
								foreach($confirmed as $status=>$status_name){
									$ar = array('value'=>$status);
									if($row[$key] == $status){
										$ar['selected'] = 'selected';
									}
									$select->appendElement('option', $ar, $status_name);
								}
							}
							else{
								$td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
							}
							break;
						case 'activity_popular':
							if($canEdit){
								$active = $row[$key] !=1?'active':'';
								$toggle = '<label class="statustab addmarg '.$active.'" onclick="setPopular(this, \''.$row['activity_id'].'\')">
								  <span data-off="Mark" data-on="Unmark" class="switch-labels"></span>
								  <span class="switch-handles"></span>
								</label>';
								$td->appendElement('plaintext', array(), $toggle, true);
							}
							else{
								$td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
							}
							break;
						case 'activity_confirm':
							if($canEdit && $row[$key]!=1){
								$confirmed = Info::getActivityConfirmStatus();
								$select = $td->appendElement('select', array('name'=>'confirmed','onchange'=>"changeConfirmStatus('Do You Want To Change ?','".$row['activity_id']."', this.value)"),'');
								foreach($confirmed as $status=>$status_name){
									$ar = array('value'=>$status);
									if($row[$key] == $status){
										$ar['selected'] = 'selected';
									}
									$select->appendElement('option', $ar, $status_name);
								}
							}
							else{
								$td->appendElement('plaintext', array(), Info::getActivityConfirmStatusByKey($row[$key]));
							}
							
							break;
						case 'activity_start_date':
						case 'activity_end_date':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key]));
							break;
						
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>FatUtility::generateUrl('activities','details',array($row['activity_id'])), 'class'=>'button small green', 'title'=>'View Details'),'<i class="ion-navicon-round icon"></i>', true);	
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


