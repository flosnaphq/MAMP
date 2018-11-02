<?php
defined('SYSTEM_INIT') or die('Invalid Usage');
$frm->setFormTagAttribute('class','form form--default form--vertical');
$frm->developerTags['fld_default_col'] = 12;
$btn_submit = $frm->getField('btn_submit');
$frm->setRequiredStarWith(Form::FORM_REQUIRED_STAR_WITH_NONE);
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
				if(!empty($record['user_id'])){
					if($record['user_id'] == User::getLoggedUserId()){
						$user_name = Info::t_lang('ME');
					}
					else{
						$user_name = @$record['user_firstname'].' '.@$record['user_lastname'];
					}
				}
				else{
					$user_name ='Admin';
				}
				$last_message_id = $record['message_id'];
				$i++;
				$li_class = $record['message_user_id']!= User::getLoggedUserId()?'person--first':'person--second';
				$media_class = $record['message_user_id'] != User::getLoggedUserId()?'media--left':'media--right';
                                       $message_time = FatDate::format($record['message_date'],true);
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
										<br/>
										<em> <?php echo Info::t_lang('BY').' '.$user_name; ?></em>
                                                                                <br/>
                                                                                <span class="message-time"><?php echo $message_time;?></span>
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
<tr id="message_text_td">
	<th >
		<?php echo $frm->getFormHtml();?>
	</th>
</tr>
</tfoot>
</table>
<script>
message_id = '<?php echo $last_message_id; ?>';
</script>