<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<?php	
			$arr_flds = array(
				'listserial'=>'Sr no.',
				//'photo'=>'Photo',
				'user_name'=>'Name',
				'udetails_dob'=>'DOB',
				'udetails_sex'=>'Gender',
				'user_email' => 'Email',
				'address' => 'Adress',
				//'user_is_merchant' => 'User type',
				'user_registered' => 'Reg. Date',
				'user_active' => 'Status',
				'user_verified' => 'Verified',
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
				if($row['user_active']==0) {
					$tr->setAttribute ("class","inactive-tr");
				}
				foreach ($arr_flds as $key=>$val){
					$td = $tr->appendElement('td');
					switch ($key){
						case 'listserial':
							$td->appendElement('plaintext', array(), $sr_no);
							break;
						case 'photo':
							$td->appendElement('img', array('id'=>'user_profile_photo_'.$row['user_id'], 'src'=>FatUtility::generateUrl('users','photo',array($row['user_id'],50,50))));
							if($canEdit){
								$uploadForm->fill(array('user_id'=>$row['user_id']));
								$uploadForm->setformTagAttribute('id','upload_image_'.$row['user_id']);
								$file = $uploadForm->getField('photo');
								$file->setFieldTagAttribute('onchange',"submitImage(".$row['user_id'].")");
								$td->appendElement('plaintext',array(),$uploadForm->getFormHtml(),true);
							}
							break;
						case 'user_active':
							$td->appendElement('plaintext', array(), Info::getSearchUserStatusByKey($row[$key]));
							break;
						case 'udetails_sex':
							$td->appendElement('plaintext', array(), Info::getSexValue($row[$key]));
							break;
						case 'user_verified':
							$td->appendElement('plaintext', array(), Info::getEmailStatusByKey($row[$key]));
							break;
						case 'user_is_merchant':
							$td->appendElement('plaintext', array(), Info::getUserTypeByKey($row[$key]));
							break;
						case 'user_registered':
							$td->appendElement('plaintext', array(), FatDate::format($row[$key],true));
							break;
						case 'action':
							$ul = $td->appendElement("ul",array("class"=>"actions"));
							
							if($canView){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>'Javascript:popupView("'.FatUtility::generateUrl('users','view',array('user_id'=>$row['user_id'])).'");', 'class'=>'button small green', 'title'=>'View detail'),'<i class="ion-eye icon"></i>', true);	
							}
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"getForm(".$row['user_id'].")"),'<i class="ion-edit icon"></i>', true);	
							}
							if($canEdit){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>"javascript:;", 'class'=>'button small green', 'title'=>'Change Password',"onclick"=>"getPasswordForm(".$row['user_id'].")"),'<i class="ion-locked icon"></i>', true);	
							}
							if($canEditWallet && $canViewWallet){
								$li = $ul->appendElement("li");
								$li->appendElement('a', array('href'=>FatUtility::generateUrl('wallet','user',array('user_id'=>$row['user_id'])), 'class'=>'button small green', 'title'=>'View Wallet'),'<i class="ion-social-usd-outline icon"></i>', true);	
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
	echo FatUtility::getPageString('<li><a href="javascript:void(0);" onclick="listing(\''.$tab.'\',xxpagexx);">xxpagexx</a></li>', 
	$totalPage, $page, $lnkcurrent = '<li class="selected"><a href="javascript:void(0);" >xxpagexx</a></li>', '  ', 5, 
	'<li class="more"> <a href="javascript:void(0);" onclick="listing(\''.$tab.'\',xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
	' <li class="more"><a href="javascript:void(0);" onclick="listing(\''.$tab.'\',xxpagexx);"><span class="ink animate" style="height: 35px; width: 35px; top: 0.5px; left: 4px;"></span></a></li>', 
	'<li class="prev"> <a href="javascript:void(0);" onclick="listing(\''.$tab.'\',xxpagexx);"></a></li>', 
	'<li class="next"> <a href="javascript:void(0);" onclick="listing(\''.$tab.'\',xxpagexx);"></a></li>');

	?>
	</ul>
	</aside>  
	</div>
	<?php
}	
?>
                                                  

