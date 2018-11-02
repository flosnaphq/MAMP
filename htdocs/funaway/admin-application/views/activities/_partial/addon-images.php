<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl('activities','setupAddonImage',array($activity_id)) );
$frm->setFormTagAttribute ( 'onsubmit', 'submitAddonImageForm(formValidator, '.$addon_id.'); return false;');
$frm->developerTags['fld_default_col'] = 6;
?>
<section class="section">
<div class="sectionhead">
<h4>Addon Images (<?php echo $addon['activityaddon_text']?>)</h4><a html="javascript:;" onclick = "closeForm()" class="close-form"><i class="ion-close-round"></i></a>
</div>
<div class="sectionbody space">
<i>Activity Addon images: 1200X900 or [aspect ratio 4:3]</i>
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
		$img = $logothumb->appendElement('img',array('src'=>FatUtility::generateUrl('image','addon',array($image['afile_id'], 200,400, rand(1,1000)),CONF_BASE_DIR)));
		
		if($canEdit){
			$logothumb->appendElement('a',array('href'=>'javascript:;', 'onclick'=>"removeAddonImage('".$image['afile_id']."')",'class'=>'deleteLink white'), '<i class="ion-close-circled icon"></i>',true);
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