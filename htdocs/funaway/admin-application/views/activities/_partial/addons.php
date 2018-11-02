<?php 
		$arr_flds = array(
				'listserial'=>'Sr no.',
				'activityaddon_text'=>Info::t_lang('ADD-ON'),
				'activityaddon_price'=>Info::t_lang('PRICE'),
				'activityaddon_comments'=>Info::t_lang('DESCRIPTION'),
				'action' => 'Action',
			);
			
			$tbl = new HtmlElement('table', array('width'=>'100%', 'class'=>'table table-responsive'));
			$th = $tbl->appendElement('thead')->appendElement('tr');
			
			foreach ($arr_flds as $val) {				
				$e = $th->appendElement('th', array(), $val);		
			}
			$sr_no = 0;
			foreach ($arr_listing as $sn=>$row){
				$sr_no++;
				
				$tr = $tbl->appendElement('tr');
				
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						
						case 'activityaddon_price':
							$td->appendElement('plaintext', array(), Currency::displayPrice($row[$key]));
							break;
						
						
						case 'activity_active':
							if($canEdit){
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
						case 'activity_confirm':
							if($canEdit){
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
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'javascript:;', 'onclick' =>"getAddOnForm( ".$row['activityaddon_id'].")", 'class'=>'button small green', 'title'=>'Edit'),'<i class="ion-edit icon"></i>', true);	
							}
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'javascript:;', 'onclick' =>"addonImages( ".$row['activityaddon_id'].")", 'class'=>'button small green', 'title'=>'View Images'),'<i class="ion-images icon"></i>', true);	
							}
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'javascript:;', 'onclick' =>"deleteAddOn('Do You Want To Delete?', ".$row['activityaddon_activity_id'].",".$row['activityaddon_id'].")", 'class'=>'button small green', 'title'=>'Delete'),'<i class="ion-ios-trash-outline icon"></i>', true);	
							}
							
							break;
						default:
							$td->appendElement('plaintext', array(), $row[$key], true);
							break;
					}
				}
				
			}
			
			if (count($arr_listing) == 0) $tbl->appendElement('tr')->appendElement('td', array('colspan'=>count($arr_flds)), 'No records found');

			echo $tbl->getHtml(); ?>

	