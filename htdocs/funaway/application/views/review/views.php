<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$frm->setFormTagAttribute('class','form form--default form--vertical');
$frm->developerTags['fld_default_col'] = 12;
$btn_submit = $frm->getField('btn_submit');
$btn_submit->setFieldTagAttribute('class','button button--fill button--red fl--right');
$last_message_id = 0;
?>
<table class="table table--fixed table--bordered table--responsive reply">


<tbody>
<tr>
   <td>
	   <?php 
	   if(!empty($records)){
			?>
			 <ul class="list list--vertical comment__list scrollable--y" style="max-height:1000px" id="chat">
			 <?php 
				$total_count = count($records);
				$i=0;
				foreach($records as $record){ 
				$last_message_id = $record['message_id'];
				$i++;
				$li_class = $thread['messagethread_first_user_id'] == $record['message_user_id']?'person--first':'person--second';
				$media_class = $thread['messagethread_first_user_id'] == $record['message_user_id']?'media--left':'media--right';
				?>
				<li class="person <?php echo $li_class; ?>" id="<?php if($total_count == $i) echo 'last_msg'?>">
					<div class="media person__media">
						<div class="media__figure <?php echo $media_class; ?>">
						   <a class="person__image">
								<img src="<?php echo FatUtility::generateUrl('image','user',array($record['message_user_id'],220,220))?>" alt="" class="image">
							</a>
						</div>
						<div class="media__body">
							<div class="person__comment">
								<ul class="list list--vertical comment__list">
									<li class="comment">
										<?php echo nl2br($record['message_text']);?>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</li>
			 <?php } ?>
			 </ul>
			<?php
	   }
	   ?>
	   
	</td>
</tr>
</tbody>

<tfoot>
<tr>
	<td>
		<?php echo $frm->getFormHtml();?>
		
	</td>
</tr>
</tfoot>
</table>
<script>
message_id = '<?php echo $last_message_id; ?>';
</script>