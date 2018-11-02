<?php 
defined('SYSTEM_INIT') or die('Invalid Usage');
if(!empty( $allComments ) ) { 
	foreach( $allComments as $comment ) { ?>
		<div class="comment">
			<div class="media">
				<div class="media__figure media--left">
					<div class="comment__image comnt-prsnImg">
						<span>							 		 
							<img src="<?php echo FatUtility::generateUrl('image','user',array($comment[Blogcomments::DB_TBL_PREFIX.'user_id'],48,48))?>"/>							 			
						</span>
					</div>
				</div>
				<div class="media__body">
			<div class="comment__name">
				<a href="javascript:;">
				<?php echo $comment[Blogcomments::DB_TBL_PREFIX.'author_name']?>
				</a>
				<span class="comment__date"><?php echo date('M d, Y',strtotime($comment[Blogcomments::DB_TBL_PREFIX.'date_time']))?></span>
			</div>
				</div>
			</div>
			<div class="comment__text">
			<p><?php echo $comment[Blogcomments::DB_TBL_PREFIX.'content']?> </p>
			 </div>
		</div>
	<?php }
	}
	else{
		//echo Helper::noRecord(Info::t_lang('NO_COMMENT'));
	}
?>