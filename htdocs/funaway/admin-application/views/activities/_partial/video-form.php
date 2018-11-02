<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setValidatorJsObjectName ( 'formValidator' );
$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute ( 'id', 'action_form' );
$frm->setFormTagAttribute ( 'class', 'web_form' );
$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl('activities','setupVideo') );
$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator); return false;');
$frm->developerTags['fld_default_col'] = 6;
?>
<section class="section">

<div class="sectionbody space">
<?php
$counter =0;
$openTr =true;
if(!empty($videos)){ ?>
	<table class="table_form_horizontal threeCol" cellpadding='0' cellspacing='0'border='0' width='100%'> 
	<tr>
	<?php foreach($videos as $vid){ 
		$counter ++;
		if($openTr == false){
			$openTr = true;
			?>
			<tr>
			<?php
		}
	?> 
		
		<td>
			
			<div class="logoWrap"><div class="logothumb blackclr">
			<?php if($vid['activityvideo_type'] == 2){?>
				<a href="/" class="thumb__iframe">
					<iframe src="https://player.vimeo.com/video/<?php echo $vid['activityvideo_videoid']?>" width="100%" height="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				</a>
			<?php } else { ?>	
				<a href="/" class="thumb__iframe">
					<iframe src="https://www.youtube.com/embed/<?php echo $vid['activityvideo_videoid']?>" width="100%" height="100%" frameborder="0"  allowfullscreen></iframe>
				</a>	
			<?php } ?>
			
			<?php if($canEdit){ ?>
				<a class="deleteLink white" href="javascript:;" onclick="removeVideo('Do You Want To Delete?',<?php echo $vid['activityvideo_id']?>)" class="button button--small button--fill button--dark thumb__delete"><i class="ion-close-circled icon"></i></a>
			<?php } ?>
			
			</div></div>
		</td>
		<?php
		if($counter % 3 == 0){
				$openTr = false;
				?>
				</tr>
				
				<?php
			}
		?>
	<?php } 
	if($openTr){
			?>
			<tr>
			<?php
		} ?>
	</table>
	
	<?php
}
if($canEdit){
	echo $frm->getFormHtml();
}
?>
</div>

</section>