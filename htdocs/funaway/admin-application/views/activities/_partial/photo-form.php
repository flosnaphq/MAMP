<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl('activities','setupPhoto') );
$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return false;');
$frm->developerTags['fld_default_col'] = 6;
?>
<section class="section">

<div class="sectionbody space">
<?php
$counter =0;
$table = new HtmlElement('table',array('class'=>'table_form_horizontal threeCol','cellpadding'=>0,'cellspacing'=>0,'border'=>0,'width'=>'100%'));
$tr = $table->appendElement('tr');
if(!empty($images)){
	
	foreach($images as $image){
		$counter ++;
		$td = $tr->appendElement('td');
		$logoWrap = $td->appendElement('div',array('class'=>'logoWrap'));
		$logothumb = $logoWrap->appendElement('div',array('class'=>'logothumb blackclr'));
		$img = $logothumb->appendElement('img',array('src'=>FatUtility::generateUrl('image','adminActivity',array($image['afile_id'], 200,400, rand(1,1000)),CONF_WEBROOT_URL)));
		
		if($canEdit){
			$logothumb->appendElement('a',array('href'=>'javascript:;', 'onclick'=>"removeFile('Do You Want To Remove ?', '".$image['afile_id']."')",'class'=>'deleteLink white'), '<i class="ion-close-circled icon"></i>',true);
		}
		
		
		$td->appendElement('label',array(),'Default : ');
		$default_option = array('type'=>'radio', 'value'=>$image['afile_id'],'name'=>'default_image');
		
		if($canEdit){
			if($image['afile_id'] == $flds['activity_image_id']){
				$default_option['checked'] = 'checked';
			}
			$default_option['onchange'] = "defaultImage(this)";
			$td->appendElement('input',$default_option);
		}
		else{
			
			$td->appendElement('plantext',array(), Info::getIsValue($image['afile_id'] == $flds['activity_image_id']));
		}
		
		
		
		
		if($counter % 3 == 0){
			$tr = $table->appendElement('tr');
		}
	}
}
echo $table->getHtml();
if($canEdit){
	echo $frm->getFormHtml();
}
?>
</div>

</section>