<?php
	$i=0;
$table = new HtmlElement('table',array('width'=>'100%','border'=>0,'class'=>'table_form_horizontal threeCol'));

  foreach($banners as $banner){
	if($i%3==0 || $i==0){
		$tr = $table->appendElement('tr');
	}
	$td = $tr->appendElement('td');
	$logoWrap = $td->appendElement('div',array('class'=>'logoWrap'));
	$logothumb= $logoWrap->appendElement('div',array('class'=>'logothumb blackclr'));
	$logothumb->appendElement('img',array('src'=>FatUtility::generateUrl('banners','image',array($banner['afile_id'],270,230))));
	if($canEdit){
		$logothumb->appendElement('a',array('class'=>'deleteLink white','href'=>"javascript:;deleteFile('".$banner['afile_id']."')"),'<i class="ion-close-circled icon"></i>',true);
	}
	$div = $td->appendElement('div');
	$div->appendElement('lable',array(),'Display Order::');
	$div->appendElement('input',array('class'=>'text-display-order','type'=>'text','onblur'=>"updateOrder(this,'".$banner['afile_id']."')",'value'=>$banner['afile_display_order']),'Display Order::');
	
	$i++;
  }

 echo $table->getHtml(); 
if($canEdit){
  ?>
  <div id = "form-tab" >
	<?php  echo $frm->getFormHtml(); ?>
</div>
<?php } ?>
