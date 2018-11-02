<div class='video-section'>
<?php if(!empty($videos)){
    $delete = '<svg class="icon icon--search"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-delete"></use></svg>';
    
    ?>
<ul class="gallery gallery--3 thumb__list video__list">
<?php foreach($videos as $vid){ 
?> 
	<li class="gallery__item thumb__item">
		<?php if($vid['activityvideo_type'] == 2){?>
			<a  class="thumb__iframe youtube_video" rel="facebox" href="https://player.vimeo.com/video/<?php echo $vid['activityvideo_videoid']?>" >
				<iframe src="https://player.vimeo.com/video/<?php echo $vid['activityvideo_videoid']?>" width="100%" height="100%" frameborder="0" ></iframe>
			</a>
		<?php } else { ?>	
			<a  class="thumb__iframe youtube_video" rel="facebox" href="https://www.youtube.com/embed/<?php echo $vid['activityvideo_videoid']?>">
				<iframe  src="https://www.youtube.com/embed/<?php echo $vid['activityvideo_videoid']?>" width="100%" height="100%" frameborder="0" ></iframe>
			</a>	
		<?php } ?>
		<div class="buttons__group">
			<a href="javascript:;" ng-click="removeVideo(<?php echo $vid['activityvideo_id']?>)" class="button button--small button--fill button--red thumb__delete"><?php echo $delete?></a>
		</div>
	</li>
<?php } ?>
</ul>
<?php }else{?>
	<?php echo Info::t_lang('NO_VIDEOS_YET');?>
<?php } ?>
</div>	
<hr>

<?php 
$frm->setRequiredStarPosition(Form::FORM_REQUIRED_STAR_POSITION_NONE);
$frm->setFormTagAttribute('class', 'form form--default form--horizontal');
$frm->setFormTagAttribute('id', 'frmVideo');
$frm->setFormTagAttribute('action', FatUtility::generateUrl("manage-activity",'saveVideo'));
$frm->setValidatorJsObjectName('setup3Validator');
$frm->setFormTagAttribute('ng-submit', 'saveVideo($event); return(false);');
$frm->developerTags['fld_default_col'] =12;
echo $frm->getFormHtml();
?>